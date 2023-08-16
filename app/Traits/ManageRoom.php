<?php

/**
 * Trait for Manage Hotel
 *
 * @package     HyraHotel
 * @subpackage  Traits
 * @category    ManageHotel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Traits;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelAddress;
use App\Models\HotelPhoto;
use App\Models\HotelRoomPrice;
use App\Models\HotelRoomPhoto;
use App\Models\HotelCalendar;
use App\Models\HotelRoomPromotion;
use App\Models\HotelRoomCancellationPolicy;
use App\Models\RoomType;
use App\Models\AmenityType;
use App\Models\Amenity;
use App\Models\HotelRule;
use App\Models\GuestAccess;
use App\Models\PropertyType;
use App\Models\Country;
use App\Models\BedType;

trait ManageRoom
{
    /**
     * Get All Common Management data related to Manage Room
     *
     * @return Array
     */
    protected function commonManagementData()
    {
        $room_types = RoomType::activeOnly()->get();
        $property_types = PropertyType::activeOnly()->get();
        $amenity_types = AmenityType::activeOnly()->whereHas('amenities',function($query) {
            $query->where('list_type','room');
        })
        ->with(['amenities' => function($query) {
            return $query->where('list_type','room');
        }])->get();
        $bed_types = BedType::activeOnly()->get();

        $meal_plan_options = resolve('MealPlan')->activeOnly();

        return compact('room_types','property_types','amenity_types','bed_types','meal_plan_options');
    }

    /**
     * Get All Management data related to Given Room
     *
     * @return Array
     */
    protected function roomManagementData($room)
    {
        $selected_amenities = Amenity::whereIn('id', explode(',',$room->amenities))->get();
        $selected_amenity_types = AmenityType::activeOnly()->whereHas('amenities',function($query) use($room) {
            return $query->where('list_type','room')->whereIn('id', explode(',',$room->amenities));
        })
        ->with(['amenities' => function($query) use($room) {
            return $query->where('list_type','room')->whereIn('id', explode(',',$room->amenities));
        }])->get();

        $selected_payment_methods = collect(PAYMENT_METHODS)->where(function($payment_method) use($room) {
            return in_array($payment_method['key'],explode(',',$room->payment_method));
        });
        
        return compact('room','selected_amenities','selected_amenity_types','selected_payment_methods');
    }

    /**
     * Save Room Data
     *
     * @param String $room_id
     * @param Array $room_data
     * @return Void
     */
    protected function saveRoomData($room_id, $room_data = array())
    {
        $room = HotelRoom::find($room_id);
        foreach($room_data as $key => $value) {
            $room->$key = removeEmailNumber($value);
        }
        $room->save();
    }

    /**
     * Save Room Price Data
     *
     * @param String $room_id
     * @param Array $price_data
     * @return Void
     */
    protected function saveRoomPriceData($room_id, $price_data)
    {
        $room_price = HotelRoomPrice::where('room_id',$room_id)->first();
        foreach($price_data as $key => $value) {
            $room_price->$key = $value;
        }
        $room_price->save();
    }

    /**
     * Save Room Data
     *
     * @param String $room_id
     * @param Array $room_data
     * @return Void
     */
    protected function saveRoomPricingRulesData($hotel_id,$room_id, $room_price_rules_data = array())
    {
        $room_price = HotelRoomPrice::where('room_id',$room_id)->first();

        $updated_rules = [];
        foreach ($room_price_rules_data['meal_plans'] ?? [] as $key => $meal_plan) {
            $room_price_rule = \App\Models\HotelRoomPriceRule::firstOrNew(['id' => $meal_plan['id'],'room_id' => $room_id,'hotel_id' => $hotel_id]);
            $room_price_rule->type = 'meal';
            $room_price_rule->type_id = $meal_plan['plan'];
            $room_price_rule->currency_code = $room_price->getRawOriginal('currency_code');
            $room_price_rule->price = $meal_plan['price'];
            $room_price_rule->save();

            $updated_rules[] = $room_price_rule->id;
        }
        
        // Delete Other Rules
        \App\Models\HotelRoomPriceRule::where('room_id',$room_id)->where('hotel_id',$hotel_id)->where('type','meal')->whereNotIn('id',$updated_rules)->delete();

        $updated_rules = [];
        foreach ($room_price_rules_data['extra_beds'] ?? [] as $key => $bed) {
            $room_price_rule = \App\Models\HotelRoomPriceRule::firstOrNew(['id' => $bed['id'],'room_id' => $room_id,'hotel_id' => $hotel_id]);
            $room_price_rule->type = 'bed';
            $room_price_rule->type_id = $bed['plan'];
            $room_price_rule->currency_code = $room_price->getRawOriginal('currency_code');
            $room_price_rule->price = $bed['price'];
            $room_price_rule->size = $bed['size'];
            $room_price_rule->guest_type = $bed['guest_type'];
            $room_price_rule->save();

            $updated_rules[] = $room_price_rule->id;
        }
        
        // Delete Other Rules
        \App\Models\HotelRoomPriceRule::where('room_id',$room_id)->where('hotel_id',$hotel_id)->where('type','bed')->whereNotIn('id',$updated_rules)->delete();
    }

    /**
     * Save Room Promotion data
     * 
     */
    protected function savePromotionData($hotel_id, $room_id, $all_promotions) 
    {
        $updated_ids = [];
        foreach($all_promotions as $type => $data) {
            foreach($data as $result) {
                $hotel_room_promotion = HotelRoomPromotion::firstOrNew(['id' => $result['id'], 'hotel_id' => $hotel_id, 'room_id' => $room_id]);
                $hotel_room_promotion->hotel_id = $hotel_id;
                $hotel_room_promotion->room_id = $room_id;
                $hotel_room_promotion->name = $result['name'];
                $hotel_room_promotion->type = $type;
                $hotel_room_promotion->currency_code = session('currency');
                $hotel_room_promotion->value_type = $result['value_type'];
                $hotel_room_promotion->value = $result['value'];
                $hotel_room_promotion->status = $result['status'];
                $hotel_room_promotion->min_los = $result['min_los'] ?? '';
                $hotel_room_promotion->max_los = $result['max_los'] ?? '';
                $hotel_room_promotion->days = $result['days'] ?? '';
                $hotel_room_promotion->save();
                $updated_ids[] = $hotel_room_promotion->id;
            }
        }

        HotelRoomPromotion::where('hotel_id',$hotel_id)->where('room_id',$room_id)->whereNotIn('id',$updated_ids)->delete();
    }

    protected function saveCancellationData($hotel_id,$room_id,$cancellation_data = array())
    { 
        $updated_ids = [];
        foreach ($cancellation_data['cancellation_policies'] ?? [] as $key => $cancel) {
            $room_cancellation = HotelRoomCancellationPolicy::firstOrNew(['id' => $cancel['id'],'room_id' => $room_id,'hotel_id' => $hotel_id]);
            $room_cancellation->days = $cancel['days'];
            $room_cancellation->percentage = $cancel['percentage'];
            $room_cancellation->save();
            $updated_ids[] = $room_cancellation->id;
        }

            HotelRoomCancellationPolicy::where('room_id',$room_id)->whereNotIn('id',$updated_ids)->delete();

        
    }
    /**
     * Save Hotel Description translation Data
     *
     * @param String $room_id
     * @param Array $translations
     * @return Void
     */
    protected function saveDescriptionData($room_id, $translations)
    {
        if(count($translations)) {
            $room = HotelRoom::find($room_id);
            $room->update($translations);
        }
    }

    /**
     * Delete Hotel Description translation Data
     *
     * @param String $room_id
     * @param Array $locales
     * @return Void
     */
    protected function deleteDescriptionData($room_id, $locales)
    {
        $locales = explode(',',$locales);
        if(count($locales)) {
            $room = HotelRoom::find($room_id);
            $room->deleteTranslations($locales);
        }
    }

    /**
     * Check and Update Room Status
     *
     * @param String $room_id
     * @return Void
     */
    protected function updateRoomStepStatus($room_id)
    {
        $room = HotelRoom::find($room_id);
        if($room->completed_percent != 100 && $room->status != '') {
            $room->status = 'Unlisted';
            $room->save();
        }
    }

     /**
     * Delete Given Room Photos
     *
     * @param Array $image_ids
     *
     * @return Boolean
     */
    public function deleteHotelPhotos($image_ids)
    {
        if(count($image_ids) == 0) {
            return false;
        }
        $hotel_photos = HotelPhoto::whereIn('id',$image_ids)->get();
        $hotel_photos->each(function($hotel_photo) {
            $handler = $hotel_photo->getImageHandler();
            $image_data['target_dir'] = $hotel_photo->filePath.'/'.$hotel_photo->hotel_id;
            $image_data['name'] = $hotel_photo->image;
            $handler->destroy($image_data);

            $hotel_photo->delete();
        });
        return true;
    }

    /**
     * Upload Hotel Room Photos with Order
     *
     * @param String $room_id
     * @param File $photos_list
     *
     * @return Object Upload Result
     */
    protected function updateRoomPhotos($hotel_id,$room_id,$photos_list)
    {
        $last_photo = HotelRoomPhoto::where('hotel_id',$hotel_id)->where('room_id',$room_id)->latest('order_id')->first();
        $last_order_id = optional($last_photo)->order_id;

        $image_data['add_time'] = true;
        $room_photo = new HotelRoomPhoto;
        $room_photo->hotel_id = $hotel_id;
        $image_data['target_dir'] = $room_photo->getUploadPath();
        $image_data['compress_size'] = array();

        $image_handler = resolve('App\Contracts\ImageHandleInterface');
        $error_list = [];
        foreach($photos_list as $index => $photo) {
            $image_size = number_format($photo->getSize() / 1048576, 2);
            if($image_size <= 5) {
                $image_data['name_prefix'] = 'hotel_room_'.$room_id.'_'.$index;
                $upload_result = $image_handler->upload($photo,$image_data);

                if($upload_result['status']) {
                    $photos = new HotelRoomPhoto;
                    $photos->hotel_id = $hotel_id;
                    $photos->room_id = $room_id;
                    $photos->image = $upload_result['file_name'];
                    $photos->upload_driver= $upload_result['upload_driver'];
                    $photos->order_id = ++$last_order_id;
                    $photos->save();
                }
            } else {
                array_push($error_list,$index);
            }
        }

        if (count($error_list) > 0) {
            return $error_list;
        }
        return [];
    }

    /**
     * Delete Given Hotel Photos
     *
     * @param Array $image_ids
     *
     * @return Boolean
     */
    public function deleteRoomPhotos($image_ids)
    {
        if(count($image_ids) == 0) {
            return false;
        }
        $room_photos = HotelRoomPhoto::whereIn('id',$image_ids)->get();
        $room_photos->each(function($room_photo) {
            $handler = $room_photo->getImageHandler();
            $image_data['target_dir'] = $room_photo->getUploadPath();
            $image_data['name'] = $room_photo->image;
            $handler->destroy($image_data);

            $room_photo->delete();
        });
        return true;
    }    

    /**
     * Delete Room with all relations
     *
     * @param  Integer  $room_id
     * @return Array $return_data
     */
    protected function deleteRoom($room_id)
    {
        \DB::beginTransaction();
        try {
            $room = HotelRoom::where('id',$room_id)->first();
            $room_photos = HotelRoomPhoto::where('room_id',$room_id)->get()->pluck('id')->toArray();
            $this->deleteRoomPhotos($room_photos);
            $room->delete();
        }
        catch (\Exception $e) {
            \DB::rollBack();
            return ['status' => false, 'status_message' => $e->getMessage()];
        }
        \DB::commit();
        return ['status' => true, 'status_message' => ''];
    }
}