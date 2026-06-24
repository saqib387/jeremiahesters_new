<?php

namespace App;

use App\Model\Cryptocurrency;
use App\Model\CryptoTransaction;
use App\Model\CryptoWallet;
use App\Model\Post;
use App\Model\Subscription;
use App\Model\UserList;
use App\Providers\GenericHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class User_bk extends \TCG\Voyager\Models\User implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'role_id', 'password', 'username', 'bio', 'birthdate', 'location', 'website', 'avatar', 'cover', 'postcode', 'settings',
        'billing_address', 'first_name', 'last_name', 'profile_access_price',
        'gender_id', 'gender_pronoun',
        'profile_access_price_6_months',
        'profile_access_price_12_months',
        'profile_access_price_3_months',
        'public_profile', 'city', 'country', 'state', 'email_verified_at', 'paid_profile',
        'auth_provider', 'auth_provider_id', 'enable_2fa', 'enable_geoblocking', 'open_profile', 'referral_code', 'country_id',
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
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'password';
    }

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
     * Get cryptocurrencies created by this user
     */
    public function createdCryptocurrencies()
    {
        return $this->hasMany('App\Model\Cryptocurrency', 'creator_user_id');
    }
    
    /**
     * Get crypto wallets owned by this user
     */
    public function cryptoWallets()
    {
        return $this->hasMany('App\Model\CryptoWallet');
    }
    
    /**
     * Get cryptocurrency buy transactions for this user
     */
    public function cryptoBuyTransactions()
    {
        return $this->hasMany('App\Model\CryptoTransaction', 'buyer_user_id');
    }
    
    /**
     * Get cryptocurrency sell transactions for this user
     */
    public function cryptoSellTransactions()
    {
        return $this->hasMany('App\Model\CryptoTransaction', 'seller_user_id');
    }
    
    /**
     * Get a specific cryptocurrency wallet
     */
    public function getCryptoWallet($cryptocurrencyId)
    {
        return $this->cryptoWallets()->where('cryptocurrency_id', $cryptocurrencyId)->first();
    }
    
    /**
     * Check if user has a specific cryptocurrency
     */
    public function hasCryptocurrency($cryptocurrencyId)
    {
        $wallet = $this->getCryptoWallet($cryptocurrencyId);
        return $wallet && $wallet->balance > 0;
    }
    
    /**
     * Get total value of all cryptocurrency holdings
     */
    public function getTotalCryptoValueAttribute()
    {
        return $this->cryptoWallets()->with('cryptocurrency')->get()->sum(function($wallet) {
            return $wallet->value;
        });
    }
} 