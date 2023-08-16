<?php

/**
 * User Saved Card Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    UserSavedCardModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSavedCard extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function getFormattedCardAttribute()
    {
        return 'XXXX XXXX XXXX '.$this->last4;
    }
}
