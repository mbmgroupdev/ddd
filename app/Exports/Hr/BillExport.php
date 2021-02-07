<?php

namespace App\Exports\Hr;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BillExport implements FromView, WithHeadingRow
{
	use Exportable;

    public function __construct($data, $input)
    {
        $this->data = $data;
        $this->input = $input;
    }
    
    public function view(): View
    {
    	$getBillList = $this->data;
    	$input = $this->input;
    	
        return view('hr.operation.bill.excel', compact('getBillList', 'input'));
    }
    public function headingRow(): int
    {
        return 3;
    }
}