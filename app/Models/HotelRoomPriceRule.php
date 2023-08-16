<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CurrencyConversion;
use Lang;

class HotelRoomPriceRule extends Model
{
    use HasFactory, CurrencyConversion;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['room_id','hotel_id'];

    /**
     * The attributes are converted based on session currency.
     *
     * @var array
     */
    public $convert_fields = ['price'];

    /**
     * Get Name
     *
     */
    public function getNameAttribute()
    {
        $table = $this->type == 'meal' ? 'MealPlan' : 'BedType';
        $type_name = resolve($table)->where('id', $this->type_id)->first();
        return optional($type_name)->name ?? '';
    }
}
