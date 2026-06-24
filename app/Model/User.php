<?php

namespace App\Model;

use App\Model\Post;
use App\Model\Subscription;
use App\Model\UserList;
use App\Providers\GenericHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class User extends \TCG\Voyager\Models\User implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'role_id', 'password', 'username', 'bio', 'birthdate', 'location', 'website', 'avatar', 'cover', 'postcode', 'settings',
        'wallet_address', // on-chain wallet (embedded or external) for NFT ownership
        'billing_address', 'first_name', 'last_name', 'profile_access_price',
        'gender_id', 'gender_pronoun',
        'profile_access_price_6_months',
        'profile_access_price_12_months',
        'profile_access_price_3_months',
        'public_profile', 'city', 'country', 'state', 'email_verified_at', 'paid_profile',
        'auth_provider', 'auth_provider_id', 'enable_2fa', 'enable_geoblocking', 'open_profile', 'referral_code', 'country_id',
        // Account type
        'account_type',
        // Phone verification
        'phone_number', 'phone_verified_at', 'phone_verification_code',
        // Legal compliance timestamps
        'terms_accepted_at', 'privacy_accepted_at', 'community_guidelines_accepted_at',
        'data_processing_consent_at', 'marketing_consent', 'marketing_consent_at',
        // Age verification
        'age_verified_at', 'age_verification_method',
        // KYC/AML
        'kyc_status', 'kyc_level', 'kyc_verified_at', 'kyc_expiry_date', 'aml_risk_score', 'aml_last_check',
        // Creator compliance
        'creator_terms_accepted_at', 'content_rights_acknowledged_at', 'legal_name', 'tax_id', 'tax_form_submitted', 'tax_form_type',
        // 2257 Compliance
        'compliance_2257_verified', 'compliance_2257_verified_at',
        // Security
        'login_attempts', 'locked_until', 'last_login_at', 'last_login_ip', 'registration_ip', 'fraud_score', 'is_flagged', 'flag_reason',
        'session_timeout_minutes', 'force_logout_at',
        // Transaction limits
        'daily_transaction_limit', 'monthly_transaction_limit', 'withdrawal_limit',
        // GDPR
        'cookie_consent_at', 'gdpr_consent_at', 'data_deletion_requested_at',
        // Social links
        'twitter_url', 'instagram_url', 'tiktok_url',
        // Wallet
        'wallet_address', 'wallet_type', 'wallet_connected_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'public_profile' => 'boolean',
        'settings' => 'array',
        // Phone verification
        'phone_verified_at' => 'datetime',
        // Legal compliance
        'terms_accepted_at' => 'datetime',
        'privacy_accepted_at' => 'datetime',
        'community_guidelines_accepted_at' => 'datetime',
        'data_processing_consent_at' => 'datetime',
        'marketing_consent' => 'boolean',
        'marketing_consent_at' => 'datetime',
        // Age verification
        'age_verified_at' => 'datetime',
        // KYC/AML
        'kyc_verified_at' => 'datetime',
        'kyc_expiry_date' => 'date',
        'aml_risk_score' => 'decimal:2',
        'aml_last_check' => 'datetime',
        // Creator compliance
        'creator_terms_accepted_at' => 'datetime',
        'content_rights_acknowledged_at' => 'datetime',
        'tax_form_submitted' => 'boolean',
        // 2257 Compliance
        'compliance_2257_verified' => 'boolean',
        'compliance_2257_verified_at' => 'datetime',
        // Security
        'locked_until' => 'datetime',
        'last_login_at' => 'datetime',
        'fraud_score' => 'decimal:2',
        'is_flagged' => 'boolean',
        'force_logout_at' => 'datetime',
        // Transaction limits
        'daily_transaction_limit' => 'decimal:8',
        'monthly_transaction_limit' => 'decimal:8',
        'withdrawal_limit' => 'decimal:8',
        // GDPR
        'cookie_consent_at' => 'datetime',
        'gdpr_consent_at' => 'datetime',
        'data_deletion_requested_at' => 'datetime',
        // Wallet
        'wallet_connected_at' => 'datetime',
    ];

    /*
     * Virtual attributes
     * TODO: This causes some issues when we're trying to internally refer to to actual raw values
     * TODO: Maybe refactor
     */
    public function getAvatarAttribute($value)
    {
        return GenericHelperServiceProvider::getStorageAvatarPath($value);
    }

    public function getCoverAttribute($value)
    {
        return GenericHelperServiceProvider::getStorageCoverPath($value);
    }

    /**
     * Gets current count of active subscribers.
     * @return int
     * @throws \Exception
     */
    public function getFansCountAttribute() {
        $activeSubscriptionsCount = Subscription::query()
            ->where('recipient_user_id', Auth::user()->id)
            ->whereDate('expires_at', '>=', new \DateTime('now', new \DateTimeZone('UTC')))
            ->count('id');

        return $activeSubscriptionsCount;
    }

    /**
     * Gets the count of followers.
     * @return int|mixed
     */
    public function getFollowingCountAttribute() {
        $userId = Auth::user()->id;
        $userFollowingMembers = UserList::query()
            ->where(['user_id' => $userId, 'type' => 'following'])
            ->withCount('members')->first();

        return $userFollowingMembers != null && $userFollowingMembers->members_count > 0 ? $userFollowingMembers->members_count : 0;
    }

    public function getIsActiveCreatorAttribute($value)
    {
        if(getSetting('compliance.monthly_posts_before_inactive')){
            $check = Post::where('user_id', $this->id)->where('created_at', '>=', Carbon::now()->subdays(30))->count();
            $hasPassedPreApprovedLimit = true;
            if(getSetting('compliance.admin_approved_posts_limit')){
                $hasPassedPreApprovedLimit = Post::where('user_id', $this->id)->where('status', Post::APPROVED_STATUS)->count();
                $hasPassedPreApprovedLimit = $hasPassedPreApprovedLimit >= (int)getSetting('compliance.admin_approved_posts_limit');
            }
            return $hasPassedPreApprovedLimit && $check >= (int)getSetting('compliance.monthly_posts_before_inactive');
        }
        return true;
    }

    /*
     * Relationships
     */
    public function posts()
    {
            if(getSetting('compliance.admin_approved_posts_limit') > 0) {
                return $this->hasMany('App\Model\Post')->where('status', Post::APPROVED_STATUS);
            } else {
                return $this->hasMany('App\Model\Post');
            }
    }

    public function postComments()
    {
        return $this->hasMany('App\Model\PostComment');
    }

    public function reactions()
    {
        return $this->hasMany('App\Model\Reaction');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Model\Subscription');
    }

    public function activeSubscriptions()
    {
        return $this->hasMany('App\Model\Subscription', 'sender_user_id')->where('status', 'completed');
    }

    public function activeCanceledSubscriptions()
    {
        return $this->hasMany('App\Model\Subscription', 'sender_user_id')->where('status', 'canceled')->where('expire_at', '<', Carbon::now());
    }

    public function subscribers()
    {
        return $this->hasMany('App\Model\Subscription', 'recipient_user_id');
    }

    public function transactions()
    {
        return $this->hasMany('App\Model\Transaction');
    }

    public function withdrawals()
    {
        return $this->hasMany('App\Model\Withdrawal');
    }

    public function attachments()
    {
        return $this->hasMany('App\Model\Attachment');
    }

    public function lists()
    {
        return $this->hasMany('App\Model\UserList');
    }

    public function bookmarks()
    {
        return $this->hasMany('App\Model\UserBookmark');
    }

    public function wallet()
    {
        return $this->hasOne('App\Model\Wallet');
    }

    public function verification()
    {
        return $this->hasOne('App\Model\UserVerify');
    }

    public function offer()
    {
        return $this->hasOne('App\Model\CreatorOffer');
    }

    public function userCountry()
    {
        return $this->belongsTo('App\Model\Country', 'country_id');
    }

    /**
     * Safely get the blocked list ID
     *
     * @return int|null
     */
    public function getBlockedListId()
    {
        if ($this->lists && $this->lists->count() > 0) {
            $blockedList = $this->lists->firstWhere('type', 'blocked');
            if ($blockedList) {
                return $blockedList->id;
            }
        }
        return null;
    }
    
    /**
     * Override to handle inheritance issue with Voyager
     *
     * @return mixed|null
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'getBlockedListId') {
            return self::getBlockedListIdStatic();
        }
        
        return parent::__callStatic($method, $parameters);
    }
    
    /**
     * Static version of getBlockedListId
     *
     * @return int|null
     */
    public static function getBlockedListIdStatic()
    {
        if (Auth::check() && Auth::user()) {
            $user = Auth::user();
            if (method_exists($user, 'lists')) {
                $lists = $user->lists;
                if ($lists && $lists->count() > 0) {
                    $blockedList = $lists->firstWhere('type', 'blocked');
                    if ($blockedList) {
                        return $blockedList->id;
                    }
                }
            }
        }
        return null;
    }
}
