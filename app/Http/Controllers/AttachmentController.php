<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadAttachamentRequest;
use App\Model\Attachment;
use App\Providers\AttachmentServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Http\UploadedFile;
use Log;
use Pusher\Pusher;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /**
     * Process the attachment and upload it to the selected storage driver.
     *
     * @param UploadAttachamentRequest $request
     * @param bool $type Dummy param to follow route parameters
     * @param bool $chunkedFile If using chunk uploads, this final chunked file is sent over this request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, $type = false, $chunkedFile = false)
    {
        // Simple defensive code first
        try {
            // Log incoming parameters
            Log::info('AttachmentController::upload called', [
                'type' => $type,
                'has_file' => $request->hasFile('file'),
                'has_chunked' => $chunkedFile ? true : false
            ]);
            
            // Check if file exists
            if (!$request->hasFile('file') && !$chunkedFile) {
                return response()->json([
                    'success' => false, 
                    'errors' => ['error' => 'No file provided']
                ], 422);
            }

            // Get file from request or chunked
            if ($chunkedFile) {
                $file = $chunkedFile;
            } else {
                $file = $request->file('file');
            }

            if (!$file) {
                return response()->json(['success' => false, 'errors' => ['error' => 'Invalid file']], 422);
            }

            // Get route type parameter
            $type = $request->route('type');

            // Set up storage
            $fileSystem = config('filesystems.defaultFilesystemDriver', 'public');
            $storage = Storage::disk($fileSystem);

            // Define directory based on file mime type
            $fileMimeType = $file->getMimeType();
            Log::info('File mime type: ' . $fileMimeType);
            
            // Fixed directory structure to match storage/app/public/post/images
            if ($type == 'post') {
                if (strpos($fileMimeType, 'image') !== false) {
                    $directory = 'post/images';
                } elseif (strpos($fileMimeType, 'video') !== false) {
                    $directory = 'post/videos';
                } elseif (strpos($fileMimeType, 'audio') !== false) {
                    $directory = 'post/audio';
                } else {
                    $directory = 'post/files';
                }
            } else {
                // For other types, keep the old structure
                if (strpos($fileMimeType, 'image') !== false) {
                    $directory = $type . '/images';
                } elseif (strpos($fileMimeType, 'video') !== false) {
                    $directory = $type . '/videos';
                } elseif (strpos($fileMimeType, 'audio') !== false) {
                    $directory = $type . '/audio';
                } else {
                    $directory = $type . '/files';
                }
            }
            
            // Log the directory path
            Log::info('Storing file in directory: ' . $directory);

            // Create directory if needed
            if (!$storage->exists($directory)) {
                $storage->makeDirectory($directory);
            }

            // Generate unique filename
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $fullPath = $directory . '/' . $fileName;

            // Simple stream copy
            $stream = fopen($file->getRealPath(), 'r');
            $storage->put($fullPath, $stream, 'public');
            if (is_resource($stream)) {
                fclose($stream);
            }

            // Create a simple attachment record with all columns
            $attachment = new Attachment();
            $attachment->id = uniqid();
            $attachment->user_id = Auth::check() ? Auth::id() : null;
            $attachment->filename = $fullPath;
            $attachment->type = $fileMimeType;
            $attachment->file_size = $file->getSize();
            $attachment->mime_type = $fileMimeType;
            $attachment->has_thumbnail = false; // Default to false, will set to true if we create a thumbnail
            
            // Set driver to default (PUBLIC_DRIVER = 0)
            $attachment->driver = Attachment::PUBLIC_DRIVER;
            
            // Set additional attributes (based on file type)
            if ($type == 'post') {
                $post_id = $request->input('post_id');
                if ($post_id) {
                    $attachment->post_id = $post_id;
                }
            } elseif ($type == 'message') {
                $message_id = $request->input('message_id');
                if ($message_id) {
                    $attachment->message_id = $message_id;
                }
            }
            
            // Generate thumbnail if it's an image
            $thumbnailPath = null;
            if (strpos($fileMimeType, 'image') !== false) {
                try {
                    $thumbnailDir = $directory . '/thumbnails';
                    if (!$storage->exists($thumbnailDir)) {
                        $storage->makeDirectory($thumbnailDir);
                    }
                    
                    // Create thumbnail using PHP's GD library
                    $image = \Image::make($file->getRealPath());
                    $image->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    
                    $thumbnailName = 'thumb_' . $fileName;
                    $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                    
                    // Save thumbnail
                    $storage->put($thumbnailPath, $image->encode()->encoded, 'public');
                    
                    // Set has_thumbnail flag
                    $attachment->has_thumbnail = true;
                } catch (\Exception $e) {
                    Log::error('Error creating thumbnail: ' . $e->getMessage());
                }
            }
            
            // Save attachment
            $attachment->save();

            // Return success with attachment info
            return response()->json([
                'success' => true,
                'attachmentID' => $attachment->id,
                'path' => Storage::url($attachment->filename),
                'type' => AttachmentServiceProvider::getAttachmentType($attachment->type),
                'thumbnail' => $thumbnailPath ? Storage::url($thumbnailPath) : null,
                'coconut_id' => $attachment->coconut_id,
                'has_thumbnail' => $attachment->has_thumbnail,
                'file_size' => $attachment->file_size,
            ], 200);

        } catch (\Exception $exception) {
            // Log the error
            Log::error('Upload attachment error: ' . $exception->getMessage());
            Log::error($exception->getTraceAsString());
            
            // Return error response
            return response()->json([
                'success' => false,
                'errors' => ['error' => 'File upload failed: ' . $exception->getMessage()],
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Chunk uploadining method.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UploadMissingFileException
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     */

     public function uploadChunk(UploadAttachamentRequest $request, $type = false, UploadedFile|bool $chunkedFile = false){
     $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        $save = $receiver->receive();
        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            $saveRequest = new UploadAttachamentRequest(['file'=>$save->getFile()]);
            $saveRequest->validate($saveRequest->rules());
            return $this->upload($saveRequest, $type, $save->getChunk());
        }
        // we are in chunk mode, lets send the current progress
        $handler = $save->handler();
        return response()->json(['success' => true, 'data' => ['percentage'=>$handler->getPercentageDone()]]);
    }
    
    /**
     * Simple diagnostic upload test
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testUpload(Request $request) {
        try {
            $file = $request->file('file');
            if (!$file) {
                return response()->json(['success' => false, 'message' => 'No file detected']);
            }
            
            $mimeType = $file->getMimeType();
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $size = $file->getSize();
            
            // Test file upload directly - save to a test directory
            $fileInfo = [
                'success' => true, 
                'message' => 'File detected successfully',
                'mime' => $mimeType,
                'extension' => $extension,
                'name' => $originalName,
                'size' => $size
            ];
            
            // Try direct storage to see if it works
            try {
                $storage = Storage::disk(config('filesystems.defaultFilesystemDriver'));
                $testDirectory = 'test_uploads';
                
                if (!$storage->exists($testDirectory)) {
                    $storage->makeDirectory($testDirectory);
                }
                
                $randomName = md5(time() . $originalName) . '.' . $extension;
                $testPath = $testDirectory . '/' . $randomName;
                
                // Try storing with different methods
                $stream = fopen($file->getRealPath(), 'r');
                $storage->put($testPath, $stream, 'public');
                if (is_resource($stream)) {
                    fclose($stream);
                }
                
                $fileInfo['direct_storage'] = [
                    'success' => true,
                    'path' => $testPath,
                    'url' => $storage->url($testPath)
                ];
            } catch (\Exception $e) {
                $fileInfo['direct_storage'] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
            
            return response()->json($fileInfo, 200, [], JSON_INVALID_UTF8_IGNORE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 200, [], JSON_INVALID_UTF8_IGNORE);
        }
    }
    
    /**
     * Show a helpful error page for upload issues
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function uploadError(Request $request) {
        $errorMessage = $request->get('error', 'Unknown upload error');
        return view('errors.upload', ['errorMessage' => $errorMessage]);
    }

    /**
     * Removes attachment out of db & out of the storage driver.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAttachment(Request $request)
    {
        try {
            $attachment = Attachment::where('id', $request->get('attachmentId'))->first();
            if ($attachment != null) {
                AttachmentServiceProvider::removeAttachment($attachment);
                $attachment->delete();
            }
            return response()->json(['success' => true, 'data' => [__('Attachments removed successfully')]]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]]);
        }
    }

    /**
     * Handles coconut webhook.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Pusher\ApiErrorException
     * @throws \Pusher\PusherException
     */
    public static function handleCoconutHook(Request $request) {

        Log::channel('coconut')->info(__("New coconut payload available"));
        Log::channel('coconut')->info(json_encode($request->all()));

        $attachmentID = $request->get('attachmentId');
        $attachment = Attachment::where('id', $attachmentID)->first();
        $username = $attachment->user->username;

        if(config('broadcasting.connections.pusher.key')){
            $options = [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
            ];
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                $options
            );
        }

        if($request->get('event') === 'job.completed'){
            // 2. Delete the temporary attachment that got transcoded
            $storage = Storage::disk(AttachmentServiceProvider::getStorageProviderName($attachment->driver));
            $storage->delete($attachment->filename);

            $attachment->filename = "posts/videos/{$attachmentID}.mp4";
            $attachment->type = "mp4";
            $attachment->has_thumbnail = 1;
            $attachment->save();

            // Notify the UI via a websocket call
            if(config('broadcasting.connections.pusher.key')){
                unset($attachment->user);
                $attachment->setAttribute('success', true);
                $pusher->trigger($username, 'video-processing', $attachment);
            }
        }
        elseif($request->get('event') === 'job.failed' || $request->get('event') === 'output.failed'){
            // Notify the UI via a websocket call
            if(config('broadcasting.connections.pusher.key')){
                $attachment->setAttribute('success', false);
                $pusher->trigger($username, 'video-processing', $attachment);
            }
        }

        return response()->json(['success' => true, 'message' => __("Video updated")], 200);

    }

    /**
     * Check storage configuration
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStorage()
    {
        try {
            $result = [
                'success' => true,
                'storage' => [
                    'driver' => config('filesystems.default'),
                    'default_filesystem_driver' => config('filesystems.defaultFilesystemDriver'),
                    'disks' => []
                ],
                'directories' => []
            ];
            
            // Check public disk
            $publicDisk = Storage::disk('public');
            $result['disks']['public'] = [
                'exists' => true,
                'url' => $publicDisk->url(''),
                'permissions' => [
                    'storage_path_writable' => is_writable(storage_path('app/public'))
                ]
            ];
            
            // Check directories
            $directories = [
                'post/images',
                'post/videos',
                'post/audio',
                'message/images',
                'message/videos',
                'message/audio'
            ];
            
            foreach ($directories as $dir) {
                if (!$publicDisk->exists($dir)) {
                    $publicDisk->makeDirectory($dir);
                }
                
                $result['directories'][$dir] = [
                    'exists' => $publicDisk->exists($dir),
                    'path' => storage_path('app/public/' . $dir),
                    'url' => $publicDisk->url($dir),
                    'writable' => is_writable(storage_path('app/public/' . $dir))
                ];
            }
            
            // Test file creation
            $testFile = 'test-' . time() . '.txt';
            $publicDisk->put($testFile, 'Test file content');
            $result['test_file'] = [
                'created' => $publicDisk->exists($testFile),
                'url' => $publicDisk->url($testFile)
            ];
            
            // Clean up test file
            $publicDisk->delete($testFile);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
