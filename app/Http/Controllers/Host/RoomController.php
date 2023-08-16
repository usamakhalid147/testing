<?php

/**
 * Room Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    RoomController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\Host\HotelRoomsDataTable;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelRoomPrice;
use App\Models\HotelRoomCombo;
use App\Models\HotelRoomPhoto;
use App\Models\RoomReservation;
use App\Traits\ManageRoom;
use App\Traits\Translatable;
use Lang;
use Auth;
use Validator;

class RoomController extends Controller
{
    use ManageRoom, Translatable;
    
    /**
    * Constructor
    *
    */
    public function __construct()
    {
        $this->view_data['translatable_fields'] = $this->translatable_fields = collect([
            ['key' => 'name', 'title' => Lang::get('messages.hotel_name'),'type' => 'text','rules' => 'required'],
            ['key' => 'description', 'title' => Lang::get('messages.description'),'type' => 'textarea','rules' => 'required'],
        ]);
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_rooms');
        $this->view_data['sub_title'] = Lang::get('admin_messages.manage_rooms');
        $this->view_data['active_menu'] = 'rooms';
        $this->base_path = 'host.rooms';
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(HotelRoomsDataTable $dataTable,$id = 0)
    {
        $user_id = Auth::id();
        $this->view_data['hotels'] = $hotels = Hotel::authUser()->get()->pluck('name','id');
        $this->view_data['filter'] = $hotels[$id] ?? Lang::get('admin_messages.all_rooms');
        $this->view_data['id'] = $id;

        return $dataTable->setHotel($id)->render($this->base_path.'.view',$this->view_data);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create($id)
    {
        $this->view_data['sub_title'] = Lang::get('admin_messages.add_room');
        $this->view_data['result'] = new HotelRoom;
        $this->view_data['hotel'] = Hotel::findOrFail($id);
        
        $management_data = $this->commonManagementData();
        $this->view_data = array_merge($this->view_data,$management_data);

        return view($this->base_path.'.add', $this->view_data);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $rules = array(
            'hotel_id' => 'required|exists:hotels,id',
            'name' => 'required',
            'description' => 'required',
            'bed_type' => 'required',
            'beds' => 'required|numeric|gt:0',
        );
        $attributes = [
            'name' => Lang::get('admin_messages.name'),
            'description' => Lang::get('messages.description'),
            'bed_type' => Lang::get('admin_messages.bed_type'),
            'beds' => Lang::get('admin_messages.beds'),
        ];
        $this->validate($request,$rules,[],$attributes);
        $current_user = getCurrentUser();
        $room = new HotelRoom;
        $room->user_id = getHostId();
        $room->hotel_id = $request->hotel_id;
        $room->name  = $request->name;
        $room->description = $request->description;
        $room->bed_type = $request->bed_type;
        $room->beds = $request->beds;
        $room->save();

        $hotel_room_price = new HotelRoomPrice;
        $hotel_room_price->hotel_id = $room->hotel_id;
        $hotel_room_price->room_id = $room->id;
        $hotel_room_price->currency_code = session('currency');
        $hotel_room_price->save();

        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
        $redirect_url = route('host.rooms.edit',['id' => $room->id]);
        return redirect($redirect_url);
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit(Request $request,$id)
    {
        $user_id = Auth()->id();
        $this->view_data['sub_title'] = Lang::get('admin_messages.edit_room');
        $room = HotelRoom::authUser()->loadRelations()->findOrFail($id);
        $room_service = resolve("App\Services\RoomService");
        $step_data = $room_service->getRoomSteps($room);

        $this->view_data['translations'] = $this->getTranslationsExceptDefault($room);
        $this->view_data['step_data'] = $step_data;
        $this->view_data['status_array'] = [
            '' => Lang::get('admin_messages.select'),
            'In Progress' => Lang::get('admin_messages.in_progress'),
            'Pending' => Lang::get('admin_messages.pending'),
            'Listed' => Lang::get('admin_messages.listed'),
            'Unlisted' => Lang::get('admin_messages.unlisted'),
        ];

        $this->view_data['current_step'] = $this->view_data['step_data']->where('step',$request->current_tab ?? 'room_details')->first();
        $this->view_data['prev_amenities'] = explode(',', $room->amenities);

        $management_data = array_merge($this->commonManagementData(), $this->roomManagementData($room));
        $this->view_data = array_merge($this->view_data,$management_data);

        $this->view_data['day_array'] = [
            '3' => '3 '.Lang::choice('messages.days',3),
            '5' => '5 '.Lang::choice('messages.days',5),
            '7' => '7 '.Lang::choice('messages.days',7),
            '14' => '14 '.Lang::choice('messages.days',14),
            '21' => '21 '.Lang::choice('messages.days',21),
            '30' => '30 '.Lang::choice('messages.days',30),
            '45' => '45 '.Lang::choice('messages.days',45),
        ];

        return view($this->base_path.'.edit', $this->view_data);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request)
    {       
        $id = $request->room_id;
        $hotel_id = $request->hotel_id;
        $room = HotelRoom::authUser()->find($id);
        $room_service = resolve("App\Services\RoomService");
        $validate = $this->validateRequest($request, $id);
        if($validate->fails()){
            return response()->json([
                'error' => true,
                'step'  => $request->step,
                'step_data' => $room_service->getRoomSteps($room),
                'status' => 'Failed',
                'error_messages' => $validate->messages(),
                'status_message' => Lang::get('messages.please_fill_all_mandatory_field')
            ]);
        }
        try {
            if($request->step == 'room_details') {
                $room_data = $request->only(['name','description','room_type','room_size','room_size_type','beds','bed_type']);
                $this->saveRoomData($id,$room_data);
                $this->saveDescriptionData($id,$request->translations ?? []);
                $cancellation_data = $request->only(['cancellation_policies']);
                $this->saveCancellationData($hotel_id,$request->id,$cancellation_data);
            }
            else if($request->step == 'more_details') {
                $more_details_data = $request->only(['amenities']);
                $this->saveRoomData($id,$more_details_data);
            }
            else if($request->step == 'photos') {
                $photos_list = $request->photos ?? [];
                $total_photo_count = count($photos_list);
                if ($total_photo_count > 8) {
                    $photo_error = [
                        'photos' => "you can upload only 8 Images",
                    ];
                    return response()->json([
                        'error' => true,
                        'status' => 'Failed',
                        'error_messages' => $photo_error,
                        'status_message' => Lang::get('messages.please_fill_all_mandatory_field')
                    ]);
                }
                $removed_photos = explode(',',$request->removed_photos);
                $this->deleteRoomPhotos($removed_photos);
                
                if(isset($photos_list) && count($photos_list) > 0) {
                    $result = $this->updateRoomPhotos($hotel_id,$id,$photos_list);
                    if (count($result) > 0) {
                        $room = Room::loadRelations()->findOrFail($id);
                        $step_data = $room_service->getHotelSteps($room);
                        $room->append('completed_percent');
                        return response()->json([
                            'error' => true,
                            'status' => 'Failed',
                            'error_messages' => $result,
                            'data' => $room,
                            'step_data' => $step_data,
                            'status_message' => Lang::get('messages.please_fill_all_mandatory_field')
                        ]);
                    }
                }
            }
            else if($request->step == 'price_details') {
                $room_data = $request->only(['number','adults','children','max_adults','max_children']);
                $this->saveRoomData($id,$room_data);
                $price_data = $request->only(['price','adult_price','children_price']);
                $this->saveRoomPriceData($id,$price_data);
            } 
            else if($request->step == 'other_prices') {
                $price_rules_data = $request->only(['meal_plans','extra_beds']);
                $this->saveRoomPricingRulesData($hotel_id,$id,$price_rules_data);
            }
            else if($request->step == 'promotions') {
                $this->savePromotionData($hotel_id,$id, $request->promotions ?? []);
            }
            else if($request->step == 'payment_method') {
                $payment_method_data = $request->only(['payment_method']);
                $this->saveRoomData($id,$payment_method_data);
            }

            $room = HotelRoom::authUser()->find($id);
            if($room->completed_percent == 100) {
                $status_data = [
                    'status' => 'Listed',
                ];
                $this->saveRoomData($id,$status_data);
            }

            return response()->json([
                'error' => false,
                'status' => 'Success',
                'step_data' => $room_service->getRoomSteps($room),
                'status_message' => Lang::get('admin_messages.successfully_updated')
            ]);
        }
        catch(\Exception $e) {
            return response()->json([
                'error' => true,
                'status' => 'Failed',
                'step_data' => $room_service->getRoomSteps($room),
                'status_message' => $e->getMessage()
            ]);
        }
    }

    /**
    * Update Room Related Options
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function updateRoomOptions(Request $request)
    {
        $room = HotelRoom::findOrFail($request->room_id);
        $room->status = $request->status;
        $room->save();
        flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
        return response()->json(['status' => true,'status_message' => Lang::get('admin_messages.successfully_updated')]);
    }

    /**
     * update Image Order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json return_data
     */
    public function updatePhotoOrder(Request $request)
    {
        $order_id = 1;
        foreach($request->image_order_list as $image_id) {
            HotelRoomPhoto::where('id',$image_id)->update(['order_id' => $order_id++]);
        }

        $hotel_subroom_photos = HotelRoomPhoto::where('room_id',$request->room_id)->Ordered()->get();

        $return_data = array(
            'status' => true,
            'hotel_subroom_photos' => $hotel_subroom_photos,
        );
        return response()->json($return_data);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $can_destroy = $this->canDestroy($id);
        if($can_destroy['status']) {
            $result = $this->deleteRoom($id);
            if($result['status']) {
                flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
            }
            else {
                flashMessage('danger',Lang::get('admin_messages.failed'),$result['status_message']);
            }
        }
        else {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
        }
        $redirect_url = route('host.rooms');
        return redirect($redirect_url);
    }

    /**
    * Check the specified resource Can be deleted or not.
    *
    * @param  int  $id
    * @return Array
    */
    protected function canDestroy($id)
    {
        $reservation_count = RoomReservation::where('room_id',$id)->count();
        if($reservation_count > 0) {
            return ['status' => false,'status_message' => Lang::get('admin_messages.this_room_has_some_reservation')];
        }
        return ['status' => true,'status_message' => Lang::get('admin_messages.success')];
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
        $room_service = resolve("App\Services\RoomService");
        $validation_data = $room_service->getValidationRule($request_data->step);
        $locales = array_keys($request_data->translations ?? []);
        $translation_rules = $this->getTranslationValidation($locales);
        $validation_data['rules'] = array_merge($validation_data['rules'],$translation_rules['rules']);
        $validation_data['attributes'] = array_merge($validation_data['attributes'],$translation_rules['attributes']);

        $validator = Validator::make($request_data->all(),$validation_data['rules'],$validation_data['messages'],$validation_data['attributes']);

        $validator->after(function ($validator) use ($request_data) {
            $check_times = [];
            foreach($request_data->cancellation_policies ?? [] as $key => $day) {
                $days = $day['days'];
                if(in_array($days,$check_times)) {
                    $validator->errors()->add('cancellation_policies.'.$key.'.days',Lang::get('messages.duplicate_entry'));
                }
                else {
                    array_push($check_times,$days);
                }
            }
        });

        return $validator;
    }
}
