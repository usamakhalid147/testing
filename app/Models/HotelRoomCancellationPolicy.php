<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoomCancellationPolicy extends Model
{
    use HasFactory;


   public $timestamps = false;

    protected $fillable = ['room_id','hotel_id'];
}
