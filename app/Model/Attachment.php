<?php

namespace App\Model;

use App\Providers\AttachmentServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Log;

class Attachment extends Model
{
    public const PUBLIC_DRIVER = 0;
    public const S3_DRIVER = 1;
    public const WAS_DRIVER = 2;
    public const DO_DRIVER = 3;
    public const MINIO_DRIVER = 4;
    public const PUSHR_DRIVER = 5;

    // Disable auto incrementing as we set the id manually (uuid)
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'post_id', 'filename', 'type', 'id', 'driver', 'payment_request_id', 'message_id', 'coconut_id', 'has_thumbnail', 'file_size', 'mime_type',
    ];

    protected $appends = ['attachmentType', 'path', 'thumbnail'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
    ];

    /*
     * Virtual attributes
     */

    public function getAttachmentTypeAttribute()
    {
        return AttachmentServiceProvider::getAttachmentType($this->type);
    }

    public function getPathAttribute()
    {
        $path = AttachmentServiceProvider::getFilePathByAttachment($this);
        
        // Try to fix path issues with post/posts directories
        if (!$this->fileExists($path)) {
            // If the original path doesn't exist, try the alternative path
            Log::warning("File not found at: " . $path);
            
            // Try alternate path format 
            if (strpos($path, 'post/images') !== false) {
                $alternatePath = str_replace('post/images', 'posts/images', $path);
                Log::info("Trying alternate path: " . $alternatePath);
                
                // If the file exists at the alternate path, return it
                if ($this->fileExists($alternatePath)) {
                    Log::info("File found at alternate path: " . $alternatePath);
                    return $alternatePath;
                }
            } elseif (strpos($path, 'posts/images') !== false) {
                $alternatePath = str_replace('posts/images', 'post/images', $path);
                Log::info("Trying alternate path: " . $alternatePath);
                
                // If the file exists at the alternate path, return it
                if ($this->fileExists($alternatePath)) {
                    Log::info("File found at alternate path: " . $alternatePath);
                    return $alternatePath;
                }
            }
        }
        
        return $path;
    }

    /**
     * Helper method to check if a file exists at a given URL path
     */
    private function fileExists($path)
    {
        // For local storage, check if file exists in filesystem
        if ($this->driver == self::PUBLIC_DRIVER) {
            // Extract the relative path from the URL
            $urlParts = parse_url($path);
            if (isset($urlParts['path'])) {
                $relativePath = trim(str_replace('/storage', '', $urlParts['path']), '/');
                return Storage::disk('public')->exists($relativePath);
            }
        }
        
        // For remote storage, we can't reliably check file existence
        return true;
    }

    public function getThumbnailAttribute()
    {
        $path = '/post/images/';
        if ($this->message_id) {
            $path = '/messenger/images/';
        }
        if($this->type == 'video'){
            $path = 'post/videos'.'/thumbnails/'.$this->id.'.jpg';
        }
        return AttachmentServiceProvider::getThumbnailPathForAttachmentByResolution($this, 150, 150, $path);
    }

    /*
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function post()
    {
        return $this->belongsTo('App\Model\Post', 'post_id');
    }

    public function paymentRequest()
    {
        return $this->belongsTo('App\Model\PaymentRequest', 'payment_request_id');
    }
}
