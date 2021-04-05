<?php

namespace App\Http\Controllers\Merch\Style;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Merch\Buyer;
use App\Models\Merch\Brand;
use App\Models\Merch\Country;
use App\Models\Merch\ProductType;
use App\Models\Merch\ProductSize;
use App\Models\Merch\ProductSizeGroup;
use App\Models\Merch\Operation;
use App\Models\Merch\Spmachine;
use App\Models\Merch\GarmentsType;
use App\Models\Merch\Season;
use App\Models\Merch\SampleType;
use App\Models\Merch\Style;
use App\Models\Merch\StyleOperation;
use App\Models\Merch\StyleImage;
use App\Models\Merch\OperationCost;
use App\Models\Merch\StyleSpecialMachine;
use App\Models\Merch\SampleStyle;
use App\Models\Merch\StyleHistory;
use App\Models\Merch\BomCostingBooking;
use App\Models\Merch\BomCostingHistory;
use App\Models\Merch\BomStyleCosting;
use App\Models\Merch\WashType;
use App\Models\Merch\WashCategory;
use App\Models\Merch\StlWashType;
use App\Models\Merch\StyleSizeGroup;
use App\Models\Merch\BomCosting;
use App\Models\Merch\BomOtherCosting;
use App\Models\Merch\StyleCostApproval;

use DB;

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
	    $data['sampleType']   = collect(sample_type_by_id())->pluck('sample_name','sample_id');

	    return view('merch/style/style-create', $data);
  	}

  	public function editForm($id)
  	{
	    $buyerList        = collect(buyer_by_id())->pluck('b_name', 'b_id')->toArray();;
	    $productType  = collect(product_type_by_id())->pluck('prd_type_name', 'prd_type_id');
	    
	    $garmentsType = collect(garment_type_by_id())->pluck('gmt_name','gmt_id');
	    $sampleType   = collect(sample_type_by_id())->pluck('sample_name','sample_id');
	    $brand    		  = collect(brand_by_id())->pluck('br_name', 'br_id');
	    $country          = collect(country_by_id())->pluck('cnt_name','cnt_name');
	    $product          = ProductSize::get();
	    $sizegroup        = ProductSize::pluck('mr_product_pallete_name', 'mr_product_size_group_id');
	    $buyer            = Buyer::pluck('b_name', 'b_id');
	    $sizegroupList    = ProductSizeGroup::pluck('size_grp_name','id');
	    $wash             = WashType::pluck('wash_name','id');

	    $style = DB::table('mr_style AS s')
                ->select("s.*","b.b_name","b.b_id","p.*")
                ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
                ->leftJoin('mr_product_type AS p', 'p.prd_type_id', '=', 's.prd_type_id')
                ->leftJoin('mr_garment_type AS g', 'g.gmt_id', '=', 's.gmt_id')
                ->where('s.stl_id',$id)
                ->first();

	    $season  = Season::where('b_id','=',$style->b_id)->pluck('se_name','se_id');

	

      	// get selected operation 
        $operationList = Db::table('mr_style_operation_n_cost AS s')
	      ->where('mr_style_stl_id', $id)
	      ->select([
	        's.style_op_id',
	        'o.opr_id',
	        'o.opr_name',
	        'o.opr_type',
	        'o.image'
	      ])
	      ->leftJoin('mr_operation AS o', 'o.opr_id', 's.mr_operation_opr_id')
	      ->get();
	    $selectedOpData = view('merch.common.get_default_selected_operation', compact('operationList'))->render();

	    $selectedOp = collect($operationList)->pluck('opr_id')->toArray();

	    //Operation List Show in Modal
	    $operationList = Operation::get();
    
      	$operationData = view('merch.common.get_operation', compact('operationList','selectedOp'))->render();

      	$machineList      = special_machine_by_id();
      	$spSelectedMachine = DB::table('mr_style_sp_machine')
      							->where('stl_id', $id)
      							->pluck('spmachine_id')
      							->toArray();

      	$operationData = view('merch.common.get_special_machine', compact('machineList','spSelectedMachine'))->render();



      	//wash modal
      	$washCategoryList = WashCategory::get();
      	$selectedWash   = StlWashType::where('mr_style_stl_id', $id)->pluck('mr_wash_type_id')->toArray();

      	$washData       = view('merch.common.get_wash_type', compact('washCategoryList','selectedWash'))->render();

	      //Selected Wash Type Show
	      $selectedWahsData = '';
	      $selectedWashes = DB::table('mr_stl_wash_type AS s')
	                        ->leftJoin('mr_wash_type AS w', 'w.id', 's.mr_wash_type_id')
	                        ->where('s.mr_style_stl_id', $id)
	                        ->select([
	                          's.id',
	                          's.mr_wash_type_id',
	                          'w.wash_name',
	                          'w.id as wash_id'
	                        ])
	                        ->get();
      $tr_end1           = 0;
      $selectedWahsData .= '<table class="table" style="margin-top: 30px;">';
      $selectedWahsData .= '<thead>';
      $selectedWahsData .= '<tr>';
      $selectedWahsData .= '<td colspan="3" class="text-center">Wash</td>';
      $selectedWahsData .= '</tr>';
      $selectedWahsData .= '</thead>';
      $selectedWahsData .= '<tbody>';
      // dd($selectedWashes);
      foreach ($selectedWashes as $k=>$selW) {
        if(strlen((string)($k/3)) === 1) {
          $selectedWahsData .= '<tr>';
          $tr_end1 = $k+2;
        }

        $selectedWahsData .= '<td style="border-bottom: 1px solid lightgray;">'.$selW->wash_name.'</td>';
        $selectedWahsData .= '<input type="hidden" name="wash[]" value="'.$selW->mr_wash_type_id.'"></input>';

        if($tr_end1 == 3 || $tr_end1 == 6 || $tr_end1 == 9) {
          $selectedWahsData .= '</tr>';
        }
      }
      $selectedWahsData .= '</tbody>';
      $selectedWahsData .= '</table>';

    //dd($selectedWahsData);exit;
      $StyleSizeGroups= DB::table('mr_stl_size_group AS s')
      ->where('s.mr_style_stl_id', $id)
      ->select([
        'p.id',
        'size_grp_name'
      ])
      ->leftJoin('mr_product_size_group AS p', 'p.id', 's.mr_product_size_group_id')
      ->get();

      //Size group list for modal
      $pdSizeList = DB::table('mr_product_type')->pluck('prd_type_name','prd_type_id');
      //dd($style->prd_type_id);exit;
      $sizegroupList = ProductSizeGroup::where('b_id', $style->b_id)->where('size_grp_product_type', $pdSizeList[$style->prd_type_id])->select('size_grp_name','id')->get();

      $stl_sz_g= DB::table('mr_stl_size_group')->where('mr_style_stl_id', $id)->pluck('mr_product_size_group_id')->toArray();


      $sizegroupListModal='<div class="col-xs-12"><div class="checkbox">';

      foreach ($sizegroupList as $sgl) {
        if(in_array($sgl->id, $stl_sz_g)) {
          $sizegroupListModal.= "<label class='col-sm-2' style='padding:0px;'>
          <input name='sizeGroups[]' type='checkbox' class='ace' value='".$sgl->id."' checked>
          <span class='lbl'>".$sgl->size_grp_name."</span>
          </label>";
        } else {
          $sizegroupListModal.= "<label class='col-sm-2' style='padding:0px;'>
          <input name='sizeGroups[]' type='checkbox' class='ace' value='".$sgl->id."'>
          <span class='lbl'>".$sgl->size_grp_name."</span>
          </label>";
        }

      }
      $sizegroupListModal.="</div></div>";

      //size group list show
      $sizeGroupDatatoShow='';
      $j=0;
      foreach($StyleSizeGroups AS $szs) {
        $dataRows= DB::table('mr_product_size')->where('mr_product_size_group_id', $szs->id)->get();
        $i=0;
        $result='<table class="table table-bordered" style="margin-bottom:0px;"><thead><tr><th colspan="5">'.$szs->size_grp_name.'</th></tr></thead><tbody>';
        foreach($dataRows AS $row){
          if($i==0){
            $result.='<tr style="border-bottom: 1px solid lightgray;">';
          }

          $result.='<td>'.$row->mr_product_pallete_name.'</td>';
          $i++;

          if($i==5){
            $i=0;
            $result.='</tr>';
          }
        }
        if($i!=0) $result.='</tr>';

        $result.= '</tbody></table>';
        $result.= '<input type="hidden" name="prdsz_id[]" value="'.$szs->id.'"></input>';

        $sizeGroupDatatoShow.=$result;
      }

      //./size group show
      $sizegroupListModal='<div class="col-xs-12"><div class="checkbox">';
      foreach ($sizegroupList as $sgl) {
        $sizeList = ProductSize::where('mr_product_size_group_id',$sgl->id)->pluck('mr_product_pallete_name','id');
        if(in_array($sgl->id, $stl_sz_g)) {
          $sizegroupListModal.= "<label class='col-sm-2' style='padding:0px;'>
          <input name='sizeGroups[]' type='checkbox' class='ace' value='".$sgl->id."' checked>
          <span class='lbl'>".$sgl->size_grp_name."</span>";
        } else {
          $sizegroupListModal.= "<label class='col-sm-2' style='padding:0px;'>
          <input name='sizeGroups[]' type='checkbox' class='ace' value='".$sgl->id."'>
          <span class='lbl'>".$sgl->size_grp_name."</span>";
        }
        if(count($sizeList) > 0) {
          $sizegroupListModal .= '<ul>';
          foreach($sizeList as $k=>$size) {
            $sizegroupListModal .= "<li>$size</li>";
          }
          $sizegroupListModal .= '</ul>';
        }
        $sizegroupListModal .= '</label>';
      }
      $sizegroupListModal.="</div></div>";

      //size group list show
      $sizeGroupDatatoShow='';
      $j=0;
      foreach($StyleSizeGroups AS $szs)
      {
        $dataRows= DB::table('mr_product_size')->where('mr_product_size_group_id', $szs->id)->get();
        $i=0;
        $result='<table class="table table-bordered" style="margin-bottom:0px;"><thead><tr><th colspan="5">'.$szs->size_grp_name.'</th></tr></thead><tbody>';
        foreach($dataRows AS $row){
          if($i==0){
            $result.='<tr style="border-bottom: 1px solid lightgray;">';
          }

          $result.='<td>'.$row->mr_product_pallete_name.'</td>';
          $i++;

          if($i==5){
            $i=0;
            $result.='</tr>';
          }
        }
        if($i!=0) $result.='</tr>';

        $result.= '</tbody></table>';
        $result.= '<input type="hidden" name="prdsz_id[]" value="'.$szs->id.'"></input>';

        $sizeGroupDatatoShow.=$result;
      }

      //./size group show
      $stlsize = DB::table('mr_stl_size_group AS s')
        ->select(
          "s.*",
          "p.id",
          "p.size_grp_name"
          )
        ->leftJoin('mr_product_size_group AS p', 'p.id', '=', 's.mr_product_size_group_id')
        ->where('s.mr_style_stl_id',$id)
        ->get();
      $stlwash = DB::table('mr_stl_wash_type AS sw')
        ->select(
          "sw.*",
          "mw.id",
          "mw.wash_name"
          )
        ->leftJoin('mr_wash_type AS mw', 'mw.id', '=', 'sw.mr_wash_type_id')
        ->where('sw.mr_style_stl_id',$id)
        ->get();

      $stlImageGallery = StyleImage::where('mr_stl_id',$id)->get();

    $style_id = $id;
    return view('merch/style/style-edit', compact(
      'buyerList',
      'country',
      'productType',
      'operationList',
      'machineList',
      'garmentsType',
      'sizegroupList',
      'sampleType',
      'buyer',
      'brand',
      'sizegroup',
      'stlsize',
      'wash',
      'stlwash',
      'season',
      'style',
      'stlImageGallery',
      'operationData',
      'selectedOpData',
      'washData',
      'selectedWahsData',
      'sizeGroupDatatoShow',
      'sizegroupListModal',
      'sizeGroupDatatoShow',
      'style_id',
      'pdSizeList'
    ));
  }
}
