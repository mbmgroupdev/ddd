<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\ProductSize;
use App\Models\Merch\ProductSizeGroup;
use App\Models\Merch\Buyer;
use App\Models\Merch\Brand;
use Validator, DB;
class ProductsizeController extends Controller
{

    // Product Size type form
  public function productSize(){
    $productType = DB::table('mr_product_type')->pluck('prd_type_name','prd_type_id');
    // $product= ProductSize::get();
    $sizegroup= ProductSizeGroup::pluck('size_grp_name', 'id');
       //$buyer=Buyer::pluck('b_name', 'b_id');
       //$brand=Brand::pluck('br_name', 'br_id');

    $b_permissions = auth()->user()->buyer_permissions();
    $buyer        = DB::table('mr_buyer')
    ->whereIn('b_id', $b_permissions)
    ->pluck('b_name', 'b_id')
    ->toArray();
       //for product size list
    $Prodsizegroup= ProductSizeGroup::with('buyer')->whereIn('b_id', $b_permissions)->orderBy('id')->get();

    $productSizeId = array_column($Prodsizegroup->toArray(), 'id');
    $sizeModalData=array();
    $productSize = DB::table('mr_product_size')->whereIn('mr_product_size_group_id', $productSizeId)->get()->groupBy('mr_product_size_group_id')->toArray();

    $getProductSize = collect($productSize)->map(function($row) {
        return collect($row)->pluck('mr_product_pallete_name')->toArray();
    });
    $sizeItems= DB::table('mr_prdz_size_pallete')
    ->select([
    DB::raw("DISTINCT(sl)"),
    "size"
    ])
    ->get();

    $sizeTypes= DB::table('mr_prdz_size_pallete')
    ->orderBy('size','ASC')
    ->get();

    $digit = [];
    foreach($sizeTypes as $k=>$sizeT) {
      if($sizeT->size_type == 'Digit') {
        $digit[$k] = $sizeT;
        unset($sizeTypes[$k]);
      }
    }
    usort($digit, function($a, $b) {
      return $a->size - $b->size;
    });
    foreach($digit as $k=>$dig) {
      $sizeTypes[] = $dig;
    }

    foreach ($sizeTypes as $size1) {
      $dataGroup[$size1->size_type][] = $size1;
    }

    $sizeModalData[] = view('merch.setup.psize', compact('dataGroup'))->render();

    return view('merch/setup/product_size', compact('sizegroup','buyer','Prodsizegroup', 'sizeModalData','productType', 'getProductSize'));

  }

        # Return Brand List by Buyer ID
  public function brandGenerate(Request $request)
  {
    $list = "<option value=\"\">Select Brand</option>";
    if (!empty($request->b_id))
    {

      $brandList  = Brand::where('b_id', $request->b_id)
      ->pluck('br_name','br_id');

      foreach ($brandList as $key => $value)
      {
        $list .= "<option value=\"$key\">$value</option>";
      }
    }
    return $list;
  }


      // Save Size Group Ajax
  public function sizegroupSave(Request $request)
  {
    if (empty($request->sizegroup))
    {
      $data['status'] = false;
      $data["error"] = "Invalid Group!";
      return $data;
    }

    $req_upper=strtoupper($request->sizegroup);
    $newgroup=trim($req_upper);

    $store= new ProductSize();
    $store->prdsz_group  = $newgroup;
    $store->save();

    if ($store)
    {
      $this->logFileWrite("Product Size Entry", $store->id );

      $data['status'] = true;
      $data['result'] = (object)array(
        'id'=> $store->id,
        'text'=> $request->sizegroup,
      );
      $data["success"] = "Inserted Successfully!";
    }
    else
    {
      $data['status'] = false;
      $data["error"] = "Please try again.";
    }

    return $data;
  }

  public function productsizestore(Request $request){

    $validator= Validator::make($request->all(),[
     'buyer'        =>'required|max:11',
     'brand'        =>'required|max:11',
     'product_type' =>'required|max:45',
     'gender'       =>'required|max:45'
    ]);
    if($validator->fails()){
        foreach ($validator->errors()->all() as $message){
          toastr()->error($message);
        }
        return back();
    }
    $input = $request->all();
    DB::beginTransaction();
    try {
      $szgroup_upper_=strtoupper($request->product_size_group);
      $newgroup=trim($szgroup_upper_);

      $productType = DB::table('mr_product_type')->pluck('prd_type_name','prd_type_id');

      $data= new ProductSizeGroup();
      $data->b_id                   = $request->buyer;
      $data->br_id                  = $request->brand;
      $data->size_grp_product_type  = $productType[$request->product_type];
      $data->size_grp_gender        = $request->gender;
      $data->size_grp_name          = $this->quoteReplaceHtmlEntry($request->sg_name);
      $data->created_by             = auth()->user()->id;
      $data->save();

      $last_id = $data->id;

      for($i=0; $i<sizeof($request->seleted_sizes); $i++)
      {
        productsize::insert([
          'mr_product_size_group_id'  => $last_id,
          'mr_product_pallete_name'   => $request->seleted_sizes[$i]
        ]);
      }

      $this->logFileWrite("Product Size Group Saved",$last_id );
      toastr()->success("Product Size Saved Successfully!!");
      DB::commit();
      return back();
    } catch (\Exception $e) {
      DB::rollback();
      $bug = $e->getMessage();
      toastr()->error($bug);
      return back();
    }
    
  }


  public function productSizeDelete($id){

    #-----------------------------------------------------------#
    ProductSizeGroup::where('id', $id)->delete();
    productsize::where('mr_product_size_group_id', $id)->delete();

    $this->logFileWrite("Product Size Deleted",$id );
    return back()
    ->with('success', "Product Size  Deleted Successfully!!");
  }

  public function productSizeEdit($id){

    $productType = DB::table('mr_product_type')->pluck('prd_type_name','prd_type_id');

    $product= ProductSize::get();
    $sizegroup= ProductSizeGroup::pluck('size_grp_name', 'id');
          //$buyer=Buyer::pluck('b_name','b_id');
    $b_permissions = auth()->user()->buyer_permissions();
    $buyer        = DB::table('mr_buyer')
    ->whereIn('b_id', $b_permissions)
    ->pluck('b_name', 'b_id')
    ->toArray();
    //$brand=Brand::pluck('br_name','br_id');
    $Prodsizegroup= ProductSizeGroup::get(); //for list
     //existing
    $Prodsizegroup_up= ProductSizeGroup::where('id', $id)->first(); //for list
    // dd($Prodsizegroup_up);exit;
    $product_type_id = DB::table('mr_product_type')
    ->where('prd_type_name',$Prodsizegroup_up->size_grp_product_type )
    ->value('prd_type_id');
    // dd($product_type_id);
    $brand=Brand::where('b_id','=',$Prodsizegroup_up->b_id)->pluck('br_name','br_id')->toArray();

    $sizeGroup= ProductSize::where('mr_product_size_group_id', $id)->get();
    //dd($sizeGroup);exit;
    $sizeGroups= ProductSize::where('mr_product_size_group_id', $id)->get()->toArray();
    //dd($sizeGroups);
    $s_id = array_column($sizeGroups, 'mr_product_pallete_name');
    //dd($s_id);exit;

    $checkedSize= ProductSize::where('mr_product_size_group_id', $id)->pluck('mr_product_pallete_name')->toArray();

    $sizeModalData=array();
    $sizeItems= DB::table('mr_prdz_size_pallete')
    ->select([
      DB::raw("DISTINCT(sl)"),
      "size"
    ])
    ->get();
    $sizeTypes= DB::table('mr_prdz_size_pallete')
    ->orderBy('size','ASC')
    ->get();

    $digit = [];
    foreach($sizeTypes as $k=>$sizeT) {
      if($sizeT->size_type == 'Digit') {
        $digit[$k] = $sizeT;
        unset($sizeTypes[$k]);
      }
    }
    usort($digit, function($a, $b) {
      return $a->size - $b->size;
    });
    foreach($digit as $k=>$dig) {
      $sizeTypes[] = $dig;
    }

    foreach ($sizeTypes as $size1) {
      $dataGroup[$size1->size_type][] = $size1;
    }
    $sizeModalData[] = view('merch.setup.psize_edit', compact('dataGroup','s_id'))->render();

    return view('merch/setup/product_size_edit', compact('product', 'productType','sizeModalData', 'sizeGroups', 'sizegroup','buyer','brand','Prodsizegroup', 'Prodsizegroup_up', 'product_type_id'));

  }

  public function productSizeUpdate(Request $request){

     $validator= Validator::make($request->all(),[
       'buyer'               =>'required|max:11',
       'brand'               =>'required|max:11',
       'product_type'        =>'required|max:45',
       'gender'              =>'required|max:45'

     ]);
     if($validator->fails()){
      return back()
      ->withInput()
      ->with('error', "Incorrect Input!!");
    }
    else{
      $productType = DB::table('mr_product_type')->pluck('prd_type_name','prd_type_id');
        // dd($productType);

      $Productsizegrp = ProductSizeGroup::where('id', $request->prod_id)->update([
       'b_id'                   => $request->buyer,
       'br_id'                  => $request->brand,
       'size_grp_product_type'  => $productType[$request->product_type],
       'size_grp_gender'        => $request->gender,
       'size_grp_name'          => $this->quoteReplaceHtmlEntry($request->sg_name)

     ]);

      productsize::where('mr_product_size_group_id', $request->prod_id)->delete();
      if(isset($request->seleted_sizes)){


        if(sizeof($request->seleted_sizes)>0){

          for($i=0; $i<sizeof($request->seleted_sizes); $i++)
          {
            productsize::insert([
              'mr_product_size_group_id'  => $request->prod_id,
              'mr_product_pallete_name'   => $request->seleted_sizes[$i]
            ]);
          }
        }
      }

      $this->logFileWrite("Product Size Group Updated", $request->prod_id );
      return redirect("merch/setup/productsize")
      ->with('success', "Product Size Successfully updated.");
    }

  }

}
