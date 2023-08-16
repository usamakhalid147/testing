<?php

/**
 * Calendar Controller
 *
 * @package     Hyra
 * @subpackage  Controllers
 * @category    CalendarController
 * @author      Cron24 Technologies
 * @version     1.4
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelRoom;
use App\Models\HotelRoomPrice;
use App\Models\HotelRoomCalendar;
use App\Models\Reservation;
use App\Models\ImportedCalendar;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\EventStatus;
use Auth;
use Lang;
use Validator;
use App\Models\Currency;

class CalendarController extends Controller
{    
    /**
     * Get Calendar Events
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array events
     */
    public function getCalendarData(Request $request)
    {
        $rules = array(
            'hotel_id' => 'required',
            'room_id' => 'required',
            'user_id' => 'required',
            'start' => 'required',
            'end' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'error_message' => $validator->messages()->first(),
            ]);
        }

        $room = HotelRoom::where('hotel_id',$request->hotel_id)->find($request->room_id);
        $start = getDateObject($request->start);
        $end = getDateObject($request->end);

        $calendar_data = HotelRoomCalendar::where('hotel_id',$request->hotel_id)->where('room_id',$request->room_id)->whereBetween('reserve_date',[$start->format('Y-m-d'),$end->format('Y-m-d')]);
        $calendar_data = $calendar_data->get();
        $cal_events = $calendar_data->map(function($calendar) use($room) {
            $currency = resolve('Currency')->where('code',$calendar->currency_code);
            $class_names = \Str::of($calendar->source)->lower();
            $display_color = $room->hotel_room_price->price != $calendar->price ? $calendar->display_color : '#8fdf82';
            return [
                'id' => "calendar_".$calendar->id,
                'display' => 'background',
                'calendar_id' => $calendar->id,
                'source' => $calendar->source,
                'room_id' => $calendar->room_id,
                'start' => $calendar->reserve_date,
                'end' => $calendar->reserve_date,
                'currency_code' => $calendar->currency_code,
                'price' => $calendar->price,
                'status' => $calendar->status,
                'classNames' => 'src-'.$class_names,
                'color' => $display_color,
                'title' => "\n",
                'notes' => $calendar->notes ?? '',
                'available' => $room->number - $calendar->number,
                'sold' => $calendar->number ?? 0,
                'room_price' => $calendar->price > 0 ? $calendar->currency_symbol.$calendar->price : $room->hotel_room_price->currency_symbol.$room->hotel_room_price->price,

            ];
        });

        $today = getDateObject();
        $start_date = $start->startOfMonth()->format('Y-m-d');
        if($today->gte($start)) {
            $start_date = $today->format('Y-m-d');
        }
        $end_date = $start->endOfMonth()->format('Y-m-d');

        $all_dates = getDays($start_date, $end_date);
        $calendar_dates = $calendar_data->pluck('reserve_date')->toArray();

        foreach($all_dates as $date) {
            if(!in_array($date, $calendar_dates)) {
                $cal_events[] = [
                    'id' => "",
                    'display' => 'background',
                    'calendar_id' => '',
                    'source' => '',
                    'room_id' => $request->room_id,
                    'start' => $date,
                    'end' => $date,
                    'currency_code' => $room->hotel_room_price->currency_code,
                    'price' => $room->hotel_room_price->price,
                    'status' => 'available',
                    'classNames' => '',
                    'color' => '#8fdf82',
                    'title' => "\n",
                    'notes' => '',
                    'available' => $room->number,
                    'sold' => 0,
                    'room_price' => $room->hotel_room_price->currency_symbol.$room->hotel_room_price->price,
                ];
            }
        }


        return response()->json([
            'status' => true,
            'events' => $cal_events,
        ]);
    }

    /**
     * Create Or Update Calendar Event
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array events
     */
    public function updateCalendarEvent(Request $request)
    {
        if($request->type == 'delete') {
            HotelRoomCalendar::where('id',$request->calendar_id)->delete();
            return response()->json([
                'status' => true,
                'status_text' => Lang::get('messages.success'),
                'status_message' => Lang::get('messages.calendar_updated_successfully'),
            ]);
        }
        if($request->room_id == '') {
            return response()->json([
                'status' => false,
                'status_text' => Lang::get('messages.failed'),
                'status_message' => Lang::get('messages.invalid_request'),
            ]);
        }
        $dates = getDays($request->start_date,$request->end_date);

        $room_price = HotelRoomPrice::where('hotel_id',$request->hotel_id)->where('room_id',$request->room_id)->first();

        $currency_code = $room_price->getRawOriginal('currency_code');
        $min_price = ceil(currencyConvert(global_settings('min_price'),global_settings('default_currency'),$currency_code));
        $max_price = ceil(currencyConvert(global_settings('max_price'),global_settings('default_currency'),$currency_code));
        $rules['price'] = 'required|numeric|between:'.$min_price.','.$max_price;
        $messages['price.between'] = Lang::get('validation.between.numeric',['min' => $currency_code.' '.$min_price,'max' => $currency_code.' '.$max_price,'attribute' => Lang::get('messages.listing.price')]);

        $validator = Validator::make($request->all(),$rules,[],[]);
        
        if($validator->fails()) {
            return response()->json([
                'error' => true,
                'error_messages' => $validator->messages(),
            ]);
        }

        $update_data = [
            'user_id' => $request->user_id,
            'hotel_id' => $request->hotel_id,
            'room_id' => $request->room_id,
            // 'calendar_id' => $request->calendar_id ?? "",
            'notes' => $request->notes,
            'status' => $request->status,
            'currency_code' => $currency_code,
            'source' => 'Calendar',
        	'price' => ($request->price > 0) ? $request->price : $room_price->price,
            'notes' => $request->notes,
        ];

        foreach ($dates as $date) {
        	$this->checkAndUpdateCalendar($request->room_id,$date,$update_data);
        }

        return response()->json([
            'status' => true,
            'status_text' => Lang::get('messages.success'),
            'status_message' => Lang::get('messages.calendar_updated_successfully'),
        ]);
    }

	/**
	 * Sync Ical with our calendar
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function importCalendar(Request $request)
	{
        $rules = array(
            'room_id' => 'required',
            'calendar_name' => 'required',
            'calendar_url' => 'required|url',
        );

        $attributes = array(
            'url'  => Lang::get('messages.listing.calendar_url'),
            'name' => Lang::get('messages.listing.calendar_name')
        );

        $validator = Validator::make($request->all(), $rules,[],$attributes);

        if ($validator->fails()) {
            // Back with popup_code 4 to show import calendar popup
            return back()->withErrors($validator)->withInput()->with('popup_code',4);
        }

        $calendar_data = [
            'user_id' => $request->user_id,
            'room_id' => $request->room_id,
            'url' => $request->calendar_url,
            'name' => $request->calendar_name,
            'last_sync' => now()->format('Y-m-d H:i:s')
        ];

        // Update or Create a iCal imported data        
        $imported_calendar = ImportedCalendar::updateOrCreate(['room_id' => $calendar_data['room_id'], 'url' => $calendar_data['url']], $calendar_data);

        $result = $this->syncCalendar($imported_calendar);

        if(isset($result['error'])) {
            flashMessage('danger',Lang::get('messages.failed'),$result['error_message']);
        }
        else {
            flashMessage('success',Lang::get('messages.success'),Lang::get('messages.listing.calendar_import_success'));
        }

        return redirect()->route('admin.rooms.edit',['id'=> $request->room_id,'current_tab' => 'calendar']);
	}
	
    /**
     * Remove already synced calendar
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeCalendar(Request $request)
    {
        $imported_calendar = ImportedCalendar::where('user_id',Auth::id())->whereIn('id',$request->calendar ?? []);
        foreach ($imported_calendar->get() as $calendar) {
            RoomCalendar::where('room_id',$calendar->room_id)->where('source','Sync')->delete();
        }
        $imported_calendar->delete();
        
        flashMessage('success',Lang::get('messages.success'),Lang::get('messages.listing.calendar_remove_success'));
        return redirect()->route('admin.rooms.edit',['id'=> $request->room_id,'current_tab' => 'calendar']);
    }

    /**
     * Manually Sync all Calendars
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function syncNow(Request $request)
    {
        $imported_calendars = ImportedCalendar::where('room_id',$request->room_id)->get();
        
        if($imported_calendars->count() == 0) {
            flashMessage('danger',Lang::get('messages.failed'),Lang::get('messages.listing.no_calendar_synced'));
            return redirect()->route('admin.rooms.edit',['id'=> $request->room_id,'current_tab' => 'calendar']);
        }

        $imported_calendars->each(function($imported_calendar) {
            $this->syncCalendar($imported_calendar);
        });
        flashMessage('success',Lang::get('messages.success'),Lang::get('messages.listing.calendar_sync_success'));
        return redirect()->route('admin.rooms.edit',['id'=> $request->room_id,'current_tab' => 'calendar']);
    }

    /**
     * Sync Ical with our calendar
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function syncCalendar($imported_calendar)
    {
        $room_id = $imported_calendar->room_id;
        RoomCalendar::where('room_id',$room_id)->where('source','Sync')->delete();

        try {
            $ical_service = new \App\Services\ICal\IcalParser($imported_calendar->url);
        }
        catch(\Exception $e) {
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
            ];
        }

        if($ical_service->eventCount == 0) {
            return [
                'status' => true,
            ];
        }
        $events = $ical_service->events();
        $room_price = HotelRoomPrice::where('room_id',$room_id)->first();
        $price_data = [
            'user_id' => $imported_calendar->user_id,
            'currency_code' => $room_price->getRawOriginal('currency_code'),
            'price' => $room_price->getRawOriginal('price'),
        ];

        $this->syncEventsWithCalendar($room_id,$events,$price_data);

        $imported_calendar->last_sync = date('Y-m-d H:i:s');
        $imported_calendar->save();

        return [
            'status' => true,
        ];
    }

    /**
     * Sync All Events with our room calendar
     *
     * @param  String $room_id
     * @param  Array \App\Services\ICal\Event
     * @return Boolean
     */
    public function syncEventsWithCalendar($room_id,$events,$price_data)
    {
    	ini_set('max_execution_time', 300);
        foreach ($events as $event) {
            $start_date = iCalDateToUnixTimestamp($event->dtstart);
            $end_date   = iCalDateToUnixTimestamp($event->dtend);
            $start = getDateObject($start_date);
            $end = getDateObject($end_date);
            if($start->format('Y-m-d') == $end->format('Y-m-d')) {
                $date = $start->format('Y-m-d');
                $calendar_data = [
                    'user_id' => $price_data['user_id'],
                    'room_id' => $room_id,
                    'currency_code' => $price_data['currency_code'],
                    'price'   => $price_data['price'],
                    'notes'   => $event->description ?? $event->summary ?? 'Externel Event',
                    'source'  => 'Sync',
                    'status'  => 'not_available',
                ];
                $this->checkAndUpdateCalendar($room_id,$date,$calendar_data);
            }
            else {
                $dates = getDays($start->timestamp, $end->timestamp);
                foreach ($dates as $date) {
                    $calendar_data = [
                        'user_id' => $price_data['user_id'],
                        'room_id' => $room_id,
                        'currency_code' => $price_data['currency_code'],
                        'price'   => $price_data['price'],
                        'notes'   => $event->description ?? $event->summary ?? 'Externel Event',
                        'source'  => 'Sync',
                        'status'  => 'not_available',
                    ];

                    $this->checkAndUpdateCalendar($room_id,$date,$calendar_data);
                }
            }
        }

        return true;
    }

    /**
     * check and update room calendar
     *
     * @param  String $room_id
     * @param  String $date
     * @param  Array $update_data
     * @return Boolean
     */
	protected function checkAndUpdateCalendar($room_id,$date,$update_data)
	{
     	$update_data['reserve_date'] = $date;
        HotelRoomCalendar::updateOrCreate(['room_id' => $room_id, 'reserve_date' => $date], $update_data);
	}
}

