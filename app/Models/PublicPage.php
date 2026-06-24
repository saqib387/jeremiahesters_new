<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicPage extends Model
{
    use HasFactory;

    protected $table = 'public_pages';

    protected $fillable = [
        'slug',
        'title',
        'short_title',
        'content',
        'is_privacy',
        'is_tos',
        'shown_in_footer',
        'page_order',
    ];

    protected $casts = [
        'is_privacy' => 'boolean',
        'is_tos' => 'boolean',
        'shown_in_footer' => 'boolean',
    ];

    // Scope to get only privacy pages
    public function scopePrivacy($query)
    {
        return $query->where('is_privacy', 1);
    }

    // Scope to get only terms of service pages
    public function scopeTermsOfService($query)
    {
        return $query->where('is_tos', 1);
    }

    // Scope to get pages shown in footer
    public function scopeFooterPages($query)
    {
        return $query->where('shown_in_footer', 1);
    }

    // Get privacy page
    public static function getPrivacyPage()
    {
        return self::where('is_privacy', 1)->first();
    }

    // Get terms of service page
    public static function getTermsPage()
    {
        return self::where('is_tos', 1)->first();
    }
}