<?php
namespace App\Http\Controllers\Merch\Setup;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Merch\ShortCodeLib as ShortCodeLib;
use App\Models\Merch\Buyer;
use App\Models\Merch\CatItemUom;
use App\Models\Merch\MainCategory;
use App\Models\Merch\MaterialColor;
use App\Models\Merch\MaterialColorAttach;
use App\Models\Merch\MaterialSize;
use App\Models\Merch\McatItem;
use App\Models\Merch\MrMaterialSubCategory;
use App\Models\Merch\SubCategory;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Validator, DB;

class MaterialController extends Controller
{
    public function itemForm(){
        
        $categoryList= MainCategory::get();
        $cat_list= MainCategory::pluck('mcat_name','mcat_id');
        $itemList= DB::table('mr_cat_item AS mi')
                    ->select([
                        'mi.*',
                        'mc.*',
                        'msub.msubcat_id as mr_material_sub_cat_id',
                        'msub.msubcat_name'
                    ])
                    ->leftJoin('mr_material_category AS mc', 'mc.mcat_id', 'mi.mcat_id')
                    ->leftJoin('mr_material_sub_cat AS msub', 'msub.msubcat_id', 'mi.mr_material_sub_cat_id')
                    ->get();
         
        $uom= DB::table('uom')->pluck('measurement_name','id');
        $buyerList = Buyer::pluck("b_name", "b_id");
        

        return view('merch/setup/item', compact('categoryList','cat_list','itemList','uom','buyerList'));
    }

    public function itemData(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $data = DB::table('mr_cat_item AS mi')
              ->select([
                  'mi.*',
                  'mc.*',
                  'msub.msubcat_id as mr_material_sub_cat_id',
                  'msub.msubcat_name'
              ])
              ->leftJoin('mr_material_category AS mc', 'mc.mcat_id', 'mi.mcat_id')
              ->leftJoin('mr_material_sub_cat AS msub', 'msub.msubcat_id', 'mi.mr_material_sub_cat_id')
              ->orderBy('id', 'desc')
              ->get();
        $uoms = DB::table('mr_cat_item_uom as item')
        ->select('measurement_name', 'mr_cat_item_id')
        ->leftJoin('uom','item.uom_id','=','uom.id')
        ->get()->toArray();
        $getUoms = collect($uoms)->groupBy('mr_cat_item_id',true)->map(function($row) {
            return collect($row)->pluck('measurement_name')->toArray();
        });

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('main_category', function ($data) {
            return $data->mcat_name;
        })
        ->addColumn('sub_category', function ($data) {
            return $data->msubcat_name;
        })
        ->addColumn('uom', function ($data) use ($getUoms) {
            if(isset($getUoms[$data->id]) && count($getUoms[$data->id]) > 0){
              return implode(', ',$getUoms[$data->id]);
            }
            return '';  
        })
        ->addColumn('depends', function ($data) {
            if($data->dependent_on == 1){
              return 'Color';
            }
            elseif($data->dependent_on == 2){
              return 'Size';
            }
            elseif($data->dependent_on == 3){
              return 'Color & Size';
            }
            else{
              return 'None';
            }
        })
        
        ->addColumn('action', function ($data) {
            
            return '<div class="btn-group"><a type="button" class="btn btn-sm btn-primary generate-drawer text-white" title="Update" data-url="'.url('merch/setup/item_edit_ajax/'.$data->id).'" data-headline="'.$data->item_name.'"><i class="ace-icon fa fa-pencil bigger-120"></i></a><a  href="'.url('merch/setup/item_delete/'.$data->id).'" type="button" class="btn btn-sm btn-danger" onclick=\'return confirm("Are you sure you want to delete this Item?");\' title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a></div>';
        })
        ->rawColumns(['DT_RowIndex', 'main_category', 'sub_category', 'item_code','item_name','uom','depends','action'])
        ->make(true);
    }

    public function getMaterialSubcategorySuggestion(Request $request)
    {
        $input = $request->all();

        $getSubCatName = MrMaterialSubCategory::where('msubcat_name', 'LIKE', '%'.$input['name_startsWith'].'%')
                                                ->where(function ($query) use ($request){
                                                  if(!is_null($request->mcat_id)){
                                                    return $query->where('mcat_id', $request->mcat_id);
                                                  }
                                                })
                                                ->get();
        // dd($getSubCatName);
        $data = array();

        if(count($getSubCatName) > 0){
            foreach ($getSubCatName as $sub) {
                $data[] = $sub->msubcat_name.'|'.$sub->id;
            }
        }
        return $data;

    }

    public function itemStore(Request $request){

       $validator= Validator::make($request->all(),[
            'mcat_name' => 'required',
            'item_name*' => 'required',
            'depends*' => 'required|max:45'
        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{

            $i=0;
            foreach ($request->item_name as $key => $value) {

                $existing= McatItem::where('mcat_id',$request->mcat_name)->orderBy('id','DESC')->value('id');
                $item_code= "0".$request->mcat_name.((($existing!=null)? $existing:0)+1);
                //dd($request->uom[$key]);
                $sub_cat_exists = MrMaterialSubCategory::where('msubcat_name', $request->subcategory_name)
                                                          ->value('msubcat_id');
                if(empty($sub_cat_exists) || is_null($sub_cat_exists)){
                // dd("if", $sub_cat_exists);
                  $new = new MrMaterialSubCategory();
                  $new->mcat_id      = $request->mcat_name; 
                  $new->msubcat_name = $this->quoteReplaceHtmlEntry($request->subcategory_name);
                  $new->save();
                  $msub_cat_id = $new->id;
                  
                  MrMaterialSubCategory::where('msubcat_id', $msub_cat_id)->update([
                      'subcat_index' => $msub_cat_id
                  ]);
                }
                else{
                  // dd("else",$sub_cat_exists);
                  $msub_cat_id = $sub_cat_exists;
                }

                McatItem::insert([
                    'mcat_id'      => $request->mcat_name,
                    'mr_material_sub_cat_id'  => $msub_cat_id,
                    'item_name'    => $this->quoteReplaceHtmlEntry($value),
                    'item_code'    => $item_code,
                    'description'  => $request->description[$i++],
                    'dependent_on' => $request->depends[$key]
                ]);

                $id = DB::getPdo()->lastInsertId();
                McatItem::where('id', $id)->update([
                    'tab_index' => $id
                ]);

                if(isset($request->uom[$key]) && isset($id)){
                  foreach ($request->uom[$key] as $uom) {
                    $CatItemUom = new CatItemUom;
                    $CatItemUom->mr_cat_item_id = $id;
                    $CatItemUom->uom_id = $uom;
                    $CatItemUom->save();
                  }
                }

                $this->logFileWrite("Material Category Item Saved", $id);
            }
        return back()
               ->with('success', " Saved Successfully!!");
              // return view('my_view')->withErrors(['Duplicate Record.']);
      }

    }
    public function itemStoreAjax(Request $request)
    {
      $request->validate([
          'mcat_name' => 'required',
          'item_name' => 'required',
          'depends'   => 'required'
      ]);
      $data = array();
      $data['type'] = 'error';
      $input = $request->all();
      // check existing item
      $input['mcat_id'] = $request->mcat_name;
      $item = McatItem::checkExistItem($input);

      if($item != null){
          $data['message'] = ' This Item already exists';
          return response()->json($data);
      }
      DB::beginTransaction();
      try {
          if($input['item_code'] == null){
            $existing= McatItem::where('mcat_id',$request->mcat_name)->orderBy('id','DESC')->value('id');
            $item_code= "0".$request->mcat_name.((($existing!=null)? $existing:0)+1);
          }else{
            $item_code = $input['item_code'];
          }
          
          if($request->subcategory_name != null){
            $sub_cat_exists = MrMaterialSubCategory::where('msubcat_name', $request->subcategory_name)
                                                    ->value('msubcat_id');
            if(empty($sub_cat_exists) || is_null($sub_cat_exists)){

              $new = new MrMaterialSubCategory();
              $new->mcat_id      = $request->mcat_name; 
              $new->msubcat_name = $this->quoteReplaceHtmlEntry($request->subcategory_name);
              $new->save();
              $msub_cat_id = $new->id;
              
              MrMaterialSubCategory::where('msubcat_id', $msub_cat_id)->update([
                  'subcat_index' => $msub_cat_id
              ]);
            }
            else{
              // dd("else",$sub_cat_exists);
              $msub_cat_id = $sub_cat_exists;
            }
          }else{
            $msub_cat_id = null;
          }
          

          $id = McatItem::insertGetId([
              'mcat_id'      => $request->mcat_name,
              'mr_material_sub_cat_id'  => $msub_cat_id,
              'item_name'    => $this->quoteReplaceHtmlEntry($input['item_name']),
              'item_code'    => $item_code,
              'description'  => $request->description,
              'dependent_on' => $request->depends,
              'buyer_id'     => $request->buyer_id,
              'created_by'   => auth()->user()->id
          ]);

          McatItem::where('id', $id)->update([
              'tab_index' => $id
          ]);

          if(isset($request->uom) && isset($id)){
            foreach ($request->uom as $uom) {
              $CatItemUom = new CatItemUom;
              $CatItemUom->mr_cat_item_id = $id;
              $CatItemUom->uom_id = $uom;
              $CatItemUom->save();
            }
          }

          $this->logFileWrite("Material Category Item Saved", $id);

          $data['type'] = 'success';
          $data['url'] = url()->previous();
          $data['message'] = "Material Item successfully done.";
          DB::commit();
          return response()->json($data);
      } catch (\Exception $e) {
        DB::rollback();
        $bug = $e->getMessage();
        $data['message'] = $bug;
        return response()->json($data);
      }
    }
    public function mainCategoryStore(Request $request){
      
    	for($i=0; $i<sizeof($request->msubcat_name); $i++){
    		$data= new SubCategory();
    		$data->mcat_id= $request->mcat_id;
    		$data->msubcat_name= $request->msubcat_name[$i];
    		$data->save();
    	}
      $this->logFileWrite("Material Sub Category Saved", $data->msubcat_id);
    	return back()
    	->with('success', "Material Category Saved Successfully!!");

    }

    public function itemEdit($id){
        
    	$maincategory= MainCategory::where('mcat_id', $id)->first();
    	$item= McatItem::where('id', $id)->first();
        $cat_list= MainCategory::pluck('mcat_name','mcat_id');

        $mitem= DB::table('mr_cat_item as m')
                    ->Select(
                        'm.*',
                        'mc.mcat_name',
                        'mc.mcat_id',
                        'msc.msubcat_name',
                        'msc.msubcat_id'
                    )
                  ->leftJoin('mr_material_category AS mc', 'mc.mcat_id', '=', 'm.mcat_id')
                  ->leftJoin('mr_material_sub_cat AS msc', 'msc.msubcat_id', '=', 'm.mr_material_sub_cat_id')
                  ->where('m.id', $id)
                  ->first();
        $uomThis=CatItemUom::where('mr_cat_item_id',$id)
                  ->pluck('uom_id');
        $uom= DB::table('uom')->pluck('measurement_name','id');

        //dd($uomThis);

    	return view('merch/setup/item_edit', compact('uomThis','uom','maincategory', 'item','cat_list','mitem'));
    }

    public function itemEditAjax($id)
    {
        $maincategory= MainCategory::where('mcat_id', $id)->first();
        $item= McatItem::where('id', $id)->first();
        $cat_list= MainCategory::pluck('mcat_name','mcat_id');

        $mitem= DB::table('mr_cat_item as m')
                ->Select(
                    'm.*',
                    'mc.mcat_name',
                    'mc.mcat_id',
                    'msc.msubcat_name',
                    'msc.msubcat_id'
                )
              ->leftJoin('mr_material_category AS mc', 'mc.mcat_id', '=', 'm.mcat_id')
              ->leftJoin('mr_material_sub_cat AS msc', 'msc.msubcat_id', '=', 'm.mr_material_sub_cat_id')
              ->where('m.id', $id)
              ->first();
        // dd($mitem);
        $uomThis=CatItemUom::where('mr_cat_item_id',$id)
                  ->pluck('uom_id');
        $uom= DB::table('uom')->pluck('measurement_name','id');
        $buyerList = Buyer::pluck("b_name", "b_id");
        return view('merch/setup/item_edit_drawer', compact('uomThis','uom','maincategory', 'item','cat_list','mitem', 'buyerList'));
    }

    public function itemUpdate(Request $request){
      
        $validator= Validator::make($request->all(),[
            'mcat_name' => 'required',
            'item_name' => 'required|max:45',
            'item_code' => 'required|max:45',
            'depends'   => 'required'
        ]);

        if($validator->fails()){
            foreach ($validator->errors()->all() as $message){
              toastr()->error($message);
            }
            return back();
        }
        $input = $request->all();
        DB::beginTransaction();
        // return $input;
        try {
          if($request->msubcat_id == '' || $request->msubcat_id == null){
            $sub_cat_exists = MrMaterialSubCategory::where('msubcat_name', $request->subcategory_name)
                                                        ->value('msubcat_id');

            if(empty($sub_cat_exists) || is_null($sub_cat_exists)){

                $new = new MrMaterialSubCategory();
                $new->mcat_id      = $request->mcat_name; 
                $new->msubcat_name = $this->quoteReplaceHtmlEntry($request->subcategory_name);
                $new->save();
                $msub_cat_id = $new->id;
              }
              else{
                $msub_cat_id = $sub_cat_exists;
              }
          }
          else{
            if($request->choice == 'rename'){       
                MrMaterialSubCategory::where('msubcat_id', $request->msubcat_id)->update([
                      'msubcat_name' => $request->subcategory_name
                ]);

                $msub_cat_id = $request->msubcat_id;
            }else{
                $new = new MrMaterialSubCategory();
                $new->mcat_id      = $request->mcat_name; 
                $new->msubcat_name = $this->quoteReplaceHtmlEntry($request->subcategory_name);
                $new->save();
                $msub_cat_id = $new->id;
            }
          }

          McatItem::where('id', $request->mcat_id)->update([
              'mcat_id'      => $request->mcat_name,
              'item_name'    => $this->quoteReplaceHtmlEntry($request->item_name),
              'mr_material_sub_cat_id'  => $msub_cat_id,
              'item_code'    => $request->item_code,
              'description'  => $request->description,
              'dependent_on' => $request->depends,
              'updated_by'   => auth()->user()->id
         ]);
          //dd($request->uom);
          if(isset($request->uom)){
            $prevUom = CatItemUom::where('mr_cat_item_id',$request->mcat_id)
                        ->pluck('uom_id')
                        ->toArray();
            $toDel = array_diff($prevUom, $request->uom);
            $toInsert = array_diff($request->uom,$prevUom);

            foreach ($toDel as $uom) {
               CatItemUom::where([
                  'mr_cat_item_id'=>$request->mcat_id,
                  'uom_id' => $uom
                ])
                ->delete();
            }

            foreach ($toInsert as $uom) {
              $CatItemUom = new CatItemUom;
              $CatItemUom->mr_cat_item_id = $request->mcat_id;
              $CatItemUom->uom_id = $uom;
              $CatItemUom->save();
            }
          }else{
            CatItemUom::where('mr_cat_item_id', $request->mcat_id)->delete();
          }
          $this->logFileWrite("Material Category Item Updated", $request->mcat_id);
          toastr()->success("Item updated Successfully!!!");
          DB::commit();
          return back();
        } catch (\Exception $e) {
          DB::rollback();
          $bug = $e->getMessage();
          toastr()->error($bug);
          return back();
        }
        
    }
    /// Product Size  Delete

    public function itemDelete($id){
        DB::beginTransaction();
        try {
          McatItem::where('id', $id)->delete();
          CatItemUom::where('mr_cat_item_id', $id)->delete();
          $this->logFileWrite("Material Category Item Deleted", $id);
          DB::commit();
          toastr()->success('Item  Deleted Successfully!!');
          return back();
        } catch (\Exception $e) {
          DB::rollback();
          $bug = $e->getMessage();
          toastr()->error($bug);
          return back();
        }
    }

    // Sub category list by Category..
    public function getSubCatByMainCat(Request $request){
        
    	if($request->mcat_id){
    		$subList= SubCategory::where('mcat_id', $request->mcat_id)
    					->pluck('msubcat_name', 'msubcat_id');
    		$data= "<option value=\"\">Select Sub Category</option>";
    		foreach ($subList as $key => $subcatname) {
    			$data.= "<option value=\"$key\">$subcatname</option>";
    		}
    		if(!empty($data)){
    			return $data;
    		}
    	}
    	return "<option value=\"\">No SubCategory Available!</option>";
    }

    public function color(){

        $color= MaterialColor::with('attached_files')
                  ->orderBy('clr_id', 'desc')->get();
        return view('merch/setup/color', compact('color'));
    }
    /// Color Store
    public function colorStore(Request $request){
   
  	  $validator= Validator::make($request->all(),[
      	'march_color'        =>'required|max:50'
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
        $data= new MaterialColor();
        $data->clr_name = $this->quoteReplaceHtmlEntry($request->march_color);
        $data->clr_code = $this->quoteReplaceHtmlEntry($request->march_color_code);
        $data->created_by = auth()->user()->id;
        $data->save();

        $last_id = $data->id;
         
        $this->logFileWrite("Material Color Saved", $last_id);

        if(isset($request->march_file) && !empty($request->march_file)){

          if(sizeof($request->march_file)>0){
            for($i=0; $i<sizeof($request->march_file); $i++){
              ///File upload///
              $march_file = null;
             if($request->hasFile('march_file.'. $i)){

              $file = $request->file('march_file.'. $i);

              $filename = uniqid() . '.' . $file->getClientOriginalExtension();
              $dir  = '/assets/files/materialcolor/';
              $file->move( public_path($dir) , $filename );
              $march_file = $dir.$filename;
              MaterialColorAttach::insert([
                        'clr_id'               => $last_id,
                        'col_attach_url'       => $march_file
                    ]);
              }
            }

          }
        }

        toastr()->success("Material Color Saved Successfully!!");
        DB::commit();
        return back();
      } catch (\Exception $e) {
        DB::rollback();
        $bug = $e->getMessage();
        toastr()->error($bug);
        return back();
      }
    }

    /// Color Delete

    public function colorDelete($id){
      DB::beginTransaction();
      try {
        MaterialColor::where('clr_id', $id)->delete();
        MaterialColorAttach::where('clr_id', $id)->delete();

        $this->logFileWrite("Material Color Deleted", $id);
        toastr()->success("Material Color Saved Successfully!!");
        DB::commit();
        return back(); 
      } catch (\Exception $e) {
        DB::rollback();
        $bug = $e->getMessage();
        toastr()->error($bug);
        return back();
      }
    }

   /// Color Update
    public function colorEdit($id){

        
          $color=MaterialColor::where('clr_id', $id)->first();
          $filesearch=MaterialColorAttach::where('clr_id', $id)->first();
          $colorfile=MaterialColorAttach::where('clr_id', $id)->get();
          return view('merch/setup/color_edit',compact('color','colorfile','filesearch'));

    }

    public function colorUpdate(Request $request){
    	//dd($request->all());
       
        #-----------------------------------------------------------#

       $validator= Validator::make($request->all(),[
          	'march_color'        =>'required|max:50'


        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{

        $color = MaterialColor::where('clr_id', $request->color_id)->update([
               'clr_name'        => $this->quoteReplaceHtmlEntry($request->march_color),
               'clr_code'        => $this->quoteReplaceHtmlEntry($request->march_color_code)

        ]);

  	   //
        //if($request->march_file[0] != null)
        //{
  		  //dd($request->all());
       		$colorfile=MaterialColorAttach::where('clr_id', $request->color_id)->delete();

       			//File upload///
             $dir  = '/assets/files/materialcolor/';
        if ($request->march_file != "" ){
             for($i=0; $i<sizeof($request->march_file); $i++){

                  //dd($request->all());
                  $march_file = null;
                  if(!empty($request->march_file[$i])){

                    $path=$request->march_file[$i];
                       if (substr($path, 0, 14) == '/assets/files/'){
                               $march_file=$path;
                             }
                       else{
                        $filename1 = uniqid() . '.' . $path->getClientOriginalExtension();

                        $path->move( public_path($dir) , $filename1 );
                        $march_file = $dir.$filename1;}

                  }

        	///File Url Store //////////

        		MaterialColorAttach::insert([
                        'clr_id'               => $request->color_id,
                        'col_attach_url'       => $march_file
                    ]);
           }
       }
       //Log Entry
       $this->logFileWrite("Material Color Updated", $request->color_id);

        return back()
        ->with('success', "Material Color Successfully Updated!!");
     }

    //return redirect('merch/setup/infoBrand');
  }


  //drag items to change item order-rkb
  public function itemTabIndex(){
    $modalCats = MainCategory::get();
    $catItem = [];
    $cat= [];
    foreach ($modalCats as $category){
        $subItem = DB::table('mr_cat_item as i')
                    ->select(
                      'i.id',
                      'i.item_name',
                      'i.mr_material_sub_cat_id'
                    )
                    ->leftJoin('mr_material_sub_cat as s','i.mr_material_sub_cat_id','s.msubcat_id')
                    ->where("i.mcat_id", $category->mcat_id)
                    ->orderBy('i.tab_index','ASC')
                    ->orderBy('s.subcat_index','ASC')
                    ->get();

        $cat[$category->mcat_id] = $category;
        $catItem[$category->mcat_id] = collect($subItem)->groupBy('mr_material_sub_cat_id',true)->toArray();
    }
    //dd($catItem);
    $subcat = SubCategory::pluck('msubcat_name','msubcat_id');
    return view('merch/setup/item_tab_index', compact('cat','catItem','subcat'));
  }

  public function storeItemTabIndex(Request $request){
    try{

      //dd($request->subcat);

        if(!empty($request->item_tab)){
          foreach ($request->item_tab as $key => $value) {
              $item = McatItem::find($value);
              $item->tab_index = $key;
              $item->save();
          }
        }
        if(!empty($request->subcat)){
          //dd($request->subcat);
          foreach ($request->subcat as $key1 => $value) {
              $subcat = SubCategory::where('msubcat_id',$value)
                        ->update([
                          'subcat_index' => $key1
                        ]);
          }
        }
        return back()
               ->with('success', " Saved Successfully!!");

    }catch (\Exception $e) {
      $bug = $e->getMessage();
      return $bug;
    }
  }


}
