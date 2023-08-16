<?php

/**
 * Role Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    RoleModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Shanmuga\LaravelEntrust\Models\EntrustRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends EntrustRole
{
	use HasFactory;
	
    /**
	* The attributes that aren't mass assignable.
	*
	* @var array
	*/
	protected $guarded = [];
}