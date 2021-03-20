<?php

namespace App\Http\Controllers\Merch\Style;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Merch\ShortCodeLib as ShortCodeLib;
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
use DB, Validator, Auth, DataTables, Response,Image;
use App\Models\Employee;

class NewStyleController extends Controller
{
  # show form
  public function showForm()
  {
    $b_permissions    = explode(',', auth()->user()->buyer_permissions);
    $buyerList        = Buyer::whereIn('b_id', $b_permissions)->pluck('b_name', 'b_id')->toArray();
    $productTypeList  = ProductType::pluck('prd_type_name', 'prd_type_id');
    $machineList      = Spmachine::pluck('spmachine_name', 'spmachine_id');
    $garmentsTypeList = GarmentsType::pluck('gmt_name','gmt_id');
    $country          = Country::pluck('cnt_name','cnt_name');
    $buyer            = Buyer::pluck('b_name', 'b_id');
    $brand            = Brand::pluck('br_name', 'br_id');
    $season           = Season::pluck('se_name','se_id');

    return view('merch/style/style_new', compact(
      'buyerList',
      'country',
      'productTypeList',
      'machineList',
      'garmentsTypeList',
      'buyer',
      'brand',
      'season'
    ));
  }

  public function fetchWashGroup(Request $request)
  {
    $washCategoryList = WashCategory::get();
    $data = '<div class="col-sm-12"><div class="checkbox">';
    if($washCategoryList) {
      if(count($washCategoryList) > 0) {
        foreach ($washCategoryList as $key => $value) {
          $data.= "<label class='col-sm-2' style='padding:0px;'>
          <span class='lbl'> ".$value->category_name."</span>";
          if(count($value->mr_wash_type) > 0) {
            $data .= '<ul>';
            foreach($value->mr_wash_type as $k=>$wash) {
              $checked = '';
              if(!empty($request->checkedWash)) {
                $checked = in_array($wash->id, $request->checkedWash)!==FALSE?'checked="checked"':'';
              }
              $washName = $wash->wash_name;
              $data .= "<li style='list-style-type: none;'>";
              $data .= "<label style='padding:0px;'>";
              $data .= "<input name='washType[]' type='checkbox' class='ace' value='".$wash->id."' ".$checked.">";
              $data .= "<span class='lbl'> ".$washName."</span>";
              $data .= "</label>";
              $data .= "</li>";
            }
            $data .= '</ul>';
          }
          $data .= "</label>";
        }
      } else {
          $data .= '<div class="row"><h4 class="center" style="padding: 15px;">No Wash Group Found</h4></div>';
      }
      $data.="</div></div>";
    } else {
      $data .= '<div class="row"><h4 class="center" style="padding: 15px;">No Wash Group Found</h4></div>';
    }
    return json_encode($data);
  }

  // ajax get size group
  public function fetchSizeGroup($buyer_id,$productType)
  {
    $typeName = ProductType::where('prd_type_id',$productType)->first()->prd_type_name;
    $sizegroupList = ProductSizeGroup::where('b_id', $buyer_id)->where('size_grp_product_type',$typeName)->pluck('size_grp_name','id');


    $data = '<div class="col-sm-12"><div class="checkbox">';
    if($sizegroupList) {
      if(count($sizegroupList) > 0) {
        foreach ($sizegroupList as $key => $value) {
          $sizeList = ProductSize::where('mr_product_size_group_id',$key)->pluck('mr_product_pallete_name','id');
          $data.= "<label class='col-sm-2' style='padding:0px;'>
          <input name='sizeGroups[]' type='checkbox' id='sizeGroups' class='ace' value='".$key."'>
          <span class='lbl'> ".$value."</span>";
          if(count($sizeList) > 0) {
            $data .= '<ul>';
            foreach($sizeList as $k=>$size) {
              $data .= "<li>$size</li>";
            }
            $data .= '</ul>';
          }
          $data .= "</label>";
        }
      } else {
          $data .= '<div class="row"><h4 class="center" style="padding: 15px;">No Size Group Found</h4></div>';
      }
      $data.="</div></div>";
    } else {
      $data .= '<div class="row"><h4 class="center" style="padding: 15px;">No Size Group Found</h4></div>';
    }
    return json_encode($data);
  }

  //Size group Modal Data
  public function getSzGrpModalData(Request $request)
  {
    $sizegroupList = ProductSizeGroup::where('b_id', $request->b_id)->where('size_grp_product_type', $request->prd_type_id)->pluck('size_grp_name','id');
       //dd($sizegroupList);exit;
    $data='<div class="col-xs-12"><div class="checkbox">';
    foreach ($sizegroupList as $key => $value) {
      $sizeList = ProductSize::where('mr_product_size_group_id',$key)->pluck('mr_product_pallete_name','id');
      $data.= "<label class='col-sm-2' style='padding:0px;'>
      <input name='sizeGroups[]' type='checkbox' class='ace' value='".$key."'>
      <span class='lbl'> ".$value."</span>";
      if(count($sizeList) > 0) {
        $data .= '<ul>';
        foreach($sizeList as $k=>$size) {
          $data .= "<li>$size</li>";
        }
        $data .= '</ul>';
      }
      $data .= "</label>";
    }
    $data.="</div></div>";

    $operationList  = Operation::where("opr_type", 1)->get();
    $operationData  = '';
    $tr_end         = 0;
    $operationData .= '<table class="table table-bordered" style="margin-bottom:0px;">';
    // $operationData .= '<thead>';
    // $operationData .= '<tr>';
    // $operationData .= '<td colspan="3" class="text-center">Operations</td>';
    // $operationData .= '</tr>';
    // $operationData .= '</thead>';
    $operationData .= '<tbody>';
    foreach ($operationList as $k=>$operation) {
      if($operation->opr_type==1){
        if(strlen((string)($k/10)) === 1) {
          $operationData .= '<tr>';
          $tr_end = $k+9;
        }

        $operationData .= '<td style="border-bottom: 1px solid lightgray;">'.$operation->opr_name.'</td>';
        $operationData .= '<input type="hidden" name="opr_id[]" value="'.$operation->opr_id.'"></input>';
        $operationData .= '<input type="hidden" name="opr_type[]" value="'.$operation->opr_type.'"></input>';

        if($tr_end == 10) {
          $operationData .= '</tr>';
        }
      }
    }
    $operationData .= '</tbody>';
    $operationData .= '</table>';
    // $operationData.="</div></div>";

    $oputput["moData"]=$data;
    $oputput["opData"]=$operationData;
    return $oputput;
  }

  //get size group details of selected size groups
  public function getSzGrpDetails(Request $request)
  {
    $oputput='';
    $j=0;
    foreach($request->selected_sizes AS $szs)
    {
      $dataRows= DB::table('mr_product_size')->where('mr_product_size_group_id', $szs)->get();
      $i=0;
      $result='<table class="table table-bordered" style="margin-bottom:0px;"><thead><tr><th colspan="5" style="text-align:center;">'.$request->names[$j++].'</th></tr></thead><tbody>';
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
      $result.= '<input type="hidden" name="prdsz_id[]" value="'.$szs.'" />';
      //$result.= '<input type="hidden" name="selected_sizes[]" value="'.$request->selected_sizes.'" />';

      $oputput.=$result;
    }
    return Response::json($oputput);
  }

  # Return product List
  public function productList(Request $request)
  {
    $list = "";
    $productTypeList  = ProductType::orderBy('prd_type_id', 'desc')
    ->pluck('prd_type_name', 'prd_type_id');
    foreach ($productTypeList as $key => $value)
    {
      $list .= "<option value=\"$key\">$value</option>";
    }

    return $list;
  }

  # Return Season List
  public function seasonList(Request $request)
  {
    // Season List Query
    $seasonlist = "";
    $seasons=Season::where('b_id', $request->b_id)->pluck('se_name','se_id');
    foreach ($seasons as $key => $value)
    {
      $seasonlist .= "<option value=\"$key\">$value</option>";
    }

    return $seasonlist;
  }

  # Return garment List on product select
  public function garmentsList(Request $request)
  {
    // Season List Query
    $garmentlist = "<option value=\"\">Select Garments Type </option>";
    $garments=GarmentsType::where('prd_id', $request->prd_id)->pluck('gmt_name','gmt_id');
    foreach ($garments as $key => $value)
    {
      $garmentlist .= "<option value=\"$key\">$value</option>";
    }

    $latest= GarmentsType::orderBy('gmt_id', 'desc')->first();

    return response()->json(['gmlist' => $garmentlist, 'lastGm'=> $latest->gmt_id]);
  }

  # Return Wash List
  public function washList(Request $request)
  {
    // Season List Query
    $washlist = "";
    $washs= WashType::orderBy('id', 'desc')->pluck('wash_name','id');
    foreach ($washs as $key => $value) {
      $washlist .= "<label class='col-sm-2' style='padding:0px;'>
      <input name='washType[]' type='checkbox' class='ace' value='".$key."'>
      <span class='lbl'>".$value."</span>
      </label>";
    }
    return $washlist;
  }

  public function fetchspecialmechines(Request $request)
  {
    // Season List Query
    $machinelist = "";
    //Spmachine::pluck('spmachine_name', 'spmachine_id');
    $spmachines= Spmachine::orderBy('spmachine_id', 'desc')->pluck('spmachine_name', 'spmachine_id');
    foreach ($spmachines as $key => $value)
    {
      $machinelist .= "<label class='col-sm-2' style='padding:0px;'>
      <input name='sp_machine_id[]' type='checkbox' class='ace' value='".$key."'>
      <span class='lbl'>".$value."</span>
      </label>";
    }
   return Response::json($machinelist);
    // return $machinelist;
  }

  # Size Group List
  public function sizegroupList(Request $request)
  {
    // Size Group List Query
    $sizelist = "";
    $sizes= ProductSizeGroup::orderBy('id', 'desc')
    ->where('b_id', $request->buyer)
    ->pluck('size_grp_name','id');
    foreach ($sizes as $key => $value) {
      $sizelist .= "<option value=\"$key\">$value/</option>";
    }
    //dd($sizelist);exit;
    return $sizelist;

  }

  # Buyer List
  public function buyerList()
  {
    // Size Group List Query
    $buyerList = "";
    $buyers = Buyer::orderBy('b_id', 'desc')->pluck('b_name', 'b_id');
    foreach ($buyers as $key => $value)
    {
      $buyerList .= "<option value=\"$key\">$value</option>";
    }
    return $buyerList;
  }

  # Return Sample and Season List by Buyer Type
  public function getSampleByBuyer(Request $request)
  {
    $list = "";
    if (!empty($request->b_id))
    {
      // Sample List Query
      $sample  = SampleType::where('b_id', $request->b_id)
      ->get();

      foreach ($sample as  $value)
      {
        $list.="<label class='col-sm-6' style='padding:0px;'>
        <input name=\"mr_sample_style[]\" id=\"mr_sample_style\" type=\"checkbox\" class=\"ace\" value=\"$value->sample_id\">
        <span class=\"lbl\"> $value->sample_name</span>
        </label>
        ";
      }

      // Season List Query
      $seasonlist = "<option value=\"\">Select Season Name </option>";

      $seasons=Season::where('b_id', $request->b_id)->pluck('se_name','se_id');
      foreach ($seasons as $key => $value)
      {
        $seasonlist .= "<option value=\"$key\">$value</option>";
      }

      // Size Group List Query
      $sizelist = "<option value=\"\">Select Size Group </option>";

      $sizegroups=ProductSizeGroup::where('b_id', $request->b_id)->pluck('size_grp_name','id');
      foreach ($sizegroups as $key => $value)
      {
        $sizelist .= "<option value=\"$key\">$value</option>";
      }

      //return $list;
      /* Json multiple variable return*/
      return response()->json(['samplelist' => $list, 'selist' => $seasonlist,
      'sizelist' => $sizelist]);
    }
  }

  # store Style data
  public function store(Request $request)
  {
    $request->merge([
      'mr_buyer_b_id' => $request->b_id,
      'mr_season_se_id' => $request->se_id,
      'stl_type'=>$request->stl_order_type,
    ]);
    $validator = Validator::make($request->all(), [
      "stl_smv"          => "required|max:20",
      "stl_no"           => "required|max:30|unique:mr_style,stl_no,stl_type,mr_buyer_b_id,prd_type_id,mr_season_se_id"
    ]);
    
    if ($validator->fails()) {
      $failedRules = $validator->failed();

      if(isset($failedRules['stl_no']['CompositeUnique'])) {
        return back()
        //->withErrors($validator)
        ->withInput();
        toastr()->error("This value Buyer,Style Reference,Style Type, Product Type,Season already exists!");
      }else{
        foreach ($validator->errors()->all() as $message){
            toastr()->error($message);
        }
        return redirect()->back()->withErrors($validator)->withInput();
        
      }

    }
    $input = $request->all();
    // return $input;
    DB::beginTransaction();
    try {
      // get unit from hr_basic table

      $associate_id = auth()->user()->associate_id;
      $user_basic_info = Employee::select('as_unit_id')->where(['associate_id' => $associate_id])->first();

      if(isset($user_basic_info->as_id)) {
        $unit_id = $user_basic_info->as_unit_id;
      } else {
        // user basic info not found
        return back()
        ->withInput();
        toastr()->error("User unit not found in basic table.");
      }

      $data = new Style;
      // Style Image Upload
      if ($request->style_img_n != "" ) {
        $file     = $request->file('style_img_n');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        //dd(Image::make($file)->width());
        $file = Image::make($file);
        if($file->width()>800){

          $file = $file->resize(800, null, function ($constraint) {
                              $constraint->aspectRatio();
                          });
        }

        $dir  = 'assets/files/style/'.$filename;
        $file->save($dir);
        $stlimg = $dir;
      } else {
        $stlimg   = $request->style_img;
      }
      //dd($pdSizeList);exit;
      //$pdSizeList = DB::table('mr_product_type')->where('');
      //dd($pdSizeList[9]);exit;
      //$pd = $request->prd_type_id == 'undefined' ? 0:$request->prd_type_id;
      // Style Data Store
      /*$pdSizeList             = ProductType::where('prd_type_id',$request->prd_type_id)->first()->prd_type_name;*/
      $data->stl_type         = $request->stl_order_type;
      $data->mr_buyer_b_id    = $request->b_id;
      $data->prd_type_id      = $request->prd_type_id;
      $data->stl_product_name = $this->quoteReplaceHtmlEntry($request->stl_product_name);
      $data->stl_smv          = $this->quoteReplaceHtmlEntry($request->stl_smv);
      $data->stl_no           = $this->quoteReplaceHtmlEntry($request->stl_no);
      $data->gmt_id           = $request->gmt_id;
      $data->stl_description  = $this->quoteReplaceHtmlEntry($request->stl_description);
      $data->mr_season_se_id  = $request->se_id;
      $data->stl_img_link     = $stlimg;     //Image url
      $data->mr_brand_br_id   = $request->mr_brand_br_id;
      $data->stl_addedby      = (!empty(Auth::id())?(Auth::id()):null);
      $data->stl_updated_by   = null;
      $data->stl_updated_on   = null;
      $data->gender           = $request->gender;
      $data->unit_id          = $unit_id;

      if ($data->save()) {
        $stl_id = $data->id;
        $this->logFileWrite("New Style Saved", $stl_id );
        // Store Style Operation
        if (is_array($request->opr_id) && sizeof($request->opr_id) > 0)

        foreach($request->opr_id as $k=>$opr) {

          if (!empty($opr)) {
            OperationCost::insert([
              "mr_style_stl_id"     => $stl_id,
              "mr_operation_opr_id" => $opr,
              "opr_type"            => $request->opr_type[$k]
            ]);
          }
        }

        if (is_array($request->style_img_multi) && sizeof($request->style_img_multi) > 0)
        foreach($request->style_img_multi as $k=> $img) {
          if ($img != "" ) {
            $filename = uniqid() . '.' . $img->getClientOriginalExtension();
            $img = Image::make($img);
            if($img->width()>800){

              $img = $img->resize(800, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
            }

            $stlImgLink  = 'assets/files/style/'.$filename;
            $img->save($stlImgLink);
            StyleImage::insert([
              "mr_stl_id"     => $stl_id,
              "image" => $stlImgLink
            ]);
          }

        }

        // Store Style Special Machine
        if (is_array($request->machine_id) && sizeof($request->machine_id) > 0)
        foreach($request->machine_id as $machine) {
          if (!empty($machine))
          StyleSpecialMachine::insert([
            "stl_id"        => $stl_id,
            "spmachine_id"  => $machine
          ]);
        }

        // Store Style Sample
        if (is_array($request->mr_sample_style) && sizeof($request->mr_sample_style) > 0)
        foreach($request->mr_sample_style as $sample) {
          if (!empty($sample))
          SampleStyle::insert([
            "stl_id"    => $stl_id,
            "sample_id" => $sample
          ]);
        }

        // Store Style Size Group
        if (is_array($request->prdsz_id) && sizeof($request->prdsz_id) > 0)
        foreach($request->prdsz_id as $psize) {
          if (!empty($psize))
          StyleSizeGroup::insert([
            "mr_style_stl_id"           => $stl_id,
            "mr_product_size_group_id"  => $psize
          ]);

          $this->logFileWrite("Style Size Gruop Created", DB::getPdo()->lastInsertId());
        }

        // Store Wash
        if (is_array($request->wash) && sizeof($request->wash) > 0)
        foreach($request->wash as $swash) {
          if (!empty($swash))
          StlWashType::insert([
            "mr_style_stl_id" => $stl_id,
            "mr_wash_type_id" => $swash
          ]);
          $this->logFileWrite("Style Wash Inserted", DB::getPdo()->lastInsertId());
        }

        //------------store history--------------
        // StyleHistory::insert([
        //  "stl_id" => $stl_id,
        //  "stl_history_desc" => "Create",
        //  "stl_history_ip"   => $request->ip(),
        //  "stl_history_mac"  => $this->GetMAC(),
        //  "stl_history_userid" => auth()->user()->associate_id,
        // ]);
        //---------------------------------------

        DB::commit();
        return redirect('merch/style/style_new_edit/'.$stl_id);
        toastr()->success("Style Successfuly Created");
      } else {
        return back()->withInput();
        toastr()->error("Something Wrong, Please try again");
      }
    } catch (\Exception $e) {
      DB::rollback();
      $bug = $e->getMessage();
      toastr()->error($bug);
      return back()->withInput();
    }

  }
  # show list
  public function showList()
  {
    $b_permissions = explode(',', auth()->user()->buyer_permissions);
    $buyerList        = DB::table('mr_buyer as b')
    ->whereIn('b.b_id', $b_permissions)
    ->pluck('b.b_name', 'b.b_id')
    ->toArray();
    //$buyerList  = Buyer::pluck('b_name', 'b_id');
    $seasonList = Season::pluck('se_name','se_id');
    return view("merch/style/style_list", compact(
      "buyerList",
      "seasonList"
    ));
  }

  # get data
  public function getData()
  {
    $b_permissions = explode(',', auth()->user()->buyer_permissions);

    $data = DB::table('mr_style AS s')
        ->select(
            "s.stl_id",
            "s.stl_type",
            "s.prd_type_id",
            "s.stl_img_link",
            "s.stl_updated_on",
            "b.b_id",
            "b.b_name",
            "s.stl_no",
            "s.stl_product_name",
            "s.stl_smv",
            "br.br_name",
            "s.mr_season_se_id",
            "pt.prd_type_name",
            "se.se_name"
        )
        ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
        ->leftJoin('mr_product_type AS pt', 'pt.prd_type_id', '=', 's.prd_type_id')
        ->leftJoin('mr_season AS se', 'se.se_id', '=', 's.mr_season_se_id')
        ->leftJoin('mr_brand AS br', 'br.br_id', '=', 's.mr_brand_br_id')
        ->whereIn('b.b_id', $b_permissions)
        ->orderBy('s.stl_id', 'desc')
        ->get();

    return DataTables::of($data)

        ->editColumn('stl_img_link', function ($data) {
          if($data->stl_img_link == null){
            $imageUrl = "/assets/files/style/empty_style.jpg";
          }else{
            $imageUrl = "/$data->stl_img_link";
          }
          return '<img src="'.url('/').$imageUrl.'" width="30" height="40">';
        })

        ->editColumn('stl_type', function ($data) {
            if ($data->stl_type == "Bulk")
            {
                return "<span class='text-primary'>$data->stl_type</span>";
            }
            else
            {
                return "<span class='text-warning'>$data->stl_type</span>";
            }
        })
        ->editColumn('b_name', function ($data) {
            return ($data->b_name);
        })

        ->editColumn('action', function ($data) {


            $return = "<div class=\"btn-group\" style=\"width: 150px; \">";

                $return .= "<a href=".url('merch/style/style_new_edit/'.$data->stl_id)." class=\"btn btn-sm btn-primary\" data-toggle=\"tooltip\" title=\"Edit Style\" style=\"width:28px;\">
                    <i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                </a>
                <a href=".url('merch/style/style_profile/'.$data->stl_id)." class=\"btn btn-sm btn-info\" data-toggle=\"tooltip\" title=\"Show Style\">
                    <i class=\"ace-icon fa fa-eye bigger-120\"></i>
                </a>
                <a href=".url('merch/style/delete/'.$data->stl_id)." class=\"btn btn-sm btn-danger\" onClick=\"return window.confirm('Are you sure?')\" title=\"Delete\" data-toggle=\"tooltip\" style=\"width:28px;\">
                    <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                </a>

                <a href=".url('merch/style/create_bulk/?style_no='.$data->stl_id)." class=\"btn btn-sm btn-warning\" data-toggle=\"tooltip\" title=\"Create Bulk\">
                    <i class=\"ace-icon fa fa-archive bigger-120\"></i>
                </a>"
                ;


                // $return .= "<a href=".url('merch/bom/bom_bulk/'.$data->stl_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Create BOM for Bulk\">
                //     BOM
                // </a>";

            // $return .= "<a href=".url('merch/stylelibrary/style_details/'.$data->stl_id)." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
            //       <i class=\"ace-icon fa fa-eye bigger-120\"></i>
            //     </a>";
            $return .= "</div>";

            return $return;
        })
        ->rawColumns([
            'stl_img_link',
            'stl_type',
            'se_name',
            'b_name',
            'stl_no',
            'action'
        ])
        ->make(true);
  }

  # Delete Style
  public function styleDelete($id)
  {
      Style::where('stl_id', $id)->delete();
      OperationCost::where('mr_style_stl_id', $id)->delete();
      StyleSpecialMachine::where('stl_id', $id)->delete();
      SampleStyle::where('stl_id', $id)->delete();
      StyleSizeGroup::where('mr_style_stl_id', $id)->delete();
      StlWashType::where('mr_style_stl_id', $id)->delete();
      BomCosting::where('mr_style_stl_id', $id)->delete();
      BomOtherCosting::where('mr_style_stl_id', $id)->delete();
      OperationCost::where('mr_style_stl_id', $id)->delete();
      StyleCostApproval::where('mr_style_stl_id', $id)->delete();


      $this->logFileWrite("Style and Style related data Deleted", $id );
      return back()
      ->with('success', "Style Deleted Successfully!!");
  }

  # Style development edit form
  public function styleDevelopmentEditForm($id)
  {
    //$buyerList        = Buyer::pluck('b_name', 'b_id');
    $b_permissions    = explode(',', auth()->user()->buyer_permissions);
    $buyerList        = Buyer::whereIn('b_id', $b_permissions)->pluck('b_name', 'b_id')->toArray();
    $productTypeList  = ProductType::pluck('prd_type_name', 'prd_type_id');
    $operationList    = Operation::pluck('opr_name', 'opr_id');
    $machineList      = Spmachine::pluck('spmachine_name', 'spmachine_id');
    $garmentsTypeList = GarmentsType::pluck('gmt_name','gmt_id');
    $sizegroupList    = ProductSizeGroup::pluck('size_grp_name','id');
    $country          = Country::pluck('cnt_name','cnt_name');
    $product          = ProductSize::get();
    $sizegroup        = ProductSize::pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $buyer            = Buyer::pluck('b_name', 'b_id');
    $wash             = WashType::pluck('wash_name','id');
    $style = DB::table('mr_style AS s')
                ->select(
                    "s.*",
                    "b.b_name",
                    "b.b_id",
                    "p.*"
                )
                ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
                ->leftJoin('mr_product_type AS p', 'p.prd_type_id', '=', 's.prd_type_id')
                ->leftJoin('mr_garment_type AS g', 'g.gmt_id', '=', 's.gmt_id')
                ->where('s.stl_id',$id)
                ->first();
               // dd($style);
    $sampleTypeList   = SampleType::where('b_id',$style->b_id)->pluck('sample_name','sample_id');
    // $sizegroup        = ProductSize::where('b_id',$style->b_id)->where('size_grp_product_type',$style->prd_type_id)->pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $season  = Season::where('b_id','=',$style->b_id)->pluck('se_name','se_id');

    $styleOps = OperationCost::where('mr_style_stl_id', $id)->pluck('mr_operation_opr_id')->toArray();
    $brand    = Brand::where('b_id', $style->b_id)->pluck('br_name','br_id');

    //Operation List Show in Modal
    $operationList    = Operation::get();
    $operationData= '<div class="col-xs-12"><div class="checkbox">';
    foreach ($operationList as $operation) {
        $checked="";
        if($operation->opr_type==1){
            $checked.="checked readonly  onclick='return false;'";
        }
        if($operation->opr_type !=1 && in_array($operation->opr_id, $styleOps)){
            $checked.="checked";
        }

        $operationData.= "<label class='col-sm-2' style='padding:0px;'>
        <input name='operations[]' type='checkbox' class='ace' data-content-type='".$operation->opr_type."' value='".$operation->opr_id."'".$checked.">
        <span class='lbl'>".$operation->opr_name."</span>
        </label>";
      }
      $operationData.="</div></div>";


      //Selected Operation Show
      $selectedOpData='';
      $selectedStyleOps= Db::table('mr_style_operation_n_cost AS s')
      ->where('mr_style_stl_id', $id)
      ->select([
        's.style_op_id',
        'o.opr_id',
        'o.opr_name',
        'o.opr_type'
      ])
      ->leftJoin('mr_operation AS o', 'o.opr_id', 's.mr_operation_opr_id')
      ->get();
      $tr_end         = 0;
      $selectedOpData .= '<table class="table table-bordered" style="margin-bottom:0px;">';
      // $selectedOpData .= '<thead>';
      // $selectedOpData .= '<tr>';
      // $selectedOpData .= '<td colspan="3" class="text-center">Operations</td>';
      // $selectedOpData .= '</tr>';
      // $selectedOpData .= '</thead>';
      $selectedOpData .= '<tbody>';
      foreach ($selectedStyleOps as $k=>$selOps) {
        if(!empty($selOps->opr_name)){
          if(strlen((string)($k/10)) === 1) {
            $selectedOpData .= '<tr>';
            $tr_end = $k+9;
          }
          $selectedOpData .= '<td style="border-bottom: 1px solid lightgray;">'.$selOps->opr_name.'</td>';
          $selectedOpData .= '<input type="hidden" name="opr_id[]" value="'.$selOps->opr_id.'"></input>';
          $selectedOpData .= '<input type="hidden" name="opr_type[]" value="'.$selOps->opr_type.'"></input>';
          if($tr_end == 10) {
            $selectedOpData .= '</tr>';
          }
        }
      }
      $selectedOpData .= '</tbody>';
      $selectedOpData .= '</table>';

      //wash modal
      $washCategoryList = WashCategory::get();
      $selectedWash   = StlWashType::where('mr_style_stl_id', $id)->pluck('mr_wash_type_id')->toArray();
      $washData       = '<div class="col-xs-12"><div class="checkbox" id="washStoreDiv">';
      foreach ($washCategoryList as $washCategory) {
        // wash type
        $washData.= "<label class='col-sm-2' style='padding:0px;'>
          <span class='lbl'> ".$washCategory->category_name."</span>";
          if(count($washCategory->mr_wash_type) > 0) {
            $washData .= '<ul>';
            foreach($washCategory->mr_wash_type as $k=>$wash) {
              $checked = '';
              if(!empty($selectedWash)) {
                $checked = in_array($wash->id, $selectedWash)!==FALSE?'checked="checked"':'';
              }
              $washName  = $wash->wash_name;
              $washData .= "<li style='list-style-type: none;'>";
              $washData .= "<label style='padding:0px;'>";
              $washData .= "<input name='washType[]' type='checkbox' class='ace' value='".$wash->id."' ".$checked.">";
              $washData .= "<span class='lbl'> ".$washName."</span>";
              $washData .= "</label>";
              $washData .= "</li>";
            }
            $washData .= '</ul>';
          }
          $washData .= "</label>";
      }
      $washData .= "</div></div>";
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
    return view('merch/style/style_new_edit', compact(
      'buyerList',
      'country',
      'productTypeList',
      'operationList',
      'machineList',
      'garmentsTypeList',
      'sizegroupList',
      'sampleTypeList',
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

  public function removeGalleryImage(Request $request){
    $im_id = $request->im_id;
    StyleImage::where('id',$im_id )->delete();
    return 'success';
  }

  # Update Style data
  public function styleUpdate(Request $request)
  {
    // dd($request->all());
    $request->merge([
      'mr_buyer_b_id' => $request->b_id,
      'mr_season_se_id' => $request->se_id,
      'stl_type'=>$request->stl_order_type,
    ]);
    $validator = Validator::make($request->all(), [
      "stl_order_type"   => "required|max:11",
      "b_id"             => "required|max:11",
      "prd_type_id"      => "required|max:11",
      //"stl_product_name" => "required|max:50",
      "stl_smv"          => "required|max:20",
      // "stl_no"           => "required|max:30|unique:mr_style,stl_no,stl_type,mr_buyer_b_id,prd_type_id,mr_season_se_id,ignore-$request->style_id",
      "stl_no"           => "required",
      "gmt_id"           => "required|max:11",
      //"stl_description"  => "required|max:128",
      "se_id"            => "required|max:11",
      //"wash"             => "required|max:20",
      "opr_id.*"         => "max:11",
      "sp_machine_id.*"  => "max:11"
      // "mr_sample_style.*" => "max:11"
    ]);

    //dd($request->file('style_img_n'));
    $getStyle = Style::where('stl_id', $request->style_id)->first();

    if ($validator->fails()) {
      $failedRules = $validator->failed();

      if(isset($failedRules['stl_no']['CompositeUnique'])) {
        return back()
        //->withErrors($validator)
        ->withInput();
        toastr()->error("This value Buyer,Style Reference,Style Type, Product Type,Season already exists!");
      }else{
        foreach ($validator->errors()->all() as $message){
            toastr()->error($message);
        }
        return redirect()->back()->withErrors($validator)->withInput();
        
      }

    }
    $input = $request->all();
    // return $input;
    DB::beginTransaction();
    try {
      //$data = new Style;
      // Style Image Upload
      if ($request->style_img_n != "" ) {
        $file = $request->file('style_img_n');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file = Image::make($file)->resize(800, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                    });
        //dd($file);
        $dir  = 'assets/files/style/'.$filename;
        //$file->move( public_path($dir) , $filename );
        $file->save($dir);
        $stlimg = $dir;
      } else {
        $stlimg = $getStyle->stl_img_link;
      }
      $pdSizeList = DB::table('mr_product_type')->pluck('prd_type_id','prd_type_name');

      // Style Data Update
      $style_record = Style::where('stl_id', $request->style_id)->update([
        /*'stl_type'         => $request->stl_order_type,*/
        'mr_buyer_b_id'    => $request->b_id,
        'prd_type_id'      => $request->prd_type_id,
        'stl_product_name' => $this->quoteReplaceHtmlEntry($request->stl_product_name),
        'stl_smv'          => $this->quoteReplaceHtmlEntry($request->stl_smv),
        'stl_no'           => $this->quoteReplaceHtmlEntry($request->stl_no),
        'gmt_id'           => $request->gmt_id,
        'stl_description'  => $this->quoteReplaceHtmlEntry($request->stl_description),
        'gender'           => $request->gender,
        'mr_season_se_id'  => $request->se_id,
        'stl_img_link'     => $stlimg,     //Image url
        'mr_brand_br_id'   =>$request->mr_brand_br_id,
        'stl_updated_by'   => (!empty(Auth::id())?(Auth::id()):null),
        'stl_updated_on'   => null
      ]);


      if (is_array($request->style_img_multi) && sizeof($request->style_img_multi) > 0)
        foreach($request->style_img_multi as $k=> $img) {
          if ($img != "" ) {
            $filename = uniqid() . '.' . $img->getClientOriginalExtension();
            $img = Image::make($img);
            if($img->width()>800){

              $img = $img->resize(800, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
            }

            $stlImgLink  = 'assets/files/style/'.$filename;
            $img->save($stlImgLink);
            StyleImage::insert([
              "mr_stl_id"     => $request->style_id,
              "image" => $stlImgLink
            ]);
          }

        }

        if (is_array($request->style_img_multi_up) && sizeof($request->style_img_multi_up) > 0)
        foreach($request->style_img_multi_up as $k=> $img) {
          if ($img != "" ) {
            $filename = uniqid() . '.' . $img->getClientOriginalExtension();
            $img = Image::make($img);
            if($img->width()>800){

              $img = $img->resize(800, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
            }
            $id = $request->img_multi_up[$k];
            $stlImgLink  = 'assets/files/style/'.$filename;
            $img->save($stlImgLink);

            $styleImage = StyleImage::find($id);
            $styleImage->image = $stlImgLink;
            $styleImage->save();
          }

        }


      // Update Style Operation
      if (is_array($request->opr_id) && sizeof($request->opr_id) > 0) {
        // delete previous operations then insert new
        OperationCost::where('mr_style_stl_id', $request->style_id)->delete();
        foreach($request->opr_id as $k=>$opr) {
          if (!empty($opr))
          OperationCost::insert([
            "mr_style_stl_id"       => $request->style_id,
            "mr_operation_opr_id"   => $opr,
            "opr_type"              => $request->opr_type[$k]
          ]);
        }
      }else{
        OperationCost::where('mr_style_stl_id', $request->style_id)->delete();
      }

      // Update Style Special Machine
      if (is_array($request->machine_id) && sizeof($request->machine_id) > 0) {
        StyleSpecialMachine::where('stl_id', $request->style_id)->delete();
        foreach($request->machine_id as $machine) {
          if (!empty($machine))
          //dd($machine);exit;
          StyleSpecialMachine::insert([
            "stl_id"       => $request->style_id,
            "spmachine_id" => $machine
          ]);
        }
      }else{
        StyleSpecialMachine::where('stl_id', $request->style_id)->delete();
      }

      // Update Style Sample
      if (is_array($request->mr_sample_style) && sizeof($request->mr_sample_style) > 0) {
        SampleStyle::where('stl_id', $request->style_id)->delete();
        foreach($request->mr_sample_style as $sample) {
          if (!empty($sample))
          SampleStyle::insert([
            "stl_id"    => $request->style_id,
            "sample_id" => $sample
          ]);
        }
      }else{
        SampleStyle::where('stl_id', $request->style_id)->delete();

      }

      // Update Style Size Group
      if (is_array($request->prdsz_id) && sizeof($request->prdsz_id) > 0) {
        StyleSizeGroup::where('mr_style_stl_id', $request->style_id)->delete();
        foreach($request->prdsz_id as $psize) {
          if (!empty($psize))
          StyleSizeGroup::insert([
            "mr_style_stl_id" => $request->style_id,
            "mr_product_size_group_id" => $psize
          ]);
          $this->logFileWrite("Style Size Gruop Created", DB::getPdo()->lastInsertId());
        }
      }else{
        StyleSizeGroup::where('mr_style_stl_id', $request->style_id)->delete();

      }

      // Update Wash
      if (is_array($request->wash) && sizeof($request->wash) > 0) {
        StlWashType::where('mr_style_stl_id', $request->style_id)->delete();
        foreach($request->wash as $swash) {
          if (!empty($swash)){
          StlWashType::insert([
            "mr_style_stl_id" => $request->style_id,
            "mr_wash_type_id" => $swash
          ]);
        }
          //$this->logFileWrite("Style Wash Inserted", DB::getPdo()->lastInsertId());
        }
      }else{
        StlWashType::where('mr_style_stl_id', $request->style_id)->delete();
      }
      //------------store history--------------
      // StyleHistory::insert([
      //  "stl_id" => $stl_id,
      //  "stl_history_desc" => "Create",
      //  "stl_history_ip"   => $request->ip(),
      //  "stl_history_mac"  => $this->GetMAC(),
      //  "stl_history_userid" => auth()->user()->associate_id,
      // ]);
      //---------------------------------------

      $this->logFileWrite("Style and Style related data Updated", $request->style_id );
      toastr()->success('Style Update Successfuly');
      DB::commit();
      return redirect('merch/style/style_new_edit/'.$request->style_id)->with('success', 'Updated Successful.');
      // return back()->with('success', 'Updated Successful.');
    } catch (\Exception $e) {
      DB::rollback();
      $bug = $e->getMessage();
      toastr()->error($bug);
      return back()->withInput();
    }
  }

  # Style Copy form
  public function styleCopyForm($id)
  {
    $buyerList        = Buyer::pluck('b_name', 'b_id');
    $productTypeList  = ProductType::pluck('prd_type_name', 'prd_type_id');
    $operationList    = Operation::pluck('opr_name', 'opr_id');
    $machineList      = Spmachine::pluck('spmachine_name', 'spmachine_id');
    $garmentsTypeList = GarmentsType::pluck('gmt_name','gmt_id');
    $sizegroupList    = ProductSizeGroup::pluck('size_grp_name','id');
    $sampleTypeList   = SampleType::pluck('sample_name','sample_id');
    $country          = Country::pluck('cnt_name','cnt_name');
    $product          = ProductSize::get();
    $sizegroup        = ProductSize::pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $buyer            = Buyer::pluck('b_name', 'b_id');
    $brand            = Brand::pluck('br_name', 'br_id');
    $season           = Season::pluck('se_name','se_id');
    $wash             = WashType::pluck('wash_name','id');

    // $operationStlList = opera::where(,$request->)  mr_style_operation_n_cost
    //                   get();mr_operation

    $style = DB::table('mr_style AS s')
    ->select(
      "s.*",
      "b.b_name",
      "b.b_id",
      "p.*"
      )
    ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
    ->leftJoin('mr_product_type AS p', 'p.prd_type_id', '=', 's.prd_type_id')
    ->leftJoin('mr_garment_type AS g', 'g.gmt_id', '=', 's.gmt_id')
    ->where('s.stl_id',$id)
    ->first();

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

    return view('merch/style/style_copy', compact(
      'buyerList',
      'country',
      'productTypeList',
      'operationList',
      'machineList',
      'garmentsTypeList',
      'sizegroupList',
      'sampleTypeList',
      'buyer',
      'brand',
      'sizegroup',
      'stlsize',
      'wash',
      'stlwash',
      'season',
      'style'
    ));
  }

  # store Style copy data
  public function storeCopy(Request $request)
  {
    // dd($request->all());exit;
    $validator = Validator::make($request->all(), [
      "style_id" => "required"
    ]);
    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput()
        ->with('error', "Incorrect Input!!");
    }
    DB::beginTransaction();
    try {
      $styleToCopy = Style::where('stl_id',$request->style_id)->first();
      $data = new Style;
      // Style Data Store
      $data->stl_type         = $styleToCopy->stl_type;
      $data->mr_buyer_b_id    = $styleToCopy->mr_buyer_b_id;
      $data->prd_type_id      = $styleToCopy->prd_type_id;
      $data->stl_product_name = $this->quoteReplaceHtmlEntry($styleToCopy->stl_product_name);
      $data->stl_smv          = $this->quoteReplaceHtmlEntry($styleToCopy->stl_smv);
      $data->stl_no           = $this->quoteReplaceHtmlEntry($request->stl_no);
      $data->gmt_id           = $styleToCopy->gmt_id;
      $data->stl_description  = $this->quoteReplaceHtmlEntry($styleToCopy->stl_description);
      $data->mr_season_se_id  = $styleToCopy->mr_season_se_id;
      $data->stl_img_link     = $styleToCopy->stl_img_link;     //Image url
      $data->stl_addedby      = (!empty(Auth::id())?(Auth::id()):null);
      $data->stl_updated_by   = null;
      $data->stl_updated_on   = null;
      $data->stl_status       = 0;
      $data->gender           = $styleToCopy->gender;
      $data->unit_id          = $styleToCopy->unit_id;

      if ($data->save()) {
        $stl_id = $data->id;
        // Store Style Special Machine
        $machineToCopy = StyleSpecialMachine::where('stl_id',$request->style_id)->get();
        foreach ($machineToCopy as $machine) {
          StyleSpecialMachine::insert([
            "stl_id"        => $stl_id,
            "spmachine_id" => $machine->spmachine_id
          ]);
        }

        $imageToCopy = StyleImage::where('mr_stl_id',$request->style_id)->get();
        foreach ($imageToCopy as $image) {
          StyleImage::insert([
            "mr_stl_id"    => $stl_id,
            "image" => $image->image
          ]);
        }

        $sampleToCopy = SampleStyle::where('stl_id',$request->style_id)->get();
        foreach ($sampleToCopy as $sample) {
          SampleStyle::insert([
            "stl_id"    => $stl_id,
            "sample_id" => $sample->sample_id
          ]);
        }
        $sizegroupToCopy = StyleSizeGroup::where('mr_style_stl_id',$request->style_id)->get();
        foreach ($sizegroupToCopy as $sizegroup) {
          StyleSizeGroup::insert([
            "mr_style_stl_id" => $stl_id,
            "mr_product_size_group_id" => $sizegroup->mr_product_size_group_id
          ]);
        }
        $washToCopy = StlWashType::where('mr_style_stl_id',$request->style_id)->get();
        foreach ($washToCopy as $wash) {
          StlWashType::insert([
            "mr_style_stl_id" => $stl_id,
            "mr_wash_type_id" => $wash->mr_wash_type_id
          ]);
        }

        // Data copy from BomCosting and store
        $bomlist= BomCosting::where('mr_style_stl_id',$request->style_id)->get();

        if (!empty($bomlist)){
          foreach ($bomlist as  $bom) {
            BomCosting::insert([
              "mr_style_stl_id"    => $stl_id,
              "mr_material_category_mcat_id" => $bom->mr_material_category_mcat_id,
              "mr_cat_item_id"     => $bom->mr_cat_item_id,
              "item_description"   => $bom->item_description,
              "clr_id"             => $bom->clr_id,
              "size"               => $bom->size,
              "mr_supplier_sup_id" => $bom->mr_supplier_sup_id,
              "mr_article_id"      => $bom->mr_article_id,
              "mr_composition_id"  => $bom->mr_composition_id,
              "mr_construction_id" => $bom->mr_construction_id,
              "uom"                => $bom->uom,
              "consumption"        => $bom->consumption,
              "extra_percent"      => $bom->extra_percent,
              "bom_term"           => null
            ]);
          }
        }

        // Data copy from Bom Other Costing and store
        // $otherbomlist= BomOtherCosting::where('mr_style_stl_id', $request->style_id)->get();
        // if (!empty($otherbomlist)){
        //   foreach ($otherbomlist as  $otherbom) {
        //     BomOtherCosting::insert([
        //       "mr_style_stl_id"             => $stl_id,
        //       "testing_cost"                => $otherbom->testing_cost,
        //       "cm"                          => $otherbom->cm,
        //       "commercial_cost"             => $otherbom->commercial_cost,
        //       "net_fob"                     => $otherbom->net_fob,
        //       "buyer_comission_percent"     => $otherbom->buyer_comission_percent,
        //       "buyer_fob"                   => $otherbom->buyer_fob,
        //       "agent_comission_percent"     => $otherbom->agent_comission_percent,
        //       "agent_fob"                   => $otherbom->agent_fob
        //     ]);
        //   }
        // }

        // Data copy from Style operation cost and store
        $Operationcostlist= OperationCost::where('mr_style_stl_id', $request->style_id)->get();
        if (!empty($Operationcostlist)){
          foreach ($Operationcostlist as  $opcost) {
            OperationCost::insert([
              "mr_style_stl_id"          => $stl_id,
              "mr_operation_opr_id"      => $opcost->mr_operation_opr_id,
              "opr_type"                 => $opcost->opr_type,
              "uom"                      => $opcost->uom
              //"unit_price"               => $opcost->unit_price

            ]);
          }
        }

        // Data copy from Style costing approval and store
        // $stlapplovalList= StyleCostApproval::where('mr_style_stl_id', $request->style_id)->get();
        // if (!empty($stlapplovalList)){
        //   foreach ($stlapplovalList as  $approval) {
        //     StyleCostApproval::insert([
        //       "mr_style_stl_id" =>  $stl_id,
        //       "title"           =>  $approval->title,
        //       "submit_by"       =>  $approval->submit_by, //auth()->user()->associate_id
        //       "submit_to"       =>  $approval->submit_to,
        //       "comments"        =>  $approval->comments,
        //       "status"          =>  $approval->status,
        //       "created_on"      =>  $approval->created_on,
        //       "level"           =>  $approval->level
        //     ]);
        //   }

        // }

        DB::commit();
        $this->logFileWrite("Style ".$request->style_id." copied to ", $stl_id );

        return redirect()->back()
        ->with('success', "Style Copied Successfuly!!");
      } else {
        return back()->withInput()->with('error', 'Please try again.');
      }
    } catch (\Exception $e) {
      DB::rollback();
      $bug = $e->getMessage();
      return redirect()->back()->with('error', $bug);
    }
  }

  # Style Bulk Form
  public function styleBulkForm()
  {
    // $country   = Country::pluck('cnt_name','cnt_name');
    // $brand     = Brand::pluck('br_name', 'br_id');
    // return view('merch/style/style_new_bulk', compact(
    //     'country',
    //     'brand',
    //     'stylelist'
    // ));
    //$stylelist = Style::groupBy('stl_no')->pluck('stl_no','stl_id');
    $stylelist=Style::groupBy('stl_no')
      ->havingRaw('COUNT(stl_type) <= 1')
      ->orderBy('stl_id', 'desc')
      ->pluck('stl_no','stl_id');
    $buyerList        = Buyer::pluck('b_name', 'b_id');
    $productTypeList  = ProductType::pluck('prd_type_name', 'prd_type_id');
    $operationList    = Operation::pluck('opr_name', 'opr_id');
    $machineList      = Spmachine::pluck('spmachine_name', 'spmachine_id');
    $garmentsTypeList = GarmentsType::pluck('gmt_name','gmt_id');
    $sizegroupList    = ProductSizeGroup::pluck('size_grp_name','id');
    $sampleTypeList   = SampleType::pluck('sample_name','sample_id');
    $country          = Country::pluck('cnt_name','cnt_name');
    $product          = ProductSize::get();
    $sizegroup        = ProductSize::pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $buyer            = Buyer::pluck('b_name', 'b_id');
    $brand            = Brand::pluck('br_name', 'br_id');
    $season           = Season::pluck('se_name','se_id');
    $wash             = WashType::pluck('wash_name','id');
    // Product::groupBy('category_id')->havingRaw('COUNT(*) > 1')->get();

    return view('merch/style/style_new_bulk', compact(
      'buyerList',
      'country',
      'productTypeList',
      'operationList',
      'machineList',
      'garmentsTypeList',
      'sizegroupList',
      'sampleTypeList',
      'buyer',
      'brand',
      'sizegroup',
      'wash',
      'season',
      'stylelist'
    ));
  }

  public function styleCopySearchForm(Request $request)
  {
    $stylelist=Style::groupBy('stl_no')
                    ->orderBy('stl_id', 'desc')
                    ->pluck('stl_no','stl_id');
    if(!empty($request->style_no)){
      $id = $request->style_no;
      $buyerList        = Buyer::pluck('b_name', 'b_id');
      $productTypeList  = ProductType::pluck('prd_type_name', 'prd_type_id');
      $operationList    = Operation::pluck('opr_name', 'opr_id');
      $machineList      = Spmachine::pluck('spmachine_name', 'spmachine_id');
      $garmentsTypeList = GarmentsType::pluck('gmt_name','gmt_id');
      $sizegroupList    = ProductSizeGroup::pluck('size_grp_name','id');
      $sampleTypeList   = SampleType::pluck('sample_name','sample_id');
      $country          = Country::pluck('cnt_name','cnt_name');
      $product          = ProductSize::get();
      $sizegroup        = ProductSize::pluck('mr_product_pallete_name', 'mr_product_size_group_id');
      $buyer            = Buyer::pluck('b_name', 'b_id');
      $brand            = Brand::pluck('br_name', 'br_id');
      $season           = Season::pluck('se_name','se_id');
      $wash             = WashType::pluck('wash_name','id');

    // $operationStlList = opera::where(,$request->)  mr_style_operation_n_cost
    //                   get();mr_operation

      $style = DB::table('mr_style AS s')
        ->select(
          "s.*",
          "b.b_name",
          "b.b_id",
          "p.*"
          )
        ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
        ->leftJoin('mr_product_type AS p', 'p.prd_type_id', '=', 's.prd_type_id')
        ->leftJoin('mr_garment_type AS g', 'g.gmt_id', '=', 's.gmt_id')
        ->where('s.stl_id',$id)
        ->first();

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

      return view('merch/style/style_new_copy', compact(
        'stylelist',
        'buyerList',
        'country',
        'productTypeList',
        'operationList',
        'machineList',
        'garmentsTypeList',
        'sizegroupList',
        'sampleTypeList',
        'buyer',
        'brand',
        'sizegroup',
        'stlsize',
        'wash',
        'stlwash',
        'season',
        'style'
      ));
    } else {
      return view('merch/style/style_new_copy', compact('stylelist'));
    }
  }

  # store Style Bulk data
  public function storeBulk(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "style_id" => "required"
    ]);
    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput()
        ->with('error', "Incorrect Input!!");
    }
    DB::beginTransaction();
    try {
      $data = new Style;
      // Style Image Upload
      //$stlimg = null;
      //
      #----------- Store Bulk Same as Developement Style (Fetch data from develpoment style)---#
      $development= DB::table("mr_style")
                      ->where('stl_id', $request->style_id)
                      ->first();
      // Style Data Store
      $data->stl_type         = "Bulk";
      $data->mr_buyer_b_id    = $development->mr_buyer_b_id;
      $data->stl_no           = $this->quoteReplaceHtmlEntry($development->stl_no);
      $data->prd_type_id      = $development->prd_type_id;
      $data->gmt_id           = $development->gmt_id;
      $data->stl_product_name = $this->quoteReplaceHtmlEntry($development->stl_product_name);
      $data->stl_description  = $this->quoteReplaceHtmlEntry($development->stl_description);
      $data->mr_season_se_id  = $development->mr_season_se_id;
      $data->mr_brand_br_id   = $development->mr_brand_br_id;
      $data->stl_smv          = $this->quoteReplaceHtmlEntry($development->stl_smv);
      $data->stl_img_link     = $development->stl_img_link;   //Image url
      $data->stl_addedby      = (!empty(Auth::id())?(Auth::id()):null);
      $data->stl_updated_by   = null;
      $data->stl_updated_on   = null;
      $data->stl_status       = $development->stl_status;
      $data->gender           = $development->gender;
      $data->unit_id          = $development->unit_id;
      if ($data->save()) {
        $stl_id = $data->id;
        // Store Style Operation
        // if (is_array($request->opr_id) && sizeof($request->opr_id) > 0)
        // foreach($request->opr_id as $opr)
        // {
        //     if (!empty($opr))
        //     OperationCost::insert([
        //         "mr_style_stl_id"     => $stl_id,
        //         "mr_operation_opr_id" => $opr
        //     ]);
        // }

        $imageToCopy = StyleImage::where('mr_stl_id',$request->style_id)->get();
        foreach ($imageToCopy as $image) {
          StyleImage::insert([
            "mr_stl_id"    => $stl_id,
            "image" => $image->image
          ]);
        }
        // Store Style Special Machine
        $machineToCopy = StyleSpecialMachine::where('stl_id',$request->style_id)->get();
        foreach ($machineToCopy as $machine) {
          StyleSpecialMachine::insert([
            "stl_id"        => $stl_id,
            "spmachine_id" => $machine->spmachine_id
          ]);
        }

        $sampleToCopy = SampleStyle::where('stl_id',$request->style_id)->get();
        foreach ($sampleToCopy as $sample) {
          SampleStyle::insert([
            "stl_id"    => $stl_id,
            "sample_id" => $sample->sample_id
          ]);
        }

        $sizegroupToCopy = StyleSizeGroup::where('mr_style_stl_id',$request->style_id)->get();
        foreach ($sizegroupToCopy as $sizegroup) {
          StyleSizeGroup::insert([
            "mr_style_stl_id" => $stl_id,
            "mr_product_size_group_id" => $sizegroup->mr_product_size_group_id
          ]);
        }

        $washToCopy = StlWashType::where('mr_style_stl_id',$request->style_id)->get();
        foreach ($washToCopy as $wash) {
          StlWashType::insert([
            "mr_style_stl_id" => $stl_id,
            "mr_wash_type_id" => $wash->mr_wash_type_id
          ]);
        }
        // Data copy from BomCosting and store
        $bomlist= BomCosting::where('mr_style_stl_id',$request->style_id)->get();
        // dd($request->style_id);
        if (!empty($bomlist)){
          foreach ($bomlist as  $bom) {
            BomCosting::insert([
              "mr_style_stl_id"    => $stl_id,
              "mr_material_category_mcat_id" => $bom->mr_material_category_mcat_id,
              "mr_cat_item_id"     => $bom->mr_cat_item_id,
              "item_description"   => $bom->item_description,
              "clr_id"             => $bom->clr_id,
              "size"               => $bom->size,
              "mr_supplier_sup_id" => $bom->mr_supplier_sup_id,
              "mr_article_id"      => $bom->mr_article_id,
              "mr_composition_id"  => $bom->mr_composition_id,
              "mr_construction_id" => $bom->mr_construction_id,
              "uom"                => $bom->uom,
              "consumption"        => $bom->consumption,
              "extra_percent"      => $bom->extra_percent,
              "bom_term"           => $bom->bom_term,
              "precost_fob"        => $bom->precost_fob,
              "precost_lc"         => $bom->precost_lc,
              "precost_freight"    => $bom->precost_freight,
              "precost_req_qty"    => $bom->precost_req_qty,
              "precost_unit_price" => $bom->precost_unit_price,
              "precost_value"      => $bom->precost_value
            ]);
          }
        }

        // Data copy from Bom Other Costing and store
        $otherbomlist= BomOtherCosting::where('mr_style_stl_id', $request->style_id)->get();
        if (!empty($otherbomlist)){
          foreach ($otherbomlist as  $otherbom) {
            BomOtherCosting::insert([
              "mr_style_stl_id"              => $stl_id,
              "testing_cost"                => $otherbom->testing_cost,
              "cm"                          => $otherbom->cm,
              "commercial_cost"             => $otherbom->commercial_cost,
              "net_fob"                     => $otherbom->net_fob,
              "buyer_comission_percent"     => $otherbom->buyer_comission_percent,
              "buyer_fob"                   => $otherbom->buyer_fob,
              "agent_comission_percent"     => $otherbom->agent_comission_percent,
              "agent_fob"                   => $otherbom->agent_fob
            ]);
          }
        }

        // Data copy from Style operation cost and store
        $Operationcostlist= OperationCost::where('mr_style_stl_id', $request->style_id)->get();
        if (!empty($otherbomlist)){
          foreach ($Operationcostlist as  $opcost) {
            OperationCost::insert([
              "mr_style_stl_id"          => $stl_id,
              "mr_operation_opr_id"      => $opcost->mr_operation_opr_id,
              "opr_type"                 => $opcost->opr_type,
              "uom"                      => $opcost->uom,
              "unit_price"               => $opcost->unit_price
            ]);
          }
        }

        // Data copy from Style costing approval and store
        $stlapplovalList= StyleCostApproval::where('mr_style_stl_id', $request->style_id)->get();
        if (!empty($stlapplovalList)){
          foreach ($stlapplovalList as  $approval) {
            StyleCostApproval::insert([
              "mr_style_stl_id" => $stl_id,
              "title"           => $approval->title,
              "submit_by"       =>  $approval->submit_by, //auth()->user()->associate_id
              "submit_to"       => $approval->submit_to,
              "comments"        => $approval->comments,
              "status"          =>  $approval->status,
              "created_on"      =>  $approval->created_on,
              "level"           =>  $approval->level

            ]);
          }

          $this->logFileWrite("Style Bulk Created", $stl_id);
        }
        DB::commit();
        
        return redirect('merch/style/style_new_edit/'.$stl_id)
        ->with('success', 'Bulk Created Successfully !!');
      } else {
        return back()->withInput()->with('error', 'Please try again.');
      }
    } catch (\Exception $e) {
      DB::rollback();
      $bug = $e->getMessage();
      return redirect()->back()->with('error', $bug);
    }
  }

  public function getStyleSteps($id){
    $data = [];
    $data['bom'] = DB::table("mr_stl_bom_n_costing")
                    ->where('mr_style_stl_id', $id)
                    ->first();

    $data['costing'] = DB::table("mr_stl_bom_other_costing")
                      ->where('mr_style_stl_id', $id)
                      ->first();

    $data['approval'] = DB::table('mr_stl_costing_approval')
                        ->leftJoin('users','mr_stl_costing_approval.submit_to','users.associate_id')
                        ->where('mr_style_stl_id', $id)
                        ->first();

    return view("merch/style/style_steps", compact('data','id'))->render();

  }

  public function getStyleProfile($styleId=null)
  {
    $stylebom_id=$styleId;
    $style = DB::table("mr_style AS s")
                ->select(
                  "s.stl_id",
                  "s.stl_type",
                  "s.stl_no",
                  "b.b_name",
                  "t.prd_type_name",
                  "g.gmt_name",
                  "s.stl_product_name",
                  "s.stl_description",
                  "se.se_name",
                  "s.stl_smv",
                  "s.stl_img_link",
                  "s.stl_addedby",
                  "s.stl_added_on",
                  "s.stl_updated_by",
                  "s.stl_updated_on",
                  "s.stl_status"
                  )
                  ->leftJoin("mr_buyer AS b", "b.b_id", "=", "s.mr_buyer_b_id")
                  ->whereIn('b.b_id', auth()->user()->buyer_permissions())
                  ->leftJoin("mr_product_type AS t", "t.prd_type_id", "=", "s.prd_type_id")
                  ->leftJoin("mr_garment_type AS g", "g.gmt_id", "=", "s.gmt_id")
                  ->leftJoin("mr_season AS se", "se.se_id", "=", "s.mr_season_se_id")
                  ->where("s.stl_id", $stylebom_id)
                  ->first();
              //dd($style);exit;
              $styleImages = DB::table('mr_style_image')->where('mr_stl_id',$stylebom_id)->get();
      //sampleTypes
      $style_steps = $this->getStyleSteps($styleId);
      $samples = DB::table("mr_stl_sample AS ss")
                    ->select(DB::raw("GROUP_CONCAT(st.sample_name SEPARATOR ', ') AS name"))
                    ->leftJoin("mr_sample_type AS st", "st.sample_id", "ss.sample_id")
                    ->where("ss.stl_id", $stylebom_id)
                    ->first();

      //operations
      $operations = DB::table("mr_style_operation_n_cost AS oc")
                      ->select("o.opr_name")
                      ->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
                      ->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
                      ->where("oc.mr_style_stl_id", $stylebom_id)
                      ->first();

      //machines
      $machines = DB::table("mr_style_sp_machine AS sm")
                    ->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
                    ->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
                    ->where("sm.stl_id", $stylebom_id)
                    ->first();


      //style bom information
      $styleCatMcats = DB::table("mr_stl_bom_n_costing")
                          ->leftJoin('mr_material_category','mr_stl_bom_n_costing.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
                          ->leftJoin('mr_cat_item','mr_stl_bom_n_costing.mr_cat_item_id','=','mr_cat_item.id')
                          ->leftJoin('mr_supplier','mr_stl_bom_n_costing.mr_supplier_sup_id','=','mr_supplier.sup_id')
                          ->leftJoin('mr_article','mr_stl_bom_n_costing.mr_article_id','=','mr_article.id')
                          ->leftJoin('mr_composition','mr_stl_bom_n_costing.mr_composition_id','=','mr_composition.id')
                          ->leftJoin('mr_construction','mr_stl_bom_n_costing.mr_construction_id','=','mr_construction.id')
                          ->leftJoin('mr_material_color','mr_stl_bom_n_costing.clr_id','=','mr_material_color.clr_id')
                          ->where('mr_stl_bom_n_costing.mr_style_stl_id',$stylebom_id)
                          ->get();
      $styleCatMcatFabs = DB::table("mr_stl_bom_n_costing")
                            ->leftJoin('mr_material_category','mr_stl_bom_n_costing.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
                            ->leftJoin('mr_cat_item','mr_stl_bom_n_costing.mr_cat_item_id','=','mr_cat_item.id')
                            ->leftJoin('mr_supplier','mr_stl_bom_n_costing.mr_supplier_sup_id','=','mr_supplier.sup_id')
                            ->leftJoin('mr_article','mr_stl_bom_n_costing.mr_article_id','=','mr_article.id')
                            ->leftJoin('mr_composition','mr_stl_bom_n_costing.mr_composition_id','=','mr_composition.id')
                            ->leftJoin('mr_construction','mr_stl_bom_n_costing.mr_construction_id','=','mr_construction.id')
                            ->leftJoin('mr_material_color','mr_stl_bom_n_costing.clr_id','=','mr_material_color.clr_id')
                            ->where('mr_stl_bom_n_costing.mr_style_stl_id',$stylebom_id)
                            ->where('mr_stl_bom_n_costing.mr_material_category_mcat_id',1)
                            ->get();

      $styleCatMcatSwings = DB::table("mr_stl_bom_n_costing")
                              ->leftJoin('mr_material_category','mr_stl_bom_n_costing.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
                              ->leftJoin('mr_cat_item','mr_stl_bom_n_costing.mr_cat_item_id','=','mr_cat_item.id')
                              ->leftJoin('mr_supplier','mr_stl_bom_n_costing.mr_supplier_sup_id','=','mr_supplier.sup_id')
                              ->leftJoin('mr_article','mr_stl_bom_n_costing.mr_article_id','=','mr_article.id')
                              ->leftJoin('mr_composition','mr_stl_bom_n_costing.mr_composition_id','=','mr_composition.id')
                              ->leftJoin('mr_construction','mr_stl_bom_n_costing.mr_construction_id','=','mr_construction.id')
                              ->leftJoin('mr_material_color','mr_stl_bom_n_costing.clr_id','=','mr_material_color.clr_id')
                              ->where('mr_stl_bom_n_costing.mr_style_stl_id',$stylebom_id)
                              ->where('mr_stl_bom_n_costing.mr_material_category_mcat_id',2)
                              ->get();

      $styleCatMcatFinishing = DB::table("mr_stl_bom_n_costing")
                                ->leftJoin('mr_material_category','mr_stl_bom_n_costing.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
                                ->leftJoin('mr_cat_item','mr_stl_bom_n_costing.mr_cat_item_id','=','mr_cat_item.id')
                                ->leftJoin('mr_supplier','mr_stl_bom_n_costing.mr_supplier_sup_id','=','mr_supplier.sup_id')
                                ->leftJoin('mr_article','mr_stl_bom_n_costing.mr_article_id','=','mr_article.id')
                                ->leftJoin('mr_composition','mr_stl_bom_n_costing.mr_composition_id','=','mr_composition.id')
                                ->leftJoin('mr_construction','mr_stl_bom_n_costing.mr_construction_id','=','mr_construction.id')
                                ->leftJoin('mr_material_color','mr_stl_bom_n_costing.clr_id','=','mr_material_color.clr_id')
                                ->where('mr_stl_bom_n_costing.mr_style_stl_id',$stylebom_id)
                                ->where('mr_stl_bom_n_costing.mr_material_category_mcat_id',3)
                                ->get();
      //dd($styleCatMcats);exit;
      $special_operations = DB::table("mr_style_operation_n_cost AS oc")
                              ->leftJoin("mr_operation AS o", "oc.mr_operation_opr_id","=","o.opr_id")
                              ->where("oc.mr_style_stl_id", $stylebom_id)
                              ->where("oc.opr_type", 2)
                              ->get();

      $other_cost = BomOtherCosting::where('mr_style_stl_id', $stylebom_id)->first();
      // dd($other_cost);exit;
      $orders = DB::table('mr_order_entry')
                  ->leftJoin('mr_buyer','mr_order_entry.mr_buyer_b_id','=','mr_buyer.b_id')
                  ->leftJoin('mr_brand','mr_order_entry.mr_brand_br_id','=','mr_brand.br_id')
                  ->leftJoin('mr_season','mr_order_entry.mr_season_se_id','=','mr_season.se_id')
                  ->where('mr_style_stl_id', $stylebom_id)
                  ->get();
      $styleImages = DB::table('mr_style_image')->where('mr_stl_id',$stylebom_id)->get();
      return view('merch/style/style_profile', compact(
        "style",
        "samples",
        "operations",
        "machines",
        "styleImages",
        "stylebom_id",
        "styleCatMcats",
        "styleCatMcatFabs",
        "styleCatMcatSwings",
        "styleCatMcatFinishing",
        "other_cost",
        "special_operations",
        "orders",
        'style_steps'
      ));

    }


  # Write Every Events in Log File
  public function logFileWrite($message, $event_id)
  {
    $log_message = date("Y-m-d H:i:s")." \"".Auth()->user()->associate_id."\" ".$message." ".$event_id.PHP_EOL;
    $log_message .= file_get_contents("assets/log.txt");
    file_put_contents("assets/log.txt", $log_message);
  }

  //check style no
  public function checkStlNo(Request $request)
  {
    $status = 'no';
    $input = $request->all();
    try {
      $getStyle = Style::checkStyleNoTextWise($input['stl_no']);
      if(!empty($getStyle)){
        $status = 'yes';
      }
      return $status;
    } catch (\Exception $e) {
      return $status;
    }
  }
  public function styleGallery()
  {
    $getStyle = Style::where('stl_img_link', '!=', null)->orderBy('stl_id', 'desc')->paginate(10);
    return view('merch.style.style_gallery', compact('getStyle'));
  }

  # Style New Copy form
  public function styleNewCopyForm($id)
  {
    $stylelist=Style::groupBy('stl_no')
                    ->orderBy('stl_id', 'desc')
                    ->pluck('stl_no','stl_id');
    if(!empty($request->style_no)){
      $id = $request->style_no;
      $buyerList        = Buyer::pluck('b_name', 'b_id');
      $productTypeList  = ProductType::pluck('prd_type_name', 'prd_type_name');
    $operationList    = Operation::pluck('opr_name', 'opr_id');
    $machineList      = Spmachine::pluck('spmachine_name', 'spmachine_id');
    $garmentsTypeList = GarmentsType::pluck('gmt_name','gmt_id');
    $sizegroupList    = ProductSizeGroup::pluck('size_grp_name','id');
    $country          = Country::pluck('cnt_name','cnt_name');
    $product          = ProductSize::get();
    $sizegroup        = ProductSize::pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $buyer            = Buyer::pluck('b_name', 'b_id');
    $brand            = Brand::pluck('br_name', 'br_id');
    $wash             = WashType::pluck('wash_name','id');
    $style = DB::table('mr_style AS s')
                ->select(
                    "s.*",
                    "b.b_name",
                    "b.b_id",
                    "p.*"
                )
                ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
                ->leftJoin('mr_product_type AS p', 'p.prd_type_id', '=', 's.prd_type_id')
                ->leftJoin('mr_garment_type AS g', 'g.gmt_id', '=', 's.gmt_id')
                ->where('s.stl_id',$id)
                ->first();
               // dd($style);
    $sampleTypeList   = SampleType::where('b_id',$style->b_id)->pluck('sample_name','sample_id');
    // $sizegroup        = ProductSize::where('b_id',$style->b_id)->where('size_grp_product_type',$style->prd_type_id)->pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $season  = Season::where('b_id','=',$style->b_id)->pluck('se_name','se_id');

    $styleOps = OperationCost::where('mr_style_stl_id', $id)->pluck('mr_operation_opr_id')->toArray();

    //Operation List Show in Modal
    $operationList    = Operation::get();
    $operationData= '<div class="col-xs-12"><div class="checkbox">';
    foreach ($operationList as $operation) {
        $checked="";
        if($operation->opr_type==1){
            $checked.="checked readonly  onclick='return false;'";
        }
        if($operation->opr_type !=1 && in_array($operation->opr_id, $styleOps)){
            $checked.="checked";
        }

        $operationData.= "<label class='col-sm-2' style='padding:0px;'>
        <input name='operations[]' type='checkbox' class='ace' data-content-type='".$operation->opr_type."' value='".$operation->opr_id."'".$checked.">
        <span class='lbl'>".$operation->opr_name."</span>
        </label>";
      }
      $operationData.="</div></div>";


      //Selected Operation Show
      $selectedOpData='';
      $selectedStyleOps= Db::table('mr_style_operation_n_cost AS s')
      ->where('mr_style_stl_id', $id)
      ->select([
        's.style_op_id',
        'o.opr_id',
        'o.opr_name',
        'o.opr_type'
      ])
      ->leftJoin('mr_operation AS o', 'o.opr_id', 's.mr_operation_opr_id')
      ->get();
      $tr_end         = 0;
      $selectedOpData .= '<table class="table table-bordered" style="margin-bottom:0px;">';
      // $selectedOpData .= '<thead>';
      // $selectedOpData .= '<tr>';
      // $selectedOpData .= '<td colspan="3" class="text-center">Operations</td>';
      // $selectedOpData .= '</tr>';
      // $selectedOpData .= '</thead>';
      $selectedOpData .= '<tbody>';
      foreach ($selectedStyleOps as $k=>$selOps) {
        if(!empty($selOps->opr_name)){
          if(strlen((string)($k/10)) === 1) {
            $selectedOpData .= '<tr>';
            $tr_end = $k+9;
          }
          $selectedOpData .= '<td style="border-bottom: 1px solid lightgray;">'.$selOps->opr_name.'</td>';
          $selectedOpData .= '<input type="hidden" name="opr_id[]" value="'.$selOps->opr_id.'"></input>';
          $selectedOpData .= '<input type="hidden" name="opr_type[]" value="'.$selOps->opr_type.'"></input>';
          if($tr_end == 10) {
            $selectedOpData .= '</tr>';
          }
        }
      }
      $selectedOpData .= '</tbody>';
      $selectedOpData .= '</table>';

      //wash modal
      $washCategoryList = WashCategory::get();
      $selectedWash   = StlWashType::where('mr_style_stl_id', $id)->pluck('mr_wash_type_id')->toArray();
      $washData       = '<div class="col-xs-12"><div class="checkbox" id="washStoreDiv">';
      foreach ($washCategoryList as $washCategory) {
        // wash type
        $washData.= "<label class='col-sm-2' style='padding:0px;'>
          <span class='lbl'> ".$washCategory->category_name."</span>";
          if(count($washCategory->mr_wash_type) > 0) {
            $washData .= '<ul>';
            foreach($washCategory->mr_wash_type as $k=>$wash) {
              $checked = '';
              if(!empty($selectedWash)) {
                $checked = in_array($wash->id, $selectedWash)!==FALSE?'checked="checked"':'';
              }
              $washName  = $wash->wash_name;
              $washData .= "<li style='list-style-type: none;'>";
              $washData .= "<label style='padding:0px;'>";
              $washData .= "<input name='washType[]' type='checkbox' class='ace' value='".$wash->id."' ".$checked.">";
              $washData .= "<span class='lbl'> ".$washName."</span>";
              $washData .= "</label>";
              $washData .= "</li>";
            }
            $washData .= '</ul>';
          }
          $washData .= "</label>";
      }
      $washData .= "</div></div>";
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
    return view('merch/style/style_new_copy_form', compact(
      'stylelist',
      'buyerList',
      'country',
      'productTypeList',
      'operationList',
      'machineList',
      'garmentsTypeList',
      'sizegroupList',
      'sampleTypeList',
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
    } else {
      return view('merch/style/style_new_copy_form', compact('stylelist'));
    }
  }

  # store New copy data
  public function storeNewCopy(Request $request)
  {
    //dd($request->all());exit;
    $request->merge([
      'mr_buyer_b_id' => $request->b_id,
      'mr_season_se_id' => $request->se_id,
      'stl_type'=>$request->stl_order_type,
    ]);
    $validator = Validator::make($request->all(), [
      "style_id" => "required",
      "stl_no"           => "required|max:30|unique:mr_style,stl_no,stl_type,mr_buyer_b_id,prd_type_id,mr_season_se_id",
    ]);
    if ($validator->fails()) {
        $failedRules = $validator->failed();

        if(isset($failedRules['stl_no']['CompositeUnique'])) {
          return back()
          //->withErrors($validator)
          ->withInput()
          ->with('error', "This value Buyer,Style Referance,Style Type, Product Type,Season already exists!");
        }else{
          return back()
          ->withErrors($validator)
          ->withInput()
          ->with('error', "Incorrect Input!!");
        }

    }
    DB::beginTransaction();
    try {
      $styleToCopy = Style::where('stl_id',$request->style_id)->first();
      $getStyle = Style::where('stl_id', $request->style_id)->first();
      if ($request->style_img_n != "" ) {
        $file = $request->file('style_img_n');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file = Image::make($file)->resize(800, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                    });
        //dd($file);
        $dir  = 'assets/files/style/'.$filename;
        //$file->move( public_path($dir) , $filename );
        $file->save($dir);
        $stlimg = $dir;
      } else {
        $stlimg = $getStyle->stl_img_link;
      }
      // dd($stlimg);exit;

      $data = new Style;
      // Style Data Store
      $data->stl_type         = $request->stl_order_type;
      $data->mr_buyer_b_id    = $request->b_id;
      $data->prd_type_id      = $request->prd_type_id;
      $data->stl_product_name = $this->quoteReplaceHtmlEntry($request->stl_product_name);
      $data->stl_smv          = $this->quoteReplaceHtmlEntry($request->stl_smv);
      $data->stl_no           = $this->quoteReplaceHtmlEntry($request->stl_no);
      $data->gmt_id           = $request->gmt_id;
      $data->stl_description  = $this->quoteReplaceHtmlEntry($request->stl_description);
      $data->mr_brand_br_id   = $request->mr_brand_br_id;
      $data->mr_season_se_id  = $request->se_id;
      $data->stl_img_link     = $stlimg;     //Image url
      $data->stl_addedby      = (!empty(Auth::id())?(Auth::id()):null);
      $data->stl_updated_by   = null;
      $data->stl_updated_on   = null;
      $data->stl_status       = 0;
      $data->gender           = $request->gender;
      $data->unit_id          = $styleToCopy->unit_id;

      if ($data->save()) {
        $stl_id = $data->id;
        // Store Style Special Machine
        $this->logFileWrite("New Style Saved", $stl_id );

        if (is_array($request->style_img_multi) && sizeof($request->style_img_multi) > 0)
        foreach($request->style_img_multi as $k=> $img) {
          if ($img != "" ) {
            $filename = uniqid() . '.' . $img->getClientOriginalExtension();
            $img = Image::make($img);
            if($img->width()>800){

              $img = $img->resize(800, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
            }

            $stlImgLink  = 'assets/files/style/'.$filename;
            $img->save($stlImgLink);
            StyleImage::insert([
              "mr_stl_id"     => $stl_id,
              "image" => $stlImgLink
            ]);
          }

        }
        if (is_array($request->style_img_multi_up) && sizeof($request->style_img_multi_up) > 0)
        foreach($request->style_img_multi_up as $k=> $img) {
          if ($img != "" ) {
            $filename = uniqid() . '.' . $img->getClientOriginalExtension();
            $img = Image::make($img);
            if($img->width()>800){

              $img = $img->resize(800, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
            }
            $id = $request->img_multi_up[$k];
            $stlImgLink  = 'assets/files/style/'.$filename;
            $img->save($stlImgLink);

            $styleImage = StyleImage::find($id);
            $styleImage->image = $stlImgLink;
            $styleImage->save();
          }

        }

        // Store Style Special Machine
        if (is_array($request->machine_id) && sizeof($request->machine_id) > 0)
        foreach($request->machine_id as $machine) {
          if (!empty($machine))
          StyleSpecialMachine::insert([
            "stl_id"        => $stl_id,
            "spmachine_id"  => $machine
          ]);
        }

        // Store Style Sample
        if (is_array($request->mr_sample_style) && sizeof($request->mr_sample_style) > 0)
        foreach($request->mr_sample_style as $sample) {
          if (!empty($sample))
          SampleStyle::insert([
            "stl_id"    => $stl_id,
            "sample_id" => $sample
          ]);
        }

        // Store Style Size Group
        if (is_array($request->prdsz_id) && sizeof($request->prdsz_id) > 0)
        foreach($request->prdsz_id as $psize) {
          if (!empty($psize))
          StyleSizeGroup::insert([
            "mr_style_stl_id"           => $stl_id,
            "mr_product_size_group_id"  => $psize
          ]);

          $this->logFileWrite("Style Size Gruop Created", DB::getPdo()->lastInsertId());
        }

        // Store Wash
        if (is_array($request->wash) && sizeof($request->wash) > 0)
        foreach($request->wash as $swash) {
          if (!empty($swash))
          StlWashType::insert([
            "mr_style_stl_id" => $stl_id,
            "mr_wash_type_id" => $swash
          ]);
          $this->logFileWrite("Style Wash Inserted", DB::getPdo()->lastInsertId());
        }

        // Data copy from BomCosting and store
        $bomlist= BomCosting::where('mr_style_stl_id',$request->style_id)->get();

        if (!empty($bomlist)){
          foreach ($bomlist as  $bom) {
            BomCosting::insert([
              "mr_style_stl_id"    => $stl_id,
              "mr_material_category_mcat_id" => $bom->mr_material_category_mcat_id,
              "mr_cat_item_id"     => $bom->mr_cat_item_id,
              "item_description"   => $bom->item_description,
              "clr_id"             => $bom->clr_id,
              "size"               => $bom->size,
              "mr_supplier_sup_id" => $bom->mr_supplier_sup_id,
              "mr_article_id"      => $bom->mr_article_id,
              "mr_composition_id"  => $bom->mr_composition_id,
              "mr_construction_id" => $bom->mr_construction_id,
              "uom"                => $bom->uom,
              "consumption"        => $bom->consumption,
              "extra_percent"      => $bom->extra_percent,
              "bom_term"           => $bom->bom_term,
              "precost_fob"        => $bom->precost_fob,
              "precost_lc"        => $bom->precost_lc,
              "precost_freight"   => $bom->precost_freight,
              "precost_req_qty"   => $bom->precost_req_qty,
              "precost_unit_price" => $bom->precost_unit_price,
              "precost_value"     => $bom->precost_value
            ]);
          }
        }

        //Data copy from Bom Other Costing and store
        $otherbomlist= BomOtherCosting::where('mr_style_stl_id', $request->style_id)->get();
        if (!empty($otherbomlist)){
          foreach ($otherbomlist as  $otherbom) {
            BomOtherCosting::insert([
              "mr_style_stl_id"             => $stl_id,
              "testing_cost"                => $otherbom->testing_cost,
              "cm"                          => $otherbom->cm,
              "commercial_cost"             => $otherbom->commercial_cost,
              "net_fob"                     => $otherbom->net_fob,
              "buyer_comission_percent"     => $otherbom->buyer_comission_percent,
              "buyer_fob"                   => $otherbom->buyer_fob,
              "agent_comission_percent"     => $otherbom->agent_comission_percent,
              "agent_fob"                   => $otherbom->agent_fob
            ]);
          }
        }

        // Data copy from Style operation cost and store
        $Operationcostlist= OperationCost::where('mr_style_stl_id', $request->style_id)->get();
        foreach($request->opr_id as $k=>$opr) {
          foreach ($Operationcostlist as  $opcost){
            if($opr==$opcost->mr_operation_opr_id){
              OperationCost::insert([
              "mr_style_stl_id"          => $stl_id,
              "mr_operation_opr_id"      => $opcost->mr_operation_opr_id,
              "opr_type"                 => $opcost->opr_type,
              "uom"                      => $opcost->uom,
              "unit_price"               => $opcost->unit_price

            ]);

            }
          }
        }

        foreach ($Operationcostlist as  $opcost){
            $old_operation[]=$opcost->mr_operation_opr_id;
          }
        foreach($request->opr_id as $k=>$opr) {
          $new_operation[]=$opr;
        }

        $difference = array_diff($new_operation, $old_operation);
        if(!empty($difference)){
          foreach ($difference as $key => $diff) {
            OperationCost::insert([
              "mr_style_stl_id"     => $stl_id,
              "mr_operation_opr_id" => $diff,
              "opr_type"            => $request->opr_type[$k],
              "uom"                 => null,
              "unit_price"          => 0
            ]);
          }
        }


        // Data copy from Style operation cost and store
        // $Operationcostlist= OperationCost::where('mr_style_stl_id', $request->style_id)->get();
        // if (!empty($Operationcostlist)){
        //   foreach ($Operationcostlist as  $opcost) {
        //     OperationCost::insert([
        //       "mr_style_stl_id"          => $stl_id,
        //       "mr_operation_opr_id"      => $opcost->mr_operation_opr_id,
        //       "opr_type"                 => $opcost->opr_type,
        //       "uom"                      => $opcost->uom,
        //       "unit_price"               => $opcost->unit_price

        //     ]);
        //   }
        // }

        // Store Style Operation
        // if (is_array($request->opr_id) && sizeof($request->opr_id) > 0)
        //   //dd($request->opr_id); exit;
        // foreach($request->opr_id as $k=>$opr) {
        //   if (!empty($opr)) {
        //     OperationCost::insert([
        //       "mr_style_stl_id"     => $stl_id,
        //       "mr_operation_opr_id" => $opr,
        //       "opr_type"            => $request->opr_type[$k]
        //     ]);
        //   }
        // }


        // Data copy from Style costing approval and store
        // $stlapplovalList= StyleCostApproval::where('mr_style_stl_id', $request->style_id)->get();
        // if (!empty($stlapplovalList)){
        //   foreach ($stlapplovalList as  $approval) {
        //     StyleCostApproval::insert([
        //       "mr_style_stl_id" =>  $stl_id,
        //       "title"           =>  $approval->title,
        //       "submit_by"       =>  $approval->submit_by, //auth()->user()->associate_id
        //       "submit_to"       =>  $approval->submit_to,
        //       "comments"        =>  $approval->comments,
        //       "status"          =>  $approval->status,
        //       "created_on"      =>  $approval->created_on,
        //       "level"           =>  $approval->level
        //     ]);
        //   }

        // }

        DB::commit();
        $this->logFileWrite("Style ".$request->style_id." copied to ", $stl_id );

        return redirect('merch/style/style_new_edit/'.$stl_id)
        ->with('success', "Style Copied Successfully!!");
      } else {
        return back()->withInput()->with('error', 'Please try again.');
      }
    } catch (\Exception $e) {
      DB::rollback();
      $bug = $e->getMessage();
      return redirect()->back()->with('error', $bug);
    }
  }

  public function getBrandsData(Request $request){
    $brandList= Brand::where('b_id', $request->b_id)->pluck('br_name','br_id');

    return json_encode($brandList);
  }
  public function styleNewCopySearchForm(Request $request)
  {
    $stylelist=Style::groupBy('stl_no')
                    ->orderBy('stl_id', 'desc')
                    ->pluck('stl_no','stl_id');
    if(!empty($request->style_no)){
      $id = $request->style_no;
      $buyerList        = Buyer::pluck('b_name', 'b_id');
      $productTypeList  = ProductType::pluck('prd_type_name', 'prd_type_id');
    $operationList    = Operation::pluck('opr_name', 'opr_id');
    $machineList      = Spmachine::pluck('spmachine_name', 'spmachine_id');
    $garmentsTypeList = GarmentsType::pluck('gmt_name','gmt_id');
    $sizegroupList    = ProductSizeGroup::pluck('size_grp_name','id');
    $country          = Country::pluck('cnt_name','cnt_name');
    $product          = ProductSize::get();
    $sizegroup        = ProductSize::pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $buyer            = Buyer::pluck('b_name', 'b_id');
    $brand            = Brand::pluck('br_name', 'br_id');
    $wash             = WashType::pluck('wash_name','id');
    $style = DB::table('mr_style AS s')
                ->select(
                    "s.*",
                    "b.b_name",
                    "b.b_id",
                    "p.*"
                )
                ->leftJoin('mr_buyer AS b', 'b.b_id', '=', 's.mr_buyer_b_id')
                ->leftJoin('mr_product_type AS p', 'p.prd_type_id', '=', 's.prd_type_id')
                ->leftJoin('mr_garment_type AS g', 'g.gmt_id', '=', 's.gmt_id')
                ->where('s.stl_id',$id)
                ->first();
               // dd($style);
    $sampleTypeList   = SampleType::where('b_id',$style->b_id)->pluck('sample_name','sample_id');
    // $sizegroup        = ProductSize::where('b_id',$style->b_id)->where('size_grp_product_type',$style->prd_type_id)->pluck('mr_product_pallete_name', 'mr_product_size_group_id');
    $season  = Season::where('b_id','=',$style->b_id)->pluck('se_name','se_id');

    $styleOps = OperationCost::where('mr_style_stl_id', $id)->pluck('mr_operation_opr_id')->toArray();

    //Operation List Show in Modal
    $operationList    = Operation::get();
    $operationData= '<div class="col-xs-12"><div class="checkbox">';
    foreach ($operationList as $operation) {
        $checked="";
        if($operation->opr_type==1){
            $checked.="checked readonly  onclick='return false;'";
        }
        if($operation->opr_type !=1 && in_array($operation->opr_id, $styleOps)){
            $checked.="checked";
        }

        $operationData.= "<label class='col-sm-2' style='padding:0px;'>
        <input name='operations[]' type='checkbox' class='ace' data-content-type='".$operation->opr_type."' value='".$operation->opr_id."'".$checked.">
        <span class='lbl'>".$operation->opr_name."</span>
        </label>";
      }
      $operationData.="</div></div>";


      //Selected Operation Show
      $selectedOpData='';
      $selectedStyleOps= Db::table('mr_style_operation_n_cost AS s')
      ->where('mr_style_stl_id', $id)
      ->select([
        's.style_op_id',
        'o.opr_id',
        'o.opr_name',
        'o.opr_type'
      ])
      ->leftJoin('mr_operation AS o', 'o.opr_id', 's.mr_operation_opr_id')
      ->get();
      $tr_end         = 0;
      $selectedOpData .= '<table class="table table-bordered" style="margin-bottom:0px;">';
      // $selectedOpData .= '<thead>';
      // $selectedOpData .= '<tr>';
      // $selectedOpData .= '<td colspan="3" class="text-center">Operations</td>';
      // $selectedOpData .= '</tr>';
      // $selectedOpData .= '</thead>';
      $selectedOpData .= '<tbody>';
      foreach ($selectedStyleOps as $k=>$selOps) {
        if(!empty($selOps->opr_name)){
          if(strlen((string)($k/10)) === 1) {
            $selectedOpData .= '<tr>';
            $tr_end = $k+9;
          }
          $selectedOpData .= '<td style="border-bottom: 1px solid lightgray;">'.$selOps->opr_name.'</td>';
          $selectedOpData .= '<input type="hidden" name="opr_id[]" value="'.$selOps->opr_id.'"></input>';
          $selectedOpData .= '<input type="hidden" name="opr_type[]" value="'.$selOps->opr_type.'"></input>';
          if($tr_end == 10) {
            $selectedOpData .= '</tr>';
          }
        }
      }
      $selectedOpData .= '</tbody>';
      $selectedOpData .= '</table>';

      //wash modal
      $washCategoryList = WashCategory::get();
      $selectedWash   = StlWashType::where('mr_style_stl_id', $id)->pluck('mr_wash_type_id')->toArray();
      $washData       = '<div class="col-xs-12"><div class="checkbox" id="washStoreDiv">';
      foreach ($washCategoryList as $washCategory) {
        // wash type
        $washData.= "<label class='col-sm-2' style='padding:0px;'>
          <span class='lbl'> ".$washCategory->category_name."</span>";
          if(count($washCategory->mr_wash_type) > 0) {
            $washData .= '<ul>';
            foreach($washCategory->mr_wash_type as $k=>$wash) {
              $checked = '';
              if(!empty($selectedWash)) {
                $checked = in_array($wash->id, $selectedWash)!==FALSE?'checked="checked"':'';
              }
              $washName  = $wash->wash_name;
              $washData .= "<li style='list-style-type: none;'>";
              $washData .= "<label style='padding:0px;'>";
              $washData .= "<input name='washType[]' type='checkbox' class='ace' value='".$wash->id."' ".$checked.">";
              $washData .= "<span class='lbl'> ".$washName."</span>";
              $washData .= "</label>";
              $washData .= "</li>";
            }
            $washData .= '</ul>';
          }
          $washData .= "</label>";
      }
      $washData .= "</div></div>";
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
    return view('merch/style/style_new_copy_form', compact(
      'stylelist',
      'buyerList',
      'country',
      'productTypeList',
      'operationList',
      'machineList',
      'garmentsTypeList',
      'sizegroupList',
      'sampleTypeList',
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
    } else {
      return view('merch/style/style_new_copy_form', compact('stylelist'));
    }
  }
}
