<?php

/**
 * Hotel Room Model
 *
 * @package     HyraHotels
 * @subpackage  Models
 * @category    HotelRoomModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTranslations;
use DateTime;
use Lang;

class HotelRoom extends Model
{
    use HasFactory, HasTranslations;

	/**
	 * The attributes that are Translatable
	 *
	 * @var array
	 */
	public $translatable = ['name', 'description'];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
	public $appends = ['temp_id','removed_photos'];

	/**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'room_type' => 'string',
        'guests' => 'string',
        'bedrooms' => 'string',
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
	 * Scope to eager load all the relations
	 *
	 */
	public function scopeLoadRelations($query)
	{
		return $query->with('hotel_room_photos','hotel_room_price','hotel','cancellation_policies','hotel_room_price_rules','meal_plans','extra_beds','hotel_room_promotions');
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
	 * Join With User Table
	 *
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User','user_id');
	}

	/**
	 * Join With Hotel Table
	 *
	 */
	public function hotel()
	{
		return $this->belongsTo('App\Models\Hotel');
	}

	/**
	 * Join With Room Price Table
	 *
	 */
	public function hotel_room_price()
	{
		return $this->hasOne('App\Models\HotelRoomPrice','room_id');
	}

	/**
	 * Join With Hotel Room Price Rules Table
	 *
	 */
	public function hotel_room_price_rules()
	{
		return $this->hasMany(HotelRoomPriceRule::class,'room_id','id');
	}

	/**
	 * Join With Hotel Room Promotion
	 *
	 */
	public function hotel_room_promotions()
	{
		return $this->hasMany(HotelRoomPromotion::class,'room_id','id');
	}

	/**
	 * Join With Hotel Room Price Rules Table With Meal Plan
	 *
	 */
	public function meal_plans()
	{
		return $this->hotel_room_price_rules()->where('type','meal');
	}

	public function cancellation_policies()
	{
		return $this->hasMany(HotelRoomCancellationPolicy::class,'room_id','id');
	}

	/**
	 * Join With Hotel Room Price Rules Table With Extra Price
	 *
	 */
	public function extra_beds()
	{
		return $this->hotel_room_price_rules()->where('type','bed');
	}

	/**
	 * Join With Room Photo Table
	 *
	 */
	public function hotel_room_photos()
	{
		return $this->hasMany('App\Models\HotelRoomPhoto','room_id')->ordered();
	}

	public function getTempIdAttribute()
	{
		return $this->id;
	}

	public function getRemovedPhotosAttribute()
	{
		return [];
	}

	/**
	 * Get Room Type Name
	 *
	 */
	public function getRoomTypeNameAttribute()
	{
		$room_type = resolve("RoomType")->where('id', $this->room_type)->first();
		return optional($room_type)->name ?? '';
	}

	/**
	 * Get Bed Type Name
	 *
	 */
	public function getBedTypeNameAttribute()
	{
		$bed_type = resolve("BedType")->where('id', $this->bed_type)->first();
		return optional($bed_type)->name ?? '';
	}

	/**
     * Get Room Completed Percentage
     *
     * @return float $percentage
     */
    public function getCompletedPercentAttribute()
    {
    	$room_service = resolve("App\Services\RoomService");
    	$step_data = $room_service->getRoomSteps($this);
    	$total_steps = $step_data->count();
    	$comp_steps = $step_data->where('completed',true)->count();
    	$comp_percent = ($comp_steps / $total_steps) * 100;

    	return round($comp_percent);
    }
    /** Get Cancellation Policy
     * 
     * 
     * */
    public function getCancellationPolicyTextAttribute()
    {
    	return \Lang::get('messages.'.$this->cancellation_policy);
    }

    /**
     * Get Room Promotion
     * 
     */
    public function getRoomPromotions($start_date, $nights) 
    {
    	$all_promotions = $this->hotel_room_promotions->where('status',1)->groupBy('type');
		$today = new DateTime();
		$startDate = DateTime::createFromFormat('Y-m-d',$start_date);
		$diff = $today->diff($startDate)->days;

    	$applied_promotions = collect();
    	$all_promotions->map(function($promo,$type) use(&$applied_promotions, $diff, $nights) {
    		$checkPromo = $promo->where('type',$type);
    		if($type == 'min_max') {
    			$checkPromo = $checkPromo->where('min_los','<=',$nights)->where('max_los','>=',$nights);
    		}
    		else if($type == 'early_bird') {
    			$checkPromo = $checkPromo->where('days','<=',$diff);
    		}
    		else if($type == 'day_before_checkin') {
    			$checkPromo = $checkPromo->where('days','<=',$diff)->where('days','<=',29);
    		}
    		$checkPromo = $checkPromo->sortByDesc('value')->sortByDesc('value_type')->first();

    		if(!empty($checkPromo)) {
	    		$applied_promotions[] = [
	    			'id' => $checkPromo['id'],
	    			'type' => $checkPromo['type'],
	    			'value' => $checkPromo['value'],
	    			'value_type' => $checkPromo['value_type'],
	    		];
	    	}
    	});

    	$applied_promotions = $applied_promotions->sortByDesc('value')->sortByDesc('value_type')->first();
    	if(!empty($applied_promotions)) {
			return [ $applied_promotions ];
    	}
    	return [];
    }
}