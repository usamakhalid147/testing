<?php

/**
 * Hotel Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    HotelModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CurrencyConversion;
use App\Traits\HasTranslations;
use Auth;

class Hotel extends Model
{
    use HasFactory, HasTranslations, SoftDeletes, CurrencyConversion;

	/**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = ['logo_src'];

    public $convert_fields = ['service_charge','property_tax'];

	/**
	 * Where the Files are stored
	 *
	 * @var string
	 */
	public $filePath = "/images/hotels";

    /**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['name', 'description', 'your_space', 'interaction_with_guests', 'your_neighborhood', 'getting_around','other_things_to_note'];

	/**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'property_type' => 'string',
    ];

	/**
	 * Save Model values to database without Trigger any events
	 *
	 */
	public function saveQuietly(array $options = [])
	{
	    return static::withoutEvents(function () use ($options) {
	        return $this->save($options);
	    });
	}

	/**
	* Get All Hotel Rules
	*
	*/
	public function get_hotel_rules()
	{
		$hotel_rules = explode(',',$this->hotel_rules);
		return resolve("HotelRule")->whereIn('id',$hotel_rules);
	}

	/**
	 * Scope to eager load all the relations
	 *
	 */
	public function scopeLoadRelations($query)
	{
		return $query->with('user','hotel_address','hotel_photos','hotel_rooms.hotel_room_photos','hotel_rooms.hotel_room_price','hotel_rooms.hotel_room_price_rules');
	}
	/**
	 * Scope to get the details based on current user
	 *
	 */
	public function scopeAuthUser($query)
	{	
		$user_id = getCurrentUserId();
        if(isHost()) {
            $user_id = getHostId();
        }
		return $query->where('user_id', $user_id);
	}

	/**
	 * Scope to get the details based on current user
	 *
	 */
	public function scopeActiveUser($query)
	{
		return $query->whereHas('user', function($query)  {
            $query->activeOnly();
        });
	}

	/**
	 * Scope to get listed Hotel only
	 *
	 */
	public function scopeListed($query)
	{
		return $query->where('status', 'Listed');
	}

	/**
	 * Scope to get Verified Hotel only
	 *
	 */
	public function scopeVerified($query)
	{
		return $query->where('admin_status', 'Approved');
	}

	/**
	 * Scope to get listed & verified Hotel
	 *
	 */
	public function scopeviewOnly($query)
	{
		return $query->listed()->verified();
	}

	/**
	 * Scope to get Popular only
	 *
	 */
	public function scopeRecommended($query)
	{
		return $query->viewOnly()->where('is_recommended',1);
	}

	/**
	 * Scope to get Top Picks only
	 *
	 */
	public function scopeTopPicks($query)
	{
		return $query->viewOnly()->where('is_top_picks',1);
	}

	/**
	 * Join With User Table
	 *
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User','user_id');
	}

	/**
     * Join With Company Table
     *
     */
    public function company()
    {
        return $this->hasOne(Company::class,'user_id','user_id');
    }

	/**
	 * Join With Hotel Rooms
	 *
	 */
	public function hotel_rooms()
	{
		return $this->hasMany('App\Models\HotelRoom','hotel_id','id');
	}

	/**
	 * Join With Hotel Rooms
	 *
	 */
	public function hotel_room()
	{
		return $this->hotel_rooms->first();
	}

	/**
	 * Join With Hotel Address Table
	 *
	 */
	public function hotel_address()
	{
		return $this->hasOne('App\Models\HotelAddress','hotel_id','id');
	}

	/**
	 * Join With Hotel Photos and return ordered
	 *
	 */
	public function hotel_photos()
	{
		return $this->hasMany('App\Models\HotelPhoto')->ordered();
	}

	/**
	 * Get the First Hotel Photo Url
	 *
	 * @return String ImageUrl
	 */
	public function getImageSrcAttribute()
	{
		$this->load('hotel_photos');
		if($this->hotel_photos->count() == 0) {
			return asset('images/default_thumbnail.png');
		}
		$hotel_photo = $this->hotel_photos->first();
		$image_url = $hotel_photo->image_src;
		return $image_url;
	}

	/**
	 * Get property Type Name
	 *
	 */
	public function getPropertyTypeNameAttribute()
	{
		$property_type = resolve("PropertyType")->where('id',$this->property_type)->first();
		return optional($property_type)->name ?? '';
	}

	/**
	 * Get Translated Cancellation Policy Text
	 *
	 */
	public function getCancellationPolicyTextAttribute()
	{
		return \Lang::get('messages.'.$this->cancellation_policy);
	}

	/**
	 * Get Min Booking Date Attribute based on notice days
	 *
	 */
	public function getMinBookingDateAttribute()
	{	
		$notice_days = $this->notice_days ?? 0;
		return now()->addDays($notice_days)->format('Y-m-d');
	}

	/**
	 * Get link Attibute
	 *
	 */
	public function getLinkAttribute()
	{
		return resolveRoute('hotel_details',['id' => $this->id]);
	}

	/**
     * Get Hotel Completed Percentage
     *
     * @return float $percentage
     */
    public function getCompletedPercentAttribute()
    {
    	$hotel_service = resolve("App\Services\HotelService");
    	$step_data = $hotel_service->getHotelSteps($this);
    	$total_steps = $step_data->count();
    	$comp_steps = $step_data->where('completed',true)->count();
    	$comp_percent = ($comp_steps / $total_steps) * 100;

    	return round($comp_percent);
    }

    public function getSubroomCountAttribute()
    {
    	return $this->hotel_rooms->count();
    }

    public function getPriceAttribute()
    {
    	$room = $this->hotel_rooms->first()->hotel_room_price;
    	return optional($room)->price;
    }

    public function getPriceRangeAttribute($value)
    {
    	if($value == NULL) {
    		return '';
    	}

    	return html_entity_decode($value);
    }

    public function getGuestsAttribute()
    {
    	return $this->hotel_rooms->sum('guests');
    }

    /**
     * Join With Address Table
     *
     */
    public function list_address()
    {
        return $this->hotel_address;
    }

    /**
	 * Generate Review Stars HTML
	 *
	 */
	public function getReviewStars()
	{
		$empty = 5 - $this->rating;
		$result = '';
		for($i=1;$i<=$this->rating;$i++){
			$result = $result.'<i class="icon icon-star me-1"></i>';
		}
		for($i=1;$i<=$empty;$i++){
			$result = $result.'<i class="icon icon-star-empty me-1"></i>';
		}
		return $result;
	}

	/**
	 * Generate Review Stars HTML
	 *
	 */
	public function getHotelRatings()
	{
		$empty = 5 - $this->star_rating;
		$result = '';
		for($i=1;$i<=$this->star_rating;$i++){
			$result = $result.'<i class="icon icon-star text-dark me-1"></i>';
		}
		for($i=1;$i<=$empty;$i++){
			$result = $result.'<i class="icon icon-star-empty text-dark me-1"></i>';
		}
		return $result;
	}

	/**
	 * Get Available Room
	 *
	 */
	public function getAvailableSpotsAttribute()
	{
		return $this->hotel_rooms->sum('number_of_rooms');
	}

    /**
	 * Get Image Upload Path
	 *
	 * @return String filePath
	 */
	public function getUploadPath()
	{
		return $this->filePath.'/'.$this->hotel_id;
	}

	/**
	 * Get the Image Url
	 *
	 * @return String ImageUrl
	 */
	public function getLogoSrcAttribute()
	{
		if(!$this->id) {
			return asset('images/preview_thumbnail.png');
		}

		$handler = $this->getImageHandler();
		$image_data['name'] = $this->logo;
		$image_data['path'] = $this->getUploadPath();
		$image_data['image_size'] = $this->getImageSize();
		$image_data['version_based'] = false;

		return $handler->fetch($image_data);
	}
}
