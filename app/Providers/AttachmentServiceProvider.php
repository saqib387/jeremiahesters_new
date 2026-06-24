<?php

namespace App\Providers;

use App\Model\Attachment;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use FFMpeg\Filters\Video\CustomFilter;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Facades\Image;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Ramsey\Uuid\Uuid;

class AttachmentServiceProvider extends ServiceProvider
{
    // Mixed for ffmpeg and coconut
    public static $videoEncodingPresets = [
        'size' => ['videoBitrate'=> 500, 'audioBitrate' => 128, 'quality' => 1],
        'balanced' => ['videoBitrate'=> 1000, 'audioBitrate' => 256, 'quality' => 3],
        'quality' => ['videoBitrate'=> 2000, 'audioBitrate' => 512, 'quality' => 5],
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Filter attachments by their extension.
     *
     * @param bool $type
     * @return bool|\Illuminate\Config\Repository|mixed|string|null
     */
    public static function filterExtensions($type = false)
    {
        if ($type) {
            switch ($type) {
                case 'videosFallback':
                    if (getSetting('media.transcoding_driver') === 'ffmpeg' || getSetting('media.transcoding_driver') === 'coconut') {
                        return getSetting('media.allowed_file_extensions');
                    } else {
                        $extensions = explode(',', getSetting('media.allowed_file_extensions'));
                        $extensions = array_diff($extensions, self::getTypeByExtension('video'));
                        $extensions[] = 'mp4';
                        return implode(',', $extensions);
                    }
                    break;
                case 'imagesOnly':
                    return implode(',', self::getTypeByExtension('images'));
                    break;
                case 'manualPayments':
                    return 'jpg,jpeg,png,pdf,xls,xlsx';
                    break;
            }
        }

        return false;
    }

    /**
     * Get attachment type by extension.
     *
     * @param $type
     * @return string
     */
    public static function getAttachmentType($type)
    {
        switch ($type) {
            case 'avi':
            case 'mp4':
            case 'wmw':
            case 'mpeg':
            case 'm4v':
            case 'moov':
            case 'mov':
            case 'mkv':
            case 'wmv':
            case 'asf':
                return 'video';
                break;
            case 'mp3':
            case 'wav':
            case 'ogg':
                return 'audio';
                break;
            case 'png':
            case 'jpg':
            case 'jpeg':
                return 'image';
            case 'pdf':
            case 'xls':
            case 'xlsx':
                return 'document';
                break;
            default:
                return 'image';
                break;
        }
    }

    /**
     * Get file extensions by types.
     *
     * @param $type
     * @return array
     */
    public static function getTypeByExtension($type)
    {
        switch ($type) {
            case 'video':
                return ['mp4', 'avi', 'wmv', 'mpeg', 'm4v', 'moov', 'mov', 'mkv', 'asf'];
                break;
            case 'audio':
                return ['mp3', 'wav', 'ogg'];
                break;
            default:
                return ['jpg', 'jpeg', 'png'];
                break;
        }
    }

    /**
     * Return matching bookmarks category types to actual attachment types.
     *
     * @param $type
     * @return bool|string
     */
    public static function getActualTypeByBookmarkCategory($type)
    {
        switch ($type) {
            case 'photos':
                return 'image';
                break;
            case 'audio':
                return 'audio';
                break;
            case 'videos':
                return 'video';
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Creates attachment for the feed post.
     *
     * @param $file
     * @param $directory
     * @return mixed
     * @throws \Exception
     */
    public static function createAttachment($file, $directory, $generateThumbnail = false)
    {
        // Log detailed information about the file being processed
        \Log::info('AttachmentServiceProvider::createAttachment - Processing file', [
            'fileSize' => $file->getSize(),
            'mimeType' => $file->getMimeType(),
            'clientOriginalName' => $file->getClientOriginalName(),
            'directory' => $directory
        ]);

        try {
            // Ensure storage directory exists and is writable
            $storage = \Storage::disk(config('filesystems.defaultFilesystemDriver'));
            
            // Try to create directory with permissions if it doesn't exist
            if (!$storage->exists($directory)) {
                try {
                    $storage->makeDirectory($directory, 0755, true);
                    \Log::info("AttachmentServiceProvider - Directory created: {$directory}");
                } catch (\Exception $e) {
                    \Log::error("AttachmentServiceProvider - Failed to create directory: " . $e->getMessage());
                }
            }
            
            // Generate unique filename
            $encryptedName = self::generateAttachmentEncryptedFileName($file);
            $fileName = $directory . '/' . $encryptedName;
            
            \Log::info('AttachmentServiceProvider - Saving file', [
                'fileName' => $fileName,
                'storage' => config('filesystems.defaultFilesystemDriver')
            ]);

            // Store file to disk
            $stream = fopen($file->getRealPath(), 'r');
            $storage->put($fileName, $stream, 'public');
            if (is_resource($stream)) {
                fclose($stream);
            }

            // GD library check
            if (!extension_loaded('gd') && $generateThumbnail) {
                \Log::warning('AttachmentServiceProvider - GD library not available for thumbnail generation');
                $generateThumbnail = false;
            }

            // Get file mime type
            $attachmentType = self::getAttachmentType($file->getMimeType());
            $thumbnailPath = null;
            
            // Create thumbnail
            if($generateThumbnail && in_array($attachmentType, ['image','video'])){
                try {
                    $thumbnailDir = $directory.'/thumbnails';
                    $thumbnail150Dir = $directory.'/150X150';
                    
                    // Create thumbnail directories if needed
                    if (!$storage->exists($thumbnailDir)) {
                        $storage->makeDirectory($thumbnailDir, 0755, true);
                    }
                    if (!$storage->exists($thumbnail150Dir)) {
                        $storage->makeDirectory($thumbnail150Dir, 0755, true);
                    }
                    
                    // Create thumbnail
                    if($attachmentType == 'image'){
                        try {
                            // Use Intervention Image v2 (legacy) since that's what's installed
                            $image = Image::make($file);
                            
                            // Process for thumbnails dir
                            if(!$image->width() || !$image->height()){
                                // Skip thumbnail creation if we can't detect dimensions
                                \Log::warning("AttachmentServiceProvider - Could not detect image dimensions");
                            } else {
                                // Regular thumbnail
                                self::createThumbnail($storage, $image, $thumbnailDir.'/'.$encryptedName, 500, 800);
                                
                                // 150x150 thumbnail
                                self::createThumbnail($storage, $image, $thumbnail150Dir.'/'.$encryptedName, 150, 150);
                            }
                            $hasThumbnail = true;
                            $thumbnailPath = $thumbnailDir.'/'.$encryptedName;
                        } catch (\Exception $e) {
                            \Log::error('AttachmentServiceProvider - Image processing error: ' . $e->getMessage());
                            $hasThumbnail = false;
                            $thumbnailPath = null;
                        }
                        
                    } elseif($attachmentType == 'video' && self::shouldProcessVideo()){
                        // For videos, set placeholder thumbnail initially
                        $hasThumbnail = false;
                        $thumbnailPath = $thumbnailDir.'/'.$encryptedName.'.jpg';
                    }
                } catch (\Exception $exception) {
                    \Log::error('AttachmentServiceProvider - Thumbnail creation error: ' . $exception->getMessage());
                    $hasThumbnail = false;
                    $thumbnailPath = null;
                }
            }

            // Create attachment
            $attachment = new Attachment();
            // Generate UUID for attachment ID (required since auto-increment is disabled)
            $attachment->id = Uuid::uuid4()->toString();
            $attachment->user_id = Auth::user() ? Auth::user()->id : null;
            $attachment->filename = $fileName;
            $attachment->file_size = $file->getSize();
            $attachment->mime_type = $file->getMimeType();
            $attachment->type = self::getActualAttachmentType($file->getMimeType());
            $attachment->has_thumbnail = $generateThumbnail ? (isset($hasThumbnail) ? $hasThumbnail : false) : false;
            // Note: thumbnail_path is not stored in DB - it's calculated dynamically via Attachment::getThumbnailAttribute()
            $attachment->driver = self::getStorageProviderID(getSetting('storage.driver'));
            $attachment->save();
            
            \Log::info('AttachmentServiceProvider - Attachment created successfully', [
                'attachmentId' => $attachment->id,
                'type' => $attachment->type,
                'has_thumbnail' => $attachment->has_thumbnail
            ]);

            return $attachment;

        } catch (\Exception $exception) {
            \Log::error('AttachmentServiceProvider - Error: ' . $exception->getMessage());
            \Log::error($exception->getTraceAsString());
            throw $exception;
        }
    }

    /**
     * Method used to return real watermark path / fallback to the default one.
     *
     * @return mixed|string
     */
    public static function getWatermarkPath()
    {
        $watermark_image = getSetting('media.watermark_image');
        if($watermark_image){
            if (strpos($watermark_image, 'download_link')) {
                $watermark_image = json_decode($watermark_image);
                if ($watermark_image) {
                    $watermark_image = Storage::disk(config('filesystems.defaultFilesystemDriver'))->path($watermark_image[0]->download_link);
                }
            }
        }
        else{
            $watermark_image = public_path('img/logo-black.png');
        }
        return $watermark_image;
    }

    /**
     * Returns thumbnail path for attachment by resolution.
     * @param $attachment
     * @param $width
     * @param $height
     * @param string $basePath
     * @return string|string[]
     */
    public static function getThumbnailPathForAttachmentByResolution($attachment, $width, $height, $basePath = '/post/images/')
    {
        // Log path information for debugging
        \Log::info('Getting thumbnail path', [
            'attachment_id' => $attachment->id,
            'filename' => $attachment->filename,
            'base_path' => $basePath,
            'width' => $width,
            'height' => $height
        ]);
        
        if ($attachment->driver == Attachment::S3_DRIVER && getSetting('storage.aws_cdn_enabled') && getSetting('storage.aws_cdn_presigned_urls_enabled')) {
            return self::signAPrivateDistributionPolicy(
                'https://'.getSetting('storage.cdn_domain_name').'/'.self::getThumbnailFilenameByAttachmentAndResolution($attachment, $width, $height, $basePath)
            );
        } else {
            if(self::getAttachmentType($attachment->type) == 'video'){
                // Videos
                return  str_replace($attachment->id.'.'.$attachment->type, 'thumbnails/'.$attachment->id.'.jpg', $attachment->path);
            }
            else{
                // Regular posts + messages
                // Handle both post/images and posts/images path formats
                $correctPath = str_replace('posts/images', 'post/images', $attachment->path);
                return str_replace($basePath, $basePath.$width.'X'.$height.'/', $correctPath);
            }
        }
    }

    /**
     * Returns file thumbnail relative path, by resolution.
     * [Used to get storage paths].
     * @param $attachment
     * @param $width
     * @param $height
     * @return string|string[]
     */
    public static function getThumbnailFilenameByAttachmentAndResolution($attachment, $width, $height, $basePath = 'post/images/')
    {
        // Normalize path to ensure consistency
        $normalizedPath = $attachment->filename;
        $normalizedPath = str_replace('posts/images', 'post/images', $normalizedPath);
        
        if(self::getAttachmentType($attachment->type) == 'video'){
            return 'post/videos/thumbnails/'.$attachment->id.'.jpg';
        }
        else{
            return str_replace($basePath, $basePath.$width.'X'.$height.'/', $normalizedPath);
        }
    }

    /**
     * Removes attachment from storage disk.
     *
     * @param $attachment
     */
    public static function removeAttachment($attachment)
    {
        $storage = Storage::disk(self::getStorageProviderName($attachment->driver));
        $storage->delete($attachment->filename);
        if (self::getAttachmentType($attachment->type) == 'image' || self::getAttachmentType($attachment->type) == 'video') {
            $thumbnailPath = self::getThumbnailFilenameByAttachmentAndResolution($attachment, $width = 150, $height = 150);
            if ($thumbnailPath != null) {
                $storage->delete($thumbnailPath);
            }
        }
    }

    /**
     * Returns file path by attachment.
     *
     * @param $attachment
     * @return string
     */
    public static function getFilePathByAttachment($attachment)
    {

        // Changing to attachment file system driver, if different from the configured one
        if($attachment->driver !== self::getStorageProviderID(getSetting('storage.driver'))){
            $oldDriver = config('filesystems.default');
            SettingsServiceProvider::setDefaultStorageDriver(self::getStorageProviderName($attachment->driver));
        }

        $fileUrl = '';
        if ($attachment->driver == Attachment::S3_DRIVER) {
            if (getSetting('storage.aws_cdn_enabled') && getSetting('storage.aws_cdn_presigned_urls_enabled')) {
                $fileUrl = self::signAPrivateDistributionPolicy(
                    'https://'.getSetting('storage.cdn_domain_name').'/'.$attachment->filename
                );
            } elseif (getSetting('storage.aws_cdn_enabled')) {
                $fileUrl = 'https://'.getSetting('storage.cdn_domain_name').'/'.$attachment->filename;
            } else {
                $fileUrl = 'https://'.getSetting('storage.aws_bucket_name').'.s3.'.getSetting('storage.aws_region').'.amazonaws.com/'.$attachment->filename;
            }
        }
        elseif ($attachment->driver == Attachment::WAS_DRIVER || $attachment->driver == Attachment::DO_DRIVER) {
            $fileUrl = Storage::url($attachment->filename);
        }
        elseif($attachment->driver == Attachment::MINIO_DRIVER){
            $fileUrl = rtrim(getSetting('storage.minio_endpoint'), '/').'/'.getSetting('storage.minio_bucket_name').'/'.$attachment->filename;
        }
        elseif($attachment->driver == Attachment::PUSHR_DRIVER){
            $fileUrl = rtrim(getSetting('storage.pushr_cdn_hostname'), '/').'/'.$attachment->filename;
        }
        elseif ($attachment->driver == Attachment::PUBLIC_DRIVER) {
            $fileUrl = Storage::disk('public')->url($attachment->filename);
        }

        // Changing filesystem driver back, if needed
        if($attachment->driver !== self::getStorageProviderID(getSetting('storage.driver'))) {
            SettingsServiceProvider::setDefaultStorageDriver($oldDriver);
        }
        return $fileUrl;
    }

    /**
     * Method used for signing assets via CF.
     *
     * @param $cloudFrontClient
     * @param $resourceKey
     * @param $customPolicy
     * @param $privateKey
     * @param $keyPairId
     * @return mixed
     */
    private static function signPrivateDistributionPolicy(
        $cloudFrontClient,
        $resourceKey,
        $customPolicy,
        $privateKey,
        $keyPairId
    ) {
        try {
            $result = $cloudFrontClient->getSignedUrl([
                'url' => $resourceKey,
                'policy' => $customPolicy,
                'private_key' => $privateKey,
                'key_pair_id' => $keyPairId,
            ]);

            return $result;
        } catch (AwsException $e) {
        }
    }

    /**
     * Method used for signing assets via CF.
     *
     * @param $resourceKey
     * @return mixed
     */
    public static function signAPrivateDistributionPolicy($resourceKey)
    {
        $resourceKey = str_replace('\\', '/', $resourceKey); // Windows glitching otherwise
        $expires = time() + 24 * 60 * 60; // 24 hours (60 * 60 seconds) from now.
        $customPolicy = <<<POLICY
{
    "Statement": [
        {
            "Resource": "{$resourceKey}",
            "Condition": {
                "IpAddress": {"AWS:SourceIp": "{$_SERVER['REMOTE_ADDR']}/32"},
                "DateLessThan": {"AWS:EpochTime": {$expires}}
            }
        }
    ]
}
POLICY;
        $privateKey = base_path().'/'.getSetting('storage.aws_cdn_private_key_path');
        $keyPairId = getSetting('storage.aws_cdn_key_pair_id');

        $cloudFrontClient = new CloudFrontClient([
            'profile' => 'default',
            'version' => '2014-11-06',
            'region' => 'us-east-1',
        ]);

        return self::signPrivateDistributionPolicy(
            $cloudFrontClient,
            $resourceKey,
            $customPolicy,
            $privateKey,
            $keyPairId
        );
    }

    public static function getStorageProviderID($storageDriver) {
        if($storageDriver)
            if($storageDriver == 'public'){
                return Attachment::PUBLIC_DRIVER;
            }
        if($storageDriver == 's3'){
            return Attachment::S3_DRIVER;
        }
        if($storageDriver == 'wasabi'){
            return Attachment::WAS_DRIVER;
        }
        if($storageDriver == 'do_spaces'){
            return Attachment::DO_DRIVER;
        }
        if($storageDriver == 'minio'){
            return Attachment::MINIO_DRIVER;
        }
        if($storageDriver == 'pushr'){
            return Attachment::PUSHR_DRIVER;
        }
        else{
            return Attachment::PUBLIC_DRIVER;
        }
    }

    public static function getStorageProviderName($storageDriver) {
        if($storageDriver)
            if($storageDriver == Attachment::PUBLIC_DRIVER){
                return 'public';
            }
        if($storageDriver == Attachment::S3_DRIVER){
            return 's3';
        }
        if($storageDriver == Attachment::WAS_DRIVER){
            return 'wasabi';
        }
        if($storageDriver == Attachment::DO_DRIVER){
            return 'do_spaces';
        }
        if($storageDriver == Attachment::MINIO_DRIVER){
            return 'minio';
        }
        if($storageDriver == Attachment::PUSHR_DRIVER){
            return 'pushr';
        }
        else{
            return 'public';
        }
    }

    /**
     * Copies file from pushr to local, then copies the files on pushr again
     * Pushrcdn can't do $storage->copy due to failing AWSS3Adapter::getRawVisibility.
     * @param $attachment
     * @param $newFileName
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function pushrCDNCopy($attachment, $newFileName) {
        $storage = Storage::disk(self::getStorageProviderName($attachment->driver));
        // Pushr logic - Copy alternative as S3Adapter fails to do ->copy operations
        $remoteFile = $storage->get($attachment->filename);
        $localStorage = Storage::disk('public');
        $tmpFile = "tmp/".$attachment->id.'.'.$attachment->type;
        $localStorage->put($tmpFile, $remoteFile);
        $storage->put($newFileName, $localStorage->get($tmpFile), 'public');
        $localStorage->delete($tmpFile);
    }

    /**
     * Generates coconut storage configuration.
     * @param $storageDriver
     * @return array|bool
     */
    public static function getCoconutStorageSettings($storageDriver) {
        switch ($storageDriver) {
            case 's3':
                return [
                    'service' => 's3',
                    'bucket' => getSetting('storage.aws_bucket_name'),
                    'region' => getSetting('storage.aws_region'),
                    'credentials' => [
                        'access_key_id' => getSetting('storage.aws_access_key'),
                        'secret_access_key' => getSetting('storage.aws_secret_key'),
                    ],
                ];
            case 'do_spaces':
                return [
                    'service' => 'dospaces',
                    'bucket' => getSetting('storage.do_bucket_name'),
                    'region' => getSetting('storage.do_region'),
                    'credentials' => [
                        'access_key_id' => getSetting('storage.do_access_key'),
                        'secret_access_key' => getSetting('storage.do_secret_key'),
                    ],
                ];
            case 'wasabi':
                return [
                    'service' => 'wasabi',
                    'bucket' => getSetting('storage.was_bucket_name'),
                    'region' => getSetting('storage.was_region'),
                    'credentials' => [
                        'access_key_id' => getSetting('storage.was_access_key'),
                        'secret_access_key' => getSetting('storage.was_secret_key'),
                    ],
                ];
            case 'minio':
                return [
                    'service' => 's3other',
                    'bucket' => getSetting('storage.minio_bucket_name'),
                    'force_path_style' => true,
                    'region' => getSetting('storage.minio_region'),
                    'credentials' => [
                        'access_key_id' => getSetting('storage.minio_access_key'),
                        'secret_access_key' => getSetting('storage.minio_secret_key'),
                    ],
                    'endpoint' => getSetting('storage.minio_endpoint'),
                ];
            case 'pushr':
                return [
                    'service' => 's3other',
                    'bucket' => getSetting('storage.pushr_bucket_name'),
                    'force_path_style' => true,
                    'region' => 'us-east-1',
                    'credentials' => [
                        'access_key_id' => getSetting('storage.pushr_access_key'),
                        'secret_access_key' => getSetting('storage.pushr_secret_key'),
                    ],
                    'endpoint' => getSetting('storage.pushr_endpoint'),
                ];
            default:
                return false;
        }
    }

    /**
     * Attempts to fetch file name from a give url.
     * @param $url
     * @return bool|mixed
     */
    public static function getFileNameFromUrl($url) {
        if(preg_match('/[^\/\\&\?]+\.\w{3,4}(?=([\?&].*$|$))/', $url, $matches)){
            return $matches[0];
        }
        return false;
    }

    /**
     * Generate encrypted/unique filename for attachment.
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public static function generateAttachmentEncryptedFileName($file)
    {
        $extension = $file->getClientOriginalExtension();
        $uniqueId = Uuid::uuid4()->toString();
        $timestamp = time();
        
        // Generate a secure filename: timestamp_uuid.extension
        return $timestamp . '_' . str_replace('-', '', $uniqueId) . '.' . $extension;
    }

    /**
     * Get actual attachment type from mime type.
     * 
     * @param string $mimeType
     * @return string
     */
    public static function getActualAttachmentType($mimeType)
    {
        return self::getAttachmentType($mimeType);
    }

    /**
     * Create thumbnail for image.
     * 
     * @param \Illuminate\Contracts\Filesystem\Filesystem $storage
     * @param mixed $image Intervention Image instance
     * @param string $path
     * @param int $width
     * @param int $height
     * @return void
     */
    private static function createThumbnail($storage, $image, $path, $width, $height)
    {
        try {
            // Resize image maintaining aspect ratio
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Encode and save
            $thumbnailData = (string) $image->encode('jpg', 85);
            $storage->put($path, $thumbnailData, 'public');
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if video should be processed.
     * 
     * @return bool
     */
    private static function shouldProcessVideo()
    {
        return getSetting('media.transcoding_driver') === 'ffmpeg' || 
               getSetting('media.transcoding_driver') === 'coconut';
    }
}
