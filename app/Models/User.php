<?php

/**
 * User Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    UserModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LaravelEntrustUserTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'dob',
        'profile_picture',
        'user_document_src',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

     /**
      * Default Guard Name of the Model
      *
      * @var array<int, string>
      */
    protected $guard = 'user';

    /**
     * Where the Files are stored
     *
     * @var string
     */
    public $filePath = "/images/users";

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /**
     * Get the verification URL for the given route.
     *
     * @param  mixed  $route
     * @return string
     */
    public function verificationUrl($route)
    {
        return \URL::temporarySignedRoute(
            $route,
            now()->addMinutes(60),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the Reset Password URL for the given route.
     *
     * @param  mixed  $route
     * @return string
     */
    public function resetPasswordUrl($route)
    {
        return url(resolveRoute($route, [
            'token' => app('auth.password.broker')->createToken($this),
            'email' => $this->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Store the encrypted password to table.
     *
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Get Current Image Handler
     *
     * @return ImageHandler
     */
    public function getImageHandler()
    {
        $upload_drivers = view()->shared('upload_drivers');
        $driver = $this->attributes['upload_driver'] ?? '0';
        $handler = resolve('App\Services\ImageHandlers\\'.$upload_drivers[$driver].'ImageHandler');
        return $handler;
    }

    /**
     * Get Upload File Path
     *
     * @return string
     */
    public function getUploadPath()
    {
        return $this->filePath.'/'.$this->id;
    }

    /**
     * Get Image Size
     *
     * @return String
     */
    public function getImageSize()
    {
        if(isset($this->imageSize)) {
            return $this->imageSize;
        }
        return ['height' => '','width' => ''];
    }

    /**
     * Delete Image From Storage
     *
     * @return String ImageUrl
     */
    public function deleteImageFile()
    {
        $handler = $this->getImageHandler();
        $image_data['name'] = $this->src;
        $image_data['target_dir'] = $this->getUploadPath();
        return $handler->destroy($image_data);
    }

    // Local Scope of User

    /**
     * Get All Active Users Only
     *
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status','active');
    }

    /**
     * Get All Verified Users Only
     *
     */
    public function scopeVerifiedOnly($query)
    {
        return $query->activeOnly()->where('verification_status','verified');
    }

    /**
     * Get User Type
     * 
     */
    public function scopeAuthBased($query) 
    {
        if(isHost()) {
            return $query->whereIn('user_type',['host','sub_host']);
        }
        return $query->where('user_type', 'user');
    }

    // Relationships

    /**
     * Join With Reviews Table
     *
     */
    public function reviews()
    {
        return $this->hasMany(Review::class,'user_to','id')->activeUser();
    }

    /**
     * Join With User Penalty Table
     *
     */
    public function user_penalty()
    {
        return $this->hasOne(UserPenalty::class);
    }

    /**
     * Join With User Verification Table
     *
     */
    public function user_verification()
    {
        return $this->hasOne(UserVerification::class);
    }

    /**
     * Join With Company Table
     *
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Join With User Information Table
     *
     */
    public function user_information()
    {
        return $this->hasOne(UserInformation::class);
    }

    /**
     * Join With User Payout Methods Table
     *
     */
    public function payout_methods()
    {
        return $this->hasMany(PayoutMethod::class);
    }

    /**
     * Join With Payout Method Table with default
     *
     */
    public function default_payout_method()
    {
        return $this->hasOne(PayoutMethod::class)->where('is_default','1');
    }

    // Appends

    /**
     * Get Full name of current User
     *
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Get Member Since Attribute
     *
     */
    public function getSinceAttribute()
    {
        return $this->created_at->format('Y');
    }

    /**
     * Get Member Since Attribute
     *
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format(DATE_FORMAT);
    }

    /**
     * Get Profile Picture of the User
     *
     */
    public function getProfilePictureSrcAttribute()
    {
        $src = $this->attributes['src'] ?? '';
        if($src == '') {
            return asset('images/profile_picture.png');
        }

        if($this->attributes['photo_source'] == 'site') {
            $handler = $this->getImageHandler();
            $image_data['name'] = $src;
            $image_data['version_based'] = true;
            $image_data['path'] = $this->getUploadPath();

            $src = $handler->fetch($image_data);
        }
        return $src;
    }

    /**
     * Get Document of the User
     *
     */
    public function getUserDocumentSrcAttribute()
    {
        $document_src = $this->attributes['document_src'];
        if($document_src == '') {
            return NULL;
        }
        if($this->attributes['photo_source'] == 'site') {
            $handler = $this->getImageHandler();
            $image_data['name'] = $document_src;
            $image_data['version_based'] = true;
            $image_data['path'] = $this->getUploadPath();

            $document_src = $handler->fetch($image_data);
        }
        return $document_src ;
    }

    /**
     * Get User Preferred language
     *
     */
    public function getUserLanguageNameAttribute()
    {
        $language_list = view()->shared('language_list');
        if(!isset($this->user_language) || $this->user_language == '') {
            $this->attributes['user_language'] = global_settings('default_language');
            $this->save();
        }
        return $language_list[$this->user_language];
    }
    
    /**
     * Get Count of all Reviews about this user
     *
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews->count();
    }

    /**
     * Check User loing with Social Media or not
     *
     */
    public function getHasSignupWithEmailAttribute()
    {
        return $this->password != '';
    }

    /**
     * Get User Currency Code Attribue
     *
     */
    public function getCurrencyCodeAttribute()
    {
        if($this->user_currency != '') {
            return $this->user_currency;
        }
        return global_settings('default_currency');
    }

    /**
     * Get Unread Count of the Messages
     *
     */
    public function getInboxCountAttribute()
    {
        return \DB::Table('message_conversations')
            ->where('user_to', $this->attributes['id'])
            ->where('read', '0')
            ->groupby('message_id')
            ->get()
            ->count();
    }

    /**
    * Get The Name of the country by coutry code
    *
    */
    public function getCountryNameAttribute()
    {
        $country_list = resolve('Country');
        $country = $country_list->where('name',$this->country_code)->first();
        return optional($country)->full_name;
    }

    /**
     * Get Role Id
     *
     * @return Integer Id
     */
    public function getRoleIdAttribute()
    {
        $roles = $this->roles()->first();
        return optional($roles)->id ?? '';
    }

    /**
     * Get Role Name
     *
     * @return Integer Id
     */
    public function getRoleNameAttribute()
    {
        $role = $this->roles()->first();
        return optional($role)->display_name ?? '';
    }
}
