<?php

/**
 * Payouts Datatable
 *
 * @package     HyraHotel
 * @subpackage  DataTables
 * @category    PayoutsDataTable
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\Payout;
use Lang;

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
        ->addColumn('status',function($payout) {
            if($payout->reservation()->adminAbletoPayout()) {
                return $payout->status;
            }
            return "Upcoming";
        })
        ->addColumn('amount',function($payout) {
            $payout_amount = $payout->amount;
            if ($payout->reservation()->payment_method == 'pay_at_hotel') {
                $payout_amount = $payout->reservation()->getTotalAdminAmount();
            }
            return $payout->currency_symbol." ".$payout_amount;
        })
        ->addColumn('action',function($payout) {
            $payout_method = $payout->user->default_payout_method;
            $action_btn = auth()->guard('admin')->user()->can('view-reservations') ? '<a href="'.route('admin.reservations.show',['id' => $payout->reservation_id]).'" target="_blank" class="h3"> <i class="fa fa-eye"></i> </a>' : '';
            if($this->type != 'completed') {
                if($payout->user_type == "Host") {
                    $payout_data[Lang::get('admin_messages.user_name')] = $payout->user->first_name;
                    $payout_data[Lang::get('admin_messages.amount')] = $payout->currency_symbol.$payout->amount;
                    $payout_data['has_payout_data'] = true;
                    if($payout_method == '') {
                        $payout_data['has_payout_data'] = false;
                        $payout_data['payout_message'] = Lang::get('admin_messages.payout_details_not_provided',['user_type' => $payout->user_type]);
                    }
                    else if($payout_method->method_type == 'bank_transfer') {
                        $payout_data[Lang::get('admin_messages.payout_method')] = Lang::get('messages.bank_transfer');
                        $payout_data[Lang::get('admin_messages.payout_account_number')] = $payout_method->payout_id;
                        $payout_data[Lang::get('admin_messages.account_holder_name')] = $payout_method->payout_method_detail->holder_name;
                        $payout_data[Lang::get('admin_messages.bank_name')] = $payout_method->payout_method_detail->bank_name;
                        $payout_data[Lang::get('admin_messages.bank_location')] = $payout_method->payout_method_detail->bank_location;
                        $payout_data[Lang::get('admin_messages.bank_code')] = $payout_method->payout_method_detail->branch_code;
                    }
                    else if($payout_method->method_type == 'stripe') {
                        $payout_data[Lang::get('admin_messages.payout_method')] = Lang::get('messages.stripe');
                        $payout_data[Lang::get('admin_messages.payout_account_number')] = $payout_method->payout_id;
                    }
                    else if($payout_method->method_type == 'paypal') {
                        $payout_data[Lang::get('admin_messages.payout_method')] = Lang::get('messages.paypal');
                        $payout_data[Lang::get('admin_messages.paypal_email')] = $payout_method->payout_id;
                    }
                }
                else {
                    $payout_data['has_payout_data'] = false;
                    $payout_data['is_refund'] = true;
                }

                if ($payout->reservation()->payment_method == 'pay_at_hotel') {
                    $payout_data[Lang::get('admin_messages.payout_method')] = Lang::get('messages.pay_at_hotel');
                    $payout_data[Lang::get('admin_messages.amount')] = $payout->currency_symbol.$payout->reservation()->getTotalAdminAmount();
                }

                if($payout->reservation()->adminAbletoPayout()) {
                    $action_btn .= ' &nbsp; <a href="" data-action="'.route('admin.process_payout',['id' => $payout->id]).'" data-bs-toggle="modal" data-bs-target="#confirmPayoutModal" data-payout_id="'.$payout->id.'" data-payout_details=\''.json_encode($payout_data).'\' class="h3"><i class="fas fa-share-square" aria-hidden="true"></i></a>';
                }
                else {
                    $payout_data['has_payout_data'] = false;
                    $payout_data['upcoming_payout'] = true;
                    $payout_data['is_refund'] = false;
                    $payout_data['payout_message'] = Lang::get('admin_messages.booking_not_eligible_for_payout');

                    $action_btn .= ' &nbsp; <a href="" data-action="'.route('admin.process_payout',['id' => $payout->id]).'" data-bs-toggle="modal" data-bs-target="#confirmPayoutModal" data-payout_id="'.$payout->id.'" data-payout_details=\''.json_encode($payout_data).'\' class="disabled" disabled><i class="fas fa-share-square" aria-hidden="true"></i></a>';
                }
            }
            
            return $action_btn;
        });
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
        $model = $model->join('users',function ($join) {
            $join->on('users.id', '=','payouts.user_id');
        })
        ->join('currencies',function ($join) {
            $join->on('currencies.code','=','payouts.currency_code');
        })
        ->join('hotels',function ($join) {
            $join->on('hotels.id','=','payouts.list_id');
        })
        ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(hotels.name, \'$.'.$locale.'\')) as hotel_name,users.first_name as user_name,payouts.status as status,payouts.amount as amount,payouts.currency_code as currency_code,payouts.user_type as user_type,payouts.*');
        if($this->type == 'future') {
            $model = $model->where('payouts.status','Future');
        }
        else if($this->type == 'completed') {
            $model = $model->where('payouts.status','Completed');
        }
        return $model->where('payouts.list_type','hotel');
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
                    ->addAction()
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
            ['data' => 'reservation_id', 'name' => 'reservation_id', 'title' => Lang::get('admin_messages.reservation_id')],
/*            ['data' => 'hotel_name', 'name' => 'hotel_name', 'title' => Lang::get('admin_messages.'.$this->list_type.'_name')],
*/            ['data' => 'user_name', 'name' => 'users.first_name', 'title' => Lang::get('admin_messages.user_name')],
            ['data' => 'user_type', 'name' => 'user_type', 'title' => Lang::get('admin_messages.user_type')],
            ['data' => 'amount', 'name' => 'payouts.amount', 'title' => Lang::get('admin_messages.total').' '.Lang::get('admin_messages.amount')],
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