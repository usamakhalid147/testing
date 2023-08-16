<?php

/**
 * Array Export
 *
 * @package     HyraHotel
 * @subpackage  Export
 * @category    ArrayExport
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArrayExport implements FromArray, WithHeadings
{
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        $heading_data = $this->data[0] ?? array();
        $headings = array_keys($heading_data);
        return $headings;
    }

}