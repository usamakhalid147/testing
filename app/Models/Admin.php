<?php

/**
 * Admin Model
 *
 * @package     HyraHotel
 * @subpackage  Models
 * @category    AdminModel
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, LaravelEntrustUserTrait;

    /**
     * Specify the Guard of the Auth
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
	* Store the encrypted password to table.
	*
	*/
	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = \Hash::make($value);
	}

    /**
     * Scope to return Active Records Only
     *
     * @param Query Builder $query
     * @return Query Builder
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status',1);
    }

    /**
     * Scope to return Primary Admin Records Only
     *
     * @param Query Builder $query
     * @return Query Builder
     */
    public function scopePrimaryOnly($query)
    {
        return $query->where('primary',1);
    }

    /**
     * Scope to return Primary Admin Users Records
     *
     * @param Query Builder $query
     * @return Query Builder
     */
    public function scopePrimaryUsers($query)
    {
        return $query->activeOnly()->primaryOnly();
    }

    /**
     * Get Role Id
     *
     * @return Integer Id
     */
    public function getRoleIdAttribute()
    {
        $roles = $this->roles()->first();
        return optional($roles)->id ?? '';
    }

    /**
     * Get Role Name
     *
     * @return Integer Id
     */
    public function getRoleNameAttribute()
    {
        $role = $this->roles()->first();
        return optional($role)->display_name ?? '';
    }
}
