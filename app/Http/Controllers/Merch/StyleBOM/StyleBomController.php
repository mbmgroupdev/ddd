<?php

namespace App\Http\Controllers\Merch\StyleBOM;

use App\Http\Controllers\Controller;
use App\Models\Merch\Article;
use App\Models\Merch\BomCosting;
use App\Models\Merch\Buyer;
use App\Models\Merch\CatItemUom;
use App\Models\Merch\MaterialColor;
use App\Models\Merch\MaterialColorAttach;
use App\Models\Merch\Season;
use App\Models\Merch\Supplier;
use App\Models\Merch\SupplierContact;
use App\Models\Merch\SupplierItemType;
use App\Models\UOM;
use DB, Validator, Response, Form, Exception, DataTables, ACL;
use Illuminate\Http\Request;

class StyleBomController extends Controller
{
    /**
     * Display a listing of the style bom resource.
     * @method showList()
     * @param No parameter
     * @return Style BOM List
    */

    public function showList()
    {
        
        $buyerList  = Buyer::whereIn('b_id', auth()->user()->buyer_permissions())
                        ->pluck('b_name', 'b_id');

        $seasonList = Season::pluck('se_name','se_id');
    	return view("merch.style_bom.style_bom_list", compact(
            'buyerList',
            'seasonList'
        ));
    }

    public function getListData()
    {
        ini_set('zlib.output_compression', 1);
    	$data = DB::table("mr_style AS s")
    		->select(
    			"s.stl_id",
    			"sb.mr_style_stl_id",
    			"s.stl_type",
    			"s.stl_no",
    			"b.b_name",
                "br.br_name",
    			"t.prd_type_name",
    			"g.gmt_name",
    			"s.stl_product_name",
    			"s.stl_description",
    			"se.se_name",
    			"s.stl_smv",
    			"s.stl_img_link",
    			"s.stl_status"
    		)
			->leftJoin("mr_stl_bom_n_costing AS sb", "sb.mr_style_stl_id", "=", "s.stl_id")
			->leftJoin("mr_buyer AS b", "b.b_id", "=", "s.mr_buyer_b_id")
            ->whereIn('b.b_id', auth()->user()->buyer_permissions())
			->leftJoin("mr_product_type AS t", "t.prd_type_id", "=", "s.prd_type_id")
			->leftJoin("mr_garment_type AS g", "g.gmt_id", "=", "s.gmt_id")
			->leftJoin("mr_season AS se", "se.se_id", "=", "s.mr_season_se_id")
            ->leftJoin("mr_brand AS br", "br.br_id", "=", "s.mr_brand_br_id")
			->groupBy("s.stl_id")
            ->orderBy('s.stl_id', 'desc')
			->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('stl_type', function ($data) {
                if ($data->stl_type == "Bulk")
                {
                    return $data->stl_type;
                }
                else
                {
                    return $data->stl_type;
                }
            })
            ->editColumn('se_name', function ($data)
            {
                return htmlspecialchars_decode($data->se_name);
            })
            ->editColumn('action', function ($data) {
                $return = "<div class=\"btn-group\">";
            	if (empty($data->mr_style_stl_id))
            	{
            		$return .= "<a href=".url('merch/style/bom/'.$data->stl_id)." class=\"btn btn-sm btn-warning\" data-toggle=\"tooltip\" title=\"Create Style BOM\">BOM</a>";
            	}
            	else
            	{
                    $return .= "<a href=".url('merch/style/bom/'.$data->stl_id)." class=\"btn btn-sm btn-success\" data-toggle=\"tooltip\" title=\"Edit Style BOM\">
                     		<i class=\"ace-icon fa fa-pencil bigger-120\"></i>
                        </a>
                        <a href=".url('merch/style_bom/'.$data->stl_id.'/delete')." class=\"btn btn-sm btn-danger\" data-toggle=\"tooltip\" onClick=\"return window.confirm('Are you sure?')\" title=\"Delete\">
                        <i class=\"ace-icon fa fa-trash bigger-120\"></i>
                    </a>";
            	}
                $return .= "</div>";
                return $return;
            })
            ->rawColumns([
                'stl_type', 'stl_no', 'b_name', 'br_name', 'stl_product_name', 'se_name', 'action'
            ])
            ->make(true);
    }

    /**
     * Create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $request->id
     * @return \Illuminate\Http\Response
    */
    public function showForm(Request $request)
    {
        $stylebom_id=$request->id;
        $buyerData = DB::table('mr_buyer');
        $buyerDataSql = $buyerData->toSql();

        $productTypeData = DB::table('mr_product_type');
        $productTypeDataSql = $productTypeData->toSql();

        $garmentTypeData = DB::table('mr_garment_type');
        $garmentTypeDataSql = $garmentTypeData->toSql();

        $seasonData = DB::table('mr_season');
        $seasonDataSql = $seasonData->toSql();
    	$queryData = DB::table("mr_style AS s")
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
            ->whereIn('b.b_id', auth()->user()->buyer_permissions());
            $queryData->leftjoin(DB::raw('(' . $buyerDataSql. ') AS b'), function($join) use ($buyerData) {
                $join->on("b.b_id", "s.mr_buyer_b_id")->addBinding($buyerData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $productTypeDataSql. ') AS t'), function($join) use ($productTypeData) {
                $join->on("t.prd_type_id", "s.prd_type_id")->addBinding($productTypeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $garmentTypeDataSql. ') AS g'), function($join) use ($garmentTypeData) {
                $join->on("g.gmt_id", "s.gmt_id")->addBinding($garmentTypeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $seasonDataSql. ') AS se'), function($join) use ($seasonData) {
                $join->on("se.se_id", "s.mr_season_se_id")->addBinding($seasonData->getBindings());
            });
			
		$style = $queryData->where("s.stl_id", $request->id)
			->first();
        // dd($style);
        //sampleTypes
	    $samples = DB::table("mr_stl_sample AS ss")
	    	->select(DB::raw("GROUP_CONCAT(st.sample_name SEPARATOR ', ') AS name"))
	    	->leftJoin("mr_sample_type AS st", "st.sample_id", "ss.sample_id")
	    	->where("ss.stl_id", $request->id)
	    	->first();

        //operations
	    $operations = DB::table("mr_style_operation_n_cost AS oc")
	    	->select("o.opr_name")
	    	->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
	    	->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
	    	->where("oc.mr_style_stl_id", $request->id)
	    	->first();

        //machines
	    $machines = DB::table("mr_style_sp_machine AS sm")
	    	->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
	    	->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
	    	->where("sm.stl_id", $request->id)
	    	->first();

	    // BOM Items


        $existItem = [];

        //show category select is Modal
        $modalCats = DB::table("mr_material_category AS c")->get();
        $catItem = [];
        $cat = [];
        $bomItemData = '';
        foreach ($modalCats as $category){
            // $bomItemData .= $this->getItemBomData($category->mcat_id, '');
            $subItem = DB::table("mr_cat_item AS i")
                        ->select(
                            "i.mcat_id",
                            "i.item_name",
                            "i.id",
                            "s.msubcat_id",
                            "s.msubcat_name"
                        )
                        ->leftJoin("mr_material_sub_cat AS s", 's.msubcat_id', 'i.mr_material_sub_cat_id')
                        ->where("i.mcat_id", $category->mcat_id)
                        ->orderBy('s.subcat_index','ASC')
                        ->orderBy('i.tab_index','ASC')
                        ->get();
            $cat[$category->mcat_id] = $category;
            $catItem[$category->mcat_id] = collect($subItem)->groupBy('msubcat_name',true)->toArray();
        }
        $bomItem = view('merch.common.get_cat_item_modal', compact('cat','catItem','existItem'));
        
        $items = DB::table("mr_cat_item as i")
        ->select('i.*', 'ics.msubcat_name', 'ics.subcat_index')
        ->leftJoin("mr_material_sub_cat AS ics", 'ics.msubcat_id', 'i.mr_material_sub_cat_id')
        ->orderBy('i.mcat_id','ASC')
        ->orderBy('ics.subcat_index','ASC')
        ->orderBy('i.tab_index','ASC')
        ->get();

        $getSupplier = SupplierItemType::with('supplier')->get()->groupBy('mcat_id',true);
        
        $getUomItem = CatItemUom::with('uom')->get()->groupBy('mr_cat_item_id', true);
        $getUom = UOM::get();
        $colors = DB::table("mr_material_color")
            ->get();

        $countryList = DB::table('mr_country')->pluck('cnt_name','cnt_id');

        $itemList = "";

        //Loop  for Selected category list
        /*foreach ($modalCats as $cat)
        {
            $itemList .= "<div class=\"col-sm-4\">";


            $name = strtolower(str_replace(" ", "_", $cat->mcat_name));

            $sl = 1;
            $itemList .= "<ul class=\"list-unstyled\" style=\"padding-top: 15px;\">";

            $itemList .= "<li>
                         <label>
                         <input name=\"selected_item[]\" type=\"checkbox\" value=\" $cat->mcat_id \" class=\"ace checkbox-input\"><span class=\"lbl\" style=\"font-size: 14px;\">$cat->mcat_name</span>
                         </label>
                    </li>";
            $itemList .= "</ul>";
            $itemList .= "</div>";
        }*/


    	return view("merch.style_bom.style_bom_form", compact(
    		"style",
    		"samples",
    		"operations",
    		"machines",
    		"bomItem",
            "stylebom_id",
            "countryList",
            "itemList",
            'bomItemData',
            'modalCats',
            'items',
            'colors',
            'getSupplier',
            'getUom',
            'getUomItem'
    	));
    }
    public function getItemBomData($category,$id=null)
    {
        $items = DB::table("mr_cat_item as i")
                    ->select('i.*')
                    ->leftJoin("mr_material_sub_cat AS s", 's.msubcat_id', 'i.mr_material_sub_cat_id')
                    ->orderBy('s.subcat_index','ASC')
                    ->orderBy('i.tab_index','ASC')
                    ->where('i.mcat_id',$category)
                    ->get();


        $bomItem     = "";
        foreach ($items as $key => $thisitem) {

            $category_id = $thisitem->mcat_id;
            $item_id = $thisitem->id;

            $boms = null;
            if($id !=null){
                $boms = DB::table("mr_stl_bom_n_costing AS b")
                        ->select(
                            "b.id",
                            "b.mr_style_stl_id",
                            "b.item_description",
                            "b.clr_id",
                            "b.size",
                            "b.mr_supplier_sup_id",
                            "b.mr_article_id",
                            "b.mr_composition_id",
                            "com.comp_name",
                            "b.mr_construction_id",
                            "con.construction_name",
                            "b.consumption",
                            "b.extra_percent",
                            "b.uom"
                        )
                        ->leftJoin('mr_construction AS con', 'con.id', 'b.mr_construction_id')
                        ->leftJoin('mr_composition AS com', 'com.id', 'b.mr_composition_id')
                        ->where([
                            "b.mr_style_stl_id" => $id,
                            "b.mr_material_category_mcat_id" => $category_id,
                            "b.mr_cat_item_id" => $item_id
                        ])
                        ->first();

                if($boms){
                    $boms->extra_qty = number_format(($boms->consumption/100)*$boms->extra_percent,2);
                    $boms->total_value = number_format(($boms->consumption+$boms->extra_qty),2);
                }
            }
           // dd($boms);


            $item = $this->item($item_id, $category_id);
            // get color list with name
            $color = $this->color("clr_id[]", $boms->clr_id??null, [
                "class" => "form-control input-sm no-select color select2",
                "placeholder"     => "Select"
            ]);

            // get supplier list by category id
            $cl='';
            if($category_id == 1){
                $cl = 'fab-sup';
            }else if($category_id == 2){
                $cl = 'sew-sup';
            }else if($category_id == 3){
                $cl = 'fin-sup';
            }

            $supplier = $this->supplier($category_id, "mr_supplier_sup_id[]", $boms->mr_supplier_sup_id??null, [
                "class" => "form-control input-sm no-select supplier select2 ".$cl,
                "placeholder"     => "Select",
                "data-validation" => "required",
                "id" => 'sup'.$item_id

            ]);

            $uom = $this->uomItemWise($item_id, "uom[]", $boms->uom??'', [
                "class" => "form-control input-sm no-select",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);
                


            $article = $this->article($boms->mr_supplier_sup_id??null, "mr_article_id[]", $boms->mr_article_id??null, [
                        "class" => "form-control input-sm no-select bom_article",
                        "placeholder"     => "Select"
                    ]);
            // get UoM list
            $uom = $this->uomItemWise($item_id, "uom[]", $boms->uom??null, [
                "class" => "form-control input-sm no-select select2",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);

            $bomItem .= view('merch.common.get_item_bom', compact('item','color','supplier','uom','article','boms'))->render();
        }

        return $bomItem;
    }



	// get category & item
    public function item($item_id = "", $category_id = "")
    {
    	return DB::table("mr_cat_item AS i")
			->select(
				"i.id",
				"c.mcat_id",
				"c.mcat_name",
				"i.item_name",
				"i.item_code"
			)
			->leftJoin("mr_material_category AS c", "c.mcat_id", "i.mcat_id")
			->where("i.id", $item_id)
			->where("i.mcat_id", $category_id)
			->first();
    }

	// get color list with name
    public function color($name = "", $selected = "", $option = [])
    {
    	$colors = DB::table("mr_material_color")
    		->get();
        $colorData = array();
        foreach ($colors as $color) {
            $colorData[$color->clr_id] =  $color->clr_name.' - '.$color->clr_code;
        }
        //dd($colorDatas);
    	$selectedColor = DB::table("mr_material_color")
    		->where("clr_id", $selected)
    		->value("clr_code");

    	$option["style"] = "background:$selectedColor";

        $html = "<div class='input-group'>";
        $html .=  Form::select($name, $colorData, $selected, $option);
        //edited on 03-10-2019--->>
        $html .= "<span class='input-group-btn'><button type='button' id='add_new_color_button'  data-toggle='modal'  data-target='.newColorModal' class='btn btn-sm btn-primary add_new_color_button'>+</button></span></div>";
        return $html;

    	// return Form::select($name, $colorData, $selected, $option);
    }

	// get supplier list by item id
    public function supplier($mcat_id = "", $name = "", $selected = "", $option = [])
    {
        $supid = '';
        if(isset($option['id']))
        {
            $supid = $option['id'];
        }

    	$suppliers = DB::table("mr_supplier_item_type AS si")
    		->leftJoin("mr_supplier AS s", "s.sup_id", "=", "si.mr_supplier_sup_id")
    		->where("si.mcat_id", $mcat_id)
    		->pluck("s.sup_name", "s.sup_id");

        $html = "<div class='input-group'>";
        $html .=  Form::select($name, $suppliers, $selected, $option);
        //edited on 03-10-2019--->>
        $html .= "<span class='input-group-btn'><button type='button' id='add_new_supplier_button'  data-toggle='modal' data-id='".$supid."' data-cat='".$mcat_id."' data-target='.newSupplierModal' class='btn btn-sm btn-primary'>+</button></span></div>";
        return $html;

    	//return Form::select($name, $suppliers, $selected, $option);
    }

	// get article list by supplier id
    public function article($supplier_id="", $name="", $selected="", $option = [])
    {
    	if (request()->has("supplier_id"))
    	{
    		$supplier_id = request()->get("supplier_id");
    		$name        = request()->get("name");
    		$selected    = request()->get("selected");
    		$option      = request()->get("option");
    	}

    	$articles = DB::table("mr_article")
    		->where("mr_supplier_sup_id", $supplier_id)
    		->pluck("art_name", "id");

    	$html = "<div class='input-group'>";
    	$html .=  Form::select($name, $articles, $selected, $option);
    	$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newArticleModal' class='btn btn-sm btn-primary'>+</button></span></div>";
    	return $html;
    }

	// get composition list by supplier id
    public function composition($supplier_id="", $name="", $selected="", $option = [])
    {
    	if (request()->has("supplier_id"))
    	{
    		$supplier_id = request()->get("supplier_id");
    		$name        = request()->get("name");
    		$selected    = request()->get("selected");
    		$option      = request()->get("option");
    	}

    	$compositions = DB::table("mr_composition")
    		->where("mr_supplier_sup_id", $supplier_id)
    		->pluck("comp_name", "id");

    	$html = "<div class='input-group'>";
    	$html .=  Form::select($name, $compositions, $selected, $option);
    	$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newCompositionModal' class='btn btn-sm btn-primary'>+</button></span></div>";
    	return $html;
    }

	// get construction list by supplier id
    public function construction($supplier_id="", $name="", $selected="", $option = [])
    {
    	if (request()->has("supplier_id"))
    	{
    		$supplier_id = request()->get("supplier_id");
    		$name        = request()->get("name");
    		$selected    = request()->get("selected");
    		$option      = request()->get("option");
    	}

    	$constructions = DB::table("mr_construction")
    		->where("mr_supplier_sup_id", $supplier_id)
    		->pluck("construction_name", "id");

    	$html = "<div class='input-group'>";
    	$html .=  Form::select($name, $constructions, $selected, $option);
    	$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newConstructionModal' class='btn btn-sm btn-primary'>+</button></span></div>";
    	return $html;
    }

	// get item wise UoM list
    public function uomItemWise($item, $name = "", $selected = "", $option = [])
    {
        $getUom = CatItemUom::getItemWiseUom($item);
        if(count($getUom) > 0){
            $list = array();
            foreach($getUom as $uom){
                $value = $uom->uom['measurement_name'];
                $list[$value] = $uom->uom['measurement_name'];
            }
        }else{
            $getUom = UOM::get()->toArray();
            $list = array_column($getUom, 'measurement_name', 'measurement_name');
        }
    	return Form::select($name, $list, $selected, $option);
    }
    // get UoM list
    public function uom($name = "", $selected = "", $option = [])
    {
        $list = [
            "Millimeter" => "Millimeter",
            "Centimeter" => "Centimeter",
            "Meter" => "Meter",
            "Inch" => "Inch",
            "Feet" => "Feet",
            "Yard" => "Yard",
            "Piece" => "Piece"
        ];
        return Form::select($name, $list, $selected, $option);
    }

    //get Compostion and Construction By Article ID
    public function compositionByArticle(Request $request)
    {
        $cons= DB::table('mr_construction')->where('mr_article_id', $request->article_id)->pluck('construction_name')->first();
        if($cons== null)
            $cons="N/A";

        $comp= DB::table('mr_composition')->where('mr_article_id', $request->article_id)->pluck('comp_name')->first();

        if($comp== null)
            $comp="N/A";

        $data["cons"]= $cons;
        $data["comp"]= $comp;

        return $data;
    }

    // store bom data
    public function store(Request $request)
    {
        //dd($request->all()); exit;
   	    $validator = Validator::make($request->all(), [
            "mr_style_stl_id"    => "required",
			"mr_material_category_mcat_id.*" => "required",
			"mr_cat_item_id.*"   => "required",
			"mr_supplier_sup_id.*" => "required",
			"uom.*"                => "required",
			"consumption.*"        => "required",
			"extra_percent.*"      => "required",
    	]);

    	if ($validator->fails())
    	{
    		return back()
    			->withErrors($validator)
    			->withInput()
	            ->with('error', "Incorrect Input!!");
    	}
    	else
    	{
			// Store Style Operation
    		if (is_array($request->mr_material_category_mcat_id) && sizeof($request->mr_material_category_mcat_id) > 0)
    		{
    			$insert = array();
    			for ($i=0; $i<sizeof($request->mr_material_category_mcat_id); $i++)
    			{
                    if(isset($request->mr_article_id[$i])){
                        $comp= DB::table('mr_composition')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                        $cons= DB::table('mr_construction')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                    }else{
                        $comp = null;
                        $cons = null;
                    }

    				$insert = array(
    					"mr_style_stl_id"    => $request->mr_style_stl_id,
    					"mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
                        "mr_cat_item_id"     => $request->mr_cat_item_id[$i],
                        "item_description"   => $request->item_description[$i],
                        "clr_id"             => $request->clr_id[$i],
                        "size"               => $request->size[$i],
                        "mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
                        "mr_article_id"      => $request->mr_article_id[$i],
                        "mr_composition_id"  => $comp,
                        "mr_construction_id" => $cons,
                        "uom"            => $request->uom[$i],
                        "consumption"    => $request->consumption[$i],
                        "extra_percent"  => $request->extra_percent[$i],
    				);
					$id = BomCosting::insertGetId($insert);

			    	//------------store log history--------------
			    	$this->logFileWrite("Style BOM created", $id);
			    	//---------------------------------------
    			}

                // return redirect("merch/style_bom")
                //     ->with('success', 'Save successful.');
                 return redirect('merch/style_bom/'.$request->style_bom_id.'/edit')->with('success', 'Save successful.');


    		}
    		else
    		{
	    		return back()
	    			->withInput()
		            ->with('error', "Incorrect Input!");
    		}

    	}
    }

    // create new article by supplier id
    public function createArticle(Request $request)
    {
    	$data = array();
        $comp_name="";
        $cons_name="";
    	if (!empty($request->supplier_id) && !empty($request->article_name))
    	{
            DB::beginTransaction();  //new line added 03-10-2019
    		try
    		{
				$id = DB::table("mr_article")->insertGetId([
					"art_name" => $request->article_name,
					"mr_supplier_sup_id" => $request->supplier_id,
				]);

                //If Request Has Compostion Name then store
                if($request->has('art_composition')){
                    DB::table('mr_composition')->insert([
                        "comp_name" => $request->art_composition,
                        "mr_supplier_sup_id" => $request->supplier_id,
                        "mr_article_id"=> $id
                    ]);
                    $comp_name= $request->art_composition;
                }

                //If Request Has Construction Name then store
                if($request->has('art_construction')){
                    DB::table('mr_construction')->insert([
                        "construction_name" => $request->art_construction,
                        "mr_supplier_sup_id" => $request->supplier_id,
                        "mr_article_id"=> $id
                    ]);
                    $cons_name= $request->art_construction;
                }

		    	$articles = DB::table("mr_article")
		    		->where("mr_supplier_sup_id", $request->supplier_id)
		    		->pluck("art_name", "id");



		    	$html = "<div class='input-group'>";
		    	$html .=  Form::select($request->name, $articles, $id, $request->option);
		    	$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newArticleModal' class='btn btn-sm btn-primary'>+</button></span></div>";

		    	$data["status"] = true;
		    	$data["message"] = "Saved successful";
		    	$data["result"] = $html;
                $data["comp"] = $comp_name;
                $data["cons"] = $cons_name;

                DB::commit();  //new line added 03-10-2019
    		}
    		catch(\Exception $e)
    		{
                DB::rollback(); //new line added 03-10-2019
		    	$data["status"] = false;
		    	// $data["message"] = "Article already exists!";
                $data["message"] = "Article already exists!\n".$e->getMessage();
    		}
    	}
    	else
    	{
    		$data['status'] = false;
    		$data['message'] = "Please fill up all required fields!";
    	}
        $data["comp_name"]= $comp_name;
        $data["cons_name"]= $cons_name;


    	return Response::json($data);
    }

    // create new composition by supplier id
    public function createComposition(Request $request)
    {
    	$data = array();

    	if (!empty($request->supplier_id) && $request->composition_name)
    	{
    		try
    		{
				$id = DB::table("mr_composition")->insertGetId([
					"comp_name" => $request->composition_name,
					"mr_supplier_sup_id" => $request->supplier_id,
				]);

		    	$compositions = DB::table("mr_composition")
		    		->where("mr_supplier_sup_id", $request->supplier_id)
		    		->pluck("comp_name", "id");

		    	$html = "<div class='input-group'>";
		    	$html .=  Form::select($request->name, $compositions, $id, $request->option);
		    	$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newCompositionModal' class='btn btn-sm btn-primary'>+</button></span></div>";

		    	$data["status"] = true;
		    	$data["message"] = "Saved successful";
		    	$data["result"] = $html;
    		}
    		catch(\Exception $e)
    		{
		    	$data["status"] = false;
		    	$data["message"] = "Composition already exists!";
    		}
    	}
    	else
    	{
    		$data['status'] = false;
    		$data['message'] = "Please fill up all required fields!";
    	}

    	return Response::json($data);
    }

    // create new construction by supplier id
    public function createConstruction(Request $request)
    {
    	$data = array();

    	if (!empty($request->supplier_id) && $request->construction_name)
    	{
    		try
    		{
				$id = DB::table("mr_construction")->insertGetId([
					"construction_name" => $request->construction_name,
					"mr_supplier_sup_id" => $request->supplier_id,
				]);

		    	$construction = DB::table("mr_construction")
		    		->where("mr_supplier_sup_id", $request->supplier_id)
		    		->pluck("construction_name", "id");

		    	$html = "<div class='input-group'>";
		    	$html .=  Form::select($request->name, $construction, $id, $request->option);
		    	$html .= "<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newCompositionModal' class='btn btn-sm btn-primary'>+</button></span></div>";

		    	$data["status"] = true;
		    	$data["message"] = "Saved successful";
		    	$data["result"] = $html;
    		}
    		catch(\Exception $e)
    		{
		    	$data["status"] = false;
		    	$data["message"] = "Construction already exists!";
    		}
    	}
    	else
    	{
    		$data['status'] = false;
    		$data['message'] = "Please fill up all required fields!";
    	}

    	return Response::json($data);
    }

    public function saveNewColor(Request $request){
        // dd($request->march_color);exit;
        ACL::check(["permission" => "mr_setup"]);
        #---------------------------------------------------#

          $validator= Validator::make($request->all(),[
            'march_color'        =>'required|max:50'
        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!!");
        }
        else{

            $data= new MaterialColor();
            $data->clr_name = $this->quoteReplaceHtmlEntry($request->march_color);
            $data->clr_code = $this->quoteReplaceHtmlEntry($request->march_color_code);

            $data->save();

            /*$id= MainCategry::orderBy('mcat_id', 'DESC')
                    ->pluck('mcat_id')
                    ->first();*/

            $last_id = $data->id;

            $this->logFileWrite("Material Color Saved", $last_id);

            if(!empty($request->march_file)){
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
                        ///File Url Store //////////
                        MaterialColorAttach::insert([
                                'clr_id'               => $last_id,
                                'col_attach_url'       => $march_file
                            ]);
                        }
                    }
                }
            }

        }
        //After save.
        $colors = MaterialColor::select(['clr_name', 'clr_id', 'clr_code'])->get();
        $color = MaterialColor::where('clr_id', $last_id)->select(['clr_name', 'clr_id', 'clr_code'])->get();
        $ret['last_id']=$last_id;
        $ret['colors'] =$colors;
        $ret['color'] =$color;
        return Response::json($ret);
    }


    /**
     * Edit the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function editForm(Request $request)
    {
        $id = $request->id;
        $buyerData = DB::table('mr_buyer');
        $buyerDataSql = $buyerData->toSql();

        $bomNcostingData = DB::table('mr_stl_bom_n_costing');
        $bomNcostingDataSql = $bomNcostingData->toSql();

        $productTypeData = DB::table('mr_product_type');
        $productTypeDataSql = $productTypeData->toSql();

        $garmentTypeData = DB::table('mr_garment_type');
        $garmentTypeDataSql = $garmentTypeData->toSql();

        $seasonData = DB::table('mr_season');
        $seasonDataSql = $seasonData->toSql();

        $queryData = DB::table("mr_style AS s")
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
                "s.stl_status",
                "sb.mr_style_stl_id",
                "sb.bom_term"
            );
            $queryData->leftjoin(DB::raw('(' . $buyerDataSql. ') AS b'), function($join) use ($buyerData) {
                $join->on("b.b_id", "s.mr_buyer_b_id")->addBinding($buyerData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $bomNcostingDataSql. ') AS sb'), function($join) use ($bomNcostingData) {
                $join->on("s.stl_id", "sb.mr_style_stl_id")->addBinding($bomNcostingData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $productTypeDataSql. ') AS t'), function($join) use ($productTypeData) {
                $join->on("t.prd_type_id", "s.prd_type_id")->addBinding($productTypeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $garmentTypeDataSql. ') AS g'), function($join) use ($garmentTypeData) {
                $join->on("g.gmt_id", "s.gmt_id")->addBinding($garmentTypeData->getBindings());
            });
            $queryData->leftjoin(DB::raw('(' . $seasonDataSql. ') AS se'), function($join) use ($seasonData) {
                $join->on("se.se_id", "s.mr_season_se_id")->addBinding($seasonData->getBindings());
            });
            
            $style = $queryData->whereIn('b.b_id', auth()->user()->buyer_permissions())->where("s.stl_id", $id)
            ->first();

        //sampleTypes
        $samples = DB::table("mr_stl_sample AS ss")
            ->select(DB::raw("GROUP_CONCAT(st.sample_name SEPARATOR ', ') AS name"))
            ->leftJoin("mr_sample_type AS st", "st.sample_id", "ss.sample_id")
            ->where("ss.stl_id", $id)
            ->first();

        //operations
        $operations = DB::table("mr_style_operation_n_cost AS oc")
            ->select("o.opr_name")
            ->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
            ->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
            ->where("oc.mr_style_stl_id", $id)
            ->first();

        //machines
        $machines = DB::table("mr_style_sp_machine AS sm")
            ->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
            ->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
            ->where("sm.stl_id", $id)
            ->first();

        //costing button
        // $costing = DB::table("mr_stl_bom_n_costing AS sb")
        //     ->select(
        //         "s.stl_id",
        //         "sb.mr_style_stl_id",
        //         "sb.bom_term"
        //     )
        //     ->leftJoin("mr_style AS s", "s.stl_id", "=",  "sb.mr_style_stl_id")
        //     ->leftJoin("mr_buyer AS b", "b.b_id", "=", "s.mr_buyer_b_id")
        //     ->whereIn('b.b_id', auth()->user()->buyer_permissions())
        //     ->where("s.stl_id", $id)
        //     ->first();
        // dd($costing);

        if (empty($style->bom_term))

            {
                $costingButton = "<a href=".url('merch/style_costing/'.$style->stl_id.'/create')." class=\"btn btn-warning btn-xx pull-right\" data-toggle=\"tooltip\" rel='tooltip' data-tooltip-location='top' data-tooltip='Costing' >Costing</a>";
            }
            else
            {
                $costingButton = "<a href=".url('merch/style_costing/'.$style->stl_id.'/edit')." class=\"btn btn-warning btn-xx pull-right\" data-toggle=\"tooltip\" rel='tooltip' data-tooltip-location='top' data-tooltip='Costing' >Costing</a>";
            }
            //dd($costingButton);

        //---------- BOM ITEM MODAL----------------

        $existItem = DB::table('mr_stl_bom_n_costing')
                        ->where('mr_style_stl_id',$id)
                        ->pluck('mr_cat_item_id')
                        ->toArray();

        //show category select is Modal
        $modalCats = DB::table("mr_material_category AS c")->get();
        $catItem = [];
        $cat = [];
        $bomItemData = '';
        foreach ($modalCats as $category){
            // $bomItemData .= $this->getItemBomData($category->mcat_id,$id);
            $subItem = DB::table("mr_cat_item AS i")
                        ->select(
                            "i.mcat_id",
                            "i.item_name",
                            "i.id",
                            "s.msubcat_id",
                            "s.msubcat_name"
                        )
                        ->leftJoin("mr_material_sub_cat AS s", 's.msubcat_id', 'i.mr_material_sub_cat_id')
                        ->where("i.mcat_id", $category->mcat_id)
                        ->orderBy('s.subcat_index','ASC')
                        ->orderBy('i.tab_index','ASC')
                        ->get();
            $cat[$category->mcat_id] = $category;
            $catItem[$category->mcat_id] = collect($subItem)->groupBy('msubcat_name',true)->toArray();
        }
        $modalItem = view('merch.common.get_cat_item_modal', compact('cat','catItem','existItem'));


        $items = DB::table("mr_cat_item as i")
        ->select('i.*', 'ics.msubcat_name', 'ics.subcat_index')
        ->leftJoin("mr_material_sub_cat AS ics", 'ics.msubcat_id', 'i.mr_material_sub_cat_id')
        ->orderBy('i.mcat_id','ASC')
        ->orderBy('ics.subcat_index','ASC')
        ->orderBy('i.tab_index','ASC')
        ->get();

        $getSupplier = SupplierItemType::with('supplier')->get()->groupBy('mcat_id',true);
        $getSupArticle = Article::get()->groupBy('mr_supplier_sup_id',true);
        $getUomItem = CatItemUom::with('uom')->get()->groupBy('mr_cat_item_id', true);
        $getUom = UOM::get();
        $colors = DB::table("mr_material_color")
            ->get();
        //---------- END BOM ITEM MODAL----------------

        /*
        * LOAD BOM ITEM DATA
        *---------------------------------------------
        */
        $boms = DB::table("mr_stl_bom_n_costing AS b")
            ->select(
                "b.id",
                "b.mr_style_stl_id",
                "b.mr_material_category_mcat_id",
                "c.mcat_name",
                "b.mr_cat_item_id",
                "i.item_name",
                "i.item_code",
                "b.item_description",
                "b.clr_id",
                "b.size",
                "b.mr_supplier_sup_id",
                "b.mr_article_id",
                "b.mr_composition_id",
                "com.comp_name",
                "b.mr_construction_id",
                "con.construction_name",
                "b.consumption",
                "b.extra_percent",
                "b.uom"
            )
            ->leftJoin("mr_material_category AS c", function($join) {
                $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
            })
            ->leftJoin("mr_cat_item AS i", function($join) {
                $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                $join->on("i.id", "=", "b.mr_cat_item_id");
            })
            ->leftJoin('mr_construction AS con', 'con.id', 'b.mr_construction_id')
            ->leftJoin('mr_composition AS com', 'com.id', 'b.mr_composition_id')
            ->where("b.mr_style_stl_id", $id)
            ->orderBy('b.mr_material_category_mcat_id');


        // $mrCatIdArray = array_column($boms->get()->toArray(), 'mr_material_category_mcat_id');
        // $mrCatNameArray = $boms->pluck('mcat_name','mr_material_category_mcat_id')->toArray();
        $bomCostingArray = $boms->pluck('id')->toArray();
        $bomItemsData = $boms->get()->keyBy('mr_cat_item_id');
        // $bomArray = [];
        // return ($bomItemsData);exit;
        // $catItems =(clone $boms)->select('mr_cat_item_id')->get();
        // $oldArray =[];
        // foreach ($catItems as $key => $item) {
        //     array_push($oldArray, strval($item->mr_cat_item_id));
        // }
        //dd($oldArray);
        //$oldArray =["25","26"];
        //group by category data
        // $gropup1 = (clone $boms)->get()->where('mr_material_category_mcat_id',1);
        // $gropup2= (clone $boms)->get()->where('mr_material_category_mcat_id',2);
        // $gropup3 = (clone $boms)->get()->where('mr_material_category_mcat_id',3);
        // $bomItemData1 = $this->getGroupByItems($gropup1,'fab');
        // $bomItemData2 = $this->getGroupByItems($gropup2,'sa');
        // $bomItemData3 = $this->getGroupByItems($gropup3,'fa');
        //dd($bomItemData1);

        $countryList = DB::table('mr_country')->pluck('cnt_name','cnt_id');
        // $items = DB::table("mr_material_category")->get();
        $itemList = "";

        //Loop  for Selected category list
        /*foreach ($modalCats as $cat)
        {
            $itemList .= "<div class=\"col-sm-4\">";
            $name = strtolower(str_replace(" ", "_", $cat->mcat_name));

            $sl = 1;
            $itemList .= "<ul class=\"list-unstyled\" style=\"padding-top: 15px;\">";

            $itemList .= "<li>
                         <label>
                         <input name=\"selected_item[]\" type=\"checkbox\" value=\" $cat->mcat_id \" class=\"ace checkbox-input\"><span class=\"lbl\" style=\"font-size: 14px;\">$cat->mcat_name</span>
                         </label>
                    </li>";
            $itemList .= "</ul>";
            $itemList .= "</div>";
        }*/
        // return $itemList;
        /*
        * END BOM ITEM DATA
        *---------------------------------------------
        */
        return view("merch.style_bom.style_bom_edit", compact(
            "style",
            // "bomArray",
            "bomCostingArray",
            // "mrCatNameArray",
            "samples",
            "operations",
            "machines",
            "modalItem",
            // "bomItemData1",
            // "bomItemData2",
            // "bomItemData3",
            "itemList",
            // 'oldArray',
            "countryList",
            "costingButton",
            "bomItemData",
            "modalCats",
            'items',
            'getSupplier',
            'getUom',
            'colors',
            'getUomItem',
            'bomItemsData',
            'getSupArticle'
        ));
    }


    public function getGroupByItems($group){
        $bomItemData ="";
        foreach ($group as $bom)
        {
            $bomArray[$bom->mr_material_category_mcat_id][] = $bom->mr_cat_item_id;
            // get color list with name
            $color = $this->color("clr_id[]", $bom->clr_id, [
                "class" => "form-control input-sm no-select color",
                "placeholder"     => "Select"
                //"data-validation" => "required"
            ]);

            // get supplier list by category id
            $supplier = $this->supplier($bom->mr_material_category_mcat_id, "mr_supplier_sup_id[]", $bom->mr_supplier_sup_id, [
                "class" => "form-control input-sm no-select supplier",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);

            // get UoM list
            $uom = $this->uomItemWise($bom->mr_cat_item_id, "uom[]", $bom->uom, [
                "class" => "form-control input-sm no-select",
                "placeholder"     => "Select",
                "data-validation" => "required"
            ]);

            // get Article list
            $article = $this->article($bom->mr_supplier_sup_id, "mr_article_id[]", $bom->mr_article_id, [
                "class" => "form-control input-sm no-select bom_article",
                "placeholder"     => "Select"
            ]);


            // get composition list
            $composition = $this->composition($bom->mr_supplier_sup_id, "mr_composition_id[]", $bom->mr_composition_id, [
                "class" => "form-control input-sm no-select",
                "placeholder"     => "Select"
            ]);

            // get construction list
            $construction = $this->construction($bom->mr_supplier_sup_id, "mr_construction_id[]", $bom->mr_construction_id, [
                "class" => "form-control input-sm no-select",
                "placeholder"     => "Select"
                ]
            );

            $extra_qty = number_format((($bom->consumption/100)*$bom->extra_percent), 2);
            $total     = number_format(($bom->consumption+$extra_qty), 2);
            if($bom->comp_name == null)
                 $comp_name= "N/A";
            else
                $comp_name= $bom->comp_name;

            if($bom->construction_name == null)
                $construction_name= "N/A";
            else
                $construction_name= $bom->construction_name;


            $bomItemData .= "<tr id=\"$bom->mr_cat_item_id\" data-catId=\"$bom->mr_material_category_mcat_id\">
                <td  class=\"vertical-align-center fixed-side\">
                    <input type=\"hidden\" class=\"form-control input-sm\"  data-validation=\"required\" value=\"$bom->mcat_name\" readonly/>
                     <span style=\"font-size: 9px;\">$bom->mcat_name</span>
                </td>
                <td class=\"fixed-side\">
                    <input type=\"hidden\" name=\"id[]\" value=\"$bom->id\"/>
                    <input type=\"hidden\" name=\"mr_material_category_mcat_id[]\" value=\"$bom->mr_material_category_mcat_id\">
                    <input type=\"hidden\" class=\"form-control input-sm\"  data-validation=\"required\" value=\"$bom->item_name\" readonly/>
                    <input type=\"hidden\" name=\"mr_cat_item_id[]\" value=\"$bom->mr_cat_item_id\"> $bom->item_name
                </td>
                <td><input type=\"hidden\" class=\"form-control input-sm\"  data-validation=\"required\" value=\"$bom->item_code\" readonly/>$bom->item_code</td>
                <td><input type=\"text\" name=\"item_description[]\" class=\"form-control input-sm bg_field\" placeholder=\"Description\" value=\"$bom->item_description\"/></td>
                <td>$color</td>
                <td><input type=\"text\" name=\"size[]\" class=\"form-control input-sm\"  placeholder=\"Size/Width\" value=\"$bom->size\"/></td>
                <td>$supplier</td>
                <td>$article</td>
                <td class=\"comp_name\">$comp_name</td>
                <td class=\"construction_name\">$construction_name</td>
                <td>$uom</td>
                <td><input data-toggle=\"tooltip\" title=\"$bom->mcat_name > $bom->item_name \" type=\"text\" name=\"consumption[]\" class=\"form-control input-sm calc consumption tooltipped\" data-validation=\"required\" placeholder=\"Select\" onclick=\"this.select()\" value=\"$bom->consumption\" oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');\"/></td>
                <td><input data-toggle=\"tooltip\" title=\"$bom->mcat_name > $bom->item_name \" type=\"text\" name=\"extra_percent[]\" class=\"form-control input-sm calc extra tooltipped\"  placeholder=\"Extra\" onclick=\"this.select()\" data-validation=\"required\" value=\"$bom->extra_percent\" oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');\"/></td>
                <td><input type=\"text\" class=\"form-control input-sm qty\"  placeholder=\"Extra Qty\" data-validation=\"required\" readonly value=\"$extra_qty\"/></td>
                <td><input type=\"text\" class=\"form-control input-sm calc total\"  placeholder=\"Total\" data-validation=\"required\" readonly value=\"$total\"/></td>
            </tr>";

        }

        return $bomItemData;
    }

    public function update(Request $request)
    {
        //dd($request->all()); exit;
   	    $validator = Validator::make($request->all(), [
            "mr_style_stl_id"    => "required",
			"mr_material_category_mcat_id.*" => "required",
			"mr_cat_item_id.*"   => "required",
			"mr_supplier_sup_id.*" => "required",
			"uom.*"                => "required",
			"consumption.*"        => "required",
			"extra_percent.*"      => "required",
    	]);

    	if ($validator->fails())
    	{
    		return back()
    			->withErrors($validator)
    			->withInput()
	            ->with('error', "Incorrect Input!!");
    	}
        $input = $request->all();
        DB::beginTransaction();
    	try {
            $remove_item_list = $request->bom_costing_pre_id;
            // delete old data
            // BomCosting::where("mr_style_stl_id", $request->mr_style_stl_id)->delete();
            // Store Style Operation
            $insert = array();
            for ($i=0; $i<sizeof($request->mr_material_category_mcat_id); $i++)
            {
                if(isset($request->mr_article_id[$i])){

                    $comp= DB::table('mr_composition')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                    $cons= DB::table('mr_construction')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                }else{
                    $comp = null;
                    $cons = null;
                }
                $bomExist = BomCosting::where(['mr_style_stl_id' => $request->mr_style_stl_id, 'mr_cat_item_id' => $request->mr_cat_item_id[$i]])->first();

                if($request->id[$i]==0){
                    $insert = array(
                        "mr_style_stl_id"    => $request->mr_style_stl_id,
                        "mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
                        "mr_cat_item_id"     => $request->mr_cat_item_id[$i],
                        "item_description"   => $request->item_description[$i],
                        "clr_id"             => $request->clr_id[$i],
                        "size"               => $request->size[$i],
                        "mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
                        "mr_article_id"      => $request->mr_article_id[$i],
                        "mr_composition_id"  => $comp,
                        "mr_construction_id" => $cons,
                        "uom"            => $request->uom[$i],
                        "consumption"    => $request->consumption[$i],
                        "extra_percent"  => $request->extra_percent[$i]
                    );
                    $up_id = BomCosting::insertGetId($insert);

                    //------------store log history--------------
                    $this->logFileWrite("Style BOM updated", $up_id);
                    //---------------------------------------
                }
                else{
                    $searchExist = array_search($bomExist->id, $remove_item_list);
                    if($searchExist !== false) {
                        unset($remove_item_list[$searchExist]);
                    }
                    BomCosting::where('id', $request->id[$i])
                        ->update([
                            "mr_style_stl_id"    => $request->mr_style_stl_id,
                            "mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
                            "mr_cat_item_id"     => $request->mr_cat_item_id[$i],
                            "item_description"   => $request->item_description[$i],
                            "clr_id"             => $request->clr_id[$i],
                            "size"               => $request->size[$i],
                            "mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
                            "mr_article_id"      => $request->mr_article_id[$i],
                            "mr_composition_id"  => $comp,
                            "mr_construction_id" => $cons,
                            "uom"            => $request->uom[$i],
                            "consumption"    => $request->consumption[$i],
                            "extra_percent"  => $request->extra_percent[$i]
                        ]);
                    $this->logFileWrite("Style BOM updated", $request->id[$i]);
                }
            }
            if(!empty($remove_item_list)){
                foreach($remove_item_list as $k=>$remove_item){
                    BomCosting::where('id',$remove_item)->delete();
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Update successful.');
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {
        DB::table('mr_stl_bom_n_costing')->where('mr_style_stl_id', $request->id)->delete();

        $this->logFileWrite("Style Bom Deleted where Style Id", $request->id);
        return redirect('merch/style_bom')->with('success', "Delete successful!");
    }

    //Write Every Events in Log File
    public function logFileWrite($message, $event_id)
    {
        $log_message = date("Y-m-d H:i:s")." ".auth()->user()->associate_id." \"$message\" ".$event_id.PHP_EOL;
        $log_file = fopen('assets/log.txt', 'a');
        fwrite($log_file, $log_message);
        fclose($log_file);
    }

    public function ajaxSaveSupplier(Request $request)
    {
        //dd($request->all());
        ACL::check(["permission" => "mr_setup"]);
        #-----------------------------------------------------------#
        $validator= Validator::make($request->all(),[
            'sup_name' => 'required',
            'cnt_id' => 'required',
            'sup_address' => 'required',
            'sup_type' => 'required'
        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        else{
            $id= Supplier::orderBy('sup_id', 'DESC')->pluck('sup_id')->first();

            $data= new Supplier();
            $data->cnt_id = $request->cnt_id ;
            $data->sup_name = $request->sup_name ;
            $data->sup_address = $request->sup_address ;
            $data->sup_type = $request->sup_type ;


            if($data->save()){
                $last_id = $data->id;
                $id= Supplier::orderBy('sup_id', 'DESC')->pluck('sup_id')->first();
                for($i=0; $i<sizeof($request->scp_details); $i++){
                    SupplierContact::insert([
                        'sup_id' => $id,
                        'scp_details' => $request->scp_details[$i],
                    ]);
                }

                if(!empty($request->item_id)){
                for($i=0; $i<sizeof($request->item_id); $i++){
                    SupplierItemType::insert([
                        'mr_supplier_sup_id' => $last_id,
                        'mcat_id' => $request->item_id[$i],
                    ]);
                }
              }

              $this->logFileWrite("New Supplier Added", $last_id);

                echo $data;exit;
            }
            else{
                echo $data = [];exit;
            }
        }
    }

    public function ajaxSaveBomInfo(Request $request)
    {
        //dd($request->all()); exit;
        if (is_array($request->mr_material_category_mcat_id) && sizeof($request->mr_material_category_mcat_id) > 0)
            {
                $insert = array();
                for ($i=0; $i<sizeof($request->mr_material_category_mcat_id); $i++)
                {

                    if(isset($request->mr_article_id[$i])){
                        $comp= DB::table('mr_composition')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                        $cons= DB::table('mr_construction')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                    }else{
                        $comp = null;
                        $cons = null;
                    }

                    $insert = array(
                        "mr_style_stl_id"    => $request->mr_style_stl_id,
                        "mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
                        "mr_cat_item_id"     => $request->mr_cat_item_id[$i],
                        "item_description"   => $request->item_description[$i],
                        "clr_id"             => $request->clr_id[$i],
                        "size"               => $request->size[$i],
                        "mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
                        "mr_article_id"      => $request->mr_article_id[$i],
                        "mr_composition_id"  => $comp,
                        "mr_construction_id" => $cons,
                        "uom"            => $request->uom[$i],
                        "consumption"    => $request->consumption[$i],
                        "extra_percent"  => $request->extra_percent[$i],
                    );
                    $id = BomCosting::insertGetId($insert);

                    //------------store log history--------------
                    $this->logFileWrite("Style BOM saved", $id);
                    //---------------------------------------
                }

                // return redirect("merch/style_bom")
                //     ->with('success', 'Save successful.');
                 return Response::json('true');


            }
            else
            {
                return Response::json('false');
            }
    }


    public function ajaxUpdateBomInfo(Request $request)
    {
        //dd($request->all()); exit;

        $input = $request->all();
        DB::beginTransaction();
        try {
            $remove_item_list = $request->bom_costing_pre_id;

            //dd($remove_item_list);
            $insert = array();
            for ($i=0; $i<sizeof($request->mr_material_category_mcat_id); $i++)
            {
                if(isset($request->mr_article_id[$i])){
                    $comp= DB::table('mr_composition')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                    $cons= DB::table('mr_construction')->where('mr_article_id', $request->mr_article_id[$i])->pluck('id')->first();
                }else{
                    $comp = null;
                    $cons = null;
                }
                $bomExist = BomCosting::where(['mr_style_stl_id' => $request->mr_style_stl_id, 'mr_cat_item_id' => $request->mr_cat_item_id[$i]])->first();

                if($request->id[$i]==0){
                    $insert = array(
                        "mr_style_stl_id"    => $request->mr_style_stl_id,
                        "mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
                        "mr_cat_item_id"     => $request->mr_cat_item_id[$i],
                        "item_description"   => $request->item_description[$i],
                        "clr_id"             => $request->clr_id[$i],
                        "size"               => $request->size[$i],
                        "mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
                        "mr_article_id"      => $request->mr_article_id[$i],
                        "mr_composition_id"  => $comp,
                        "mr_construction_id" => $cons,
                        "uom"            => $request->uom[$i],
                        "consumption"    => $request->consumption[$i],
                        "extra_percent"  => $request->extra_percent[$i]
                    );
                    $up_id = BomCosting::insertGetId($insert);

                    //------------store log history--------------
                    $this->logFileWrite("Style BOM updated", $up_id);
                    //---------------------------------------
                }
                else{
                    $searchExist = array_search($bomExist->id, $remove_item_list);
                    if($searchExist !== false) {
                        unset($remove_item_list[$searchExist]);
                    }
                    BomCosting::where('id', $request->id[$i])
                        ->update([
                            "mr_style_stl_id"    => $request->mr_style_stl_id,
                            "mr_material_category_mcat_id" => $request->mr_material_category_mcat_id[$i],
                            "mr_cat_item_id"     => $request->mr_cat_item_id[$i],
                            "item_description"   => $request->item_description[$i],
                            "clr_id"             => $request->clr_id[$i],
                            "size"               => $request->size[$i],
                            "mr_supplier_sup_id" => $request->mr_supplier_sup_id[$i],
                            "mr_article_id"      => $request->mr_article_id[$i],
                            "mr_composition_id"  => $comp,
                            "mr_construction_id" => $cons,
                            "uom"            => $request->uom[$i],
                            "consumption"    => $request->consumption[$i],
                            "extra_percent"  => $request->extra_percent[$i]
                        ]);
                    $this->logFileWrite("Style BOM updated", $request->id[$i]);
                }
            }
            if(!empty($remove_item_list)){
                foreach($remove_item_list as $k=>$remove_item){
                    BomCosting::where('id',$remove_item)->delete();
                }
            }
            DB::commit();
            return Response::json('true');
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            return Response::json($bug);
        }

    }

}
