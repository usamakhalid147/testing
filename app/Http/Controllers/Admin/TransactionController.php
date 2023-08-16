<?php

/**
 * Transaction Controller
 *
 * @package     HyraHotel
 * @subpackage  Controllers\Admin
 * @category    TransactionController
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\DataTables\TransactionsDataTable;
use Lang;

class TransactionController extends Controller
{
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
		$this->view_data['main_title'] = Lang::get('admin_messages.transactions');
		$this->view_data['active_menu'] = 'transactions';
		$this->view_data['sub_title'] = Lang::get('admin_messages.transactions');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(TransactionsDataTable $dataTable)
	{
		return $dataTable->render('admin.transactions.view',$this->view_data);
	}
}
