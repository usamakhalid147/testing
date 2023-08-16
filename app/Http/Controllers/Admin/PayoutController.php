<?php

/**
 * Payout Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    PayoutController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\PayoutsDataTable;
use App\Models\Payout;
use Lang;

class PayoutController extends Controller
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_payouts');
        $this->view_data['sub_title'] = Lang::get('admin_messages.payouts');
        $this->view_data['active_menu'] = 'payouts';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PayoutsDataTable $dataTable, $type = 'future')
    {
        $this->view_data['type'] = $type;
        $this->view_data['filters_array'] = [
            'all' => Lang::get('admin_messages.all'),
            'future' => Lang::get('admin_messages.future'),
            'completed' => Lang::get('admin_messages.completed'),
        ];
        return $dataTable->setType($type)->render('admin.reservations.view',$this->view_data);
    }

    /**
     * Process Guest Refund or Host Payout.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function processPayout($id)
    {
        $payout = Payout::with('user.default_payout_method')->findOrFail($id);
        if($payout->status != 'Future') {
            flashMessage('danger',Lang::get('messages.failed'),Lang::get('admin_messages.payout_failed'));
            return back();
        }

        if ($payout->list_type == 'hotel') {
            $redirect_url = route('admin.payouts');
        }

        if ($payout->list_type == 'experience') {
            $redirect_url = route('admin.experience_payouts');
        }

        if($payout->user_type == "Guest") {
            $reservation = $payout->reservation();
            $payment_type = snakeToCamel($reservation->payment_method,true);
            $transaction_id = $reservation->transaction_id;
            $payout_service = resolve('App\Services\Payouts\\'.$payment_type.'Payout');
            $refund_data["currency_code"] = $reservation->payment_currency;
            $refund_data["amount"] = currencyConvert($payout->amount,$payout->currency_code,$refund_data["currency_code"]);
            $pay_result = $payout_service->makeRefund($transaction_id,$refund_data);

            if(!$pay_result['status']) {
                flashMessage('danger',Lang::get('messages.failed'),Lang::get('admin_messages.payout_failed').' : '.$pay_result['status_message']);
                return redirect($redirect_url);
            }

            $payout->transaction_id = $pay_result['transaction_id'];
            $payout->status = $pay_result['payout_status'];
            $payout->save();

            if($reservation->dispute_amount > 0) {
                $dispute = $reservation->dispute;
                $dispute->payment_method = $reservation->payment_method;
                $dispute->transaction_id = $pay_result['transaction_id'];
                $dispute->save();
            }

            $transaction_data = [
                'user_id' => $payout->user_id,
                'list_type' => $payout->list_type,
                'reservation_id' => $payout->reservation_id,
                'type' => 'refund',
                'description' => '',
                'currency_code' => $payout->currency_code,
                'amount' => $payout->amount,
                'transaction_id' => $payout->transaction_id,
                'payment_method' => $payment_type,
            ];
            createTransaction($transaction_data);

            resolveAndSendNotification("refundProcessed",$payout->id);
            
            flashMessage('success',Lang::get('messages.success'),$pay_result['status_message']);
            return redirect($redirect_url);
        }

        $payout_details = $payout->user->default_payout_method;
        if(!isset($payout_details)) {
            resolveAndSendNotification("addAccountDetails",$payout->user->id);
        
            flashMessage('success',Lang::get('messages.success'),Lang::get('admin_messages.notification_sent_to_user'));
            return redirect($redirect_url);
        }

        $payout_data = array();
        if($payout_details->method_type == 'paypal') {
            $payout_currency = credentials('payment_currency','Paypal');
            $amount = currencyConvert($payout->amount,$payout->currency_code,$payout_currency);
            $data = [
                'sender_batch_header' => [
                    'email_subject' => urlencode('PayPal Payment'),    
                ],
                'items' => [
                    [
                        'recipient_type' => "EMAIL",
                        'amount' => [
                            'value' => $amount,
                            'currency' => $payout_currency
                        ],
                        'receiver' => $payout_details->payout_id,
                        'note' => 'payment of commissions',
                        'sender_item_id' => $payout->reservation_id.$payout->user_id,
                    ],
                ],
            ];
            $payout_data = json_encode($data);
        }
        if($payout_details->method_type == 'stripe') {
            $payout_data['currency'] = credentials('payment_currency','Stripe');
            $payout_data['amount'] = currencyConvert($payout->amount,$payout->currency_code,$payout_data['currency']);
        }

        if($payout_details->method_type == 'pay_at_hotel') {
            $payout->status = 'Completed';
            $payout->save();

            $transaction_data = [
                'user_id' => $payout->user_id,
                'list_type' => $payout->list_type,
                'reservation_id' => $payout->reservation_id,
                'type' => 'payout',
                'description' => '',
                'currency_code' => $payout->currency_code,
                'amount' => $payout->amount,
                'transaction_id' => $payout->transaction_id,
                'payment_method' => $payout_details->method_type,
            ];
            createTransaction($transaction_data);

            resolveAndSendNotification("payoutIssued",$payout->id);

            flashMessage('success',Lang::get('messages.success'),Lang::get('admin_messages.successfully_updated'));
            return redirect($redirect_url);
        }

        $payout_service = resolve('App\Services\Payouts\\'.snakeToCamel($payout_details->method_type,true).'Payout');
        $pay_result = $payout_service->makePayout($payout_details->payout_id,$payout_data);

        if(!$pay_result['status']) {
            flashMessage('danger','Payout Failed : '.$pay_result['status_message'],Lang::get('messages.failed'));
            return redirect($redirect_url);
        }

        if($payout_details->method_type == 'paypal') {
            $payout_data = $payout_service->fetchPayoutViaBatchId($pay_result['transaction_id']);
            if(!$payout_data['status']) {
                flashMessage('danger',Lang::get('messages.failed'),'Payout Failed : '.$payout_data['status_message']);
                return redirect($redirect_url);
            }

            $pay_result = $payout_service->getPayoutStatus($payout_data['data']);
            if(!$pay_result['status']) {
                flashMessage('danger',Lang::get('messages.failed'),'Payout Failed : '.$pay_result['status_message']);
                return redirect($redirect_url);
            }
        }

        $payout->transaction_id = $pay_result['transaction_id'];
        $payout->payout_account = $payout_details->payout_id;
        $payout->status = $pay_result['payout_status'];
        $payout->save();

        $transaction_data = [
            'user_id' => $payout->user_id,
            'list_type' => $payout->list_type,
            'reservation_id' => $payout->reservation_id,
            'type' => 'payout',
            'description' => '',
            'currency_code' => $payout->currency_code,
            'amount' => $payout->amount,
            'transaction_id' => $payout->transaction_id,
            'payment_method' => $payout_details->method_type,
        ];
        createTransaction($transaction_data);

        resolveAndSendNotification("payoutIssued",$payout->id);
        
        flashMessage('success',Lang::get('messages.success'),$pay_result['status_message']);
        return redirect($redirect_url);
    }
}
