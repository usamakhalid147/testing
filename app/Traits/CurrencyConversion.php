<?php

/**
 * Convert Currency to User selected Currency
 *
 * @package     HyraHotel
 * @subpackage  Traits
 * @category    CurrencyConversion
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Traits;

use App\Models\Currency;

trait CurrencyConversion
{
    public $currency_code_field = 'currency_code',$can_convert = true;

    public $target_currency;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->target_currency = $this->getTargetCurrency();
    }

    /**
    * Join With User Profile Picture Table
    *
    */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_code', 'code');
    }

    /**
    * Get Target Currency ( Session Currency ) Attribute
    *
    */
    public function getCurrencySymbolAttribute()
    {
        $currency_code = $this->getTargetCurrency();
        if(!$this->canConvert()) {
            $currency_code = $this->getRawOriginal($this->currency_code_field);
        }
        $session_currency = resolve('Currency')->where('code',$currency_code)->first();
        if($session_currency != '') {
            return html_entity_decode($session_currency->symbol);
        }
        return '$';
    }

    /**
    * Get Original Currency Code
    *
    */
    public function getOriginalCurrencyCodeAttribute()
    {
        return $this->getRawOriginal('currency_code');
    }

    /**
    * Manually set session Currency for conversion
    *
    */
    public function setTargetCurrency($currency_code = '')
    {
        if($currency_code == '') {
            $currency_code = $this->getTargetCurrency();
        }
        $this->target_currency = $currency_code;
        return $this;
    }

    /**
    * Set Currency can convert or not
    *
    */
    public function setCanConvert($can_convert)
    {
        $this->can_convert = $can_convert;
        return $this;
    }

    /**
    * Set Currency Code field default currency_code
    *
    */
    public function setCurrencyCodeField($currency_code_field)
    {
        $this->currency_code_field = $currency_code_field;
        return $this;
    }

    /**
    * Check Currency code is active or not
    *
    */
    protected function isActiveCurrency($currency_code)
    {
        $currency = resolve('Currency')->where('code',$currency_code)->where('status','1')->count();
        return ($currency > 0);
    }

    /**
    * Check given request from admin panel
    *
    */
    protected function isAdminPanel()
    {
        return request()->segment(1) == global_settings('admin_url');
    }

    /**
    * Check given request from Manage listing
    *
    */
    protected function isManageListing()
    {
        $current_route = request()->route();
        $route_name = optional($current_route)->getName() ?? '';
        return (in_array($route_name,['manage_hotel', 'update_hotel','admin.hotels.edit','admin.hotels.update']));
    }

    /**
    * Get currency code field
    *
    */
    protected function getCurrencyCodeField()
    {
        return $this->currency_code_field;
    }

    /**
    * Check given attribute convertable
    *
    */
    protected function isConvertableAttribute($attribute)
    {
        return in_array($attribute, $this->getConvertFileds());
    }

    /**
    * Get all the convertable attributes
    *
    */
    protected function getConvertFileds()
    {
        return $this->convert_fields ?? array();
    }

    /**
    * Get target currency
    *
    */
    protected function getTargetCurrency()
    {
        $currency_code = session('currency');

        if(!$this->isActiveCurrency($currency_code) || $this->isAdminPanel()) {
            $currency_code = global_settings('default_currency');
        }

        return $currency_code;
    }

    /**
    * return convert currency or not
    *
    */
    protected function canConvert()
    {
        return $this->can_convert && !$this->isManageListing();
    }

    /**
    * get From Currency Code
    *
    */
    protected function getFromCurrencyCode()
    {
        $field = $this->getCurrencyCodeField();
        return parent::getAttribute($field);
    }

    /**
    * get Target Currency Code
    *
    */
    protected function getToCurrencyCode()
    {
        return $this->target_currency;
    }

    /**
    * Convert Currency based on given data
    *
    */
    protected function currency_convert($from, $to, $price = 0)
    {
        if($price == 0) {
            return numberFormat($price);
        }

        if($from == $to) {
            return numberFormat($price);
        }

        if($from == '') {
            $from = global_settings('default_currency');
        }

        $from_currency = resolve('Currency')->where('code',$from)->first();
        $target_currency = resolve('Currency')->where('code',$to)->first();

        $rate = $from_currency->rate;
        $session_rate = '1';
        if($target_currency) {
            $session_rate = $target_currency->rate;
        }

        if(in_array($this->table,['reservations'])) {
            $created_at = $this->attributes['accepted_at'] ?? $this->attributes['created_at'];
            $date = date('Y-m-d', strtotime($created_at));
            
            $historical_data = resolve('HistoricalCurrency')->where('date',$date)->first();
            if($historical_data != '') {
                $from_currency = $historical_data->rates->where('code',$from)->first();
                $rate = $from_currency['rate'];
                $to_currency = $historical_data->rates->where('code',$to)->first();
                $session_rate = $to_currency['rate'];
            }
        }

        if($rate == "0.0") {
            dd("Error Message : Currency value '0' (". $from . ')');
        }
        $converted_price = $price / $rate;

        return numberFormat($converted_price * $session_rate);
    }

    /**
    * Get converted amount of given price
    *
    */
    protected function getConvertedValue($price)
    {
        $from = $this->getFromCurrencyCode();
        $to = $this->getToCurrencyCode();
        $converted_price = $this->currency_convert($from, $to, $price);
        return $converted_price;
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if ($this->canConvert()) {
            foreach($this->getConvertFileds() as $field) {
                $attributes[$field] = $this->getAttribute($field);
            }
            $attributes['currency_code'] = $this->getToCurrencyCode();
        }

        return $attributes;
    }

    /**
     * Get all of the appendable values that are arrayable.
     *
     * @return array
     */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['currency_symbol', 'original_currency_code']));

        return parent::getArrayableAppends();
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        if($this->canConvert()) {
            if ($this->isConvertableAttribute($attribute)) {
                $value = parent::getAttribute($attribute);
                $converted_value = $this->getConvertedValue($value);
                return $converted_value;
            }

            if($attribute == 'currency_code') {
                return $this->getToCurrencyCode();
            }
        }
        return parent::getAttribute($attribute);
    }
}