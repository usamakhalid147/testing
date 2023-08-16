<?php

/**
 * Historical Currency Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    HistoricalCurrencyModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoricalCurrency extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function getRatesAttribute()
    {
        $rates = json_decode($this->attributes['rates'],true);
        return collect($rates);
    }
}
