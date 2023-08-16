<?php

/**
 * Payouts Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables\Host
 * @category    PayoutsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables\Host;

use Yajra\DataTables\Services\DataTable;
use App\Models\Payout;
use Lang;
use Auth;

class PayoutsDataTable extends DataTable
{

    protected $list_type = 'hotel';
    protected $type;

    /**
     * Set the value for Type
     *
     */
    public function setListType($type)
    {
        $this->list_type = $type;
        return $this;
    }


    /**
     * Set the value for Type
     *
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        ->addColumn('guest_name',function($payout){
            return $payout->user->first_name.$payout->user->last_name;
        })
        ->addColumn('email',function($payout){
            return $payout->user->email;
        })
        ->addColumn('phone_number',function($payout){
            return $payout->user->phone_number;
        })
        ->addColumn('company_id',function($payout){
            return $payout->user->company->id;
        })
        ->addColumn('company_name',function($payout){
            return $payout->user->company->company_name;
        })
        ->addColumn('company_tax_number',function($payout){
            return $payout->user->company->company_tax_number;
        })
        ->addColumn('company_tele_phone_number',function($payout){
            return $payout->user->company->company_tele_phone_number;
        })
        ->addColumn('company_fax_number',function($payout){
            return $payout->user->company->company_fax_number;
        })
        ->addColumn('company_address',function($payout){
            return $payout->user->company->address_line_1;
        })
        ->addColumn('address',function($payout){
            return $payout->user->company->address_line_2;
        })
        ->addColumn('company_city',function($payout){
            return $payout->user->company->city;
        })
        ->addColumn('company_state',function($payout){
            return $payout->user->company->state;
        })
        ->addColumn('company_country_code',function($payout){
            return $payout->user->company->country_code;
        })
        ->addColumn('company_postal_code',function($payout){
            return $payout->user->company->postal_code;
        })
        ->addColumn('company_website',function($payout){
            return $payout->user->company->company_website;
        })
        ->addColumn('company_email',function($payout){
            return $payout->user->company->company_email;
        })
        ->addColumn('star_rating',function($payout){
            return $payout->hotel->star_rating;
        })
        ->addColumn('property_type',function($payout){
            return $payout->hotel->property_type_name;
        })
        ->addColumn('property_telephone_number',function($payout){
            return $payout->hotel->tele_phone_number;
        })
        ->addColumn('extension_number',function($payout){
            return $payout->hotel->extension_number;
        })
        ->addColumn('fax_number',function($payout){
            return $payout->hotel->fax_number;
        })
        ->addColumn('property_address',function($payout){
            return $payout->hotel->hotel_address->address_line_1;
        })
        ->addColumn('ward',function($payout){
            return $payout->hotel->hotel_address->address_line_2;
        })
        ->addColumn('city',function($payout){
            return $payout->hotel->hotel_address->city;
        })
        ->addColumn('state',function($payout){
            return $payout->hotel->hotel_address->state;
        })
        ->addColumn('country_code',function($payout){
            return $payout->hotel->hotel_address->country_code;
        })
        ->addColumn('postal_code',function($payout){
            return $payout->hotel->hotel_address->postal_code;
        })
        ->addColumn('website',function($payout){
            return $payout->hotel->website;
        })
        ->addColumn('email',function($payout){
            return $payout->hotel->email;
        })
        ->addColumn('booking_date',function($payout){
            return getDateInFormat($payout->created_at);
        })
        ->addColumn('room_type',function($payout){

        })
        ->addColumn('hotel_name',function($payout) {
            return $payout->hotel->name;
        })
        ->addColumn('property_id',function($payout){
            return $payout->hotel->id;
        })
        ->addColumn('checkin',function($payout) {
            return $payout->hotel_reservation->checkin;
        })
        ->addColumn('checkin_time',function($payout){
            return $payout->hotel_reservation->checkin_at;
        })
        ->addColumn('checkout',function($payout) {
            return $payout->hotel_reservation->checkout;
        })
        ->addColumn('checkout_time',function($payout){
            return $payout->hotel_reservation->checkout_at;
        })
        ->addColumn('adults',function($payout){
            return $payout->hotel_reservation->adults;
        })
        ->addColumn('childrens',function($payout){
            return $payout->hotel_reservation->children;
        })
        ->addColumn('room_rate_per_night',function($payout){
            return $payout->currency_symbol.$payout->hotel_reservation->sub_total/$payout->hotel_reservation->total_nights;
        })
        ->addColumn('total_room_nights',function($payout){
            return $payout->hotel_reservation->total_nights;
        })
        ->addColumn('room_charges',function($payout){
            return $payout->currency_symbol.$payout->hotel_reservation->sub_total;
        })
        ->addColumn('offers',function($payout){
            return $payout->hotel_reservation->coupon_code;
        })
        ->addColumn('discount_amount',function($payout){
            return $payout->currency_symbol.$payout->hotel_reservation->coupon_price;
        })
        ->addColumn('host_payout_amount',function($payout){
            return $payout->currency_symbol.$payout->amount;
        })
         ->addColumn('payment_method',function($payout){
            return Lang::get('admin_messages.'.$payout->hotel_reservation->payment_method);
        })
        ->addColumn('property_tax',function($payout){
            return $payout->currency_symbol.$payout->hotel_reservation->property_tax;
        })
        ->addColumn('property_service_charge',function($payout){
            return $payout->currency_symbol.$payout->hotel_reservation->service_charge;
        })
        ->addColumn('service_fees',function($payout){
            return $payout->currency_symbol.$payout->hotel_reservation->service_fee;
        })
        ->addColumn('status',function($payout) {
            if($payout->hotel_reservation->adminAbletoPayout()) {
                return $payout->status;
            }
            return $payout->status;
        })
        ->addColumn('reservation_code',function($query) {
            return '<a href="'.route('host.reservations.show',['id' => $query->reservation_id]).'">'.$query->hotel_reservation->code.'</a>';
        })
        ->addColumn('amount',function($payout) {
            return $payout->currency_symbol.$payout->amount;
        })
        ->rawColumns(['reservation_code']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Payout $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Payout $model)
    {
        $locale = global_settings('default_language');
        $user_id = getHostId();
        $model = $model->where('payouts.user_id',$user_id)
            ->join('users',function ($join) {
                $join->on('users.id', '=','payouts.user_id');
            })
            ->join('companies',function($join){
                $join->on('companies.user_id','=','payouts.user_id');
            })
         
            ->join('hotel_addresses',function($join){
                $join->on('hotel_addresses.hotel_id', '=' , 'payouts.list_id');
            })
            ->join('currencies',function ($join) {
                $join->on('currencies.code','=','payouts.currency_code');
            });
        if($this->list_type == 'hotel') {
            $model = $model->join('hotels',function ($join) {
                $join->on('hotels.id','=','payouts.list_id');
            })
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(hotels.name, \'$.'.$locale.'\')) as hotel_name,users.first_name as user_name,payouts.status as status,payouts.amount as amount,payouts.currency_code as currency_code,payouts.user_type as user_type,payouts.*')
            ->where('payouts.list_type','hotel');
        }

        if($this->type == 'future') {
            $model = $model->where('payouts.status','Future');
        }
        else if($this->type == 'completed') {
            $model = $model->where('payouts.status','Completed');
        }
        return $model->get();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(0)
                    ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            ['data' => 'user_id', 'name' =>'user_id' ,'title' => Lang::get('admin_messages.user_id')],
            ['data' => 'property_id', 'name' =>'property_id' ,'title' => Lang::get('admin_messages.property_id')],            
            ['data' => 'hotel_name', 'name' => 'hotel_name', 'title' => Lang::get('admin_messages.hotel_name')],
            ['data' => 'reservation_code', 'name' => 'reservation_code', 'title' => Lang::get('admin_messages.booking_confirmation_code')],
            ['data' => 'guest_name', 'name' => 'guest_name', 'title' => Lang::get('admin_messages.full_name')],
            ['data' => 'email', 'name' => 'email', 'title' => Lang::get('admin_messages.email_address')],
            ['data' => 'phone_number', 'name' => 'phone_number', 'title' => Lang::get('admin_messages.mobile_number')],
            ['data' => 'company_id', 'name' => 'company_id', 'title' => Lang::get('admin_messages.company_id')],
            ['data' => 'company_name', 'name' => 'company_name', 'title' => Lang::get('admin_messages.company_name')],
            ['data' => 'company_tax_number', 'name' => 'company_tax_number', 'title' => Lang::get('admin_messages.company_tax_number')],
            ['data' => 'company_tele_phone_number', 'name' => 'company_tele_phone_number', 'title' => Lang::get('admin_messages.company_tele_phone_number')],
            ['data' => 'company_fax_number', 'name' => 'company_fax_number', 'title' => Lang::get('admin_messages.company_fax_number')],
            ['data' => 'company_address', 'name' => 'company_address', 'title' => Lang::get('admin_messages.company_address')],
            ['data' => 'ward','name'=>'ward','title'=>Lang::get('admin_messages.ward')],
            ['data' => 'company_city', 'name' => 'company_city', 'title' => Lang::get('admin_messages.cities')],
            ['data' => 'company_state', 'name' => 'company_state', 'title' => Lang::get('admin_messages.province')],
            ['data' => 'company_country_code', 'name' => 'company_country_code', 'title' => Lang::get('admin_messages.Country')],
            ['data' => 'company_postal_code', 'name' => 'company_postal_code', 'title' => Lang::get('admin_messages.postal_code')],
            ['data' => 'company_website', 'name' => 'company_website', 'title' => Lang::get('admin_messages.company_website')],
            ['data' => 'company_email','name' => 'company_email', 'title' => Lang::get('admin_messages.company_email')],
            ['data' => 'star_rating', 'name' =>'star_rating' ,'title' => Lang::get('admin_messages.property_star_rating')],
            ['data' => 'property_type', 'name' =>'property_type' ,'title' => Lang::get('admin_messages.property_type')],
            ['data' => 'property_telephone_number', 'name' =>'property_telephone_number' ,'title' => Lang::get('admin_messages.property_telephone_number')],
            ['data' => 'extension_number', 'name' =>'extension_number' ,'title' => Lang::get('admin_messages.extension_number')],
            ['data' => 'fax_number', 'name' =>'fax_number' ,'title' => Lang::get('admin_messages.fax_number')],
            ['data' => 'property_address', 'name' =>'property_address' ,'title' => Lang::get('admin_messages.property_address')],
            ['data' => 'ward', 'name' =>'ward' ,'title' => Lang::get('admin_messages.ward')],
            ['data' => 'city', 'name' =>'city' ,'title' => Lang::get('admin_messages.cities')],
            ['data' => 'state', 'name' =>'state' ,'title' => Lang::get('admin_messages.province')],
            ['data' => 'country_code', 'name' =>'country_code' ,'title' => Lang::get('admin_messages.Country')],
            ['data' => 'postal_code', 'name' =>'postal_code' ,'title' => Lang::get('admin_messages.postal_code')],
            ['data' => 'website', 'name' =>'website' ,'title' => Lang::get('admin_messages.property_website')],
            ['data' => 'email', 'name' =>'email' ,'title' => Lang::get('admin_messages.property_email')],
            ['data' => 'booking_date','name'=>'booking_date','title'=>Lang::get('admin_messages.booking_date')],
            ['data' => 'room_type','name'=>'room_type','title'=>Lang::get('admin_messages.room_category')],
            ['data' => 'checkin', 'name' => 'checkin', 'title' => Lang::get('admin_messages.checkin')." ".Lang::get('admin_messages.date')],
            ['data' => 'checkin_time','name'=>'checkin_time','title' => Lang::get('admin_messages.checkin_time')],
            ['data' => 'checkout', 'name' => 'checkout', 'title' => Lang::get('admin_messages.checkout')." ".Lang::get('admin_messages.date')],
            ['data' => 'checkout_time', 'name' => 'checkout_time','title'=>Lang::get('admin_messages.checkout_time')],
            ['data'=>'adults','name'=>'adults','title'=>Lang::get('admin_messages.adults')],
            ['data'=>'childrens','name'=>'childrens','title'=>Lang::get('admin_messages.childrens')],
            ['data' => 'room_rate_per_night','name'=>'room_rate_per_night','title'=>Lang::get('admin_messages.room_rate_per_night')],
            ['data'=>'total_room_nights','name'=>'total_room_nights','title'=>Lang::get('admin_messages.total_room_night')],
            ['data'=>'room_charges','name'=>'room_charges','title'=>Lang::get('admin_messages.room_charges')],
            ['data'=>'offers','name'=>'offers','title'=>Lang::get('admin_messages.offers')],
            ['data'=>'discount_amount','name'=>'discount_amount','title'=>Lang::get('admin_messages.discount_amount')],
            ['data'=>'host_payout_amount','name'=>'host_payout_amount','title'=>Lang::get('admin_messages.host_payout_amount')],
            ['data'=>'payment_method','name'=>'payment_method','title'=>Lang::get('admin_messages.payment_method')],
            ['data'=>'property_tax','name'=>'property_tax','title'=>Lang::get('admin_messages.property_tax')],
            ['data'=>'property_service_charge','name'=>'property_service_charge','title'=>Lang::get('admin_messages.property_service_charge')],
            ['data'=>'service_fees','name'=>'service_fees','title'=>Lang::get('admin_messages.service_fees')],
            ['data' => 'amount', 'name' => 'amount', 'title' => Lang::get('admin_messages.total')." ".Lang::get('admin_messages.amount')],
            ['data' => 'status', 'name' => 'status', 'title' => Lang::get('admin_messages.status')],
        ];
        if($this->type == 'completed') {
            $columns [] = ['data' => 'transaction_id', 'name' => 'transaction_id', 'title' => Lang::get('admin_messages.transaction_id')];
        }
        return $columns;
    }

    /**
     * Get builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters()
    {
        return array(
            'dom' => config('datatables-buttons.parameters.dom'),
            'buttons' => config('datatables-buttons.parameters.buttons'),
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'payouts_' . date('YmdHis');
    }
}