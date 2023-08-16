<?php

/**
 * Provide methods to access firebase
 *
 * @package     HyraHotel
 * @subpackage  Services
 * @category    FirebaseService
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Services;

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Kreait\Firebase\ServiceAccount;

class FirebaseService
{
	/**
	 * Constructor
	 * 
	 */
	public function __construct()
	{
		$filePath = resource_path('firebase/'.credentials('service_account','Firebase'));

		try {
			$serviceAccount = ServiceAccount::fromValue($filePath);

	        $this->firebase = (new Factory())->withServiceAccount($serviceAccount)->withDatabaseUri(credentials('database_url','Firebase'));
	        $this->database = $this->firebase->createDatabase();
		}
		catch(\Exception $e) {
			$this->firebase = $this->database = null;
		}
	}

	/**
	 * get Database With Reference
	 *
	 * @param String $[reference] [reference path]
	 * @return reference
	 */
	public function getDatabaseWithReference($reference)
    {
    	if(!isset($this->database)) {
    		return null;
    	}
    	$base_path = env('APP_ENV').'/';
		return $this->database->getReference($base_path.$reference);
	}

	/**
	 * Update Reference in Database
	 *
	 * @param String $[reference] [reference path]
	 * @param Json $[data]
	 * @return reference
	 */
	public function insertReference($reference,$data)
    {
    	$reference = $this->getDatabaseWithReference($reference);
    	if(!isset($reference)) {
    		return null;
    	}
		return $reference->push($data);
	}

	/**
	 * Create Custom Token
	 *
	 * @param String email
	 * @return token
	 */
	public function createCustomToken($email)
	{
        $customToken = $this->firebase->createAuth()->createCustomToken($email);
        return $customToken->toString();
	}
}