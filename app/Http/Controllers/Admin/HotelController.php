<?php

/**
 * Hotel Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    HotelController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\HotelsDataTable;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Reservation;
use App\Models\HotelPhoto;
use App\Traits\ManageHotel;
use App\Traits\Translatable;
use App\Models\HotelCalendar;
use Lang;
use Validator;
use Illuminate\Support\Facades\Storage;


class HotelController extends Controller
{
	use ManageHotel, Translatable;
	
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
		$this->view_data['main_title'] = Lang::get('admin_messages.manage_hotels');
        $this->view_data['sub_title'] = Lang::get('admin_messages.hotels');
        $this->view_data['active_menu'] = 'hotels';
        $this->base_path = 'admin.hotels';
	}

	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(HotelsDataTable $dataTable)
	{
		return $dataTable->render($this->base_path.'.view',$this->view_data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.add_hotel');
		$this->view_data['hotel'] = new Hotel;
		$this->view_data['users'] = User::where('user_type','host')->get()->pluck('first_name','id');
		
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
			'user_id' => 'required|exists:users,id',
			'star_rating'   => 'required',
			'name'			=> 'required',
			'description'	=> 'required',
		);
		$attributes = array(
			'user_id' => Lang::get('admin_messages.host'),
		);
		$this->validate($request,$rules,[],$attributes);
		$user = User::where('id',$request->user_id)->first();
		$hotel = new Hotel;
		$hotel->admin_commission = global_settings('hotel_admin_commission');
        $hotel->user_id = $request->user_id;
        $hotel->star_rating = $request->star_rating;
        $hotel->name  = $request->name;
        $hotel->description = $request->description;
        $hotel->currency_code = $user->user_currency != null ? $user->user_currency : global_settings('default_currency');
        $hotel->save();

		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_added'));
		$redirect_url = route('admin.hotels.edit',['id' => $hotel->id]);
        return redirect($redirect_url);
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit(Request $request ,$id)
	{
		$this->view_data['sub_title'] = Lang::get('admin_messages.edit_hotel');
		$hotel = Hotel::loadRelations()->findOrFail($id);
		$hotel_service = resolve("App\Services\HotelService");
		$step_data = $hotel_service->getHotelSteps($hotel);

		$this->view_data['translations'] = $this->getTranslationsExceptDefault($hotel);
		$this->view_data['step_data'] = $step_data;
		$this->view_data['status_array'] = [
			'In Progress' => Lang::get('admin_messages.in_progress'),
			'Pending' => Lang::get('admin_messages.pending'),
			'Listed' => Lang::get('admin_messages.listed'),
			'Unlisted' => Lang::get('admin_messages.unlisted'),
		];
		$this->view_data['admin_status_array'] = [
			'Pending' => Lang::get('admin_messages.pending'),
			'Approved' => Lang::get('admin_messages.approved'),
			'Resubmit' => Lang::get('admin_messages.resubmit'),
		];

		$this->view_data['checkin_times_array'] = [
			"flexible" => Lang::get('messages.flexible'),
		];
		$this->view_data['checkout_times_array'] = [
			"flexible" => Lang::get('messages.flexible'),
		];

		$this->view_data['current_step'] = $step_data->where('step',$request->current_tab)->first();
		$this->view_data['prev_amenities'] = explode(',', $hotel->amenities);
		$this->view_data['prev_house_rules'] = explode(',', $hotel->house_rules);
		$this->view_data['prev_guest_accesses'] = explode(',', $hotel->guest_accesses);

		$times_array = generateTimeRange('0:00', '24:00', '1 hour');
		$this->view_data['checkin_times_array'] = array_merge(["flexible" => Lang::get('messages.flexible')],$times_array);
		$this->view_data['checkout_times_array'] = array_merge(["flexible" => Lang::get('messages.flexible')],$times_array);

		$management_data = array_merge($this->commonManagementData(), $this->hotelManagementData($hotel));
		$this->view_data = array_merge($this->view_data,$management_data);

		return view($this->base_path.'.edit', $this->view_data);
	}

	/**
	 * Manage Hotel Rooms
	 * 
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 */
	public function manageRooms(Request $request)
	{
		$this->view_data['hotel'] = $hotel = Hotel::loadRelations()->findOrFail($request->id);
		$management_data = array_merge($this->commonManagementData(), $this->hotelManagementData($hotel));
		$this->view_data = array_merge($this->view_data,$management_data);
		
		return view($this->base_path.'.rooms', $this->view_data);
	}


	/**
	* Update the specified resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function update(Request $request, $id)
	{
		$validate = $this->validateRequest($request, $id);

		if($validate->fails()){
			return response()->json([
				'error' => true,
				'status' => 'Failed',
				'error_messages' => $validate->messages(),
				'status_message' => Lang::get('messages.please_fill_all_mandatory_field')
			]);
		}
		try {
			if($request->step == 'description') {
				$description_data = $request->only(['name','description','your_space','interaction_with_guests','your_neighborhood','getting_around','other_things_to_note','star_rating','property_type','tele_phone_number','email','extension_number','fax_number','website','email']);
				$this->saveHotelData($id,$description_data);

				$this->saveTranslationData($id,$request->translations ?? []);
				$this->deleteTranslationData($id,$request->removed_translations ?? '');
				if($request->hasFile('logo')) {
					$hotel = Hotel::findOrFail($id);
					$hotel->deleteImageFile();

					$upload_result = $this->uploadImage($request->file('logo'),$hotel->getUploadPath());
					if(!$upload_result['status']) {
						flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.failed_to_upload_image'));
						return redirect()->route('admin.hotels');
					}

					$hotel->logo = $upload_result['file_name'];
					$hotel->upload_driver = $upload_result['upload_driver'];
					$hotel->save();
				}
			}
			else if($request->step == 'location') {
				$location_data = $request->only(['address_line_1','address_line_2','city','state','country_code','postal_code','latitude','longitude']);
				$this->saveLocationData($id,$location_data);
			}
			else if($request->step == 'photos') {
				$photos_list = $request->photos ?? [];
				$total_photo_count = count($photos_list);
				if ($total_photo_count > 10) {
					$photo_error = [
						'photos' => "you can upload only 10 Images",
					];
					return response()->json([
						'error' => true,
						'status' => 'Failed',
						'error_messages' => $photo_error,
						'status_message' => Lang::get('messages.please_fill_all_mandatory_field')
					]);
				}
				$removed_photos = explode(',',$request->removed_photos);
				$this->deleteHotelPhotos($removed_photos);
				
				if(isset($photos_list) && count($photos_list) > 0) {
	                $result = $this->updateHotelPhotos($id,$photos_list);
	                if (count($result) > 0) {
	                	$hotel = Hotel::loadRelations()->findOrFail($id);
	                	$step_data = $hotel_service->getHotelSteps($hotel);
	                	$hotel->append('completed_percent');
	                	return response()->json([
	                		'error' => true,
	                		'status' => 'Failed',
	                		'error_messages' => $result,
	                		'data' => $hotel,
	                		'step_data' => $step_data,
	                		'status_message' => Lang::get('messages.please_fill_all_mandatory_field')
	                	]);
	                }
	            }
			}
			else if($request->step == 'more_details') {
				$more_details_data = $request->only(['amenities','guest_accesses']);
				$this->saveHotelData($id,$more_details_data);
			}
			else if($request->step == 'booking') {
				$booking_data = $request->only(['notice_days','max_los','min_los','booking_type','hotel_policy','checkin_time','checkout_time','hotel_rules','guidance']);
				$this->saveHotelData($id,$booking_data);
			}
			else if($request->step == 'tax') {
				$tax_data = $request->only(['service_charge','service_charge_type','property_tax','property_tax_type']);
				$this->saveHotelData($id,$tax_data);
			}
			else if($request->step == 'contacts') {
				$data = $request->only(['contact_email','cancel_email']);
				$this->saveHotelData($id,$data);
			}
			else if($request->step == 'hotel_status') {
				$hotel = Hotel::loadRelations()->findOrFail($id);
	        	if($hotel->completed_percent < 100) {
			        return response()->json([
			            'status' => false,
			            'status_message' => Lang::get('messages.please_complete_all_steps_to_continue'),
			        ]);
	        	} else {
					$status_data = $request->only(['status','admin_status','admin_commission']);
					$this->saveHotelData($id,$status_data);
				}
			}

			$hotel_service = resolve("App\Services\HotelService");

			$hotel = Hotel::loadRelations()->findOrFail($id);
        	$step_data = $hotel_service->getHotelSteps($hotel);
        	$hotel->append('completed_percent');

			return response()->json([
				'status' => Lang::get('messages.success'),
				'status_message' => $status_message ?? Lang::get('admin_messages.successfully_updated'),
				'hotel' => $hotel,
	            'step_data' => $step_data,
			]);
		}
		catch(\Exception $e) {
			return response()->json([
				'error' => true,
				'status' => 'Failed',
				'status_message' => $e->getMessage()
			]);
		}
	}

	/**
	 * Upload Given Image to Server
	 *
	 * @return Array Upload Result
	 */
	protected function uploadImage($image,$target_dir)
	{
		$image_handler = resolve('App\Contracts\ImageHandleInterface');
		$image_data = array();
		$image_data['name_prefix'] = 'hotel_';
		$image_data['add_time'] = true;
		$image_data['target_dir'] = $target_dir;

		return $image_handler->upload($image,$image_data);
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
            HotelPhoto::where('id',$image_id)->update(['order_id' => $order_id++]);
        }

        $hotel_photos = HotelPhoto::where('hotel_id',$request->hotel_id)->Ordered()->get();

        $return_data = array(
            'status' => Lang::get('messages.success'),
			'status_message' => Lang::get('admin_messages.successfully_updated'),
            'hotel_photos' => $hotel_photos,
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
			$result = $this->deleteHotel($id);
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
		$redirect_url = route('admin.hotels');
        return redirect($redirect_url);
	}

	/**
	* Update Hotel Related Options
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function updateHotelOptions(Request $request)
	{
		$hotel = Hotel::findOrFail($request->hotel_id);
		if($request->type == 'status') {
			$hotel->admin_status = $request->status;
			if($request->status == 'Approved' && $hotel->status == 'Pending') {
				$hotel->status = 'Listed';
			}
			$hotel->save();
		}

		if($request->type == 'recommend') {
			if($hotel->status == 'Listed' && $hotel->admin_status == 'Approved') {
				$is_recommended = ($hotel->is_recommended) ? 0 : 1;
				$hotel->is_recommended = $is_recommended;
				$hotel->save();
			}
			else {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.unable_to_make_recommended'));
				return response()->json(['status' => false,'status_message' => Lang::get('admin_messages.failed')]);
			}
		}
		if($request->type == 'top_picks') {
			if($hotel->status == 'Listed' && $hotel->admin_status == 'Approved') {
				$is_top_picks = ($hotel->is_top_picks) ? 0 : 1;
				$hotel->is_top_picks = $is_top_picks;
				$hotel->save();
			}
			else {
				flashMessage('danger',Lang::get('admin_messages.failed'),Lang::get('admin_messages.unable_to_make_top_picks'));
				return response()->json(['status' => false,'status_message' => Lang::get('admin_messages.failed')]);
			}
		}
		flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_updated'));
		return response()->json(['status' => true,'status_message' => Lang::get('admin_messages.successfully_updated')]);
	}

	/**
	* Check the specified resource Can be deleted or not.
	*
	* @param  int  $id
	* @return Array
	*/
	protected function canDestroy($id)
	{
		$reservation_count = Reservation::where('hotel_id',$id)->count();
		if($reservation_count > 0) {
			return ['status' => false,'status_message' => Lang::get('admin_messages.this_hotel_has_some_reservation')];
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
		$hotel_service = resolve("App\Services\HotelService");
		$validation_data = $hotel_service->getValidationRule($request_data->step);
		
		$locales = array_keys($request_data->translations ?? []);
		$translation_rules = $this->getTranslationValidation($locales);

		$validation_data['rules'] = array_merge($validation_data['rules'],$translation_rules['rules']);
		$validation_data['attributes'] = array_merge($validation_data['attributes'],$translation_rules['attributes']);

		return Validator::make($request_data->all(),$validation_data['rules'],$validation_data['messages'],$validation_data['attributes']);
	}
	public function delete_hotel_propety_logo($id)
	{
		$hotel = Hotel::find($id);
		if ($hotel->logo) {
			$result['status']=Storage::delete(public_path($hotel->logo));
			if($result['status']) {
				flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
			}
			else {
				flashMessage('danger',Lang::get('admin_messages.failed'),$result['status_message']);
			}
			$hotel->logo = '';
			$hotel->save();
		}
	}
}