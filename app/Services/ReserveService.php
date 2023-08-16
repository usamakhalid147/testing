<?php

/**
 * Provide reservation related Services like price calculation, payment form, etc.,
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    ReserveService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Reservation;
use App\Models\Payout;
use App\Models\CouponCode;
use App\Models\UserCoupon;
use App\Models\HotelCalendar;
use App\Models\HostCouponCode;
use Carbon\Carbon;
use Lang;
use Auth;

class ReserveService
{
	/**
	 * Common Function for Price Calculation
	 *
	 * @param int $hotel_id Hotel Id
	 * @param Array $price_data Data For Calculate Price
	 * @param Array $optional_data Data For Calculate Price
	 * @return Array Calculation Result
	 */
	public function priceCalculation($hotel_id, $price_data, $optional_data = [])
	{
		$return_data = $this->getDefaultReturnData();
		$hotel_details = Hotel::with('hotel_rooms','hotel_rooms')->find($hotel_id);
		if(!$hotel_details) {
			$return_data['is_available'] = false;
			$return_data['status_message'] = Lang::get('messages.invalid_request');
			return $return_data;
		}

		$is_not_available = $this->isNotAvailable($hotel_id,$price_data);
		if($is_not_available['status']) {
			$return_data['is_available'] = false;
			$return_data['status_message'] = Lang::get('messages.your_selected_rooms_not_available');
			return $return_data;
		}

		$booking_days = getDays($price_data['checkin'], $price_data['checkout']);
		array_pop($booking_days);
		$total_nights = count($booking_days);
		$total_price = 0;
		$total_rooms = 0;
		$total_adults = 0;
		$total_children = 0;
		$cleaning_fee = 0;
		$service_charge = 0;
		$property_tax = 0;
		$meal_plan_price = 0;
		$bed_price = 0;
		$selected_plans = explode(',',$price_data['selected_plans']);
		$selected_beds = explode(',',$price_data['selected_beds']);
		$meal_plans = [];
		$extra_beds = [];
		
		$hotel_room_price_rules = \App\Models\HotelRoomPriceRule::where('hotel_id',$hotel_id)->get();
		$hotel_meal_plans = $hotel_room_price_rules->where('type','meal')->whereIn('id',$selected_plans)->values();
		$hotel_extra_beds = $hotel_room_price_rules->where('type','bed')->whereIn('id',$selected_beds)->values();

		foreach($price_data['rooms'] as $key => $room_data) {
			if($room_data['selected_count'] > 0) {
				$total_price += numberFormat($room_data['total_price']);
				$total_adults += $room_data['total_adults'];
				$total_children += $room_data['total_children'];
				$total_rooms += $room_data['selected_count'];
				$meal_plan_price += $room_data['meal_plan_price'];
				$bed_price += $room_data['bed_price'];
				$hotel_room = HotelRoom::find($room_data['id']);
				$hotel_room_price = $hotel_room->hotel_room_price;

				foreach($room_data['add_rooms'] as $sub_key => $add_room) {
					$extra_adults = 0;
					$extra_children = 0;
					$meal_plan = $hotel_meal_plans->pluck('id')->implode(',');
					$meal_plan_amount = $hotel_meal_plans->sum('price');
					$extra_bed = $hotel_extra_beds->pluck('id')->implode(',');
					$extra_bed_amount = $hotel_extra_beds->sum('price');
					if($add_room['adults'] > $hotel_room->adults) {
						$extra_adults = $add_room['adults'] - $hotel_room->adults;
					}
					if($add_room['children'] > $hotel_room->children) {
						$extra_children = $add_room['children'] - $hotel_room->children;
					}

					$price_data['rooms'][$key]['add_rooms'][$sub_key]['day_price'] = $hotel_room_price->price;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['extra_adults'] = $extra_adults;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['extra_children'] = $extra_children;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['extra_adults_price'] = $extra_adults * $hotel_room_price->adult_price;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['extra_children_price'] = $extra_children * $hotel_room_price->children_price;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['extra_children_price'] = $extra_children * $hotel_room_price->children_price;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['meal_plan'] = $meal_plan;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['meal_plan_amount'] = $meal_plan_amount;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['extra_bed'] = $extra_bed;
					$price_data['rooms'][$key]['add_rooms'][$sub_key]['extra_bed_amount'] = $extra_bed_amount;
				}
			}
		}

		$total_price = $total_price - $meal_plan_price - $bed_price;
		$return_data['meal_plans'] = $meal_plans;
		$return_data['extra_beds'] = $extra_beds;
		$return_data['cancellation_policy'] = $hotel_details->cancellation_policy;
		$return_data['checkin_time'] = $hotel_details->checkin_time;
		$return_data['checkout_time'] = $hotel_details->checkout_time;
		$return_data['sub_rooms']	= $price_data['rooms'];
		$return_data['total_rooms']	= $total_rooms;
		$return_data['total_adults']= $total_adults;
		$return_data['total_children']= $total_children;
		$return_data['meal_plan_price'] = $meal_plan_price;
		$return_data['bed_price'] = $bed_price;
		$return_data['coupon_type'] = '';
		$property_tax = $hotel_details->property_tax ?? 0;
		$service_charge = $hotel_details->service_charge ?? 0;
		$sub_total = numberFormat($total_price + $meal_plan_price + $bed_price);
		$service_fee = $this->calculateServiceFee($sub_total);
		$host_fee = $this->calculateHostFee($sub_total);
		if ($hotel_details->service_charge_type == 'percentage') {
			$service_charge = ($hotel_details->service_charge / 100) * ($sub_total + $service_fee);
		}
		if ($hotel_details->property_tax_type == 'percentage') {
			$property_tax = ($hotel_details->property_tax / 100) * ($sub_total + $service_fee);
		}
		$sub_total = numberFormat($sub_total + $property_tax + $service_charge);
		$coupon_price = 0;
		if(isset($price_data['coupon_code']) && $price_data['coupon_code'] != '') {
			if($price_data['coupon_code'] == 'referral') {
				$referral_users = \App\Models\ReferralUser::authUser()->get();
				$available_credit = $referral_users->where('user_id',Auth::id())->sum('user_credited_amount') + $referral_users->where('referral_user_id',Auth::id())->sum('referral_credited_amount');
				if($available_credit > 0) {
					$return_data['coupon_type'] = 'referral';
					$return_data['coupon_code'] = 'referral';
					$coupon_price = $available_credit;
					$return_data['coupon_price'] = numberFormat($coupon_price);
				}
			}
			$coupon_code = CouponCode::where('code', $price_data['coupon_code'])->activeOnly()->first();
			$host_coupon_code = HostCouponCode::where(['code' => $price_data['coupon_code'],'user_id' => $hotel_details->user_id])->activeOnly()->first();
			if($coupon_code != '') {
				$return_data['coupon_code'] = $coupon_code->code;
				if($coupon_code->type == 'amount') {
					$coupon_price = $coupon_code->amount;
				}
				else {
					$coupon_price = ($sub_total * $coupon_code->value ) / 100 ;
				}
				$return_data['coupon_price'] = numberFormat($coupon_price);
				$return_data['coupon_type'] = 'admin';
			}
			if($host_coupon_code != '') {
				$return_data['coupon_code'] = $host_coupon_code->code;
				if($host_coupon_code->type == 'amount') {
					$coupon_price = $host_coupon_code->amount;
				}
				else {
					$coupon_price = ($sub_total * $host_coupon_code->value ) / 100 ;
				}
				$return_data['coupon_price'] = numberFormat($coupon_price);
				$return_data['coupon_type'] = 'host';
			}
		}

		$return_data['service_fee'] = $service_fee;
		$return_data['host_fee'] = $host_fee;
		$return_data['currency_code'] = $hotel_details->currency_code;
		$return_data['currency_symbol']	= $hotel_details->currency_symbol;
		$return_data['total_nights'] = $total_nights;
		$return_data['total_price']= numberFormat($total_price);
		$return_data['cleaning_fee'] = numberFormat($cleaning_fee);
		$return_data['sub_total'] = numberFormat($sub_total);
		$return_data['service_charge'] = numberFormat($service_charge);
		$return_data['property_tax'] = numberFormat($property_tax);
		$return_data['status'] = 'Available';
		$return_data['can_available'] = true;
		$return_data['status_message'] = '';
		$return_data['booking_type'] = 'instant_book';

		$payment_total = ($sub_total + $service_fee) - ($coupon_price);
		$payment_total = ($payment_total > 0) ? numberFormat($payment_total) : 0;
		$return_data['payment_total'] = $payment_total;
		$return_data['payout'] = numberFormat($sub_total - $host_fee);

		return $return_data;
	}

	/**
	 * Generate Pricing Form to display
	 *
	 * @param Array $price_data Calculated Pricing details
	 * @return Array Form With Pricing details
	 */
	public function getPricingForm($price_data,$user_type = 'Guest')
	{ 
		$symbol = $price_data['currency_symbol'] ?? session('currency_symbol');
		$pricing_form = array();
		foreach($price_data['sub_rooms'] as $room) {
			$description = $room['total_adults'].' '.($room['total_adults'] > 1 ? Lang::get('messages.adults') : Lang::get('messages.adult'));
			if($room['total_children'] > 0) {
				$description .=',  '.$room['total_children'].' '.($room['total_children'] > 1 ? Lang::get('messages.children') : Lang::get('messages.child'));
			}
			$dropdown = $room['selected_plans'] != '' || $room['selected_beds'] != '';
			$dropdown_values = [];
			$meal_plans = \App\Models\HotelRoomPriceRule::where('type','meal')->whereIn('id',$room['selected_plans'])->get();
			foreach($meal_plans as $plan) {
				$dropdown_values[] = ['key' => $plan->name, 'value' => numberFormat($plan->price)];
			}
			$bed_types = \App\Models\HotelRoomPriceRule::where('type','bed')->whereIn('id',$room['selected_beds'])->get();
			foreach($bed_types as $bed) {
				$dropdown_values[] = ['key' => $bed->name, 'value' => numberFormat($bed->price)];
			}
			foreach($room['applied_promotions'] as $promo) {
				$dropdown_values[] = ['key' => Lang::get('messages.'.$promo['type']), 'value' => $promo['amount'] * $room['selected_count'], 'prefix' => '-'];
			}
			$form_data = array('key' => $room['name'], 'value' => $symbol.' '.number_format($room['total_price'] - $room['meal_plan_price'] - $room['bed_price']), 'description' => $description, 'count' => $room['selected_count'],'dropdown' => $dropdown,'dropdown_values' => $dropdown_values);
			$pricing_form[] = formatPricingForm($form_data);
		}
		if($price_data['cleaning_fee'] > 0) {
			$form_data = array('key' => Lang::get('messages.cleaning_fee'), 'value' => $symbol.' '. number_format($price_data['cleaning_fee']));
			$pricing_form[] = formatPricingForm($form_data);
		}

		if($price_data['service_fee'] > 0) {
			$form_data = array('key' => Lang::get('messages.service_fee'), 'value' => $symbol.' '. number_format($price_data['service_fee']),'tooltip' => Lang::get('messages.helps_to_run_our_platform'));
			$pricing_form[] = formatPricingForm($form_data);
		}

		if($price_data['service_charge'] > 0) {
			$form_data = array('key' => Lang::get('admin_messages.property')." ".Lang::get('messages.service_charge'), 'value' => $symbol.' '. number_format($price_data['service_charge']));
			$pricing_form[] = formatPricingForm($form_data);
		}

		if($price_data['property_tax'] > 0) {
			$form_data = array('key' => Lang::get('messages.property_tax'), 'value' => $symbol.' '.number_format($price_data['property_tax']));
			$pricing_form[] = formatPricingForm($form_data);
		}
		
		if($price_data['coupon_price'] > 0) {
			$key = $price_data['coupon_type'] == 'referral' ? Lang::get('messages.referral_credit') : Lang::get('messages.coupon_code');
			$form_data = array('key' => $key, 'value' => $symbol.' '. number_format($price_data['coupon_price']),'class' => 'text-success','value_prefix' => '-');
			$pricing_form[] = formatPricingForm($form_data);
		}

		$form_data = array('key' => Lang::get('messages.total'), 'value' => $symbol.' '.number_format($price_data['payment_total']),'key_style' =>'font-weight:bold','class' => 'border-top pt-2 h5 primary');
		$pricing_form[] = formatPricingForm($form_data);

		return $pricing_form;
	}

	/**
	 * Set All Default Return Data
	 *
	 * @return Array $return_data
	 */
	protected function getDefaultReturnData()
	{
		$return_data['status'] = 'not_available';
		$return_data['can_available'] = false;
		$return_data['status_message'] = Lang::get('messages.something_went_wrong');
		$return_data['currency_code'] = session('currency');
		$return_data['day_price'] = "0";
		$return_data['total_days'] = 1;
		$return_data['cleaning_fee'] = '0';
		$return_data['sub_total'] = '0';
		$return_data['coupon_type'] = "";
		$return_data['coupon_code'] = "";
		$return_data['coupon_price'] = 0;
		$return_data['service_fee'] = "0";
		$return_data['host_fee'] = "0";
		$return_data['total_price'] = "0";
		$return_data['security_fee'] = "0";
		$return_data['payment_total'] = "0";
		$return_data['checkin_time'] = 'flexible';
		$return_data['checkout_time'] = 'flexible';
		$return_data['payout'] = 0;
		$return_data['total'] = "0";
		$return_data['cancellation_policy'] = "very_flexible";
		$return_data['currency_symbol'] = session('currency_symbol');

		return $return_data;
	}

	/**
	 * Calculate Service Fee for given total
	 *
	 * @param Float $price
	 * @return Float $service_fee
	 */
	protected function calculateServiceFee($price)
	{
		$min_service_fee = currencyConvert(fees("min_service_fee"),global_settings('default_currency'));
		if(fees("service_fee_type") == "percentage") {
			$service_fee = calculatePercentageAmount(fees("service_fee"),$price);
		}
		else {
			$service_fee = currencyConvert(fees("service_fee"),global_settings('default_currency'));
		}
		$service_fee = ($service_fee > $min_service_fee) ? $service_fee : $min_service_fee;
		return numberFormat($service_fee);
	}

	/**
	 * Calculate Host Fee for given total
	 *
	 * @param Float $price
	 * @return Float $host_fee
	 */
	protected function calculateHostFee($price)
	{
		return calculatePercentageAmount(fees("host_fee"),$price);
	}

	/**
	 * Check reservation date and times are available or not
	 *
	 * @param Int $hotel_id Hotel Id
	 * @param Array $booking_dates Selected dates
	 * @return Boolean available or not
	 */
	protected function isNotAvailable($hotel_id, $booking_dates)
	{
		$checkin = $booking_dates['checkin'];
		$checkout = $booking_dates['checkout'];
		$reservation_id = $booking_data['reservation_id'] ?? '';

		$booking_days = getDays($checkin, $checkout);
		// Remove Last Day Because Night Booking
		array_pop($booking_days);
		$startDate = current($booking_days);
		$endDate = end($booking_days);

		if(count($booking_days) == 0) {
			return ['status' => true];
		}
		
		foreach($booking_dates['rooms'] as $room) {
			$calendar_data = \DB::Table('hotel_room_calendars')
				->selectRaw('MAX(number) as tmp_number')
				->where('hotel_id',$hotel_id)
				->where('room_id',$room['id'])
				->whereBetween('reserve_date',[$startDate,$endDate])
				->first();
			$available_room = $room['number'] - $calendar_data->tmp_number;
			if($available_room < $room['selected_count']) {
				return ['status' => true];
			}
		}

		return [
			'status' => false,
			'booking_days' => $booking_days,
			'checkin' => $checkin,
			'checkout' => $checkout,
		];
	}

	/**
	 * update Reservation Payout for Host & Guest
	 *
	 * @param App\Models\Reservation $reservation
	 * @param Decimal $host_payout
	 * @param Decimal $guest_refund
	 * 
	 * @return void
	 */
	public function updateReservationPayout($reservation,$list_type, $host_payout,$guest_refund)
	{
		$host_data = array(
			'user_id' => $reservation->host_id,
			'reservation_id' => $reservation->id,
			'list_type' => $list_type,
			'user_type' => "Host",
		);
		$payout = Payout::firstOrNew($host_data);
		// $user_penalty = $reservation->host_user->user_penalty;
		$user_penalty = 0;
		
		// If penalty Already Applied for same reservation remove that penalty
		if($payout->penalty > 0) {
			$user_penalty->paid = currencyConvert($user_penalty->paid - $payout->penalty,$payout->currency_code,$user_penalty->getRawOriginal('currency_code'));
			$user_penalty->remaining = currencyConvert($user_penalty->remaining + $payout->penalty,$payout->currency_code,$user_penalty->getRawOriginal('currency_code'));
			$user_penalty->save();
		}

		if($host_payout > 0) {
			// If user has Penalty then apply to payout
			if($user_penalty && $user_penalty->remaining > 0) {
				if($host_payout >= $user_penalty->remaining) {
					$applied_penalty = $user_penalty->remaining;
					$host_payout = $host_payout - $applied_penalty;
					$user_penalty->remaining = 0;
				}
				else {
					$applied_penalty = $host_payout;
					$user_penalty->remaining -= $host_payout;
					$host_payout = 0;
				}

				$user_penalty->total = $user_penalty->total;
				$user_penalty->paid += $applied_penalty;
				$user_penalty->currency_code = $reservation->currency_code;
				$user_penalty->save();
			}

			$payout->user_id = $reservation->host_id;
			$payout->list_type = $list_type;
			$payout->user_type = "Host";
			$payout->list_id = $list_type == 'hotel' ? $reservation->hotel_id : $reservation->experience_id;
			$payout->reservation_id = $reservation->id;
			$payout->currency_code = $reservation->currency_code;
			$payout->amount = $host_payout;
			$payout->penalty = $applied_penalty ?? 0;
			$payout->save();
		}
		else {
			Payout::where($host_data)->delete();
		}

		$guest_data = array(
			'user_id' => $reservation->user_id,
			'reservation_id' => $reservation->id,
			'list_type' => $list_type,
			'user_type' => "Guest",
		);
		if($guest_refund > 0 ) {
			$payout = Payout::firstOrNew($guest_data);
			$payout->user_id = $reservation->user_id;
			$payout->list_type = $list_type;
			$payout->user_type = "Guest";
			$payout->list_id = $list_type == 'hotel' ? $reservation->hotel_id : $reservation->experience_id;
			$payout->reservation_id = $reservation->id;
			$payout->currency_code = $reservation->currency_code;
			$payout->amount = $guest_refund;
			$payout->save();
		}
		else {
			Payout::where($guest_data)->delete();
		}
	}

	/**
	 * Get Available Coupon Codes
	 *
	 * @param Decimal $total
	 * @param Int $user_id
	 * 
	 * @return Illuminate\Support\Collection
	 */
	public function getAvailableCoupons($total,$user_id = 0)
	{
        $coupon_codes = CouponCode::publiclyVisible()->where('min_amount','<=',$total)->activeOnly()->get();
        $coupon_codes = $coupon_codes->map(function($coupon_code) {
            return [
                'id' => $coupon_code->id,
                'code' => $coupon_code->code,
                'currency_code' => $coupon_code->currency_code,
                'amount' => $coupon_code->amount,
                'display_text' => $coupon_code->display_text,
            ];
        });

        $host_coupon_codes = HostCouponCode::publiclyVisible()->where('user_id',$user_id)->where('min_amount','<=',$total)->activeOnly()->get();
        $host_coupon_codes = $host_coupon_codes->map(function($coupon_code) {
            return [
                'id' => $coupon_code->id,
                'code' => $coupon_code->code,
                'currency_code' => $coupon_code->currency_code,
                'amount' => $coupon_code->amount,
                'display_text' => $coupon_code->display_text,
            ];
        });

        $available_coupons = collect()->concat($coupon_codes)->concat($host_coupon_codes)->sortByDesc('amount')->values();
        
        $referral_users = \App\Models\ReferralUser::authUser()->get();
		$available_credit = $referral_users->where('user_id',Auth::id())->sum('user_credited_amount') + $referral_users->where('referral_user_id',Auth::id())->sum('referral_credited_amount');
		if($available_credit > 0) {
			$referral_user = $referral_users->first();
			$available_coupons->prepend([
				'id' => 0,
				'code' => 'referral',
				'currency_code' => $referral_user->currency_code,
				'amount' => $available_credit,
				'display_text' => Lang::get('messages.apply_referral_to_get',['amount' => $referral_user->currency_symbol.''.$available_credit]),
			]);
		}

        return $available_coupons;
	}
}