<?php

/**
 * Wishlist List Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    WishlistListModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class WishlistList extends Model
{
    use HasFactory;

    /**
     * Scope to Get Type Based Records
     *
     */
    public function scopeListTypeBased($query,$type)
    {
        return $query->where('list_type',$type);
    }

    /**
     * Scope to Check Auth User has already saved given Space id
     *
     */
    public function scopeCheckUserAndListing($query,$list_id)
    {
        return $query->where('user_id',getCurrentUserId())->where('list_id',$list_id);
    }

    /**
     * Join With wishlist Table
     *
     */
    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    /**
     * Join With Room Table
     *
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class,'list_id');
    }
}
