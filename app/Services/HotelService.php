<?php

/**
 * Manage Hotel steps data
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    HotelService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services;

use App\Models\Hotel;
use Lang;

class HotelService
{
    private $description_req_steps = array('name','description','star_rating','property_type','tele_phone_number','email');
    private $location_req_steps = array('address_line_1','city','postal_code','country_code');
    private $photos_req_steps = array('name');
    private $booking_req_steps = array('min_los',);
    private $more_details_req_steps = array('amenities');
    private $price_view_req_steps = array();
    private $availabiltity_req_steps = array();
    private $tax_req_steps = array();
    private $contacts_req_steps = array('contact_email','cancel_email');
    private $hotel_status_req_steps = array('status','admin_status','admin_commission');

    protected function getHotelStepData($hotel)
    {
        $description_completed = $this->checkStepCompleted($hotel,'description');
        $location_completed = $this->checkStepCompleted($hotel->hotel_address,'location');
        $photos_completed = $this->checkStepCompleted($hotel->hotel_photos,'photos');
        $booking_completed = $this->checkStepCompleted($hotel,'booking');
        $more_details_completed = $this->checkStepCompleted($hotel,'more_details');
        $availability_completed = $this->checkStepCompleted($hotel,'availability');
        $tax_completed = $this->checkStepCompleted($hotel,'tax');
        $contacts_completed = $this->checkStepCompleted($hotel,'contacts');
        $hotel_status_completed = $this->checkStepCompleted($hotel,'hotel_status');

        $steps = collect([
            array(
                'step'      => 'description',
                'name'      => Lang::get('messages.describe_your_hotel'),
                'description' => Lang::get('messages.describe_your_place_to_guests'),
                'mandatory' => $this->description_req_steps,
                'completed' => $description_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'location',
                'name'      => Lang::get('messages.location'),
                'description' => Lang::get('messages.where_your_place_location'),
                'mandatory' => $this->location_req_steps,
                'completed' => $location_completed,
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
                'step'      => 'more_details',
                'name'      => Lang::get('messages.more_details'),
                'description' => Lang::get('messages.how_guest_will_book_with_you'),
                'mandatory' => $this->more_details_req_steps,
                'completed' => $more_details_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'booking',
                'name'      => Lang::get('messages.booking_settings'),
                'description' => Lang::get('messages.how_guest_will_book_with_you'),
                'mandatory' => $this->booking_req_steps,
                'completed' => $booking_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'tax',
                'name'      => Lang::get('messages.tax'),
                'description' => Lang::get('messages.tax'),
                'mandatory' => $this->tax_req_steps,
                'completed' => $tax_completed,
                'is_locked' => false,
            ),
            array(
                'step'      => 'contacts',
                'name'      => Lang::get('messages.contacts'),
                'description' => Lang::get('messages.your_hotel_contacts'),
                'mandatory' => $this->contacts_req_steps,
                'completed' => $contacts_completed,
                'is_locked' => false,
            ),
        ]);

        if(isAdmin()) {
            $steps->push([
                "step" => "hotel_status",
                "name" => Lang::get("admin_messages.hotel_status"),
                "mandatory" => $this->hotel_status_req_steps,
                "completed" => $hotel_status_completed,
                "is_locked" => false,
            ]);
        }

        return $steps;
    }

    protected function checkStepCompleted($model,$type)
    {
        if($type == 'photos') {
            return $model->count() > 0;
        }
        if($type == 'availability') {
            return true;
        }

        $req_step = $type.'_req_steps';

        foreach ($this->$req_step as $step) {
            if(optional($model)->$step == '') {
                return false;
            }
        }

        return true;
    }

    public function getHotelSteps($hotel)
    {
        $steps = $this->getHotelStepData($hotel);
        $data = $steps->map(function($step_data, $key) use($hotel) {
            $step_data['step_num'] = $key;
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
        
        foreach ($this->$req_step as $step) {
            $rules[$step] = 'required';
            $attributes[$step] = Lang::get('messages.'.$step);
        }
        if ($type == 'description') {
            $rules['logo'] = 'nullable|file|max:5120';
            $messages['logo.max'] = "Maximum Upload Size is 5 mb Only.";
            $rules['email'] = 'required|email';
        }
        if($type == 'location') {
            $attributes['address_line_1'] = Lang::get('messages.street_address');
            $attributes['country_code'] = Lang::get('messages.country');
        }
/*        if($type == 'photos') {
            $rules = [];
            $attributes = [];
            $hotel = Hotel::with('hotel_photos')->where('id',request()->hotel_id)->first();
            $required = $hotel->hotel_photos->count() == 0 ? 'required' : 'nullable';
            foreach(request()->photos ?? [] as $key => $photo) {
                $rules['photos.'.$key] = $required.'|file|max:5120';
                $messages['photos.'.$key.'.required'] = Lang::get('admin_messages.add_atleast_one_photo');
                $messages['photos.'.$key.'.max'] = "Maximum Upload Size is 5 mb Only.";
            }
        }*/
        if($type == 'booking') {
            $rules['notice_days'] = 'nullable|numeric';
            $rules['min_los'] = 'required|numeric|gte:1';
            $rules['max_los'] = 'nullable|numeric|gte:min_los';

            $attributes['notice_days'] = Lang::get('messages.notice_days');
            $attributes['max_los'] = Lang::get('messages.max_los');
        }
        if($type == 'tax') {
            $currency_code = session('currency');
            $min_price = ceil(currencyConvert(global_settings('min_price'),global_settings('default_currency'),$currency_code));
            $max_price = ceil(currencyConvert(global_settings('max_price'),global_settings('default_currency'),$currency_code));
            $rules['service_charge'] = 'nullable|numeric|not_in:0';
            $rules['property_tax'] = 'nullable|numeric|not_in:0';

            $attributes['service_charge'] = Lang::get('messages.service_charge');
            $attributes['property_tax'] = Lang::get('messages.property_tax');
        }
        if($type == 'contacts') {
            $rules['contact_email'] = 'required|email';
            $rules['cancel_email'] = 'required|email';
        }
        if($type == 'hotel_status') {
            $rules = [];
            $attributes = [];
        }

        return compact('rules','messages','attributes');
    }
}