<?php

/**
 * Reservation Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    ReservationController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\DataTables\Host\ReservationsDataTable;
use App\DataTables\Host\PayoutsDataTable;
use App\DataTables\PenaltiesDataTable;
use App\Models\Reservation;
use App\Models\Payout;
use App\Models\Message;
use App\Models\MessageConversation;
use App\Models\UserPenalty;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Country;
use App\Models\HotelRoomCalendar;
use Lang;
use Str;
use Carbon\Carbon;
use Auth;

class ReservationController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_reservations');
        $this->view_data['active_menu'] = 'reservations';
    }

    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReservationsDataTable $dataTable,$type = 'all')
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.reservation');
        $this->view_data['type'] = $type;
        $this->view_data['filters_array'] = [
            'all' => Lang::get('admin_messages.all'),
            'current' => Lang::get('admin_messages.current'),
            'upcoming' => Lang::get('admin_messages.upcoming'),
            'cancelled' => Lang::get('admin_messages.cancelled'),
            'completed' => Lang::get('admin_messages.completed'),
        ];
        return $dataTable->setType($type)->render('host.reservations.view',$this->view_data);
    }


    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payoutIndex(PayoutsDataTable $dataTable,$type = 'future')
    {
        $this->view_data['active_menu'] = 'payouts';
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_payouts');
        $this->view_data['sub_title'] = Lang::get('admin_messages.payouts');
        $this->view_data['type'] = $type;
        $this->view_data['filters_array'] = [
            'all' => Lang::get('admin_messages.all'),
            'future' => Lang::get('admin_messages.future'),
            'completed' => Lang::get('admin_messages.completed'),
        ];
        return $dataTable->setType($type)->render('host.reservations.view',$this->view_data);
    }

    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function penaltyIndex(PenaltiesDataTable $dataTable)
    {
        $this->view_data['active_menu'] = 'penalties';
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_penalties');
        $this->view_data['sub_title'] = Lang::get('admin_messages.penalties');
        return $dataTable->render('host.reservations.view',$this->view_data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.booking_details');
        $this->view_data['result'] = $reservation = Reservation::userBased()->with('hotel.hotel_address','user','host_user')->findOrFail($id);
        $this->view_data['room_reservation'] = $room_reservations = $reservation->room_reservations;
        $cancellation_policy = [];
        foreach($room_reservations as $room_reservation){
            $cancellation_policy[] = [
                'room_name' => $room_reservation->hotel_room->name,
                'policies' => collect(json_decode($room_reservation->cancellation_policy,true))
            ];
        }
           $room = $reservation->room_reservations;
            $date_diff = now()->diffInDays(getDateObject($reservation['checkin']))+1;
            foreach($room as $room_reservation) {
            $cancellation_policies = collect(json_decode($room_reservation->cancellation_policy,true));
            $policy = $cancellation_policies->where('days', '>=', $date_diff)->sortBy('days')->first();
        }
        $payouts = Payout::where('reservation_id',$id)->get();
        $this->view_data['host_payout'] = $payouts->where('user_type','Host')->first();        
        
        $this->view_data['pricing_details'] = $reservation->getPricingForm("Host");
        $this->view_data['type'] = $request->type;
        return view('host.reservations.details',$this->view_data,compact('cancellation_policy','date_diff'));
    }


    /**
     * Display Penalties Detailed View
     *
     * @param  \App\Models\UserPenalty $user_penalty
     * @return \Illuminate\Http\Response
     */
    public function userPenalty(UserPenalty $user_penalty)
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_penalties');
        $this->view_data['sub_title'] = Lang::get('admin_messages.penalty_details');
        $this->view_data['user_penalty'] = $user_penalty;
        $this->view_data['user'] = $user_penalty->user;
        $this->view_data['reservations'] = Reservation::where('host_id',$user_penalty->user_id)->where('host_penalty','>',0)->get();

        return view('host.reservations.penalties', $this->view_data);
    }

    public function report($id)
    {
/*       $mail_data['subject'] = 'zczx';
       $mail_data['result'] = $reservation = Reservation::userBased()->with('hotel.hotel_address','user','host_user','room_reservations')->findOrFail($id);
       $mail_data['user'] = $user = $reservation->user;
       $mail_data['host_user'] = $reservation->host_user;
       $payouts = Payout::where('reservation_id',$id)->get();
       $mail_data['host_payout'] = $payouts->where('user_type','Host')->first();        
       
       $mail_data['pricing_details'] = $reservation->getPricingForm("Admin");

       return view('host.reservations.reports',$mail_data);*/

       resolveAndSendNotification('generateReport',$id);

       return redirect()->route('host.reservations.show',['id' => $id]);
    }

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
        
        resolveAndSendNotification("bookingCancelled",$reservation->id,$user_type);
        
        flashMessage('success', Lang::get('messages.success'), Lang::get('messages.booking_cancelled_successfully'));
        return redirect()->route('host.reservations.show',['id'=>$reservation->id]);
    }
}