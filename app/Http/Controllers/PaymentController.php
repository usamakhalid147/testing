<?php

/**
 * Payment Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    PaymentController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\RoomReservation;
use App\Models\HotelRoomCalendar;
use App\Models\HotelRoom;
use App\Models\Message;
use App\Models\MessageConversation;
use App\Models\CouponCode;
use App\Models\HostCouponCode;
use App\Models\ReferralUser;
use App\Contracts\paymentInterface;
use App\Models\UserSavedCard;
use App\Models\User;
use Lang;
use Auth;

class PaymentController extends Controller
{
    /**
     * Constructor
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
    	$this->hotel_id = $request->hotel_id;
        $this->hotel_details = Hotel::loadRelations()->find($this->hotel_id);
        if(!$this->hotel_details) {
            $redirect_url = resolveRoute('home');
            return redirect($redirect_url);
        }
        
        $this->reserve_service = resolve('App\Services\ReserveService');
    }

    /**
     * Display Payment Page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
        if($this->hotel_details->user_id == Auth::id()) {
            $redirect_url = resolveRoute('home');
            return redirect($redirect_url);   
        }

		$booking_attempt_id = $request->booking_attempt_id;
        $payment_data = session('payment.'.$booking_attempt_id);
        if(!$payment_data) {
            $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
            return redirect($redirect_url);
        }

        $payment_data['checkin_formatted'] = getDateInFormat($payment_data['checkin']);
        $payment_data['checkout_formatted'] = getDateInFormat($payment_data['checkout']);
        $optional_data['coupon_code'] = $payment_data['coupon_code'] ?? '';
        $price_details = $this->reserve_service->priceCalculation($this->hotel_id, $payment_data,$optional_data);
        if(!$price_details['can_available']) {
            flashMessage('danger',Lang::get('messages.failed'),Lang::get('messages.your_selected_rooms_not_available'));
            $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
            return redirect($redirect_url);
        }

        $hotel_id = $this->hotel_details->id;
        $sub_rooms = $payment_data['rooms'];
        $hotel = $this->hotel_details;
        $hotel_rules = $this->hotel_details->hotel_rules;
        $booking_type = $price_details['booking_type'];

        $pricing_form = $this->reserve_service->getPricingForm($price_details);

        $payment_currency = credentials('payment_currency','Paypal');
        $payment_amount = currencyConvert($price_details["payment_total"],$price_details["currency_code"],$payment_currency);

        $paypal_purchase_data = [
            "description" => 'Payment for '.$this->hotel_details->name.' at '.$payment_data['checkin'].' - '.$payment_data['checkout'],
            "amount" => [
                "currency_code" => $payment_currency,
                "value" => $payment_amount
            ]
        ];

        $available_coupons = $this->reserve_service->getAvailableCoupons($price_details['sub_total'], $this->hotel_details->user_id);
        $rooms = collect($payment_data['rooms'])->pluck('id')->toArray();
        $hotel_rooms = \App\Models\HotelRoom::with('cancellation_policies')->where('hotel_id',$this->hotel_id)->whereIn('id',$rooms)->get();

        $payment_methods = \Arr::pluck(PAYMENT_METHODS, 'key');
        $hotel_rooms->each(function($room) use(&$payment_methods) {
            $payment_methods = array_intersect($payment_methods,explode(',',$room->payment_method));
        });

        $payment_methods = array_values($payment_methods);

        if(count($payment_methods) == 0) {
            flashMessage('danger',Lang::get('messages.failed'),Lang::get('messages.something_went_wrong'));
            $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
            return redirect($redirect_url);
        }

        $default_payment_method = $payment_methods[0];

        if(isset($payment_data['client_secret']) && $payment_data['client_secret'] != '') {
            if(!in_array('stripe',$payment_methods)) {
                $payment_methods[] = 'stripe';
            }
            $default_payment_method = 'stripe';
        }

        $saved_cards = UserSavedCard::where('user_id',Auth::id())->where('payment_method','!=','')->get();

        return view('payment.main',compact(
            'payment_data',
            'booking_attempt_id',
            'hotel_id',
            'sub_rooms',
            'hotel',
            'hotel_rules',
            'price_details',
            'booking_type',
            'paypal_purchase_data',
            'pricing_form',
            'payment_methods',
            'default_payment_method',
            'saved_cards',
            'available_coupons',
            'hotel_rooms',
        ));
    }

    /**
     * Complete Payment and redirect to bookings page
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contracts\paymentInterface  $payment_service
     * @return \Illuminate\Http\Response
     */
    public function completePayment(Request $request)
    {
        $payment_service = '';
        if ($request->payment_method != 'pay_at_hotel') {
            $payment_service = resolve('App\Contracts\paymentInterface');
        }

        $booking_attempt_id = $request->booking_attempt_id;
        $hotel_id = $this->hotel_id;
        $user = Auth::user();

        $payment_data = session('payment.'.$booking_attempt_id);
        if(!$payment_data) {
            $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
            return redirect($redirect_url);
        }
        $optional_data['coupon_code'] = $payment_data['coupon_code'] ?? '';
        $price_details = $this->reserve_service->priceCalculation($hotel_id, $payment_data);
        if(!$price_details['can_available']) {
            flashMessage('danger',Lang::get('messages.failed'),Lang::get('messages.your_selected_rooms_not_available'));
            $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
            return redirect($redirect_url);
        }

        if ($request->payment_method == 'pay_at_hotel') {
            $payment_info = [
                "payment_method" => "pay_at_hotel",
                "transaction_id" => hash_hmac('sha256', $user->email.$user->phone_number,$user->phone_number),
            ];
            $reservation = $this->completeReserve($booking_attempt_id, $payment_info, $request->special_request ?? null);
            
            session()->forget('payment.'.$booking_attempt_id);

            flashMessage('success',Lang::get('messages.success'),Lang::get('messages.booking_completed'));
            $redirect_url = resolveRoute('bookings',['type' => 'current']);
            return redirect($redirect_url);
        }

        if($request->filled("stripe_payment_intent")) {
            $payment_intent = $payment_service->getPayment($request->stripe_payment_intent);
            if($payment_intent["error"]) {
                flashMessage('danger',Lang::get('messages.failed'),$payment_intent["error_message"]);
                return back();
            }

            if($payment_service->checkPaymentCompleted($payment_intent["data"])) {
                $payment_info = [
                    "payment_method" => "stripe",
                    "transaction_id" => $payment_intent["data"]->id,
                ];
                if($request->special_request)
                {
                    $reservation = $this->completeReserve($booking_attempt_id,$payment_info,$request->special_request);
                }
                else
                {
                    $reservation = $this->completeReserve($booking_attempt_id,$payment_info);
                }
                session()->forget('payment.'.$booking_attempt_id);

                flashMessage('success',Lang::get('messages.success'),Lang::get('messages.payment_completed'));
                $redirect_url = resolveRoute('bookings',['type' => 'current']);
                return redirect($redirect_url);
            }
        }

        $validation_data = $payment_service->validationData();
        $request->validate($validation_data['rules'],$validation_data['messages'],$validation_data['attributes']);

        $pay_data = $payment_service->paymentDetailsFromRequest($request);

        if($request->payment_method == 'paypal') {
            $payment = $payment_service->doPayment($payment_data,$pay_data);
            if($payment["error"]) {
                return response()->json([
                    'status' => 'false',
                    'status_text' => Lang::get('messages.failed'),
                    'status_message' => $payment["error_message"],
                ]);
            }
            $payment_order = $payment_service->getPayment();
            if($payment_order["error"]) {
                return response()->json([
                    'status' => 'false',
                    'status_text' => Lang::get('messages.failed'),
                    'status_message' => $payment_order["error_message"],
                ]);
            }
            if($payment_service->checkPaymentCompleted($payment_order["data"])) {
                $transaction_id = $payment_service->getTransactionIdFromOrder($payment_order["data"]);
                $payment_info = [
                    "payment_method" => "paypal",
                    "transaction_id" => $transaction_id,
                ];
                $reservation = $this->completeReserve($booking_attempt_id, $payment_info, $request->special_request ?? null);

                session()->forget('payment.'.$booking_attempt_id);
                flashMessage('success',Lang::get('messages.success'),Lang::get('messages.payment_completed'));
                return response()->json([
                    'status' => 'redirect',
                    'redirect_url' => resolveRoute('bookings',['type' => 'current']),
                ]);
            }
            return response()->json([
                'status' => 'false',
                'status_text' => Lang::get('messages.failed'),
                'status_message' => Lang::get('messages.something_went_wrong'),
            ]);
        }
        else if ($request->payment_method == 'one_pay') {
            $payment_currency = credentials('payment_currency','OnePay');
            $payment_description = 'Payment for '.$this->hotel_details->name.' at '.$payment_data['checkin'].' - '.$payment_data['checkout'];
            $payment_amount = currencyConvert(round($price_details["payment_total"]),$price_details["currency_code"],$payment_currency);
            $purchaseData = [
                'amount' => ($payment_amount * 100),
                'description' => $payment_description,
                'currency' => $payment_currency,
                'phone_number' => $user->phone_number,
                'email' => $user->email,
                'user_id' => $user->id,
                'booking_attempt_id' => $booking_attempt_id,
                'hotel_id' => $this->hotel_id,
            ];
            $payment_url = $payment_service->doPayment($pay_data,$purchaseData);
            return redirect($payment_url);
            
            $payment_info = [
                "payment_method" => "one_pay",
                "transaction_id" => hash_hmac('sha256', $user->email.$user->phone_number,$user->phone_number),
            ];
            $reservation = $this->completeReserve($booking_attempt_id, $payment_info, $request->special_request ?? null);
            
            session()->forget('payment.'.$booking_attempt_id);

            flashMessage('success',Lang::get('messages.success'),Lang::get('messages.booking_completed'));
            $redirect_url = resolveRoute('bookings',['type' => 'current']);
            return redirect($redirect_url);
        }
        else if($request->payment_method == 'stripe') {
            $payment_currency = credentials('payment_currency','Stripe');
            $payment_amount = currencyConvert($price_details["payment_total"],$price_details["currency_code"],$payment_currency);
            $payment_description = 'Payment for '.$this->hotel_details->name.' at '.$payment_data['checkin'].' - '.$payment_data['checkout'];
            $purchaseData = array(
                'amount' => ($payment_amount * 100),
                'description' => $payment_description,
                'currency' => $payment_currency,
            );

            if($request->save_for_future_use == 1 && $request->saved_payment_method == '') {
                $saved_card = UserSavedCard::where('user_id',$user->id)->first();
                if($saved_card == '') {
                    $customer_data = [
                        'email' => $user->email,
                        'name' => $user->first_name,
                        'description' => 'Customer For the User '.$user->id,
                    ];
                    $customer = $payment_service->createCustomer($customer_data);
                    if($customer['error']) {
                        flashMessage('danger',Lang::get('messages.failed'),$customer["error_message"]);
                        return back();
                    }

                    $saved_card = new UserSavedCard;
                    $saved_card->user_id = $user->id;
                    $saved_card->customer_id = $customer['data']->id;
                    $saved_card->save();
                }

                $purchaseData['customer'] = $saved_card->customer_id;
                $purchaseData['setup_future_usage'] = 'off_session';
            }

            if($request->saved_payment_method != '') {
                $saved_card = UserSavedCard::where('payment_method',$request->saved_payment_method)->first();
                if($saved_card == '') {
                    flashMessage('danger',Lang::get('messages.failed'),Lang::get('messages.invalid_request'));
                    return back();
                }
                $purchaseData['customer'] = $saved_card->customer_id;
                $purchaseData['payment_method'] = $saved_card->payment_method;
                $purchaseData['off_session'] = true;
                
                $payment_intent = $payment_service->createPaymentIntent($purchaseData);
            }
            else {
                $payment = $payment_service->doPayment($pay_data,$purchaseData);
                if($payment["error"]) {
                    flashMessage('danger',Lang::get('messages.failed'),$payment["error_message"]);
                    return back();
                }

                $payment_intent = $payment_service->getPayment();

                if($request->save_for_future_use == 1) {
                    $this->saveCardDetails($payment_intent['data'],$payment['card']);
                }
            }

            if($payment_intent["error"]) {
                flashMessage('danger',Lang::get('messages.failed'),$payment_intent["error_message"]);
                return back();
            }

            if($payment_service->isTwoStep()) {
                $two_step_data = $payment_service->getTwoStepData();
                if($two_step_data["error"]) {
                    flashMessage('danger',Lang::get('messages.failed'),$payment["error_message"]);
                    return back();
                }
                $payment_data['client_secret'] = $two_step_data["client_secret"];
                session(['payment.'.$booking_attempt_id => $payment_data]);

                $redirect_url = resolveRoute('payment.home',['tab' => "confirm-and-pay","booking_attempt_id" => $booking_attempt_id,"room_id" => $room_id]);
                return redirect($redirect_url);
            }
            
            if($payment_service->checkPaymentCompleted($payment_intent["data"])) {
                $payment_info = [
                    "payment_method" => "stripe",
                    "transaction_id" => $payment_intent["data"]->id,
                ];
                $reservation = $this->completeReserve($booking_attempt_id, $payment_info, $request->special_request ?? null);

                // $this->sendMessageToHost($reservation,$request->message);
                
                flashMessage('success',Lang::get('messages.success'),Lang::get('messages.payment_completed'));
                session()->forget('payment.'.$booking_attempt_id);
                $redirect_url = resolveRoute('bookings',['type' => 'current']);
                return redirect($redirect_url);
            }
        }

        flashMessage('danger',Lang::get('messages.failed'),Lang::get('messages.invalid_request'));
        $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
        return redirect($redirect_url);
    }

    /**
     * Save Card Details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Void
     */
    protected function saveCardDetails($payment_data,$card_detail)
    {
        $saved_card = UserSavedCard::firstOrNew(['payment_method' => '']);
        $saved_card->user_id = getCurrentUserId();
        $saved_card->customer_id = $payment_data->customer;
        $saved_card->payment_method = $payment_data->payment_method;
        $saved_card->brand = Ucfirst($card_detail->brand);
        $saved_card->last4 = $card_detail->last4;
        $saved_card->exp_month = $card_detail->exp_month;
        $saved_card->exp_year  = $card_detail->exp_year;
        $saved_card->save();
    }

    /**
     * Apply Or Remove Coupon Code in Current Booking
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validateCoupon(Request $request)
    {
        $booking_attempt_id = $request->booking_attempt_id;
        $payment_data = session('payment.'.$booking_attempt_id);
        if(!$payment_data) {
            return response()->json([
                'status' => 'redirect',
                'redirect_url' => resolveRoute('hotel_details',['id' => $this->hotel_id]),
            ]);
        }

        $optional_data['coupon_code'] = $payment_data['coupon_code'];
        $payment_data['coupon_code'] = '';
        $price_details = $this->reserve_service->priceCalculation($this->hotel_id, $payment_data,$optional_data);
        if($request->type != 'remove') {
            $payment_data['coupon_code'] = $request->coupon_code;
            
            $admin_coupon_code = CouponCode::where('code', $request->coupon_code)->where('min_amount','<=',$price_details['total_price'])->active()->limit(1)->get();
            
            if($admin_coupon_code->count() == 0) {
                $host_coupon_code = HostCouponCode::where('user_id',$this->hotel_details->user_id)->where('min_amount','<=',$price_details['total_price'])->active()->limit(1)->get();
                if($host_coupon_code->count() == 0) {
                    return response()->json([
                        'error' => true,
                        'error_message' => Lang::get('messages.invalid_coupon'),
                    ]);
                }
                $coupon_type = 'host';
                $coupon_code = $host_coupon_code->first();
            }
            else {
                $coupon_type = 'admin';
                $coupon_code = $admin_coupon_code->first();
            }

            $reservations = Reservation::where('user_id',Auth::id())->where('coupon_type',$coupon_type)->where('coupon_code',$coupon_code->code)->get();

            if($reservations->count() > $coupon_code->per_user_limit) {
                return response()->json([
                    'error' => true,
                    'error_message' => Lang::get('messages.coupon_code_already_used'),
                ]);
            }

            if($reservations->where('hotel_id',$this->hotel_id)->count() >= $coupon_code->per_list_limit) {
                return response()->json([
                    'error' => true,
                    'error_message' => Lang::get('messages.coupon_code_already_used'),
                ]);
            }
        }

        $optional_data['coupon_code'] = $payment_data['coupon_code'];
        $price_details = $this->reserve_service->priceCalculation($this->hotel_id, $payment_data, $optional_data);

        if(!$price_details['can_available']) {
            return response()->json([
                'status' => 'redirect',
                'redirect_url' => resolveRoute('hotel_details',['id' => $this->hotel_id]),
            ]);
        }

        session(['payment.'.$booking_attempt_id => $payment_data]);

        $payment_currency = credentials('payment_currency','Paypal');
        $payment_amount = currencyConvert($price_details["payment_total"],$price_details["currency_code"],$payment_currency);

        $paypal_purchase_data = [
            "description" => 'Payment for '.$this->hotel_details->name.' at '.$payment_data['checkin'].' - '.$payment_data['checkout'],
            "amount" => [
                "currency_code" => $payment_currency,
                "value" => $payment_amount
            ]
        ];

        $pricing_form = $this->reserve_service->getPricingForm($price_details);

        return response()->json([
            'status' => 'success',
            'status_text' => Lang::get('messages.success'),
            'status_message' => ($optional_data['coupon_code'] != '') ? Lang::get('messages.coupon_applied') : Lang::get('messages.coupon_removed'),
            'pricing_form' => $pricing_form,
            'price_details' => $price_details,
            'paypal_purchase_data' => $paypal_purchase_data,
        ]);
    }
    
    /**
     * Complete Reservation
     *
     * @param  string $booking_attempt_id
     * @param  Array $payment_info
     * @return \App\Models\Reservation $reservation
     */
    protected function completeReserve($booking_attempt_id,$payment_info = [], $special_request=null)
    {
        $payment_data = session('payment.'.$booking_attempt_id);
        if(!$payment_data) {
            $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
            return redirect($redirect_url);
        }
        $payment_data['payment_currency'] = credentials('payment_currency',$payment_info['payment_method']);

        $reservation = $this->createReservation($payment_data);
        $reservation->code = getReserveCode($reservation->id);
        $reservation->transaction_id = $payment_info['transaction_id'];
        $reservation->payment_method = $payment_info['payment_method'];
        $reservation->status = "Accepted";
        $reservation->accepted_at = date("Y-m-d H:i:s");
        $reservation->special_request = $special_request ?? $reservation->special_request;
        $reservation->save();

        $booking_dates = getDays($reservation->getRawOriginal('checkin'),$reservation->getRawOriginal('checkout'));
        // Remove Last Day Because Night Booking
        array_pop($booking_dates);
        foreach($reservation->room_reservations as $room) {
            foreach ($booking_dates as $key => $date) {
                $hotel_calendar = HotelRoomCalendar::firstOrNew(['hotel_id' => $reservation->hotel_id,'room_id' => $room->room_id,'reserve_date' => $date]);
                $hotel_calendar->user_id = $reservation->host_id;
                $hotel_calendar->hotel_id = $reservation->hotel_id;
                $hotel_calendar->room_id = $room->room_id;
                $hotel_calendar->number += 1;
                $hotel_calendar->reserve_date = $date;
                $hotel_calendar->save();
            }
        }

        $host_payout = $reservation->calcHostPayoutAmount();
        $this->reserve_service->updateReservationPayout($reservation,'hotel',$host_payout,0);

        $transaction_data = [
            'user_id' => $reservation->user_id,
            'reservation_id' => $reservation->id,
            'type' => 'booking',
			'user_type' => 'user',
            'description' => '',
            'currency_code' => $reservation->currency_code,
            'amount' => $reservation->total,
            'transaction_id' => $reservation->transaction_id,
            'payment_method' => $reservation->payment_method,
        ];
        createTransaction($transaction_data);

        resolveAndSendNotification("bookingConfirmed",$reservation->id);

        return $reservation;
    }

    /**
     * Create New Reservation
     *
     * @param  Array $payment_data
     * @param  string $type instant_book | request_book
     * @return \App\Models\Reservation $reservation
     */
    protected function createReservation($payment_data,$type = 'instant_book')
    {
        $optional_data['coupon_code'] = $payment_data['coupon_code'];
        $price_details = $this->reserve_service->priceCalculation($this->hotel_id, $payment_data, $optional_data);

        $reservation = Reservation::find($payment_data['reservation_id'] ?? '');
        if($reservation == '') {
            $reservation = new Reservation;
            $reservation->penalty_enabled = fees("host_penalty_enabled");
        }
        $reservation->hotel_id = $this->hotel_details->id;
        $reservation->host_id = $this->hotel_details->user_id;
        $reservation->user_id = Auth::id();
        $reservation->checkin = $payment_data["checkin"];
        $reservation->checkin_at = $price_details["checkin_time"];
        $reservation->checkout = $payment_data["checkout"];
        $reservation->checkout_at = $price_details["checkout_time"];
        $reservation->adults = $price_details["total_adults"];
        $reservation->children = $price_details["total_children"];
        $reservation->total_rooms = $price_details["total_rooms"];
        $reservation->currency_code = $price_details["currency_code"];
        $reservation->total_nights = $price_details["total_nights"];
        $reservation->cleaning_fee = $price_details["cleaning_fee"];
        $reservation->service_fee = $price_details["service_fee"];
        $reservation->coupon_code = $price_details["coupon_code"];
        $reservation->coupon_price = $price_details["coupon_price"];
        $reservation->coupon_type = $price_details["coupon_type"];
        $reservation->sub_total = $price_details["sub_total"];
        $reservation->host_fee = $price_details["host_fee"];
        $reservation->service_charge = $price_details["service_charge"];
        $reservation->property_tax = $price_details["property_tax"];
        $reservation->total = $price_details["payment_total"];
        $reservation->payment_currency = global_settings("default_currency");
        $reservation->status = "Pending";
        $reservation->save();

        foreach($price_details['sub_rooms'] as $room_data) {
            $hotel_room = HotelRoom::where('hotel_id',$reservation->hotel_id)->find($room_data['id']);
             
            $hotel_room_prices = $hotel_room->hotel_room_price;
            $adult_price = $hotel_room_prices->adult_price;
            $children_price = $hotel_room_prices->children_price;

            $applied_promotions = !empty($room_data['applied_promotions']) ? $room_data['applied_promotions'] : NULL;
            $cancellation_policy = $hotel_room->cancellation_policies->map(function($policy) {
                return $policy->only(['days','percentage']);
            })->toJson();

            foreach($room_data['add_rooms'] as $room) {
                $host_fee = calculatePercentageAmount(fees("host_fee"),$room['price']);
                $room_reservation = new RoomReservation;
                $room_reservation->room_id = $room_data['id'];
                $room_reservation->reservation_id = $reservation->id;
                $room_reservation->hotel_id = $this->hotel_details->id;
                $room_reservation->adults = $room['adults'];
                $room_reservation->extra_adults = $room['extra_adults'] ?? 0;
                $room_reservation->extra_adults_amount = $room['extra_adults_price'] ?? 0;
                $room_reservation->children = $room['children'];
                $room_reservation->extra_children = $room['extra_children'] ?? 0;
                $room_reservation->extra_children_amount = $room['extra_children_price'] ?? 0;
                $room_reservation->currency_code = session('currency');
                $room_reservation->day_price = $room['day_price'] * $reservation->total_nights;
                $room_reservation->total_days_price = $room_reservation->day_price / $room_data['selected_count'];
                $room_reservation->meal_plan = $room['meal_plan'];
                $room_reservation->meal_plan_amount =$room['meal_plan_amount'];
                $room_reservation->extra_bed = $room['extra_bed'];
                $room_reservation->extra_bed_amount =$room['extra_bed_amount'];
                $room_reservation->applied_promotions = $applied_promotions;
                $room_reservation->promotion_amount = 0;
                $room_reservation->coupon_price = 0;
                $room_reservation->sub_total = $room['price'];
                $room_reservation->property_tax = 0;
                $room_reservation->service_charge = 0;
                $room_reservation->service_fee = 0;
                $room_reservation->total_price = $room['price'];
                $room_reservation->cancellation_policy = $cancellation_policy;
                $room_reservation->status = "Accepted";
                $room_reservation->host_payout_status = "future";
                $room_reservation->host_payout_amount = $room['price'] - $host_fee;
                $room_reservation->host_fee = $host_fee;
                $room_reservation->save();
            }
        }

        return $reservation;
    }

    /**
     * Send User entered message to host
     *
     * @param  \App\Models\Reservation $reservation
     * @param  string $user_message
     * @return void
     */
    protected function sendMessageToHost($reservation,$user_message = '',$message_type = '2')
    {
        // Create New Thread for Display in inbox
        $message = Message::firstOrNew(['list_type' => 'hotel','reservation_id' => $reservation->id]);
        $message->list_type = 'hotel';
        $message->list_id = $reservation->hotel_id;
        $message->reservation_id = $reservation->id;
        $message->user_id = $reservation->user_id;
        $message->host_id = $reservation->host_id;
        $message->guest_message = $user_message;
        $message->save();

        $message_conversation = new MessageConversation;
        $message_conversation->message_id = $message->id;
        $message_conversation->user_from = $reservation->user_id;
        $message_conversation->user_to = $reservation->host_id;
        $message_conversation->message = $user_message;
        $message_conversation->message_type = $message_type;
        $message_conversation->save();
    }

    public function completeOnePayPayment(Request $request)
    {
        $payment_service = resolve('App\Services\Payment\OnePayPaymentService');

        $booking_attempt_id = $request->booking_attempt_id;
        $payment_data = session('payment.'.$booking_attempt_id);

        $pay_data = $request->except(['booking_attempt_id','vpc_SecureHash']);

        $payment_data = session('payment.'.$booking_attempt_id);
        if(!$payment_data) {
            $redirect_url = resolveRoute('hotel_details',['id' => $this->hotel_id]);
            return redirect($redirect_url);
        }

        $result = $payment_service->validatePayment($pay_data,$request->vpc_SecureHash);
        if(isset($result['error']) && $result['error']) {
            flashMessage('danger',Lang::get('messages.failed'),$result['error_message']);
            $redirect_url = resolveRoute('payment.home',['hotel_id' => $this->hotel_id,'booking_attempt_id' => $booking_attempt_id]);
            return redirect($redirect_url);
        }

        $payment_info = [
            "payment_method" => "one_pay",
            "transaction_id" => $result['data']['transaction_id'],
        ];
        $reservation = $this->completeReserve($booking_attempt_id,$payment_info);

        session()->forget('payment.'.$booking_attempt_id);

        flashMessage('success',Lang::get('messages.success'),Lang::get('messages.booking_completed'));
        $redirect_url = resolveRoute('bookings',['type' => 'current']);
        return redirect($redirect_url);
    }
}