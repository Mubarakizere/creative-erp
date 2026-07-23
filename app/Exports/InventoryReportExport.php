<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryReportExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $headers;

    public function __construct(array $data, array $headers)
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
