<?php

/**
 * Reservation Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers
 * @category    ReservationController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Message;
use App\Models\MessageConversation;
use App\Models\HotelRoomCalendar;
use Lang;
use Str;
use Auth;

class ReservationController extends Controller
{
	/**
     * Display all the upcoming bookings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function bookings(Request $request)
	{
		$active_tab = 'current';
		if(in_array($request->type,['current','upcoming','past','pending'])) {
			$active_tab = $request->type;
		}

		return view('reservations.bookings',compact('active_tab'));
	}

	/**
     * Display all the Reservations
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function reservations(Request $request)
	{
		$active_tab = 'current';
		if(in_array($request->type,['current','upcoming','past','pending'])) {
			$active_tab = $request->type;
		}

		return view('reservations.reservations',compact('active_tab'));
	}

	/**
     * Get Reservation details based on given type
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Json $reservation_details
     */
	public function getReservations(Request $request)
	{
		$reservations = Reservation::userBased($request->user_type)->with('hotel.hotel_address','user','host_user');
		if($request->type == 'current') {
			$reservations = $reservations->where(function($query) {
	            $query->where(function($query) {
	                $query->where('checkin','>=',date('Y-m-d'))->where('checkout','<=',date('Y-m-d'));
	            })
	            ->orWhere(function($query) {
	                $query->where('checkin','<=',date('Y-m-d'))->where('checkout','>=',date('Y-m-d'))->whereIn('status',['Accepted']);
	            });
	        });
		}
		else if($request->type == 'past') {
			$reservations = $reservations->where('checkout','<',date('Y-m-d'));
		}
		else if($request->type == 'upcoming') {
			$reservations = $reservations->where('checkin','>',date('Y-m-d'))->whereIn('status',['Accepted']);
		}
		else if($request->type == 'pending') {
			$reservations = $reservations->whereIn('status',['Pending','Pre-Accepted','Pre-Approved']);
		}
		else if($request->type == 'cancelled') {
			$reservations = $reservations->whereIn('status',['Cancelled']);
		}
		
		$reservations = $reservations->orderByDesc('id')->get();
		$reservation_data = $this->mapReservationsData($reservations,'host_user');

		return response()->json([
			'status' => true,
			'status_message' => Lang::get('messages.listed_successfully'),
			'data' => $reservation_data->values(),
		]);
	}

	/**
     * Cancel Reservation for given id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect to reservations or bookings page
     */
	public function cancelReservation(Request $request)
	{
		$user_id = Auth::id();
		
		$reservation = Reservation::authUser()->find($request->reservation_id);
		
		if(in_array($reservation->status,['Cancelled','Declined', 'Expired', 'Completed'])) {
			flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.booking_already_cancelled'));
			return back();
		}

		$room_reservations = $reservation->room_reservations->where('status','Accepted')->values();
		if($request->room_reservations != 'all') {
			$room_reservations = $room_reservations->where('status','Accepted')->where('id',$request->room_reservations)->values();
		}

		$booking_dates = getDays($reservation->getRawOriginal('checkin'),$reservation->getRawOriginal('checkout'));

        array_pop($booking_dates);
        foreach($reservation->room_reservations as $room) {
            foreach ($booking_dates as $key => $date) {
                $hotel_calendar = HotelRoomCalendar::firstOrNew(['hotel_id' => $reservation->hotel_id,'room_id' => $room->room_id,'reserve_date' => $date]);
                $hotel_calendar->number -= 1;
                $hotel_calendar->save();
            }
        }		

		if($room_reservations->count() == 0) {
			flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.booking_already_cancelled'));
			return back();
		}

		$user_type = ($reservation->user_id == $user_id) ? "Guest" : "Host";		
		$cancel_service = resolve("App\Services\CancellationPolicies\FlexibleCancellation",['user_type' => $user_type, 'reservation' => $reservation,'room_reservations' => $room_reservations]);
		$cancel_service->setCancelReason($request->cancel_reason);
		$payout_refund_data = $cancel_service->calcPayoutRefundAmount();
		
		$price_service = resolve('App\Services\ReserveService');
		$price_service->updateReservationPayout($reservation,'hotel',$payout_refund_data['host_payout_amount'],$payout_refund_data['guest_refund_amount']);
		$message_type = getMessageType(strtolower($user_type).'_cancel_booking');

		$message = Message::where('reservation_id',$reservation->id)->first();
		$user_message = removeEmailNumber($request->cancel_message);

		/*$message_conversation = new MessageConversation;
        $message_conversation->message_id = $message->id;
        $message_conversation->user_from = $user_id;
        $message_conversation->user_to = $reservation->user_id;
        $message_conversation->message = $user_message;
        $message_conversation->message_type = $message_type;
        $message_conversation->save();*/

		$return_url = ($user_type == "Host") ? 'reservations' : 'bookings';

		resolveAndSendNotification("bookingCancelled",$reservation->id,$user_type);
		
		flashMessage('success', Lang::get('messages.success'), Lang::get('messages.booking_cancelled_successfully'));
		
		$redirect_url = resolveRoute($return_url);
        return redirect($redirect_url);
	}

	/**
     * Pre Accept, Pre Approve or Decline Reservation for given id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect to reservations or bookings page
     */
	public function requestAction(Request $request)
	{
		if($request->user_type == "Guest") {
			return $this->guestRequestAction($request);
		}
		
		$user_id = Auth::id();

		$message = Message::where('host_id',$user_id)->find($request->message_id);
		$reservation = optional($message)->room_reservation;
		if($message == '' || !in_array(optional($reservation)->status,['Inquiry','Pending'])) {
			flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.invalid_request'));
			return response()->json([
				'status' => 'reload',
			]);
		}

		if($request->type == 'decline') {
			$message_type = getMessageType('booking_declined');
			$reservation->status = "Declined";
			$reservation->cancel_reason = $request->reason;
			$reservation->cancelled_at = now();
		}
		else if($request->type == 'pre_approve') {
			$message_type = getMessageType('request_pre_approved');
			$reservation->status = "Pre-Approved";
		}
		else {
			$message_type = getMessageType('booking_pre_accepted');
			$reservation->pre_accepted_at = now();
			$reservation->status = "Pre-Accepted";			
		}
		
		$reservation->save();

		$user_message = removeEmailNumber($request->message);

		$message->host_message = $user_message;
		$message->save();

		$message_conversation = new MessageConversation;
        $message_conversation->message_id = $message->id;
        $message_conversation->user_from = $user_id;
        $message_conversation->user_to = $reservation->user_id;
        $message_conversation->message = $user_message;
        $message_conversation->message_type = $message_type;
        $message_conversation->save();

		if($request->type == 'decline') {
			resolveAndSendNotification("requestDeclined",$reservation->id);
			flashMessage('success',Lang::get('messages.success'), Lang::get('messages.request_declined_successfully'));
		}
		else if($request->type == 'pre_approve') {
			resolveAndSendNotification("requestPreApproved",$reservation->id);
			flashMessage('success',Lang::get('messages.success'), Lang::get('messages.request_pre_approved'));
		}
		else {
			resolveAndSendNotification("requestPreAccepted",$reservation->id);
			flashMessage('success',Lang::get('messages.success'), Lang::get('messages.request_pre_accepted'));
		}
		
		return response()->json([
			'status'    => true,
			'status_action' => 'reload',
		]);
	}

	/**
     * Pre Accept, Pre Approve or Decline Reservation for given id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect to reservations or bookings page
     */
	public function guestRequestAction(Request $request)
	{
		$user_id = Auth::id();

		$message = Message::where('user_id',$user_id)->find($request->message_id);
		$reservation = optional($message)->room_reservation;
		if($message == '' || optional($reservation)->status != 'Pending') {
			flashMessage('danger', Lang::get('messages.invalid_request'),Lang::get('messages.failed'));
			return response()->json([
				'status' => 'reload',
			]);
		}

		if($request->type == 'cancel_request') {
			$message_type = getMessageType('guest_cancel_request');
			$reservation->cancel_reason = $request->reason;
			$reservation->status = "Cancelled";
			$reservation->cancelled_by = "Guest";
			$reservation->cancelled_at = now();
		}
		
		$reservation->save();

		$user_message = removeEmailNumber($request->message);

		$message->host_message = $user_message;
		$message->save();

		$message_conversation = new MessageConversation;
        $message_conversation->message_id = $message->id;
        $message_conversation->user_from = $user_id;
        $message_conversation->user_to = $reservation->host_id;
        $message_conversation->message = $user_message;
        $message_conversation->message_type = $message_type;
        $message_conversation->save();

        if($request->type == 'cancel_request') {
			flashMessage('success',Lang::get('messages.success'), Lang::get('messages.request_cancelled_successfully'));
		}
		
		return response()->json([
			'status' => 'reload',
		]);
	}

	/**
     * Display Receipt of given reservation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function viewReceipt(Request $request)
	{
		$reservation = Reservation::with('hotel.hotel_address','hotel.hotel_photos','host_user')->where('code',$request->code)->where('user_id',Auth::id())->firstOrFail();

		$cancellation_policies = [];
		foreach($reservation->room_reservations as $room_reservation) {
			$policies = collect(json_decode($room_reservation->cancellation_policy,true));
			if(count($policies) > 0){
				$cancellation_policies[] = [
					'room_name' => $room_reservation->hotel_room->name,
					'policies' => collect(json_decode($room_reservation->cancellation_policy,true))
				];
			}
	    }

		$pricing_data = $reservation->getReceiptPricingForm('Guest');
		$redirect_url = resolveRoute('download_receipt',['code' => $reservation->code]);
		return view('reservations.receipt', compact('reservation','pricing_data','cancellation_policies','redirect_url'));
	}

	/**
     * Download Receipt of given reservation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function downloadReceipt(Request $request)
	{
		$data['reservation'] = $reservation = Reservation::with('hotel.hotel_address','hotel.hotel_photos','host_user')->where('code',$request->code)->where('user_id',Auth::id())->firstOrFail();
		$reservations =  $reservation->room_reservations;
		$cancellation_policies = [];
		foreach($reservations as $room_reservation){
			$policies = collect(json_decode($room_reservation->cancellation_policy,true));
			if(count($policies) > 0){
			    $cancellation_policies[] = [
				    'room_name' => $room_reservation->hotel_room->name,
				     'policies' => collect(json_decode($room_reservation->cancellation_policy,true))
			    ];
	        }
	    }

		$data['pricing_data'] = $reservation->getPricingForm('Guest');
		return view('reservations.download_receipt', $data,compact('cancellation_policies','reservations'));
	}

	/**
     * Display Itinerary of given reservation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function viewItinerary(Request $request)
	{
		$reservation = Reservation::with('hotel.hotel_address','host_user')->where('code',$request->code)->firstOrFail();

		$user_type = getUserType($reservation->host_id);
		$user_type = 'Guest';
		$pricing_data = $reservation->getPricingForm($user_type);
		
		return view('reservations.'.strtolower($user_type).'_itinerary', compact('reservation','pricing_data'));
	}

	/**
	 * Get Formatted Reservations data
	 *
	 * @param  \App\Models\Reservation $reservations
	 * @param  String $user_type host_user | user
	 * @return \Illuminate\Support\Collection $reservations
	 */
	public function mapReservationsData($reservations,$user_type)
	{
		return $reservations->map(function($reservation) use($user_type) {
			if($reservation->status == 'Accepted') {
				$formatted_address = $reservation->hotel->hotel_address->full_address;
				$contact_mail = $reservation->hotel->contact_email;
				$phone_number = $reservation->$user_type->contact_no;
			}
			else {
				$formatted_address = $reservation->hotel->hotel_address->address_line_display;
			}

			$reviews = $reservation->reviews->where('user_from',Auth::id())->count();
			if(in_array($reservation->status,['Pre-Accepted','Pre-Approved'])) {
				$booking_url = resolveRoute('confirm_reserve',['reservation_id' => $reservation->id]);
			}
			if($reservation->status == 'Accepted' || $reservation->status == 'Cancelled') {
				$itinerary_url = resolveRoute('view_itinerary',['code' => $reservation->code]);
				$receipt_url = resolveRoute('view_receipt',['code' => $reservation->code]);
			}

			$room_reservations = $reservation->room_reservations->map(function($room_reservation) {
				return [
					'id' => $room_reservation->id,
					'room_name' => $room_reservation->hotel_room->name,
					'adults' => $room_reservation->adults,
					'extra_adults' => $room_reservation->extra_adults,
					'children' => $room_reservation->children,
					'extra_children' => $room_reservation->extra_children,
					'cancellation_policy' => json_decode($room_reservation->cancellation_policy,true),
					'status' => $room_reservation->status,
				];
			});
			$room = $reservation->room_reservations;
			$date_diff = now()->diffInDays(getDateObject($reservation['checkin']))+1;
			foreach($room as $room_reservation) {
			$cancellation_policies = collect(json_decode($room_reservation->cancellation_policy,true));
			$cancellation_policy = $cancellation_policies->where('days', '>=', $date_diff)->sortBy('days')->first();
		}

			return [
				'id' => $reservation->id,
				'address' => $formatted_address,
				'status' => $reservation->status,
				'list_type' => 'hotel',
				'name' => $reservation->hotel->name,
				'hotel_link' => $reservation->hotel->link,
				'hotel_image_src' => $reservation->hotel->image_src,
				'currency_symbol' => $reservation->currency_symbol,
				'total' => $reservation->guestOrHostTotal(),
				'dates' => $reservation->formatted_checkin.' - '.$reservation->formatted_checkout,
				'checkin' => $reservation->formatted_checkin,
				'checkout' => $reservation->formatted_checkout,
				// 'inbox_url' => resolveRoute('conversation',['id' => $reservation->message_id]),
				'itinerary_url' => $itinerary_url ?? '#',
				'receipt_url' => $receipt_url ?? '#',
				'user_name' => $reservation->$user_type->first_name,
				'company_name' => optional($reservation->$user_type->company)->company_name,
				'company_logo_src' => optional($reservation->hotel)->logo_src,
				'contact_mail' => $contact_mail ?? '',
				'phone_number' => $phone_number ?? '',
				'user_link' => resolveRoute('view_profile',['id' => $reservation->$user_type->id]),
				'profile_picture_src' => $reservation->$user_type->profile_picture_src,
				'is_available' => true,
				'canCancelButtonShow' => !in_array($reservation->status,["Cancelled","Declined","Expired","Pending","Pre-Approved","Pre-Accepted"]) && $cancellation_policy < $date_diff && !$reservation->checkoutCrossed(),
				'canReviewButtonShow' => $reservation->canWriteReview() && $reservation->checkoutCrossed(),
				'review_text' => $reviews > 0 ? Lang::get('messages.edit_review') : Lang::get('messages.write_review'),
				'review_url' => resolveRoute('edit_review',['id' => $reservation->id]),
				'cancel_url' => resolveRoute('cancel_reservation'),
				'booking_url' => $booking_url ?? '',
				'room_reservations' => $room_reservations,
			];
		});
	}
}