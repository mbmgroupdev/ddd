<?php

namespace App\Exports\Hr;

use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalarySheetExport implements FromView, WithHeadingRow
{
	use Exportable;

    public function __construct($data, $page_type)
    {
        $this->data = $data;
        $this->page_type = $page_type;
    }
    
    public function view(): View
    {
    	$fields = $this->data;
    	
        if($this->page_type == 'bank'){
            return view('hr.payroll.bank_part.excel',$fields);
        }

    }
    public function headingRow(): int
    {
        return 3;
    }
}