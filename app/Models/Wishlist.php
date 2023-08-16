<?php

/**
 * Wishlist Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    WishlistModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Join With wishlist_lists Table
     *
     */
    public function wishlist_lists()
    {
        return $this->hasMany(WishlistList::class);
    }
}
