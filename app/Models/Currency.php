<?php

/**
 * Currency Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    CurrencyModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
    * Set the encoded Symbol of the currency to database
    *
    * @return String symbol
    */
    public function setSymbolAttribute($symbol)
    {
        $this->attributes['symbol'] = htmlentities($symbol);
    }

    /**
    * Get the decoded Symbol of the currency
    *
    * @return String symbol
    */
    public function getSymbolAttribute($symbol)
    {
        return html_entity_decode($symbol);
    }
}
