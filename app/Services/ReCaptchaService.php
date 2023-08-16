<?php

/**
 * Manage ReCaptcha Request
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    ReCaptchaService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Lang;

class ReCaptchaService 
{
    protected $verify_url = "https://www.google.com/recaptcha/api/siteverify";

    /**
     * Validate Recaptcha
     *
     * @param  string $captcha 
     * @return string $response
     */
    public function validateReCaptcha($captcha)
    {
        $response = Http::asForm()->post($this->verify_url, [
            'secret' => credentials('secret_key','ReCaptcha'),
            'response' => $captcha,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ]);

        if($response->successful()) {
            $response_data = $response->json();
            return [
                'status' => $response_data['success'] ?? false,
                'status_message' => Lang::get('messages.success'),
            ];
        }
        return [
            'status' => false,
            'status_message' => Lang::get('messages.please_complete_captcha_to_continue'),
        ];
    }
}