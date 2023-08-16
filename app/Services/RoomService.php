<?php

/**
 * Manage Room steps data
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    RoomService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services;

use App\Models\HotelRoom;
use Lang;

class RoomService
{
    private $room_details_req_steps = array('name','description','bed_type','beds',);
    private $more_details_req_steps = array();
    private $photos_req_steps = array('name');
    private $price_details_req_steps = array('price','number','adults','max_adults');
    private $other_price_details_req_steps = array();
    private $promotions_req_steps = array();
    private $payment_method_req_steps = array('payment_method');
    private $room_status_req_steps = array('status');

    protected function getRoomStepData($room)
    {
        $room_details_completed = $this->checkStepCompleted($room,'room_details');
        $more_details_completed = $this->checkStepCompleted($room,'more_details');
        $photos_completed = $this->checkStepCompleted($room->hotel_room_photos,'photos');
        $price_details_completed = $this->checkStepCompleted($room->hotel_room_price,'price_details');
        $room_payment_method_completed = $this->checkStepCompleted($room,'payment_method');
        $room_status_completed = $this->checkStepCompleted($room,'room_status');

        $steps = collect([
            array(
                'step'      => 'room_details',
                'name'      => Lang::get('admin_messages.room_details'),
                'description' => Lang::get('messages.describe_your_place_to_guests'),
                'mandatory' => $this->room_details_req_steps,
                'completed' => $room_details_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'more_details',
                'name'      => Lang::get('messages.room_more_details'),
                'description' => Lang::get('messages.how_guest_will_book_with_you'),
                'mandatory' => $this->more_details_req_steps,
                'completed' => $more_details_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'photos',
                'name'      => Lang::get('messages.photos'),
                'description' => Lang::get('messages.liven_up_with_photos'),
                'mandatory' => $this->photos_req_steps,
                'completed' => $photos_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'price_details',
                'name'      => Lang::get('admin_messages.room_price_details'),
                'description' => Lang::get('messages.liven_up_with_photos'),
                'mandatory' => $this->price_details_req_steps,
                'completed' => $price_details_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'other_prices',
                'name'      => Lang::get('admin_messages.other_prices'),
                'description' => Lang::get('messages.liven_up_with_photos'),
                'mandatory' => $this->other_price_details_req_steps,
                'completed' => true,
                'is_locked' => false,
            ),
            array(
                'step'      => 'promotions',
                'name'      => Lang::get('admin_messages.promotions'),
                'description' => Lang::get('messages.liven_up_with_photos'),
                'mandatory' => $this->promotions_req_steps,
                'completed' => true,
                'is_locked' => false,
            ),
            array(
                'step'      => 'payment_method',
                'name'      => Lang::get('messages.payment_methods'),
                'description' => Lang::get('messages.your_room_contacts'),
                'mandatory' => $this->payment_method_req_steps,
                'completed' => $room_payment_method_completed,
                'is_locked' => false,
            ),
            // array(
            //     'step'      => 'room_status',
            //     'name'      => Lang::get('admin_messages.room_status'),
            //     'description' => Lang::get('messages.liven_up_with_photos'),
            //     'mandatory' => $this->room_status_req_steps,
            //     'completed' => $room_status_completed,
            //     'is_locked' => false,
            // ),
        ]);
        if($room->status == 'Listed') {
            $steps->push([
                "step" => "calendar",
                "name"  => Lang::get("messages.calendar"),
                "description" => Lang::get("messages.calendar_desc"),
                "mandatory" => [],
                "completed" => true,
                "is_locked" => false,
            ]);
        }
        // dd($room->completed_percent);

        return $steps;
    }

    protected function checkStepCompleted($model,$type)
    {
        if($type == 'photos') {
            return $model->count() > 0;
        }
        $req_step = $type.'_req_steps';

        if($type == 'price_details') {
            foreach ($this->$req_step as $step) {
                if(in_array($step,['number','adults','max_adults','children','max_children'])) {
                    if(empty($model->hotel_room->$step)) {
                        return false;
                    }
                }
                else if(empty($model->$step)) {
                    return false;
                }
            }
            return true;
        }

        foreach ($this->$req_step as $step) {
            if(in_array($step, ['dedicated_space','bedrooms','bathrooms','notice_days'])) {
                if($model->$step < 0) {
                    return false;
                }
            }
            else if($model->$step == '') {
                return false;
            }
        }
        return true;
    }

    public function getRoomSteps($hotel)
    {
        $steps = $this->getRoomStepData($hotel);
        $data = $steps->map(function($step_data, $key) use($hotel) {
            $step_data['step_num'] = $key;
            $step_data['step_text'] = Lang::get('messages.edit');
            // $step_data['url'] = resolveRoute('manage_hotel',['id' => $hotel->id,'current_tab' => $step_data['step']]);
            return $step_data;
        });

        return $data;
    }

    public function getValidationRule($type)
    {
        $rules = [];
        $messages = [];
        $attributes = [];
        $req_step = $type.'_req_steps';

        foreach ($this->$req_step ?? [] as $step) {
            $rules[$step] = 'required';
            $attributes[$step] = Lang::get('admin_messages.'.$step);
        }
        if($type == 'room_details') {
            $rules['beds'] = 'required|numeric|not_in:0|gt:0';
            foreach (request()->cancellation_policies ?? [] as $key => $cancellation) {
                $rules['cancellation_policies.'.$key.'.days'] = 'required|numeric|gt:0|';
                $attributes['cancellation_policies.'.$key.'.days'] = Lang::get('messages.days');
                $rules['cancellation_policies.'.$key.'.percentage'] = 'required|numeric|gt:0|max:100';
                $attributes['cancellation_policies.'.$key.'.percentage'] = Lang::get('messages.percentage');
            }

        }
        else if($type == 'location') {
            $attributes['address_line_1'] = Lang::get('messages.listing.street_address');
            $attributes['country_code'] = Lang::get('messages.listing.country');
        }
/*        else if($type == 'photos') {
            $rules = [];
            $attributes = [];
            $room = HotelRoom::with('hotel_room_photos')->where('id',request()->room_id)->first();
            $required = $room->hotel_room_photos->count() == 0 ? 'required' : 'nullable';
            foreach(request()->photos ?? [] as $key => $photo) {
                $rules['photos.'.$key] = $required.'|file|max:5120';
                $messages['photos.'.$key.'.required'] = Lang::get('admin_messages.add_atleast_one_photo');
                $messages['photos.'.$key.'.max'] = "Maximum Upload Size is 5 mb Only.";
            }
        }*/
        else if($type == 'price_details') {
            $currency_code = session('currency');
            $min_price = ceil(currencyConvert(global_settings('min_price'),global_settings('default_currency'),$currency_code));
            $max_price = ceil(currencyConvert(global_settings('max_price'),global_settings('default_currency'),$currency_code));
            $rules['price'] = 'required|numeric|not_in:0|between:'.$min_price.','.$max_price;
            $max_guests = view()->shared('max_guests');
            $rules['number'] = 'required|numeric|gt:0';
            $rules['adults'] = 'required|numeric|not_in:0|between:'.'1,'.$max_guests;
            $rules['max_adults'] = 'required|numeric|not_in:0|gte:adults|lte:'.$max_guests;

            $rules['children'] = 'required|numeric|between:'.'0,'.$max_guests;
            $rules['max_children'] = 'required|numeric|gte:children|lte:'.$max_guests;

            $attributes['adults'] = Lang::get('admin_messages.adults');
            $attributes['max_adults'] = Lang::get('admin_messages.max_adults');
            $attributes['children'] = Lang::get('admin_messages.children');
            $attributes['max_children'] = Lang::get('admin_messages.max_children');

            $rules['adult_price'] = 'nullable|numeric|gte:0';
            $rules['children_price'] = 'nullable|numeric|gte:0';
        }
        else if ($type == 'other_prices') {
            foreach (request()->meal_plans ?? [] as $key => $plan) {
                $rules['meal_plans.'.$key.'.plan'] = 'required|numeric';
                $attributes['meal_plans.'.$key.'.plan'] = Lang::get('messages.plan');
                $rules['meal_plans.'.$key.'.price'] = 'required|numeric|gt:0';
                $attributes['meal_plans.'.$key.'.price'] = Lang::get('messages.price');
            }

            foreach (request()->extra_beds ?? [] as $key => $beds) {
                $rules['extra_beds.'.$key.'.plan'] = 'required|numeric';
                $attributes['extra_beds.'.$key.'.plan'] = Lang::get('messages.plan');
                $rules['extra_beds.'.$key.'.price'] = 'required|numeric|gt:0';
                $attributes['extra_beds.'.$key.'.price'] = Lang::get('messages.price');
                $rules['extra_beds.'.$key.'.size'] = 'required';
                $attributes['extra_beds.'.$key.'.size'] = Lang::get('admin_messages.bed_size');
                $rules['extra_beds.'.$key.'.guest_type'] = 'required';
                $attributes['extra_beds.'.$key.'.guest_type'] = Lang::get('admin_messages.guest_type');
            }
        }
        else if($type == 'promotions') {
            $all_promotions = request()->promotions ?? [];
            $room = HotelRoom::with('hotel_room_price')->where('id',request()->room_id)->first();
            $room_price = $room->hotel_room_price->price;
            foreach($all_promotions as $type => $data) {
                foreach($data as $key => $result) { 
                    $rules['promotions.'.$type.'.'.$key.'.value'] = 'required|numeric|gt:0';
                    $attributes['promotions.'.$type.'.'.$key.'.value'] = Lang::get('admin_messages.discount_value');
                    if($result['value_type'] == 'fixed') {

                        $rules['promotions.'.$type.'.'.$key.'.value'] .= '|lt:'.$room_price;
                  }
                    else {
                        $rules['promotions.'.$type.'.'.$key.'.value'] .= '|between:1,80';
                    }
                    if($type == 'min_max') {
                        $rules['promotions.'.$type.'.'.$key.'.min_los'] = 'required|numeric|gt:0';
                        // $rules['promotions.'.$type.'.'.$key.'.max_los'] = 'required|numeric|gte:promotions.'.$type.'.'.$key.'.min_los';
                        $attributes['promotions.'.$type.'.'.$key.'.min_los'] = Lang::get('messages.min_los');
                        // $attributes['promotions.'.$type.'.'.$key.'.max_los'] = Lang::get('messages.max_los');
                    }
                    else if($type == 'early_bird') {
                        $rules['promotions.'.$type.'.'.$key.'.days'] = 'required|numeric';
                        $attributes['promotions.'.$type.'.'.$key.'.days'] = Lang::get('admin_messages.guest_must_book');
                    }
                    else if($type == 'day_before_checkin') {
                        $rules['promotions.'.$type.'.'.$key.'.days'] = 'required|numeric|gt:0|lte:7';
                        $attributes['promotions.'.$type.'.'.$key.'.days'] = Lang::get('admin_messages.guest_must_book'); 
                    }
                }
            }
        }

        return compact('rules','messages','attributes');
    }
}