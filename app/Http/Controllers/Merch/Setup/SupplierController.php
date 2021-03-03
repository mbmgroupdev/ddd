<?php

namespace App\Http\Controllers\Merch\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merch\Country;
use App\Models\Merch\Supplier;
use App\Models\Merch\SupplierContact;
use App\Models\Merch\McatItem;
use App\Models\Merch\SupplierItemType;
use Yajra\DataTables\DataTables;
use Validator, DB;
use Auth;

class SupplierController extends Controller
{
    public function showForm()
    {

        $unit_id = DB::table('users')
            ->select('hr_as_basic_info.as_unit_id')
            ->join('hr_as_basic_info','users.associate_id','=','hr_as_basic_info.associate_id')
            ->where('id',Auth::id())
            ->pluck('as_unit_id')
            ->first();
    	$countryList= Country::pluck('cnt_name', 'cnt_id');

        $itemList = DB::table("mr_material_category")
                ->get();
        
    	return view('merch/setup/supplier', compact('countryList','itemList','unit_id'));
    }

    public function getData(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $data = DB::table('mr_supplier AS s')
                ->select(
                    's.*',
                    'c.cnt_name')
                ->leftJoin('mr_country AS c', 's.cnt_id', 'c.cnt_id')
                ->orderBy('sup_id', 'DESC')
                ->get();
        $supContactPerson= DB::table('mr_sup_contact_person_info')
            ->get()->toArray();
        $getContactPerson = collect($supContactPerson)->groupBy('sup_id',true)->map(function($row) {
            return collect($row)->pluck('scp_details')->toArray();
        });

        $items = DB::table('mr_supplier_item_type as itemtype')
        ->leftJoin('mr_material_category AS c','itemtype.mcat_id','=','c.mcat_id')
        ->get()->toArray();
        $getItems = collect($items)->groupBy('mr_supplier_sup_id',true)->map(function($row) {
            return collect($row)->pluck('mcat_name')->toArray();
        });

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('item_type', function ($data) use ($getItems) {
            if(isset($getItems[$data->sup_id]) && count($getItems[$data->sup_id]) > 0){
              return implode(', ',$getItems[$data->sup_id]);
            }
            return ''; 
        })
        ->addColumn('contact_person', function ($data) use ($getContactPerson) {
            if(isset($getContactPerson[$data->sup_id]) && count($getContactPerson[$data->sup_id]) > 0){
              return implode(', ',$getContactPerson[$data->sup_id]);
            }
            return '';
        })
        
        // ->addColumn('status', function ($data) {
        //     return $data->status == 2 ? 'Approved' : "Pending";
        // })
        
        ->addColumn('action', function ($data) {
            
            // return '<div class="btn-group"><a type="button" class="btn btn-sm btn-primary generate-drawer text-white" title="Update" data-url="'.url('merch/setup/supplier_edit_ajax/'.$data->sup_id).'" data-headline="'.$data->sup_name.'"><i class="ace-icon fa fa-pencil bigger-120"></i></a><a href="'.url('merch/setup/supplier_delete/'.$data->sup_id).'" type="button" class="btn btn-sm btn-danger" onclick=\'return confirm("Are you sure you want to delete this supplier?");\' title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a></div>';
            return '<div class="btn-group"><a href="'.url('merch/setup/supplier_delete/'.$data->sup_id).'" type="button" class="btn btn-sm btn-danger" onclick=\'return confirm("Are you sure you want to delete this supplier?");\' title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a></div>';
        })
        ->rawColumns(['DT_RowIndex', 'supplier_name', 'sup_type', 'item_type','sup_address', 'contact_person','action'])
        ->make(true);
    }

    public function saveData(Request $request){

        $validator= Validator::make($request->all(),[
            'sup_name' => 'required|max:150',
            'cnt_id' => 'required|max:11',
            'sup_address' => 'required|max:128',
            'sup_type' => 'required|max:11'
        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->withErrors($validator);
        }

        try {
            $data= new Supplier();
            $data->cnt_id = $request->cnt_id ;
            $data->unit_id = $request->unit_id;
            $data->sup_name = $this->quoteReplaceHtmlEntry($request->sup_name) ;
            $data->sup_address = $this->quoteReplaceHtmlEntry($request->sup_address) ;
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

                if(!empty($request->items)){
                    for($i=0; $i<sizeof($request->items); $i++){
                        SupplierItemType::insert([
                            'mr_supplier_sup_id' => $last_id,
                            'mcat_id' => $request->item_id[$i],
                        ]);
                    }

                  $this->logFileWrite("Supplier Saved", $last_id);

                }

                return back()->with('success', "Saved Successfully!!");
            }
            else{
                return back()->with('error', 'Error saving data!!');
            }    
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                return back()->with('error', "Supplier Name Already Exists");
            }
            return back()->with('error', $bug1);
        }
        
    }
    public function ajaxSaveSupplier(Request $request){

        $request->validate([
            'sup_name'    => 'required',
            'cnt_id'      => 'required',
            'sup_address' => 'required',
            'sup_type'    => 'required'
        ]);
        $data = array();
        $data['type'] = 'error';
        $input = $request->all();
        // check existing supplier
        $input['sup_name'] = $this->quoteReplaceHtmlEntry($request->sup_name);
        $supplier = Supplier::checkExistSupplier($input);

        if($supplier != null){
            $data['message'] = ' This Supplier already exists';
            return response()->json($data);
        }
        
        DB::beginTransaction();
        try {
            $sup= new Supplier();
            $sup->cnt_id = $request->cnt_id ;
            $sup->sup_name = $this->quoteReplaceHtmlEntry($request->sup_name);
            $sup->sup_address = $this->quoteReplaceHtmlEntry($request->sup_address);
            $sup->sup_type = $request->sup_type;
            $sup->created_by = auth()->user()->id;

            if($sup->save()){
                $last_id = $sup->id;
                if(count($request->scp_details) > 0){
                    for($i=0; $i<sizeof($request->scp_details); $i++){
                        if($request->scp_details[$i] != null){
                            SupplierContact::insert([
                                'sup_id' => $last_id,
                                'scp_details' => $request->scp_details[$i],
                            ]);
                        }
                    }
                }
                if(count($request->items) > 0){
                    for($i=0; $i<sizeof($request->items); $i++){
                        SupplierItemType::insert([
                            'mr_supplier_sup_id' => $last_id,
                            'mcat_id' => $request->items[$i],
                        ]);
                    }
                }
                $this->logFileWrite("Supplier Saved", $last_id);
                $data['type'] = 'success';
                $data['message'] = "Supplier successfully done.";
                DB::commit();
            }
            else{
                $data['message'] = "Something Wrong!, Please try again!";
            }

            $data['url'] = url()->previous();
            
            return response()->json($data);

        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            $data['message'] = $bug;
            return response()->json($data);
        }
    }
    public function SupplierDelete($id){

        DB::beginTransaction();
        try {
            Supplier::where('sup_id', $id)->delete();
            SupplierContact::where('sup_id', $id)->delete();
            SupplierItemType::where('mr_supplier_sup_id', $id)->delete();
            $this->logFileWrite("Supplier Deleted", $id);
            DB::commit();
            toastr()->success('Supplier  Deleted Successfully!!');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            toastr()->error($bug);
            return back();
        }
        
    }
    public function SupplierEdit($id){
        
        #-----------------------------------------------------------#
        $countryList= Country::pluck('cnt_name', 'cnt_id');
        $supplier= Supplier::where('sup_id', $id)->first();
        $sup_contact= SupplierContact::where('sup_id', $id)->get();
        $sup_category = DB::table("mr_supplier_item_type")
                     ->join('mr_material_category','mr_supplier_item_type.mcat_id','=','mr_material_category.mcat_id')
                     ->where('mr_supplier_sup_id',$id)
                     ->get();
        //dd($items);exit;

        $unit_id = DB::table('users')
            ->select('hr_as_basic_info.as_unit_id')
            ->join('hr_as_basic_info','users.associate_id','=','hr_as_basic_info.associate_id')
            ->where('id',Auth::id())
            ->get();

        $buttons = $this->approvalButtons($id);
        //dd($buttons);exit;


        // BOM Items
        $items = DB::table("mr_material_category")
                ->get();

        $supplier_name = DB::table("mr_supplier_item_type")
            ->get();

        $check = [];
        foreach ($supplier_name as $sn)
        {
            $check[$sn->mr_supplier_sup_id][] = $sn->mcat_id;
        }

        //dd($check);exit;

        // BOM Items
        $items = DB::table("mr_material_category")
            ->get();

        $itemList = "";
        //Loop  for category list
        foreach ($items as $cat)
        {

            //dd($items);exit;

           // $itemList .= "<div class=\"col-sm-4\">";


            //$mcatList = array_column($check[$id], 'mcat_id');
            // $itemList .= "<div class=\"col-sm-4\">
            //     <h4>$cat->mcat_name</h4>";
            $itemList .= "<div class=\"col-sm-4\">";

            //Query  for category item list
            // $subItem = DB::table("mr_cat_item")
            //     ->where("mcat_id", $cat->mcat_id)
            //     ->get();

            $name = strtolower(str_replace(" ", "_", $cat->mcat_name));

            $sl = 1;
            $itemList .= "<ul class=\"list-unstyled\" style=\"padding-top: 15px;\">";


            // check mcat_id exist
            if(isset($check[$id])) {
                $checked = in_array($cat->mcat_id, $check[$id]) !== false? 'checked':'';
            } else {
                $checked = '';
            }
            $itemList .= "<li>
                <label>
                    <input name=\"selected_item[]\" type=\"checkbox\" value=\" $cat->mcat_id \" $checked class=\"ace checkbox-input\">
                               <span class=\"lbl\" style=\"font-size: 14px;\">$cat->mcat_name</span>
                          </label>

                 </li>";

            $itemList .= "</ul>";
            $itemList .= "</div>";
        }


        return view('merch/setup/supplier_edit', compact('sup_category','unit_id','supplier', 'sup_contact', 'countryList','buttons','itemList'));
    }

    public function approvalButtons($id = null)
    {
		$buttons = "";

		$AppovalStatus = DB::select("
			SELECT
				CASE
					WHEN l.`status` = 2 THEN CONCAT(b.as_name, ' (Approved)')
					WHEN l.`status` = 1 THEN CONCAT(b.as_name, ' (Approval pending)')
					WHEN l.`status` = 0 THEN CONCAT(b.as_name, ' (Decline)')
				END AS name,
				l.status,
				l.comments
			FROM mr_supplier_approval AS l
			LEFT JOIN hr_as_basic_info AS b
				ON b.associate_id = l.submit_to
			WHERE l.sup_id = $id
			GROUP BY submit_to
			ORDER BY l.id ASC
		");

		$buttons .= "<table class=\"table table-bordered bg-info\"><tr>";
		foreach ($AppovalStatus as $s)
		{
			$buttons .= "<td width=\"33.33%\" class='".($s->status==2?'bg-info':'bg-danger')."'><h5 class='text-center'>$s->name</h5><p class='text-center'>$s->comments</p></td>";
		}
		$buttons .= "</tr></table>";

		/*
    	* check style costing status == 0
    	* --------------------------------
    	*/
    	$checkStyle = DB::table("mr_supplier")
    		->where("sup_id", $id)
    		->where("status", null)
    		->orWhere("status", "0")
    		->orWhere("status", "1")
    		->exists();


    	if ($checkStyle)
    	{
    		// get hierarchy level
			$levelHierarchy = DB::table("mr_approval_hirarchy")
				->where("mr_approval_type", "Supplier Approval")
				->first();


			/*
			* CHECK APPROVAL STATUS
			* --------------------------------------
			*/
			$checkStyleCost = DB::table("mr_supplier_approval")
				->where("sup_id", $id)
				->where("status", "1");

			if ($checkStyleCost->exists())
			{
				$checkStyleCostData = $checkStyleCost->first();

				$approve_id = $checkStyleCostData->id;
				$level     = $checkStyleCostData->level;
				$submit_by = $checkStyleCostData->submit_by;
				$submit_to = $checkStyleCostData->submit_to;
				$comments  = $checkStyleCostData->comments;
				$associate_id = auth()->user()->associate_id;

				if ($submit_to == $associate_id)
				{
					// approve button
					$buttons .= "
						<div class=\"col-sm-9\">
							<textarea name=\"comments\" class=\"form-control\" placeholder=\"Comments\"></textarea>
							<input type=\"hidden\" name=\"approve_id\" value=\"$approve_id\"/>
							<input type=\"hidden\" name=\"level\" value=\"$level\"/>
						</div>
						<div class=\"col-sm-3\">
							<button name=\"confirm_approval_request\" type=\"submit\" class=\"btn btn-success btn-sm\">Approved</button>
						</div>
					";
				}
				else if ($submit_by == $associate_id)
				{
					// submit button
					$buttons .= "<div class=\"col-sm-12\"><button type=\"button\" disabled class=\"btn btn-warning btn-sm\">Submit (Approval pending)</button></div>";
				}
			}
			else
			{
				// show only submit button
				// create submitted to
				$checkAppReq = DB::table("mr_supplier_approval")
					->where("sup_id", $id)
					->exists();


				if (!$checkAppReq)
				{
					$new_submit_to = $levelHierarchy->level_1;
					$buttons .= "
						<div class=\"col-sm-4\">
							<input type=\"hidden\" name=\"level\" value=\"1\"/>
							<input type=\"hidden\" name=\"submit_to\" value=\"$new_submit_to\"/>
						</div>
						<div class=\"col-sm-8\">
							<button type=\"submit\" name=\"request_for_approve\" class=\"btn btn-success btn-sm\">Request for Approval</button>
						</div>
					";
				}

			}
    	}

		return $buttons;
    }

    public function SupplierUpdate(Request $request){
        
        $validator= Validator::make($request->all(),[
            'sup_name' => 'required|max:150',
            'cnt_id' => 'required|max:11',
            'sup_address' => 'required|max:128',
            'sup_type' => 'required|max:11'
        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }
        try {
            Supplier::where('sup_id', $request->sup_id)
                ->update([
                    'cnt_id' => $request->cnt_id,
                    'unit_id' => $request->unit_id,
                    'sup_name' => $this->quoteReplaceHtmlEntry($request->sup_name),
                    'sup_address' => $this->quoteReplaceHtmlEntry($request->sup_address),
                    'sup_type' => $request->sup_type
                ]);


                //SupplierContact::where('sup_id', $request->sup_id)->delete();

                for($i=0; $i<sizeof($request->scp_details); $i++){
                    SupplierContact::where('sup_id',$request->sup_id)
                    ->update([
                        'sup_id' => $request->sup_id,
                        'scp_details' => $request->scp_details[$i],
                    ]);
                }

                $item_count = SupplierItemType::get()->count();
                if ($item_count>0){
                    SupplierItemType::where('mr_supplier_sup_id', $request->sup_id)
                        ->delete();
                        if(!empty($request->items)){
                    for($j=0; $j<sizeof($request->items); $j++){
                        SupplierItemType::insert([
                            'mr_supplier_sup_id' => $request->sup_id,
                            'mcat_id' => $request->item_id[$j]
                        ]);
                    }
                  }
                }else{
                  if(!empty($request->items)){
                    for($j=0; $j<sizeof($request->items); $j++){
                        SupplierItemType::insert([
                            'mr_supplier_sup_id' => $request->sup_id,
                            'mcat_id' => $request->item_id[$j]
                        ]);
                    }
                  }
                }
                $this->logFileWrite("Supplier Updated", $request->sup_id);


                if ($request->has("request_for_approve") && !empty($request->submit_to))
                {
                  DB::table("mr_supplier_approval")
                    ->insert([
                      "title" => "precost",
                      "sup_id" => $request->sup_id,
                      "level"     => $request->level,
                      "submit_by" => auth()->user()->associate_id,
                      "submit_to" => $request->submit_to,
                      "comments"  => $request->comments,
                      "status"    => 1,
                      "created_on"  => date("Y-m-d H:i:s"),
                    ]);
                    //log entry
                    $app_id = DB::getPdo()->lastInsertId();
                    $this->logFileWrite("Request(supplier) for approval saved", $app_id);

                      return redirect("merch/setup/supplier")
                          ->with('success', 'Request for approval successful.');
                }
                /*
                *----------------------------------------------------
                */

                /*----------------------------------------------------
                * confirm_approval_request
                *----------------------------------------------------
                */
                if ($request->has("confirm_approval_request") && !empty($request->approve_id) && !empty($request->level))
                {

                  // get approval access level
                  $approvalLevel = DB::table("mr_approval_hirarchy")
                    ->where("mr_approval_type", "Supplier Approval")
                    ->first();

                  // update approval status = 2 [request approved]
                  $approvalData = DB::table("mr_supplier_approval")
                    ->where("id", $request->approve_id)
                    ->update([
                      "comments" => $request->comments,
                      "status" => 2
                    ]);

                  if ($request->level == 1)
                  {
                    // insert new approval record
                  DB::table("mr_supplier_approval")
                  ->insert([
                    "title" => "precost",
                    "sup_id" => $request->sup_id,
                    "level"     => 2,
                    "submit_by" => auth()->user()->associate_id,
                    "submit_to" => $approvalLevel->level_2,
                    "comments"  => null,
                    "status"    => 1,
                    "created_on"  => date("Y-m-d H:i:s"),
                  ]);
                  }
                  else if ($request->level == 2)
                  {
                    // insert new approval record
                  DB::table("mr_supplier_approval")
                  ->insert([
                    "title" => "precost",
                    "sup_id" => $request->sup_id,
                    "level"     => 3,
                    "submit_by" => auth()->user()->associate_id,
                    "submit_to" => $approvalLevel->level_3,
                    "comments"  => null,
                    "status"    => 1,
                    "created_on"  => date("Y-m-d H:i:s"),
                  ]);
                  }
                  else if ($request->level == 3)
                  {
                    // update mr style table status = 2
                  DB::table("mr_supplier")
                  ->where("sup_id",  $request->sup_id)
                  ->update([
                    "status" => 2
                  ]);
                  }

                  $this->logFileWrite("Request(supplier) for approval status Updated", $request->approve_id);

                        return redirect("merch/setup/supplier")
                            ->with('success', 'Approved successful.');

                }
            return back()->with('success', "Updated Successfully!!");
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
            $bug1 = $e->errorInfo[2];
            if($bug == 1062){
                return back()->with('error', "Supplier Name Already Exists");
            }
            return back()->with('error', $bug1);
        }
    }
}
