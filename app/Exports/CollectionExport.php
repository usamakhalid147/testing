<?php

/**
 * Collection Export
 *
 * @package     HyraHotel
 * @subpackage  Export
 * @category    CollectionExport
 * @author      Cron24 Technologies
 * @version     1.0
 * @link        https://cron24.com
 */

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CollectionExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $heading_data = $this->data[0] ?? array();

        $headings = [];
        foreach($heading_data as $key => $value) {
            $headings[] = snakeToCamel($key);
        }
        
        return $headings;
    }

}