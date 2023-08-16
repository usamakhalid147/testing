<?php

/**
 * Credential Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    CredentialModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Credential extends Model
{
    use HasFactory;
    protected $fillable = ['site'];

    
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;
}
