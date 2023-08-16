<?php

/**
 * Payout Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Host
 * @category    PayoutController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com/
 */

namespace App\Http\Controllers\Host;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PayoutMethod;
use App\Models\PayoutMethodDetail;
use Lang;
use Auth;
use Validator;

class PayoutController extends Controller
{
	/**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->view_data['active_menu'] = 'payout_methods';
        $this->view_data['sub_title'] = Lang::get('admin_messages.payout_methods');
        $this->view_data['main_title'] = Lang::get('admin_messages.manage_payout_methods');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->view_data['payout_methods'] = PayoutMethod::authBased()->get();
        return view('host.payouts.view',$this->view_data);
    }

    /**
    * Create Payout Preference for Merchant
    *
    * @return \Illuminate\Http\Response
    */ 
    public function create()
    {   
        $stripe_service = resolve("App\Services\Payouts\StripePayout");
        $this->view_data['payout_country_list'] = $stripe_service->getPayoutCoutryList();
        $this->view_data['payout_currency_list'] = $stripe_service->getPayoutCurrencyList();
        $this->view_data['iban_req_countries'] = $stripe_service->getIbanRequiredCountries();
        $this->view_data['branch_code_req_countries'] = $stripe_service->getBranchCodeRequiredCountries();
        $this->view_data['mandatory_fields'] = $stripe_service->getMandatoryFieldList();

        $this->view_data['payout_methods'] = $payout_methods = collect(PAYOUT_METHODS)->filter(function($payout_method) {
            return credentials('is_enabled',$payout_method['value']);
        })
        ->map(function($payout_method) {
            $payout_method['display_name'] = Lang::get('messages.'.$payout_method['display_name']);
            return $payout_method;
        })
        ->values();
        $this->view_data['payout_method'] = $payout_methods->first()['key'];

        return view('host.payouts.add',$this->view_data);
    }

    /**
    * Store Payout Method
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $payout_methods = collect(PAYOUT_METHODS);
        $payout_methods = $payout_methods->pluck('key')->implode(',');
        $rules = array(
            'payout_method' => 'required|in:'.$payout_methods,
            'country_code'  => 'required|exists:countries,name',
        );
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            flashMessage('danger', Lang::get('messages.failed'), $validator->messages()->first());
            return back();
        }
        $user_id = getCurrentUserId();
        $country_code = $request->country_code;
        $default_count = PayoutMethod::where('user_id', $user_id)->where('is_default', '1')->count();
        $payout_method_type = snakeToCamel($request->payout_method,true);
        $payout_service = resolve('App\Services\Payouts\\'.$payout_method_type.'Payout');
        if ($payout_method_type == 'Stripe') {
            if(credentials('account_type','Stripe') == 'express') {
                $verification['country'] = $country_code;
                $verification['type'] = 'express';
                $verification['capabilities'] = [ 
                    'card_payments' => [    
                        'requested' => true,
                    ],
                    'transfers' => [
                        'requested' => true,
                    ],
                ];
                $stripe_account = $payout_service->createStripeAccount($verification);
                if(!$stripe_account['status']) {
                    return response()->json([
                        'status' => false,
                        'status_text' => Lang::get('messages.failed'),
                        'status_message' => $stripe_account['status_message'],
                    ]);
                }
                $account_id = $stripe_account['recipient']->id;
                
                $account_data = [
                    'account' => $account_id,
                    'refresh_url' => route('host.payout_methods.create'),
                    'return_url' => route('host.get_response_stripe_express',['id' => $account_id]),
                    'type' => 'account_onboarding',
                ];

                $account_link = $payout_service->makeAccountLink($account_data);
                
                return response()->json([
                    'status' => 'redirect',
                    'redirect_url' => $account_link['account_link'],
                ]);
            }
            $request['payout_country'] = $country_code;
            $iban_supported_country = $payout_service->getIbanRequiredCountries();
            $bank_data = array(
                "country" => $country_code,
                "currency" => credentials("currency_code","Stripe"),
                "account_holder_name" => $request->holder_name,
                "account_holder_type" => "individual",
            );
            if (in_array($country_code, $iban_supported_country)) {
                $bank_data['account_number'] = $request->account_number;
            }
            else {
                if ($country_code == 'AU') {
                    $request['routing_number'] = $request->bsb;
                }
                elseif ($country_code == 'HK') {
                    $request['routing_number'] = $request->clearing_code . '-' . $request->branch_code;
                }
                elseif ($country_code == 'JP' || $country_code == 'SG') {
                    $request['routing_number'] = $request->bank_code . $request->branch_code;
                }
                elseif ($country_code == 'GB') {
                    $request['routing_number'] = $request->sort_code;
                }
                $bank_data['routing_number'] = $request['routing_number'];
                $bank_data['account_number'] = $request->account_number;
            }
        }

        $validate_data = $payout_service->validateRequest($request);
        if(isset($validate_data['status']) && $validate_data['status']) {
            return $validate_data;
        }
        $image_uploader = resolve('App\Services\ImageHandlers\LocalImageHandler');
        $target_dir = '/images/payout_documents/'.getCurrentUserId();
        
        if($request->hasFile('legal_document')) {
            $image = $request->file('legal_document');
            $image_data = array();
            $image_data['name_prefix'] = 'payout_legal';
            $image_data['add_time'] = true;
            $image_data['target_dir'] = $target_dir;
            
            $upload_result = $image_uploader->upload($image,$image_data);
            
            if(!$upload_result['status']) {
                flashMessage('danger', Lang::get('messages.failed'), $upload_result['status_message']);
                return back();
            }
            $legal_doc = $upload_result['file_name'];
            $legal_doc_path = public_path($target_dir.'/'.$legal_doc);
        }
        
        if($request->hasFile('additional_document')) {
            $image = $request->file('additional_document');
            $image_data = array();
            $image_data['name_prefix'] = 'payout_additional';
            $image_data['add_time'] = true;
            $image_data['target_dir'] = $target_dir;
            
            $upload_result = $image_uploader->upload($image,$image_data);
            
            if(!$upload_result['status']) {
                flashMessage('danger', Lang::get('messages.failed'), $upload_result['status_message']);
                return back();
            }
            $additional_doc = $upload_result['file_name'];
            $additional_doc_path = public_path($target_dir.'/'.$legal_doc);
        }
        if ($payout_method_type == 'Stripe') {
            $stripe_payout_method = $payout_service->createPayoutAccount($request);
            if(!$stripe_payout_method['status']) {
                flashMessage('danger', Lang::get('messages.failed'), $stripe_payout_method['status_message']);
                return back();
            }
            $recipient = $stripe_payout_method['recipient'];
            
            if(isset($legal_doc_path)) {
                $document_result = $payout_service->uploadDocument($legal_doc_path,$recipient->id);
                if(!$document_result['status']) {
                    flashMessage('danger', Lang::get('messages.failed'), $document_result['status_message']);
                    return back();
                }
                $legal_doc_id = $document_result['stripe_document'];

                $payout_service->attachDocumentToRecipient($recipient,$legal_doc_id,'document');
            }
            
            if(isset($additional_doc_path)) {
                $document_result = $payout_service->uploadDocument($additional_doc_path,$recipient->id);
                
                if(!$document_result['status']) {
                    flashMessage('danger', Lang::get('messages.failed'), $document_result['status_message']);
                    return back();
                }
                $additional_doc_id = $document_result['stripe_document'];
                $payout_service->attachDocumentToRecipient($recipient,$additional_doc_id,'additional_document');
            }
            $payout_email = isset($recipient->id) ? $recipient->id : $user->email;
            $payout_currency = $request->payout_currency ?? '';
        }
        else if ($payout_method_type == 'Paypal') {
            $payout_email = $request->paypal_email;
            $payout_currency = credentials("payment_currency","Paypal");
        }
        else if ($payout_method_type == 'BankTransfer') {
            $request["account_number"] = $request->bank_account_number;
            $request["holder_name"] = $request->bank_holder_name;
            $payout_email       = $request->bank_account_number;
            $payout_currency    = global_settings("default_currency");
            $request['branch_code'] = $request->bank_code;
        }
        $payout_method = PayoutMethod::firstOrNew(['user_id' => $user_id,'method_type' => $request->payout_method]);
        $payout_method->user_id = $user_id;
        $payout_method->currency_code = $payout_currency;
        $payout_method->payout_id = $payout_email;
        $payout_method->method_type = $request->payout_method;
        if($payout_method->is_default != '1') {
            $payout_method->is_default = ($default_count == 0);
        }
        $payout_method->save();
        
        $method_details = PayoutMethodDetail::firstOrNew(['payout_method_id' => $payout_method->id]);
        $method_details->payout_method_id= $payout_method->id;
        $method_details->country_code = $country_code;
        $method_details->currency_code = $payout_currency;
        $method_details->routing_number = $request->routing_number ?? '';
        $method_details->account_number = $request->account_number ?? '';
        $method_details->holder_name = $request->holder_name ?? '';
        $method_details->payout_id = $payout_email;
        $method_details->address1 = $request->address1 ?? '';
        $method_details->address2 = $request->address2 ?? '';
        $method_details->city = $request->city;
        $method_details->state = $request->state;
        $method_details->postal_code = $request->postal_code;
        if (isset($legal_doc_path)) {
            $method_details->document_id = $legal_doc_id ?? '';
            $method_details->document_path = $legal_doc;
        }
        if (isset($additional_doc_id)) {
            $method_details->additional_document_id = $additional_doc_id ?? '';
            $method_details->additional_document_path = $additional_doc;
        }
        $method_details->phone_number = $request->phone_number ?? '';
        $method_details->branch_code = $request->branch_code ?? '';
        $method_details->bank_name = $request->bank_name ?? '';
        $method_details->bank_location = $request->bank_location ?? '';
        $method_details->branch_name = $request->branch_name ?? '';
        $method_details->ssn_last_4 = $request->ssn_last_4 ?? '';
        $method_details->address_kanji = isset($address_kanji) ? json_encode($address_kanji) : json_encode(array());
        $method_details->save();
        
        flashMessage('success', Lang::get('messages.success'), Lang::get('messages.added_successfully'));
        return response()->json([
            'status' => 'redirect',
            'redirect_url' =>route('host.payout_methods'),
        ]);
    }

    /**
     * get Response Stripe Express Account
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getResponseStripeExpress(Request $request)
    {
        $payout_service = resolve('App\Services\Payouts\StripePayout');
        $account = $payout_service->retrieveStripeAccount($request->id);
        $payout_enabled = $account['recipient']->payouts_enabled;

        $redirect_url = route('host.payout_methods');
        if(!$payout_enabled) {
            flashMessage('danger', Lang::get('messages.failed'), Lang::get('messages.payouts_not_enabled_for_your_account'));
            return redirect($redirect_url);
        }

        $user_id = getCurrentUserId();
        $payout_id = $account['recipient']->id;
        $default_currency = strtoupper($account['recipient']->default_currency);
        $currency = resolve("Currency")->where('code',$default_currency)->first();
        $payout_currency = $currency->code;
        
        $country_code = $account['recipient']->country;

        $default_count = PayoutMethod::where('user_id', $user_id)->where('is_default', '1')->count();

        $payout_method = PayoutMethod::firstOrNew(['user_id' => $user_id,'method_type' => 'stripe']);
        $payout_method->user_id = $user_id;
        $payout_method->currency_code = $payout_currency;
        $payout_method->payout_id = $payout_id;
        $payout_method->method_type = 'stripe';
        
        if($payout_method->is_default != '1') {
            $payout_method->is_default = ($default_count == 0);
        }
        $payout_method->save();

        $method_details = PayoutMethodDetail::firstOrNew(['payout_method_id' => $payout_method->id]);
        $method_details->payout_method_id = $payout_method->id;
        $method_details->country_code = $country_code;
        $method_details->currency_code = $payout_currency;
        $method_details->payout_id = $payout_id;
        $method_details->save();

        flashMessage('success', Lang::get('messages.success'), Lang::get('messages.added_successfully'));
        return redirect($redirect_url);
    }

    /**
    * Update Payout Method
    *
    * @param  $id
    * @return \Illuminate\Http\Response
    */
    public function update($id)
    {
        $user_id = getCurrentUserId();
        $payout_method = PayoutMethod::where('user_id',$user_id)->where('is_default',1)->first();
        if($payout_method != ''){
            $payout_method->is_default = 0;
            $payout_method->save();
        }
        $default = PayoutMethod::find($id);
        $default->is_default = 1;
        $default->save();
        $redirect_url = route('host.payout_methods');
        return redirect($redirect_url);
    }


    /**
    * Delete Payout Method
    *
    * @param  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $can_destroy = $this->canDestroy($id);
        
        if(!$can_destroy['status']) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$can_destroy['status_message']);
            $redirect_url = route('host.payout_methods');
            return redirect($redirect_url);
        }
        
        try {
            PayoutMethod::find($id)->delete();
            flashMessage('success',Lang::get('admin_messages.success'),Lang::get('admin_messages.successfully_deleted'));
        }
        catch (Exception $e) {
            flashMessage('danger',Lang::get('admin_messages.failed'),$e->getMessage());            
        }
        $redirect_url = route('host.payout_methods');
        return redirect($redirect_url);
    }

    /**
    *  Can Delete Payout Method
    *
    * @param  $id
    * @return \Illuminate\Http\Response
    */
    protected function canDestroy($id)
    {
        $is_default = PayoutMethod::where('id',$id)->pluck('is_default')->first();
        if($is_default == 1) {
            return ['status' => false,'status_message' => Lang::get('admin_messages.cannot_delete_default_payout')];
        }
        return ['status' => true,'status_message' => Lang::get('admin_messages.success')];
    }
}
