<?php

/**
 * Generate Report Updated
 *
 * @package     HyraHotel
 * @subpackage  Mail
 * @category    Generate Report
 * @author      Cron24 Technologies
 * @version     1.3
 * @link        https://cron24.com
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenerateReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * View File for the Email
     *
     * @var string
     **/
    protected $view_file = 'emails.reports';

    /**
     * View Data for the Email
     *
     * @var string
     **/
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Merge Data to existing Data.
     *
     * @return void
     */
    public function mergeData($data)
    {
        $this->data = array_merge($this->data,$data);
        return $this;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->view_file)
                    ->subject($this->data['subject'])
                    ->with($this->data);
    }
}
