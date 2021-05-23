<?php
namespace App\Http\Controllers\Commercial\Export\ExportData;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Hr\Unit;
use App\Models\Merch\Buyer; 
use App\Models\Commercial\Bank;
use App\Models\Commercial\BankAccNo;
use App\Models\Commercial\SalesContract;
use App\Models\Commercial\SalesContractOrder;
use App\Models\Merch\Country;
use App\Models\Merch\OrderEntry;
use App\Models\Commercial\ExpLcEntry;
use App\Models\Commercial\ExpLcAmmendment;
use App\Models\Commercial\ExpLcAddress;
use App\Models\Commercial\CFile;
use App\Models\Commercial\Agent;
use App\Models\Commercial\Port;
use App\Models\Commercial\Incoterm;
use App\Models\Commercial\CategoryNo;
use App\Models\Commercial\ExpDataEntry;
use App\Models\Commercial\HSCode;
use App\Models\Commercial\ExpEntryDelivery;
use App\Models\Commercial\ExpDataEntryCategory;
use App\Models\Commercial\ExpDataPO;
use App\Models\Merch\MrPoBomCostingBooking;
use App\Models\Merch\MrPoBomOtherCosting;
use App\Models\Merch\MrPoOperationNCost;
use App\Models\Merch\PurchaseOrder;

use stdClass;


use Validator, DB, ACL, Auth, DataTables,Response;
class ExportDataEntryController extends Controller
{
# Export LC Form
    public function showForm()
    {
      $buyer= Buyer::pluck('b_name','b_id');
      $bank= Bank::pluck('bank_name','id');
      $country= Country::pluck('cnt_name','cnt_id');
      $unit= Unit::whereIn('hr_unit_id', auth()->user()->unit_permissions())->pluck('hr_unit_name','hr_unit_id');
      $agent= Agent::pluck('agent_name','id');
      $fileno= CFile::pluck('file_no','id');
      $lcno=SalesContract::pluck('lc_contract_no','id');
      $port=Port::pluck('port_name','id');
      $incoterm=Incoterm::pluck('name','id');
      $category=CategoryNo::get();


    return view('commercial.export.export_data.export_data_entry', compact('buyer', 'bank', 'country','unit','agent','fileno','lcno','port','incoterm','category'));
    }

# Return File No List by Unit ID
  public function fileList(Request $request)
  {
      $list = "<option value=\"\">Select</option>";
      if (!empty($request->unit_id))
      {

          $fileList  = CFile::where('hr_unit', $request->unit_id)
                      ->pluck('file_no','id');

          foreach ($fileList as $key => $value)
          {
              $list .= "<option value=\"$key\">$value</option>";
          }
      }
      return $list;
  }
# Return ELC List by File No
  public function elcList(Request $request)
  {
      $list = "<option value=\"\">Select  </option>";
      if (!empty($request->file_id))
      {

      	$elcList= DB::table('cm_exp_lc_entry AS elc')
                  ->select([

                        'sc.lc_contract_no',
                        'sc.id as lcid'
                      ])

                  ->leftJoin("cm_sales_contract AS sc", 'sc.id', 'elc.cm_sales_contract_id')
                  ->where('elc.cm_file_id', $request->file_id)
                  ->get();

          //dd($elcList);
          foreach ($elcList as  $value)
          {
           $list .= "<option value=\"$value->lc_contract_no\">$value->lc_contract_no</option>";
          }
      }
      return $list;
  }

  public function destinationList(Request $request)
  {
      $list = "<option value=\"\">Select  </option>";
      if (!empty($request->file_id))
      {

        $destination = DB::table('cm_file AS f')
                  ->leftJoin("cm_exp_lc_entry AS lc", 'lc.cm_file_id', 'f.id')
                  ->leftJoin("cm_sales_contract AS sc", 'sc.id', 'lc.cm_sales_contract_id')
                  ->leftJoin("cm_sales_contract_order AS co", 'co.cm_sales_contract_id', 'sc.id')
                  ->leftJoin("mr_order_entry AS o", 'o.order_id', 'co.mr_order_entry_order_id')
                  ->Join("mr_purchase_order AS po", 'po.mr_order_entry_order_id', 'o.order_id')
                  ->leftJoin("mr_country AS cn", 'cn.cnt_id', 'po.po_delivery_country')
                  ->where('f.id',$request->file_id)
                  ->pluck('cn.cnt_name','cn.cnt_id');

          //dd($destination);
          foreach ($destination as $key => $value)
          {
           $list .= "<option value=\"$key\">$value</option>";
          }
      }
      return $list;
  }


# Invoice No. Generate
  public function invNo(Request $request){


       if (!empty($request->unt_id))
        {
            $list =01;
	        $entryList  = ExpDataEntry::where('unit_id', $request->unt_id)
	                     ->get();
	        $entry = $entryList->count();
	        $list=$entry+$list;
            //dd($list);
            $formatted_unit= sprintf("%02d", $request->unt_id);
            $formatted_list= sprintf("%06d", $list);
            $finalval=$formatted_unit.$formatted_list;

	        return $finalval;


       }

    }


# ELC No. Input Values
  public function elcInfoList(Request $request){

        // dd($request->all());
       if (!empty($request->elc_id))
        {

         // $elcinfo= SalesContract::select('elc_date','surname')->where('id', 1)->get();
          $elcinfo= DB::table('cm_sales_contract AS csc')
                    ->select([
                    	    'csc.id',
                          'csca.elc_amend_date',
                          'bu.b_name',
                          'bu.b_id',
                          'b.bank_name AS lc_bank',
                          'b.id AS lc_bank_id'
                        ])
                    ->leftJoin('cm_sales_contract_amend as csca', 'csca.cm_sales_contract_id', 'csc.id')
                    ->leftJoin("mr_buyer AS bu", 'bu.b_id', 'csc.mr_buyer_b_id')
                    ->leftJoin("cm_bank AS b", 'b.id', 'csc.lc_open_bank_id')
                    ->where('csc.lc_contract_no', $request->elc_id)
                    ->first();

          // dd($elcinfo);

          $elc_amend_date_array = DB::table('cm_sales_contract_amend')->select('elc_amend_date')
                                                               ->where('cm_sales_contract_id', $elcinfo->id)
                                                               ->get()
                                                               ->toArray();
          // dd($elc_amend_date_array);
          $last_amnd_date = $elc_amend_date_array[sizeof($elc_amend_date_array)-1]->elc_amend_date;                                                      

        /* Json Multiple variable return*/
         return response()->json([
           'buyername'       =>$elcinfo->b_name,
           'buyerid'         =>$elcinfo->b_id,
           'elcdate'         =>$elcinfo->elc_amend_date,
           'elcdate'         =>$last_amnd_date,
           'lc_bank'         =>$elcinfo->lc_bank,
           'lc_bank_id'      =>$elcinfo->lc_bank_id,
           'contract_id'     =>$elcinfo->id

         ]);

       }

  }

    //Po list by order
  public function poListByOrder(Request $request){
    $poList= DB::table('mr_purchase_order AS p')
        ->select([
              'p.po_id',
              'p.po_no',
            ])
        ->where('p.mr_order_entry_order_id', $request->order_no)
        ->get();
        //dd($poList);exit;
        return Response::json($poList);
  }



/// rkb change on 03/12/19
  public function getPoItemsEx(Request $request){
    if (!empty($request->file_no)){
      $where['f.id'] = $request->file_no;
      $where['po.po_delivery_country'] = $request->country;


      $query= ExpDataPO::select(
                  DB::raw('sum(inv_qty) AS inv_qty'),
                  'mr_purchase_order_po_id'
              );
      $invData = $query->groupBy('mr_purchase_order_po_id');
      $invData_sql = $invData->toSql();

      $query1 = DB::table('cm_file AS f')
                  ->select([
                        'o.*',
                        's.stl_no',
                        's.stl_id',
                        'b.agent_fob',
                        'po.*',
                        'cn.cnt_name',
                        'inv.inv_qty'
                      ])
                  ->leftJoin("cm_exp_lc_entry AS lc", 'lc.cm_file_id', 'f.id')
                  ->leftJoin("cm_sales_contract AS sc", 'sc.id', 'lc.cm_sales_contract_id')
                  ->leftJoin("cm_sales_contract_order AS co", 'co.cm_sales_contract_id', 'sc.id')
                  ->leftJoin("mr_order_entry AS o", 'o.order_id', 'co.mr_order_entry_order_id')
                  ->leftJoin("mr_style AS s", 's.stl_id', 'o.mr_style_stl_id')
                  ->leftJoin('mr_order_bom_other_costing AS b', 'b.mr_order_entry_order_id', '=', 'co.mr_order_entry_order_id')
                  ->Join("mr_purchase_order AS po", 'po.mr_order_entry_order_id', 'o.order_id')
                  ->leftJoin("mr_country AS cn", 'cn.cnt_id', 'po.po_delivery_country')
                  ->where($where);
                  $query1->leftJoin(DB::raw('(' . $invData_sql. ') AS inv'), function($join) use ($invData) {
                        $join->on('po.po_id', '=', 'inv.mr_purchase_order_po_id')->addBinding($invData->getBindings()); ;
                    });
      $poList   =$query1->get();
      //dd($poList);
      return view('commercial.export.export_data.get_po_items',
                compact('poList'))->render();

    }
  }

  public function getPoItems(Request $request){
    if (!empty($request->file_no)){
      $where['f.id'] = $request->file_no;
      $where['po.po_delivery_country'] = $request->country;

      //MrPoBomCostingBooking;
      $orders = DB::table('cm_file AS f')
                  ->leftJoin("cm_exp_lc_entry AS lc", 'lc.cm_file_id', 'f.id')
                  ->leftJoin("cm_sales_contract AS sc", 'sc.id', 'lc.cm_sales_contract_id')
                  ->leftJoin("cm_sales_contract_order AS co", 'co.cm_sales_contract_id', 'sc.id')
                  ->where('f.id',$request->file_no)
                  ->pluck('co.mr_order_entry_order_id')
                  ->toArray();
      //dd($file_order);
      $invData =  ExpDataPO::select(
                    DB::raw('sum(inv_qty) AS inv_qty'),
                    'mr_purchase_order_po_id',
                    'clr_id'
                  )->groupBy('mr_purchase_order_po_id','clr_id');
      //dd($invData->get()->toArray());
      $invData_sql = $invData->toSql();

      $orderwise = [];
      foreach ($orders as $key => $order) {
        $powise = DB::table('mr_po_bom_other_costing as pcos')
              ->select(
                'pcos.mr_order_entry_order_id',
                'cn.cnt_name',
                's.stl_no',
                's.stl_id',
                'ps.po_sub_style_qty as po_qty',
                'pcos.po_id',
                'pcos.clr_id',
                'po.po_no',
                'mc.clr_name',
                'mc.clr_code',
                'pcos.agent_fob',
                'inv.inv_qty'
                //DB::raw('sum(inv.inv_qty) as inv_qty')
              )
              ->leftJoin("mr_material_color AS mc", "mc.clr_id", "pcos.clr_id")
              ->leftJoin("mr_purchase_order AS po", 'po.po_id', 'pcos.po_id')
              ->leftJoin("mr_style AS s", 's.stl_id', 'pcos.mr_style_stl_id')
              ->leftJoin("mr_country AS cn", 'cn.cnt_id', 'po.po_delivery_country')
              ->leftJoin("mr_po_sub_style AS ps", function($join) {
                  $join->on("ps.po_id", "=", "pcos.po_id");
                  $join->on("ps.clr_id", "=", "pcos.clr_id");
              })
              /*->leftJoin("cm_exp_entry1_po AS inv", function($join) {
                  $join->on("pcos.po_id", "=", "inv.mr_purchase_order_po_id");
                  $join->on("pcos.clr_id", "=", "inv.clr_id");
              })*/
              ->leftJoin(DB::raw('(' . $invData_sql. ') AS inv'), function($join) use ($invData) {
                  $join->on('pcos.clr_id', '=', 'inv.clr_id')->addBinding($invData->getBindings());
                  $join->on("pcos.po_id", "=", "inv.mr_purchase_order_po_id")->addBinding($invData->getBindings());
              })
              ->where('pcos.mr_order_entry_order_id',$order)
              ->where('po.po_delivery_country',$request->country)
              //->groupBy('inv.clr_id')
              ->where('ps.po_sub_style_qty', '>', DB::Raw('IFNULL(inv.inv_qty,0)'))
              //->orWhere('inv.inv_qty', '=', null)
              ->get();
          //dd($powise);
          if($powise){
            $orderwise[$order] = new stdClass;
            $orderwise[$order]->po = $powise->groupBy('po_id', true);
            $orderwise[$order]->info = OrderEntry::where('order_id',$order)->first();
            $orderwise[$order]->span = count($powise);
          }
      }
      //dd($orderwise);

      return view('commercial.export.export_data.get_po_items_new',
                compact('orderwise'))->render();

    }
  }

# Return PO Input Values
  public function poValues(Request $request){

  //dd($request->all());
        if (!empty($request->po_no))
        {

         $poinfo= DB::table('mr_purchase_order AS po')
                    ->select([
                    	  'po.po_qty'
                        ])
                    ->where('po_id', $request->po_no)
                    ->first();
          //dd($poinfo);

        /* Json Multiple variable return*/
         return response()->json([
           'poqty'      =>$poinfo->po_qty

         ]);

       }

   }

# Return Cash Incentive Value
  public function cashIncentive(Request $request){
  	    if (!empty($request->cnt_id))
        {
            $ci= Country::where('cnt_id', $request->cnt_id)
                 ->pluck('cnt_cash_incentive');

       /* Json Multiple variable return*/
         return response()->json([
           'cashincentive'      =>$ci

         ]);

       }

   }
# Export Data Store
  public function storeExportData(Request $request){
    //dd($request->all());

    #------------------------------------------------#

       $validator= Validator::make($request->all(),[

            // 'unit'          => 'required|max:11',
            // 'agentname'     => 'required|max:45',
            // 'fileno'        => 'required|max:45',
            // 'invoiceno'     => 'required|max:45'


        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }

        try{

           // dd($request->all());exit;
           $cm_exp_lc_entry_id_pluck = DB::table('cm_exp_lc_entry')
                                            ->where(['cm_file_id'=> $request->fileno, 'cm_sales_contract_id' => $request->salescontractid])
                                            ->value('id');

           if($request->cancelmark == 'on')
            $cancelmark_value = 1;
           else
            $cancelmark_value = 0;

            // dd('cm_exp_lc_entry ID:',$cm_exp_lc_entry_id_pluck,'Cancel Status(0=no cancel, 1=cancel)', $cancelmark_value, 'Submitted data:', $request->all() );exit;

           $newdata= new ExpDataEntry();
           $newdata->unit_id              = $request->unit;
           $newdata->cm_agent_id          = $request->agentname;
           $newdata->cm_file_id           = $request->fileno;
           $newdata->inv_no               = $request->invoiceno;
           $newdata->inv_date             = $request->invoice_date;
           $newdata->cm_exp_lc_entry_id   = $cm_exp_lc_entry_id_pluck;

           $newdata->cm_sales_contract_id = $request->salescontractid;

           $newdata->cancel_status        = $cancelmark_value;
           $newdata->cancel_reason        = $request->reason;
           $newdata->canel_date           = $request->cancel_date;

           //$newdata->cm_exp_data_entry_1col   = $request->;

           $newdata->cm_inco_term_id      = $request->incoterms;
           $newdata->exp_no               = $request->exp_no;
           $newdata->exp_date             = $request->exp_date;

           $newdata->cnt_id               = $request->destination;
           $newdata->fabric_desc          = $request->fab_desc;
           $newdata->garment_desc         = $request->garm_desc;

           $newdata->mode                 = $request->mode;
           $newdata->cm_port_id           = $request->port_destination;
           $newdata->inspec_order_no      = $request->insp_order_no;

           $newdata->brand_name           = $request->brand_name;
           $newdata->inv_value            = $request->total_value;

           $newdata->save();
           $last_id = $newdata->id;

         // HS Code insert

        if(isset($request->cat_no)){
          foreach ($request->cat_no as $key => $cat_no) {
              ExpDataEntryCategory::insert([
                  'cm_exp_data_entry_1_id'     => $last_id,
                  'cm_category_no_id'          => $cat_no
              ]);
          }
        }

        if(isset($request->hs_code)){
          foreach ($request->hs_code as $key => $hs_code) {
              HSCode::insert([
                  'cm_exp_data_entry_1_id'     => $last_id,
                  'hs_code'                    => $hs_code,
                  'order_id'                   => $key
              ]);
          }
        }

       // Delivery center Code insert
        if(isset($request->delv_cnt_code)){
          for($j=0; $j<sizeof($request->delv_cnt_code); $j++)
          {
              ExpEntryDelivery::insert([
                  'cm_exp_data_entry_1_id'     => $last_id,
                  'delivery_centre_code'       => $request->delv_cnt_code[$j],
                  'qty'                        => $request->quantity[$j],
                  'cartoon'                    => $request->carton[$j],
              ]);
          }
        }
       // PO insert
        if(isset($request->clr_id)){
          for($k=0; $k<sizeof($request->clr_id); $k++)
            {

            	ExpDataPO::insert([
                    'cm_exp_data_entry_1_id'     => $last_id,
                    'mr_purchase_order_po_id'    => $request->po_id[$k],
                    'clr_id'                     => $request->clr_id[$k],
                    'mr_order_entry_order_id'    => $request->order_id[$k],
                    'mr_style_stl_id'            => $request->stl_id[$k],
                    'dept_no_isd'                => $request->dept_isd[$k],
                    'po_qty'                     => $request->po_qty[$k],
                    'inv_qty'                    => $request->inv_qty[$k],
                    'unit_price'                 => $request->unit_price1[$k],
                    'unit_price2'                => $request->unit_price2[$k],
                    'currency'                   => $request->currency[$k],
                    'ctn'                        => $request->cnt[$k],
                    'agent_unit_price'           => $request->agent_unit_price[$k],
                    'cbm'                        => $request->cbm[$k],
                    'gross_wt'                   => $request->gross_weight[$k],
                    'net_wt'                     => $request->net_weight[$k],
                    'n_n_wt'                     => $request->nn_weight[$k]
                ]);

            }
          }

          $this->logFileWrite("Commercial-> Export Data Entry Saved", $last_id);
        return back()
                ->with('success', "Export Data Saved Successfully");

       } catch(\Exception $e){
          $bug = $e->getMessage();
          return back()->with('error', $bug);
       }

  }

  public function viewExportData(){
    $unit=DB::table('hr_unit')->pluck('hr_unit_name');
    $file=DB::table('cm_file')->pluck('file_no');
    $buyer=DB::table('mr_buyer')->pluck('b_name');
    return view('commercial.export.export_data.export_data_entry_list', compact('unit', 'file', 'buyer') );
  }

  public function ajaxExportDataGet(){
              #-------------------------------#
     $data=  DB::table('cm_exp_data_entry_1 AS a')
                ->select([
                      'a.id',
                      'a.inv_no',
                      'a.inv_date',
                      'u.hr_unit_name',
                      'f.file_no',
                      'sc.lc_contract_no',
                      // 'sca.elc_amend_date as elc_date',
                      'by.b_name'
                ])
                ->leftJoin("cm_exp_lc_entry AS b", 'b.id', 'a.cm_exp_lc_entry_id')
                ->leftJoin("cm_sales_contract AS sc", 'sc.id', 'a.cm_sales_contract_id')
                // ->leftJoin("cm_sales_contract_amend as sca", "sca.cm_sales_contract_id","sc.id")
                ->leftJoin("cm_file as f", 'f.id', 'a.cm_file_id')
                ->leftJoin("hr_unit AS u", 'u.hr_unit_id', 'a.unit_id')
                ->leftJoin("mr_buyer AS by", 'by.b_id', 'sc.mr_buyer_b_id')
                ->orderBy('a.id', 'DESC')
                ->get();

             // dd($data);exit;


      return DataTables::of($data)
            /// Query for Action
            ->editColumn('action', function ($data) {
                    $btn = "
                    <a href=".url('commercial/export/export_data_entry_1a_edit/'.$data->id )." class=\"btn btn-xs btn-success\" data-toggle=\"tooltip\" title=\"View\">
                        <i class=\"fa fa-pencil\"></i>
                        </a>
                    </div>
                   <a href=".url('commercial/export/export_data_entry_1a_delete/'.$data->id )." class=\"btn btn-xs btn-danger\" data-toggle=\"tooltip\" title=\"delete\" onclick=\"return confirm('Are you sure you want to delete this LC?');\">
                        <i class=\"fa fa-trash\"></i>
                        </a>
                    ";

                // $btn ="";
                return $btn;
              })
            ->rawColumns(['action'])
            ->toJson();



  }

  public function entryEdit($id){
      $base_data     = DB::table('cm_exp_data_entry_1 as ede1')
                        ->select([
                          'ede1.*',
                          'u.hr_unit_name',
                          'by.b_name as buyer',
                          'f.file_no',
                          'sc.lc_contract_no',
                          'sc.id as sc_id',
                          // 'sca.elc_amend_date as elc_date',
                          'ag.agent_name',
                          'b.bank_name',
                          'it.name as inco_term',
                          'c.cnt_name as country',
                          'p.port_name',
                        ])
                        ->leftJoin('hr_unit as u', 'u.hr_unit_id', 'ede1.unit_id')
                        ->leftJoin('cm_sales_contract as sc', 'sc.id', 'ede1.cm_sales_contract_id')
                        // ->leftJoin("cm_sales_contract_amend as sca", "sca.cm_sales_contract_id","sc.id")
                        ->leftJoin('cm_bank as b', 'b.id', 'sc.lc_open_bank_id')
                        ->leftJoin('mr_buyer as by', 'by.b_id', 'sc.mr_buyer_b_id')
                        ->leftJoin('cm_file as f', 'f.id', 'ede1.cm_file_id')
                        ->leftJoin('cm_agent as ag', 'ag.id', 'ede1.cm_agent_id')
                        ->leftJoin('cm_inco_term as it', 'it.id', 'ede1.cm_inco_term_id')
                        ->leftJoin('mr_country as c', 'c.cnt_id', 'ede1.cnt_id')
                        ->leftJoin('cm_port as p', 'p.id', 'ede1.cm_port_id')
                        ->where('ede1.id', $id)
                        ->first();
      // dd($base_data);
      $check = DB::table('cm_exp_update2')->where('invoice_no',$base_data->inv_no)->first();
      $elc_amend_date_array = DB::table('cm_sales_contract_amend')->select('elc_amend_date')
                                                               ->where('cm_sales_contract_id', $base_data->sc_id)
                                                               ->get()
                                                               ->toArray();
          // dd($elc_amend_date_array);
      $last_amnd_date = $elc_amend_date_array[sizeof($elc_amend_date_array)-1]->elc_amend_date;
      $base_data->elc_date = $last_amnd_date;

      $hs_code_data  = '';
      $delivery_data = ExpEntryDelivery::where('cm_exp_data_entry_1_id', $id)->get();

      $poItems = $this->getPoItemsEdit($id);

      //dd($poItems);

      


      $agent= Agent::pluck('agent_name','id');
      $country= Country::pluck('cnt_name','cnt_id');
      $port=Port::pluck('port_name','id');
      $incoterm=Incoterm::pluck('name','id');
      $category=CategoryNo::get();
      $thisCat = ExpDataEntryCategory::pluck('cm_category_no_id')->toArray();
      // dd($base_data, $hs_code_data, $delivery_data, $po_data);exit;

      return view('commercial.export.export_data.export_data_entry_edit', compact('base_data', 'hs_code_data', 'delivery_data', 'poItems', 'agent', 'country', 'port', 'incoterm', 'category','check','thisCat' ));
  }

  public function getPoItemsEditEx($id){

      $query= ExpDataPO::select(
                  DB::raw('sum(inv_qty) AS total_inv_qty'),
                  'mr_purchase_order_po_id'
              );
      $invData = $query->groupBy('mr_purchase_order_po_id');
      $invData_sql = $invData->toSql();


      //get po items for edit
      $query1  = DB::table('cm_exp_entry1_po as e1_po')
                  ->select([
                      'e1_po.*',
                      'po.*',
                      'odr.order_code',
                      'odr.order_delivery_date',
                      'stl.stl_no',
                      'cn.cnt_name',
                      'inv.total_inv_qty'
                      //'b.agent_fob'
                  ])
                  ->leftJoin('mr_purchase_order as po', 'po.po_id', 'e1_po.mr_purchase_order_po_id')
                  ->leftJoin('mr_order_entry as odr', 'odr.order_id', 'e1_po.mr_order_entry_order_id')
                  ->leftJoin('mr_style as stl', 'stl.stl_id', 'e1_po.mr_style_stl_id')
                  ->leftJoin("mr_country AS cn", 'cn.cnt_id', 'po.po_delivery_country')
                  ->where('e1_po.cm_exp_data_entry_1_id', $id);
      $query1->leftJoin(DB::raw('(' . $invData_sql. ') AS inv'), function($join) use ($invData) {
                $join->on('po.po_id', '=', 'inv.mr_purchase_order_po_id')->addBinding($invData->getBindings()); ;
            });
      $poList   =$query1->get();

      //dd($poList);
      return view('commercial.export.export_data.get_po_items_edit',
                compact('poList'))->render();

   
  }
  public function getPoItemsEdit($id){

      $invData =  ExpDataPO::select(
                    DB::raw('sum(inv_qty) AS total_inv_qty'),
                    'mr_purchase_order_po_id',
                    'clr_id'
                  )
                  ->groupBy('mr_purchase_order_po_id','clr_id');
      //dd($invData->get()->toArray());
      $invData_sql = $invData->toSql();


      $orders = HSCode::where('cm_exp_data_entry_1_id', $id)->pluck('order_id');

      $orderwise = [];
      foreach ($orders as $key => $order) {
          $powise  = DB::table('cm_exp_entry1_po as e1_po')
                  ->select([
                      'e1_po.*',
                      'po.po_no',
                      'cn.cnt_name',
                      's.stl_no',
                      'inv.total_inv_qty',
                      'mc.clr_name'
                  ])
                  ->leftJoin("mr_style AS s", 's.stl_id', 'e1_po.mr_style_stl_id')
                  ->leftJoin("mr_material_color AS mc", "mc.clr_id", "e1_po.clr_id")
                  ->leftJoin('mr_purchase_order as po', 'po.po_id', 'e1_po.mr_purchase_order_po_id')
                  ->leftJoin("mr_country AS cn", 'cn.cnt_id', 'po.po_delivery_country')
                  ->leftJoin(DB::raw('(' . $invData_sql. ') AS inv'), function($join) use ($invData) {
                      $join->on('e1_po.clr_id', '=', 'inv.clr_id')->addBinding($invData->getBindings());
                      $join->on("e1_po.mr_purchase_order_po_id", "=", "inv.mr_purchase_order_po_id")->addBinding($invData->getBindings()); 
                  })
                  ->where('e1_po.cm_exp_data_entry_1_id', $id)
                  ->where('e1_po.mr_order_entry_order_id',$order)
                  ->get();

        $orderwise[$order] = new stdClass;
        $orderwise[$order]->po = $powise->groupBy('mr_purchase_order_po_id', true);
        $orderwise[$order]->info = OrderEntry::where('order_id',$order)->first();
        $orderwise[$order]->hs_code = HSCode::where([
                                        'cm_exp_data_entry_1_id' => $id, 
                                        'order_id' => $order]
                                      )->first();
        $orderwise[$order]->span = count($powise);
      }

      //get po items for edit
     

      //dd($orderwise);
      return view('commercial.export.export_data.get_po_items_new_edit',
                compact('orderwise'))->render();

   
  }


    public function entryUpdate(Request $request){

      $validator= Validator::make($request->all(),[

        ]);
        if($validator->fails()){
            return back()
            ->withInput()
            ->with('error', "Incorrect Input!");
        }

        try {
          //dd($request->all());

           $cm_exp_lc_entry_id_pluck = DB::table('cm_exp_lc_entry')
                                            ->where(['cm_file_id'=> $request->fileno, 'cm_sales_contract_id' => $request->salescontractid])
                                            ->value('id');

           if($request->cancelmark == 'on')
            $cancelmark_value = 1;
           else
            $cancelmark_value = 0;

            ExpDataEntry::where('id', $request->exp_data_entry_1a_id)->update([
                         'cm_agent_id'          => $request->agentname,
                         'inv_date'             => $request->invoice_date,
                         'cm_sales_contract_id' => $request->cm_sales_contract_id,
                         'cancel_status'        => $cancelmark_value,
                         'cancel_reason'        => $request->reason,
                         'canel_date'           => $request->cancel_date,
                         'cm_inco_term_id'      => $request->incoterms,
                         'exp_no'               => $request->exp_no,
                         'exp_date'             => $request->exp_date,
                         'fabric_desc'          => $request->fab_desc,
                         'garment_desc'         => $request->garm_desc,
                         'mode'                 => $request->mode,
                         'cm_port_id'           => $request->port_destination,
                         'inspec_order_no'      => $request->insp_order_no,
                         'brand_name'           => $request->brand_name,
                         'inv_value'            => $request->total_value

                        ]);

          ExpDataEntryCategory::where('cm_exp_data_entry_1_id', $request->exp_data_entry_1a_id)->delete();
          if(isset($request->cat_no)){
            //dd($request->cat_no);
            foreach ($request->cat_no as $key => $cat_no) {
              ExpDataEntryCategory::insert([
                  'cm_exp_data_entry_1_id'     => $request->exp_data_entry_1a_id,
                  'cm_category_no_id'          => $cat_no
              ]);
            }
          }

          if(isset($request->hs_code)){
            foreach ($request->hs_code as $key => $hs_code) {
                HSCode::where('id', $key)
                ->update([
                    'hs_code' => $hs_code
                ]);
            }
          }

         // Delivery center Code insert
          ExpEntryDelivery::where('cm_exp_data_entry_1_id', $request->exp_data_entry_1a_id)->delete();
          if(isset($request->delv_cnt_code)){
          for($j=0; $j<sizeof($request->delv_cnt_code); $j++)
            {
              ExpEntryDelivery::insert([
                  'cm_exp_data_entry_1_id'     => $request->exp_data_entry_1a_id,
                  'delivery_centre_code'       => $request->delv_cnt_code[$j],
                  'qty'                        => $request->quantity[$j],
                  'cartoon'                    => $request->carton[$j],
              ]);
            }
          }

         // PO insert
          if(isset($request->clr_id)){
            foreach ($request->clr_id as $key => $val) {
              ExpDataPO::where('id', $val)
                ->update([
                    'dept_no_isd'                => $request->dept_isd[$val],
                    'po_qty'                     => $request->po_qty[$val],
                    'inv_qty'                    => $request->inv_qty[$val],
                    'unit_price'                 => $request->unit_price1[$val],
                    'unit_price2'                => $request->unit_price2[$val],
                    'currency'                   => $request->currency[$val],
                    'ctn'                        => $request->cnt[$val],
                    'agent_unit_price'           => $request->agent_unit_price[$val],
                    'cbm'                        => $request->cbm[$val],
                    'gross_wt'                   => $request->gross_weight[$val],
                    'net_wt'                     => $request->net_weight[$val],
                    'n_n_wt'                     => $request->nn_weight[$val]
                ]);
            }
          }

            $this->logFileWrite("Commercial-> Export Data Entry Updated", $request->exp_data_entry_1a_id);

            return back()
                ->with('success', "Export Data Updated Successfully !!!");

         } catch (\Exception $e) {
           $bug = $e->getMessage();
           return back()->with('error', $bug);
         }

    }

    public function entryDelete($id){
      try{

        ExpDataEntry::where('id', $id)->delete();
        HSCode::where('cm_exp_data_entry_1_id', $id)->delete();
        ExpEntryDelivery::where('cm_exp_data_entry_1_id', $id)->delete();
        ExpDataPO::where('cm_exp_data_entry_1_id', $id)->delete();

        $this->logFileWrite("Commercial-> Export Data Entry Deleted", $id);

       return back()->with('success', 'Data Deleted Successfully');

      } catch (\Exception $e){
        $bug = $e->getMessage();
        return back()->withInput()->with('error', $bug);
      }



    }

}
