<?php

namespace App\Http\Controllers\Merch\Style;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StyleController extends Controller
{
    public function showForm()
  	{
	    $data['buyer']        = collect(buyer_by_id())->pluck('b_name', 'b_id')->toArray();
	    $data['productType']  = collect(product_type_by_id())->pluck('prd_type_name', 'prd_type_id');
	    $data['machine']      = collect(special_machine_by_id())->pluck('spmachine_name', 'spmachine_id');
	    $data['garmentsType'] = collect(garment_type_by_id())->pluck('gmt_name','gmt_id');
	    $data['country']      = collect(country_by_id())->pluck('cnt_name','cnt_name');
	    $data['brand']        = collect(brand_by_id())->pluck('br_name', 'br_id');

	    return view('merch/style/style-create', $data);
  	}
}
