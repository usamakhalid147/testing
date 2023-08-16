<?php

/**
 * Country Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    CountryModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    /**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $appends = ['city_count'];

    public function cities()
    {
        return $this->hasMany(City::class,'country', 'name');
    }

    public function getCityCountAttribute()
    {
        return $this->cities->count();
    }
}
