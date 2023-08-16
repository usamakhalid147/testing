<?php

/**
 * Reservation Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    ReservationController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\ReservationsDataTable;
use App\DataTables\PenaltiesDataTable;
use App\Models\Reservation;
use App\Models\Message;
use App\Models\MessageConversation;
use App\Models\Payout;
use App\Models\UserPenalty;
use Lang;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReservationsDataTable $dataTable, $type = 'all')
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.reservations');
        $this->view_data['type'] = $type;
        $this->view_data['filters_array'] = [
            'all' => Lang::get('admin_messages.all'),
            'current' => Lang::get('admin_messages.current'),
            'upcoming' => Lang::get('admin_messages.upcoming'),
            'cancelled' => Lang::get('admin_messages.cancelled'),
            'completed' => Lang::get('admin_messages.completed'),
        ];
        return $dataTable->setType($type)->render('admin.reservations.view',$this->view_data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function penaltyIndex(PenaltiesDataTable $dataTable, $type='current')
    {
        $this->view_data['active_menu'] = 'penalties';
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_penalties');
        $this->view_data['sub_title'] = Lang::get('admin_messages.penalties');
        $this->view_data['type'] = $type;
        $this->view_data['filters_array'] = [
            'all' => Lang::get('admin_messages.all'),
            'current' => Lang::get('admin_messages.current'),
            'upcoming' => Lang::get('admin_messages.upcoming'),
            'cancelled' => Lang::get('admin_messages.cancelled'),
            'completed' => Lang::get('admin_messages.completed'),
        ];
        return $dataTable->render('admin.reservations.view',$this->view_data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.booking_details');
        $this->view_data['result'] = $reservation = Reservation::with('hotel.hotel_address','user','host_user')->findOrFail($id);
        $this->view_data['room_reservations'] = $room_reservations = $reservation->room_reservations;
        $cancellation_policy = [];
        foreach($room_reservations as $room_reservation){
            $cancellation_policy[] = [
                'room_name' => $room_reservation->hotel_room->name,
                'policies' => collect(json_decode($room_reservation->cancellation_policy,true))
            ];
        }
        $payouts = Payout::where('reservation_id',$id)->get();
        $this->view_data['guest_refund'] = $payouts->where('user_type','Guest')->first();
        $this->view_data['host_payout'] = $payouts->where('user_type','Host')->first();        
        
        $this->view_data['pricing_details'] = $reservation->getPricingForm("Admin");
        return view('admin.reservations.details',$this->view_data,compact('cancellation_policy'));
    }

    /**
     * Display Conversation View
     *
     * @param  \App\Models\Reservation $message
     * @return \Illuminate\Http\Response
     */
    public function conversation(Reservation $reservation)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.chat_details');
        $this->view_data['result'] = $reservation;
        $message = Message::where('reservation_id',$reservation->id)->first();
        $conversations = MessageConversation::where('message_id',$message->id)->orderByDesc('id')->get();
        $this->view_data['host_id'] = $reservation->host_id;
        $this->view_data['messages'] = $this->mapConversationData($conversations);

        return view('admin.reservations.conversation', $this->view_data);
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

        return view('admin.reservations.penalties', $this->view_data);
    }

    /**
     * Get Formatted Message Conversation data
     *
     * @param  \App\Models\Message $messages
     * @param  String $user_type
     * @return Illuminate\Support\Collection $messages
     */
    public function mapConversationData($conversations)
    {
         return $conversations->map(function($message) {
            $message_data = $message->only(['id','message_id','user_from','user_to','message','message_type','read','sent_at','header_notification_text']);
            $user_data = [
                'user_name' => $message->from_user->full_name,
                'verification_status' => $message->from_user->verification_status,
                'profile_picture_src' => $message->from_user->profile_picture_src,
                'user_link' => $message->from_user->link,
            ];

            $special_offer_data = [];
            if($message->special_offer_id > 0) {
                $special_offer_data = [
                    'special_offer_id' => $message->special_offer->id,
                    'day_price' => $message->special_offer->day_price,
                    'total_days' => $message->special_offer->total_days,
                    'price' => $message->special_offer->price,
                ];
            }
            return array_merge($message_data,$user_data,$special_offer_data);
        })->values();
    }
}