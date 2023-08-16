<?php

/**
 * Inbox Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    InboxController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\Host\InboxDataTable;
use App\Models\Message;
use App\Models\MessageConversation;
use Lang;
use Auth;

class InboxController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title']  = Lang::get('admin_messages.all_messages');
        $this->view_data['active_menu'] = 'messages';
        $this->view_data['sub_title'] = Lang::get('admin_messages.messages');
    }

    /**
     * Display a hotel of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InboxDataTable $dataTable)
    {
        $messages = Message::where('host_id',Auth::id())->update(['host_read' => 1]);
        return $dataTable->render('host.inbox.view',$this->view_data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function conversation($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.messages');
        $user_id = Auth::id();
        $this->view_data['result'] = $result = Message::userBased('Host')->with('conversations')->findOrFail($id);

        $this->view_data['conversations'] = $this->mapConversationData($result->conversations);
        $this->view_data['user_type'] = $user_type = 'Host';
        $this->view_data['list'] = $result->list();
        $this->view_data['list_type'] = $result->list_type;
        $this->view_data['reservation'] = $reservation = $result->reservation();
        $this->view_data['list_location'] = $this->view_data['list']->list_address();
        $this->view_data['user_details'] = $result->user;
        $this->view_data['pricing_data'] = $this->view_data['reservation']->getPricingForm($user_type);
        $this->view_data['auth_user'] = Auth::user()->append('profile_picture_src');


        return view('host.inbox.conversation', $this->view_data);
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
        return $conversations->sortByDesc('id')->map(function($message) {
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

    /**
     * Send message to other users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json $message_data
     */
    public function sendMessage(Request $request)
    {
        $user_column = ($request->user_type == 'Host') ? 'host_id' : 'user_id';

        $message = Message::where($user_column,Auth::id())->find($request->message_id);
        
        if($message == '' || $request->message == '') {
            return response()->json([
                'status' => false,
                'status_message' => Lang::get('messages.invalid_request'),
            ]);
        }

        $user_to = ($request->user_type == 'Host') ? $message->user_id : $message->host_id;

        $user_message = removeEmailNumber($request->message);
        $message_type = getMessageType('booking_discuss');

        $message_column = strtolower($request->user_type).'_message';
        $message->$message_column = $user_message;
        $read_column = $request->user_type == 'Host' ? 'guest_read' : 'host_read';
        $message->$read_column = 0;
        $message->save();
        
        $message_conversation = new MessageConversation;
        $message_conversation->message_id = $message->id;
        $message_conversation->user_from = Auth::id();
        $message_conversation->user_to = $user_to;
        $message_conversation->message = $user_message;
        $message_conversation->message_type = $message_type;
        $message_conversation->save();

        resolveAndSendNotification("userConversation",$message_conversation->id);
        
        $message_data = $this->mapConversationData(collect([$message_conversation]));

        if(checkEnabled('Firebase')) {
            $data = [
                'title' => Lang::get('messages.new_message_received_from_user',['replace_key_1' => global_settings('site_name'),'replace_key_2' => $message_conversation->from_user->first_name]),
                'message' => $message_data->first(),
                'inbox_count' => $message_conversation->to_user->inbox_count,
            ];
            
            $firbase_service = resolve("App\Services\FirebaseService");
            $firbase_service->insertReference("users/".$user_to."/messages/", $data);
            
            $notify_data = [
                'title' => $data['title'],
                'message' => truncateString($message_conversation->message,30),
                'data' => [
                    'message_id' => $message->id,
                ],
            ];
            sendNotificationToUser($message_conversation->to_user,$notify_data);
        }
        
        return response()->json([
            'status' => true,
            'data' => $message_data->first(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $validate_return = $this->validateRequest($request);

        $help = Review::findOrFail($id);
        $help->public_comment = $request->public_comment;
        $help->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        $redirect_url = route('host.reviews');
        return redirect($redirect_url);
    }

    /**
     * Check the specified resource Can be deleted or not.
     *
     * @param  Illuminate\Http\Request $request_data
     * @param  Int $id
     * @return Array
     */
    protected function validateRequest($request_data, $id = '')
    {
        $rules = array(
            'public_comment'    => 'required',
        );

        $attributes = array(
            'public_comment'  => Lang::get('admin_messages.public_comment'),
        );

        $this->validate($request_data,$rules,[],$attributes);
    }
}
