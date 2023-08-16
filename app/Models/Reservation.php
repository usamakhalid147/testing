<?php

/**
 * Reservation Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    ReservationModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;
use App\Traits\ManageDispute;
use Lang;

class Reservation extends Model
{
    use HasFactory, CurrencyConversion, ManageDispute;

    /**
     * The attributes are converted based on session currency.
     *
     * @var array
     */
    public $convert_fields = ['base_price','total_days_price','length_of_stay_discount_price','booking_period_discount_price','coupon_price','cleaning_fee','additional_guest_fee','security_fee','service_fee','sub_total','host_fee','total','service_charge','property_tax','extra_charges','promotion_amount'];

    /**
	 * Scope to Get Records Based on current login user and user Type
	 *
	 * @return Illuminate\Database\Eloquent\Builder $query
	 */
    public function scopeUserBased($query)
    {
    	$user_id = getCurrentUserId();
    	if(isHost()) {
            $user_id = getHostId();
    		return $query->where('host_id',$user_id);
        }
        return $query->where('user_id',$user_id);
    }

    /**
	 * Scope to Get Records Based on current login user
	 *
	 * @return Illuminate\Database\Eloquent\Builder $query
	 */
    public function scopeAuthUser($query)
    {
    	$user_id = getCurrentUserId();
    	return $query->where(function($query) use($user_id) {
    		$query->where('user_id',$user_id)->orwhere('host_id',$user_id);
    	});
    }

    /**
	 * Scope to Get Past 6 Months Cancelled/Expired Count Of Given User
	 *
	 * @return Illuminate\Database\Eloquent\Builder $query
	 */
    public function scopeCancelCount($query,$user_id)
    {
    	return $query->where('host_id', $user_id)
            ->where('penalty_enabled','1')
            ->where(function($query) {
                $query->where(function($query) {
                    $query->where('cancelled_by', 'Host')->where('cancelled_at', '>=', \DB::raw('DATE_SUB(NOW(), INTERVAL 6 MONTH)'));
                })
                ->orWhere(function($query) {
                    $query->where('expired_on', 'Host')->where('created_at', '>=', \DB::raw('DATE_SUB(NOW(), INTERVAL 6 MONTH)'));
                });
            });
    }

    /**
	 * Scope to Get Only Checkout Crossed Reservation
	 *
	 * @return Illuminate\Database\Eloquent\Builder $query
	 */
	public function scopeafterCheckout($query)
	{
		return $query->where('checkout','<',date('Y-m-d'));
	}

    /**
	 * Join With User Table by Guest
	 *
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Join With User Table by Host
	 *
	 */
	public function host_user()
	{
		return $this->belongsTo(User::class, 'host_id', 'id');
	}

	/**
	 * Join Room Reservation Table
	 *
	 */
	public function room_reservations()
	{
		return $this->hasMany('App\Models\RoomReservation','reservation_id','id');
	}

	/**
	 * Join With Hotel Table
	 *
	 */
	public function hotel()
	{
		return $this->belongsTo(Hotel::class);
	}

	/**
	 * Join With Reviews Table
	 *
	 */
	public function reviews()
	{
		return $this->hasMany(Review::class);
	}

	/**
	 * Join With Payouts Table by Host
	 *
	 */
	public function host_payout()
	{
		return $this->hasOne(Payout::class)->listTypeBased('hotel')->where('user_type','Host');
	}

	/**
	 * Join With Payouts Table by Guest
	 *
	 */
	public function guest_refund()
	{
		return $this->hasOne(Payout::class)->listTypeBased('hotel')->where('user_type','Guest');
	}

	/**
	 * Join With Hotel Sub Room
	 *
	 */
	public function hotel_room()
	{
		return $this->belongsTo('App\Models\HotelRoom');
	}

	/**
	 * Get the Guest Admin amount
	 *
	 */
	public function getGuestAdminAmount()
	{
		return $this->service_fee;
	}

	/**
	 * Get the Host Admin amount
	 *
	 */
	public function getHostAdminAmount()
	{
		return $this->host_fee;
	}

	/**
	 * Get the Total Admin amount
	 *
	 */
	public function getTotalAdminAmount()
	{
		return $this->getGuestAdminAmount() + $this->getHostAdminAmount();
	}

	/**
	 * Get the Booking Discount Amount By Host
	 *
	 */
	public function getHostDiscounts()
	{
		$coupon_price = 0;
		if($this->coupon_type == 'host') {
			$coupon_price = $this->coupon_price;
		}
		return $this->length_of_stay_discount_price + $this->early_bird_discount_price + $coupon_price;
	}

	/**
	 * Get the Total Booking Discount Amounts
	 *
	 */
	public function getAdminDiscountsAmount()
	{
		if($this->coupon_type == 'admin' || $this->coupon_type == 'referral') {
			return $this->coupon_price;
		}

		return 0;
	}

	/**
	 * Get the Total Booking Amount Including admin service fee
	 *
	 */
	public function getTotalDayAmount()
	{
		return $this->total_days_price - $this->getHostDiscounts();
	}

	/**
	 * Get the Total Booking Amount Including admin service fee
	 *
	 */
	public function getTotalBookingAmount()
	{
		$total = $this->sub_total - $this->getHostDiscounts();
		return numberFormat($total);
	}

	/**
	* Get the Total Other fees
	*
	*/
	public function getTotalOtherAmount()
	{
		return $this->cleaning_fee;
	}

	/**
	 * Calculate totol host payout amount
	 *
	 */
	public function calcHostPayoutAmount()
	{
		$total_amount = $this->getTotalBookingAmount();
		$admin_amount = $this->getHostAdminAmount();
		$payout_amount = numberFormat($total_amount - $admin_amount);
		
		if($this->dispute_amount > 0) {
			if($this->dispute_to == 'host') {
				$payout_amount = numberFormat($payout_amount + $this->dispute_amount);
			}
			else {
				$payout_amount = numberFormat($payout_amount - $this->dispute_amount);
			}
		}

		return $payout_amount;
	}

	/**
	 * Calculate totol host payout amount
	 *
	 */
	public function calcGuestRefundAmount()
	{
		if($this->dispute_amount > 0) {
			if($this->dispute_to == 'guest') {
				$guest_refund = $this->guest_refund;
				if($guest_refund != '') {
					return numberFormat($guest_refund->amount + $this->dispute_amount);
				}

				return numberFormat($this->dispute_amount);
			}			
		}

		return 0;
	}

	/**
	 * Get Last Review Date
	 *
	 * @return $date Carbon
	 */
	public function getLastReviewDate()
	{
		$end_date = getDateObject($this->checkout)->addDays(MAX_REVIEW_DAYS);
		return $end_date;
	}

	/**
	 * Get Remaining Days for Write / Edit Review
	 *
	 */
	public function getReviewDays()
	{
		$today = getDateObject();
		$end_date = $this->getLastReviewDate();

		$interval_days = $today->diff($end_date)->format('%R%a');
		return $interval_days + 1;
	}

	/**
	 * Check User Can Able To Write / Edit Review Or Not
	 *
	 */
	public function canWriteReview()
	{
		$review_days = $this->getReviewDays();
		return $this->status == 'Accepted' && $review_days > 0 && $review_days < MAX_REVIEW_DAYS;
	}

	/**
	 * guestOrHostTotal
	 *
	 */
	public function guestOrHostTotal()
	{
		if(getCurrentUserId() == $this->user_id) {
			return $this->getTotalBookingAmount();
		}
		return $this->calcHostPayoutAmount();
	}

	/**
	 * Check whether reservation date is crossed
	 *
	 */
	public function checkoutCrossed()
	{
		return $this->attributes['checkout'] < date('Y-m-d');
	}

	/**
	 * Generate Pricing Form to display
	 *
	 * @param String $type guest or host
	 * @return Array Form With Pricing details
	 */
	public function getPricingForm($type)
	{
		$symbol = $this->currency_symbol ?? session('currency_symbol');
		$pricing_form = array();
		foreach($this->room_reservations->groupBy('room_id') as $room) {
			$description = $room->sum('adults').' '.($room->sum('adults') > 1 ? Lang::get('messages.adults') : Lang::get('messages.adult'));
			if($room->sum('children') > 0) {
				$description .=',  '.$room->sum('children').' '.($room->sum('children') > 1 ? Lang::get('messages.children') : Lang::get('messages.child'));
			}
			$dropdown = $room->first()->meal_plan != '' || $room->first()->extra_bed != '' || is_array($room->first()->applied_promotions);
			$dropdown_values = [];
			$meal_plan = explode(',',$room->first()->meal_plan);
			$extra_bed = explode(',',$room->first()->extra_bed);
			$meal_plans = \App\Models\HotelRoomPriceRule::where('type','meal')->whereIn('id',$meal_plan)->get();
			foreach($meal_plans as $plan) {
				$dropdown_values[] = ['key' => $plan->name, 'value' => $plan->price];
			}
			$bed_types = \App\Models\HotelRoomPriceRule::where('type','bed')->whereIn('id',$extra_bed)->get();
			foreach($bed_types as $bed) {
				$dropdown_values[] = ['key' => $bed->name, 'value' => $bed->price];
			}
			$applied_promotions = $room->first()->applied_promotions ?? [];
			foreach($applied_promotions as $applied) {
				$dropdown_values[] = ['key' => Lang::get('messages.'.$applied['type']), 'value' => $applied['amount'] * $room->count()];	
			}
			$form_data = array('key' => $room->first()->hotel_room->name, 'value' => $symbol.numberFormat($room->sum('total_price')), 'description' => $description, 'count' => $room->count(),'dropdown'=> $dropdown,'dropdown_values' => $dropdown_values);
			$pricing_form[] = formatPricingForm($form_data);
		}
		
		if($this->cleaning_fee > 0) {
			$form_data = array('key' => Lang::get('messages.cleaning_fee'), 'value' => $symbol.$this->cleaning_fee);
			$pricing_form[] = formatPricingForm($form_data);
		}

		if($this->additional_guest_fee > 0) {
			$form_data = array('key' => Lang::get('messages.additional_guest_fee'), 'value' => $symbol.$this->service_fee);
			$pricing_form[] = formatPricingForm($form_data);
		}

		if($this->coupon_price > 0) {
			if($type != 'Host' || $this->coupon_type == 'host') {
				$key = $this->coupon_type == 'referral' ? Lang::get('messages.referral_credit') : Lang::get('messages.coupon_code');
				$form_data = array('key' => $key, 'value' => '-'.$symbol.$this->coupon_price,'class' => 'text-success');
				$pricing_form[] = formatPricingForm($form_data);
			}
		}

		if($this->service_charge > 0) {
			$form_data = array('key' => Lang::get('admin_messages.property')." ".Lang::get('messages.service_charge'), 'value' => $symbol.$this->service_charge);
			$pricing_form[] = formatPricingForm($form_data);
		}

		if($this->property_tax > 0) {
			$form_data = array('key' => Lang::get('messages.property_tax'), 'value' => $symbol.$this->property_tax);
			$pricing_form[] = formatPricingForm($form_data);
		}

		if($type == 'Guest') {

			if($this->service_fee > 0) {
				$form_data = array('key' => Lang::get('messages.service_fee'), 'value' => $symbol.$this->service_fee,'tooltip' => Lang::get('messages.helps_to_run_our_platform'));
				$pricing_form[] = formatPricingForm($form_data);
			}

			if($type == 'Guest') {
				$form_data = array('key' => Lang::get('messages.total')." ".Lang::get('messages.amount'), 'value' => $symbol.$this->total,'key_style' =>'font-weight:bold','class' => 'border-top pt-2');
				$pricing_form[] = formatPricingForm($form_data);
			}
		}
		else if($type == 'Host') {
			
			$form_data = array('key' => Lang::get('messages.total'), 'value' => $symbol.$this->sub_total,'key_style' =>'font-weight:bold','class' => 'border-top pt-2');
			$pricing_form[] = formatPricingForm($form_data);

			if($this->host_fee > 0) {
				$form_data = array('key' => Lang::get('messages.host_fee'), 'value' => $symbol.$this->host_fee, 'value_prefix' => '-');
				$pricing_form[] = formatPricingForm($form_data);
			}

			if(optional($this->host_payout)->penalty > 0) {
				$form_data = array('key' => Lang::get('messages.deducted_penalty'), 'value' => $symbol.$this->host_payout->penalty);
				$pricing_form[] = formatPricingForm($form_data);
			}

			$payout = ($this->host_payout != '') ? $this->host_payout->amount : $this->calcHostPayoutAmount();
			$form_data = array('key' => Lang::get('messages.total_payout'), 'value' => $symbol.$payout,'key_style' =>'font-weight:bold','class' => 'border-top pt-2');
			$pricing_form[] = formatPricingForm($form_data);

			if($this->host_penalty > 0) {
				$form_data = array('key' => Lang::get('messages.applied_penalty'), 'value' => $symbol.$this->host_penalty);
				$pricing_form[] = formatPricingForm($form_data);
			}
		}
		else if($type == 'Admin') {
			
			if($this->service_fee > 0) {
				$form_data = array('key' => Lang::get('messages.service_fee'), 'value' => $symbol.$this->service_fee,'tooltip' => Lang::get('messages.helps_to_run_our_platform'));
				$pricing_form[] = formatPricingForm($form_data);
			}

			$form_data = array('key' => Lang::get('messages.total')." ".Lang::get('messages.amount'), 'value' => $symbol.$this->total,'key_style' =>'font-weight:bold','class' => 'border-top pt-2');
			$pricing_form[] = formatPricingForm($form_data);

			if($this->host_fee > 0) {
				$form_data = array('key' => Lang::get('messages.host_fee'), 'value' => $symbol.$this->host_fee, 'value_prefix' => '-');
				$pricing_form[] = formatPricingForm($form_data);
			}

			if($this->service_fee > 0) {
				$form_data = array('key' => Lang::get('messages.service_fee'), 'value' => $symbol.$this->service_fee,'tooltip' => Lang::get('messages.helps_to_run_our_platform'), 'value_prefix' => '-');
				$pricing_form[] = formatPricingForm($form_data);
			}

			if(optional($this->host_payout)->penalty > 0) {
				$form_data = array('key' => Lang::get('messages.deducted_penalty'), 'value' => $symbol.$this->host_payout->penalty);
				$pricing_form[] = formatPricingForm($form_data);
			}

			$payout = ($this->host_payout != '') ? $this->host_payout->amount : $this->calcHostPayoutAmount();
			$form_data = array('key' => Lang::get('messages.total_payout'), 'value' => $symbol.$payout,'key_style' =>'font-weight:bold','class' => 'border-top pt-2');
			$pricing_form[] = formatPricingForm($form_data);
			
			if($this->host_penalty > 0) {
				$form_data = array('key' => Lang::get('messages.applied_penalty'), 'value' => $symbol.$this->host_penalty);
				$pricing_form[] = formatPricingForm($form_data);
			}
		}

		if($this->security_fee > 0) {
			$form_data = array('key' => Lang::get('messages.security_fee'), 'value' => $symbol.$this->security_fee,'class' => 'font-weight-bolder');
			$pricing_form[] = formatPricingForm($form_data);
		}

		return $pricing_form;
	}

	/**
	 * Generate Pricing Form to display
	 *
	 * @param String $type guest or host
	 * @return Array Form With Pricing details
	 */
	public function getReceiptPricingForm($type)
	{
		$symbol = $this->currency_symbol ?? session('currency_symbol');
		$pricing_form = array();

		foreach($this->room_reservations as $room_reservation) {
			$room_pricing_form = array();
			$hotel_room = $room_reservation->hotel_room;
			
			$adults = $room_reservation->adults - $room_reservation->extra_adults;
			$form_data = array('key' => Lang::get('messages.adults'), 'value' => $adults);
			$room_pricing_form[] = formatPricingForm($form_data);

			$children = $room_reservation->children - $room_reservation->extra_children;
			if($children > 0) {
				$form_data = array('key' => Lang::get('messages.children'), 'value' => $children);
				$room_pricing_form[] = formatPricingForm($form_data);
			}

			$form_data = array('key' => Lang::get('messages.room_rate_per_night'), 'value' => $symbol.numberFormatDisplay($room_reservation->day_price / $this->total_nights));
			$room_pricing_form[] = formatPricingForm($form_data);

			$form_data = array('key' => Lang::get('messages.room_charges'), 'value' => $symbol.numberFormatDisplay($room_reservation->day_price));
			$room_pricing_form[] = formatPricingForm($form_data);

			if($room_reservation->promotion_amount > 0) {
				$form_data = array('key' => Lang::get('messages.offers'), 'value' => $symbol.numberFormatDisplay($room_reservation->promotion_amount));
				$room_pricing_form[] = formatPricingForm($form_data);
			}

			$form_data = array('key' => Lang::get('messages.room_charges_with_discount'), 'value' => $symbol.numberFormatDisplay($room_reservation->day_price - $room_reservation->promotion_amount));
			$room_pricing_form[] = formatPricingForm($form_data);

			$form_data = array('type' => 'header', 'value' => Lang::get('messages.extra_charges'));
			$room_pricing_form[] = formatPricingForm($form_data);

			if($room_reservation->extra_adults > 0) {
				$form_data = array('key' => Lang::get('admin_messages.number_of_extra_adult'), 'value' => round($room_reservation->extra_adults));
				$room_pricing_form[] = formatPricingForm($form_data);

				$form_data = array('key' => Lang::get('admin_messages.total_extra_adults_charges'), 'value' => $symbol.numberFormatDisplay($room_reservation->extra_adults_amount * $this->total_nights));
				$room_pricing_form[] = formatPricingForm($form_data);
			}

			if($room_reservation->extra_children > 0) {
				$form_data = array('key' => Lang::get('admin_messages.number_of_extra_children'), 'value' => round($room_reservation->extra_children));
				$room_pricing_form[] = formatPricingForm($form_data);

				$form_data = array('key' => Lang::get('admin_messages.number_of_extra_children'), 'value' => $symbol.numberFormatDisplay($room_reservation->extra_children_amount * $this->total_nights));
				$room_pricing_form[] = formatPricingForm($form_data);
			}

			if($room_reservation->meal_plan != '') {
				$plan_ids = explode(',',$room_reservation->meal_plan);
				$meal_plans = HotelRoomPriceRule::where('type','meal')->where('hotel_id',$this->hotel_id)->whereIn('id',$plan_ids)->get();
				$form_data = array('key' => Lang::get('admin_messages.meal_plans'), 'value' => $meal_plans->pluck('name')->implode(', '));
				$room_pricing_form[] = formatPricingForm($form_data);

				$form_data = array('key' => Lang::get('admin_messages.total_meal_charges'), 'value' => $symbol.numberFormatDisplay($room_reservation->meal_plan_amount));
				$room_pricing_form[] = formatPricingForm($form_data);
			}

			if($room_reservation->extra_bed != '') {
				$plan_ids = explode(',',$room_reservation->extra_bed);
				$extra_beds = HotelRoomPriceRule::where('type','bed')->where('hotel_id',$this->hotel_id)->whereIn('id',$plan_ids)->get();
				$form_data = array('key' => Lang::get('admin_messages.extra_beds'), 'value' => $extra_beds->pluck('name')->implode(', '));
				$room_pricing_form[] = formatPricingForm($form_data);

				$form_data = array('key' => Lang::get('admin_messages.total_extra_bed_charges'), 'value' => $symbol.numberFormatDisplay($room_reservation->extra_bed_amount));
				$room_pricing_form[] = formatPricingForm($form_data);
			}

			if($room_reservation->meal_plan != '' || $room_reservation->extra_bed != '' || $room_reservation->extra_adults > 0 || $room_reservation->extra_children) {
				$form_data = array('key' => Lang::get('admin_messages.total_extra_charges'), 'value' => $symbol.numberFormatDisplay($room_reservation->extra_bed_amount + $room_reservation->meal_plan_amount + (($room_reservation->extra_adults_amount + $room_reservation->extra_children_amount) * $this->total_nights)));
				$room_pricing_form[] = formatPricingForm($form_data);
			}

			$pricing_form[] = [
				'room_name' => $hotel_room->name,
				'status' => $room_reservation->status,
				'pricing_form' => $room_pricing_form,
			];
		}

		return $pricing_form;
	}

	/**
	 * Check admin can able to make payout to host or refund to guest
	 *
	 */
	public function adminAbletoPayout()
	{
		if($this->attributes['status'] == 'Accepted') {
			if($this->checkoutCrossed()) {
				return true;
			}
		}
		if(in_array($this->attributes['status'],['Cancelled','Declined'])) {
			return true;
		}
		return false;
	}

	/**
	 * Get Checkin At or Check out timing Text Attribute
	 *
	 */
	public function getTimingText($type)
	{
		if($this->$type == 'flexible') {
			return Lang::get('messages.flexible');
		}

		return $this->$type;
	}

	/**
	 * Get Checkin Date
	 *
	 */
	public function getFormattedCheckinAttribute()
	{
		return getDateInFormat($this->attributes['checkin']);
	}

	/**
	 * Get Checkout Date
	 *
	 */
	public function getFormattedCheckoutAttribute()
	{
		return getDateInFormat($this->attributes['checkout']);
	}

	/**
	 * Get Formatted Dates
	 *
	 */
	public function getDatesAttribute()
	{
		return $this->formatted_checkin.' - '.$this->formatted_checkout;
	}

	/**
	 * Get Last Date For Write / Edit Review
	 *
	 */
	public function getReviewEndDateAttribute()
	{
		$review_date = $this->getLastReviewDate()->format('Y-m-d');
		return getDateInFormat($review_date);
	}

	/**
	 * Get Expired At
	 *
	 */
	public function getExpiredAtAttribute()
	{
		if(\Auth::check()) {
			$timezone = \Auth::user()->timezone;
		}

		if(isset($timezone)) {
			$this->created_at->setTimeZone($timezone);
		}
		return $this->created_at->addDay();
	}

	/**
	 * Get itinerary link
	 *
	 */
	public function getItineraryLinkAttribute()
	{
		if($this->code == '') {
			return '';
		}
		return resolveRoute('view_itinerary',['code' => $this->code]);
	}

	/**
	 * Get Message ID Attribute
	 *
	 */
	public function getMessageIdAttribute()
	{
		$message = \DB::Table('messages')->where('list_type','hotel')->where('reservation_id',$this->id)->first();
		return optional($message)->id ?? '';
	}
}
