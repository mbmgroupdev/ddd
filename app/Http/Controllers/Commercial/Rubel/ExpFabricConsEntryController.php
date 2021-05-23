<?php

namespace App\Http\Controllers\commercial\rubel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Commercial\Export\CmExpFabCons;
use DB, Validator, DataTables, Redirect;

class ExpFabricConsEntryController extends Controller
{
    public function index()
    {
    	$fileList 		= DB::table('cm_exp_data_entry_1 AS a')
									->leftjoin('cm_file AS b', 'a.cm_file_id', 'b.id')
									->select('b.id','b.file_no')
									->pluck("b.file_no", "b.id")
									->toArray();
    	$invoicNoList 	= DB::table('cm_exp_data_entry_1')
									->select('id','inv_no')
									->pluck("inv_no", "inv_no")
									->toArray();
		$unitIdList 	= DB::table('cm_exp_data_entry_1 AS a')
									->leftjoin('hr_unit AS b','a.unit_id','b.hr_unit_id')
									->select('b.hr_unit_id','b.hr_unit_name')
									->pluck("b.hr_unit_name", "b.hr_unit_id")
									->toArray();
		$uom = DB::table('uom')->pluck('measurement_name','measurement_name');
		return view('commercial.export.rubel.export_fabric_cons_entry', compact('fileList','uom','invoicNoList','unitIdList'));
    }

    public function expFabricConsEntry_save()
    {
    	$data['cm_file_id'] 			= request()->input('cm_file_id');
    	$data['hr_unit'] 				= request()->input('hr_unit');
    	$data['invoice_no'] 			= request()->input('invoice_no');
    	$data['consumption'] 			= request()->input('consumption');
    	$data['cm_exp_data_entry_1_id'] = request()->input('cm_exp_data_entry_1_id');
    	foreach($data['consumption'] as $k => $each) {
    		if($each != NULL) {
    			$data['uom'] 			= isset(request()->input('uom')[$k])?request()->input('uom')[$k]:NULL;
    			$data['fabric_qty'] 	= isset(request()->input('fabric_qty')[$k])?request()->input('fabric_qty')[$k]:NULL;
    			$data['consumption'] 	= $each;
    			$final_data[] 			= $data;
    		}
    	}
    	$check = DB::table('cm_exp_fabric_consumption')
    				->insert($final_data);
		if($check) {
			$this->logFileWrite("Commercial-> Export Fabric Consumption Saved", DB::getPdo()->lastInsertId('cm_exp_fabric_consumption'));
    		return redirect('commercial/export/expFabricConsEntry')
					->with('success','Data insert success');
		} else {
			return redirect('commercial/export/expFabricConsEntry')
					->with('error','Data insert error');
		}
    }

    public function expFabricConsEntry_fetchrow()
    {
    	$uom = DB::table('uom')->pluck('measurement_name','measurement_name');
    	return view('commercial.export.rubel.ajax_export_fabric_cons_entry_form',compact('uom'));
    }

    public function exportFabricConsList(Request $request){
    	$items = DB::table('cm_exp_fabric_consumption')
    				->where('invoice_no', $request->invoice_no)
    				->get();
    	return view('commercial.export.rubel.ajax_export_fabric_cons_list',compact('items'));
    }

    public function expFabricConsEntry_fetchinvdate_qty()
    {
    	$file_id 	= request()->input('file_id');
    	$unit_id 	= request()->input('unit_id');
    	$invoice_no = request()->input('invoice_no');
    	$entry_1_data = DB::table('cm_exp_data_entry_1')
    				->where(['cm_file_id' => $file_id, 'unit_id' => $unit_id, 'inv_no' => $invoice_no])
					->first();
		if($entry_1_data) {
			$entry1_po_sum = DB::table('cm_exp_entry1_po')
				->where(['cm_exp_data_entry_1_id' => $entry_1_data->id])
				->sum('inv_qty');
			return ['status' => TRUE, 'entry_1_data' => $entry_1_data, 'entry1_po_sum' => $entry1_po_sum];
		} else {
			return ['status' => FALSE, 'entry_1_data' => '', 'entry1_po_sum' => $entry1_po_sum];
		}
    }

    public function expFabricConsEntry_fetchentryform()
    {
    	$file_id 	= request()->input('file_id');
    	$invoice_no = request()->input('invoice_no');
		$hr_unit  	= request()->input('hr_unit');
		if($file_id != '' && $invoice_no != '' && $hr_unit != '') {
			$data['expFabricConsEntryData'] = DB::table('cm_exp_update5 AS a')
												->leftjoin('hr_unit AS b','a.hr_unit','b.hr_unit_id')
												->where(['a.cm_file_id' => $file_id, 'a.invoice_no' => $invoice_no, 'a.hr_unit' => $hr_unit])
												->first();
			$render = view('commercial.export.rubel.ajax_export_fabric_cons_entry_form', $data)->render();
			return ['status' => TRUE,'render' => $render];
		} else {
			return ['status' => FALSE];
		}
    }

    public function expFabricConsEntry_fetchunitinvoice()
    {
    	$file_id = request()->input('file_id');
		if(request()->input('file_id') != '') {
			$fetch_unitList  	= DB::table('cm_exp_data_entry_1 AS a')
									->leftjoin('hr_unit AS b','a.unit_id','b.hr_unit_id')
									->select('b.hr_unit_id','b.hr_unit_name')
									->where(['a.cm_file_id' => $file_id])
									->pluck("b.hr_unit_name", "b.hr_unit_id")
									->toArray();

			$fetch_invoicNoList = DB::table('cm_exp_data_entry_1')
									->select('id','inv_no')
									->where(['cm_file_id' => $file_id])
									->pluck("inv_no", "inv_no")
									->toArray();
		} else {
			// if file id not found than show all unit data list
			$fetch_unitList 	= DB::table('cm_exp_data_entry_1 AS a')
									->leftjoin('hr_unit AS b','a.unit_id','b.hr_unit_id')
									->select('b.hr_unit_id','b.hr_unit_name')
									->pluck("b.hr_unit_name", "b.hr_unit_id")
									->toArray();
			$fetch_invoicNoList = DB::table('cm_exp_data_entry_1')
									->select('id','inv_no')
									->pluck("inv_no", "inv_no")
									->toArray();
		}
		$unitOptions 	= '';
		$invoiceOptions = '';
		// generate unit option list
		if(count($fetch_unitList) > 0) {
			$unitOptions .= '<option value="">Select Unit</option>';
			foreach($fetch_unitList as $k1 => $unit) {
				$unitOptions .= '<option value="'.$k1.'">'.$unit.'</option>';
			}
		} else  {
			$unitOptions = '<option value="">Not Found</option>';
		}

		// generate invoice option list
		if(count($fetch_invoicNoList) > 0) {
			$invoiceOptions .= '<option value="">Select invoice</option>';
			foreach($fetch_invoicNoList as $k2 => $invoice) {
				$invoiceOptions .= '<option value="'.$k2.'">'.$invoice.'</option>';
			}
		} else  {
			$invoiceOptions = '<option value="">Not Found</option>';
		}
		return ['status' => TRUE,'invoice' => $invoiceOptions, 'hr_unit' => $unitOptions];
    }

    public function expFabricConsEntry_list()
    {
        // $datas = CmExpFabCons::all();
        $datas = DB::table('cm_exp_fabric_consumption')
                     ->leftjoin('cm_file','cm_file.id','cm_exp_fabric_consumption.cm_file_id')
                     ->leftjoin('hr_unit','hr_unit.hr_unit_id','cm_exp_fabric_consumption.hr_unit')
                     ->orderBy('cm_exp_fabric_consumption.id', 'DESC')->get();
		    return view('commercial.export.rubel.export_fabric_cons_list', compact('datas'));
    }
}
