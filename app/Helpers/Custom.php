<?php
namespace App\Helpers;

use App\Models\Employee;
use App\Models\Hr\Location;
use App\Models\Hr\SalaryAdjustMaster;
use App\Models\Hr\Unit;
use App\Models\Merch\MrOrderBooking;
use App\Models\Merch\OrderBOM;
use App\Models\Merch\OrderBomCostingBooking;
use App\Models\Merch\PoBooking;
use App\Models\Merch\PoBookingDetail;
use DB;
/**
 * Create custom function
 */

class Custom
{
    // Rubel start
    static public function cinput($name,$type='text',$id='',$class='',$placeholder='',$value='',$readonly='')
    {
        $readonly = !empty($readonly)?'readonly="readonly"':'';
        return '<input type="'.$type.'" class="form-control '.$class.'" name="'.$name.'" id="'.$id.'" value="'.$value.'" placeholder="'.$placeholder.'" style="border: none; background-color: #fff" '.$readonly.'>';
    }

    static public function ifIsset(&$value, $customValue = '')
    {
        if(!empty($customValue)) {
            return $customValue;
        } else {
            return (isset($value)) ? $value : '';
        }
    }

    static public function getUnitName($unit)
    {
        if(is_numeric($unit)) {
            $unitFirst = Unit::where('hr_unit_id',$unit)->first();
            if(!empty($unitFirst)) {
                return $unitFirst->hr_unit_short_name;
            } else {
                return '';
            }
        } else {
            return $unit;
        }
    }

    static public function sselected($value1, $value2)
    {
        if($value1 == $value2) {
            return "selected='selected'";
        }
        return '';
    }

    static public function getItemPoCount($costingBookingId,$orderId)
    {
        $where = [
            'mr_order_entry_order_id' => $orderId,
            'mr_order_bom_costing_booking_id' => $costingBookingId
        ];
        $poBookingDetails = PoBookingDetail::where($where)->groupBy('mr_order_bom_costing_booking_id')->get()->toArray();
        return $poBookingDetails;
    }

    static public function getOrderBookingQtyColor($poBookingId,$costingBookingId,$itemId,$clrId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_material_color_id' => $clrId,
            'size' => null
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->booking_qty;
        } else {
            return [];
        }
    }

    static public function getOrderBookingValueQtyColor($poBookingId,$costingBookingId,$itemId,$clrId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_material_color_id' => $clrId,
            'size' => null
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->value;
        } else {
            return 0;
        }
    }

    static public function getOrderBookingReQtyColor($costingBookingId,$itemId,$clrId)
    {
        $where = [
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_material_color_id' => $clrId,
            'size' => null
        ];
        $bookingDetails = MrOrderBooking::where($where);
        if($bookingDetails->count() > 0) {
            $result['bookingQty'] = $bookingDetails->sum('booking_qty');
            $result['reqQty'] = $bookingDetails->first();
            return $result;
        } else {
            return [];
        }
    }

    static public function getOrderBookingQtySize($poBookingId,$costingBookingId,$itemId,$sizeId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'size' => $sizeId,
            'mr_material_color_id' => null
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->booking_qty;
        } else {
            return [];
        }
    }

    static public function getOrderBookingValueQtySize($poBookingId,$costingBookingId,$itemId,$sizeId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'size' => $sizeId,
            'mr_material_color_id' => null
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->value;
        } else {
            return [];
        }
    }

    static public function getOrderBookingReQtySize($costingBookingId,$itemId,$sizeId)
    {
        $where = [
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'size' => $sizeId,
            'mr_material_color_id' => null
        ];
        $bookingDetails = MrOrderBooking::where($where);
        if($bookingDetails->count() > 0) {
            $result['bookingQty'] = $bookingDetails->sum('booking_qty');
            $result['reqQty'] = $bookingDetails->first();
            return $result;
        } else {
            return [];
        }
    }

    static public function getOrderBookingQtyColorSize($poBookingId,$costingBookingId,$itemId,$clrId,$sizeId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_material_color_id' => $clrId,
            'size' => $sizeId
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->booking_qty;
        } else {
            return [];
        }
    }

    static public function getOrderBookingValueQtyColorSize($poBookingId,$costingBookingId,$itemId,$clrId,$sizeId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_material_color_id' => $clrId,
            'size' => $sizeId
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->value;
        } else {
            return [];
        }
    }

    static public function getOrderBookingReQtyColorSize($costingBookingId,$itemId,$clrId,$sizeId)
    {
        $where = [
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_material_color_id' => $clrId,
            'size' => $sizeId
        ];
        $bookingDetails = MrOrderBooking::where($where);
        if($bookingDetails->count() > 0) {
            $result['bookingQty'] = $bookingDetails->sum('booking_qty');
            $result['reqQty'] = $bookingDetails->first();
            return $result;
        } else {
            return [];
        }
    }

    //rkb
    static public function getOrderBookingQtyNoDepend($poBookingId,$costingBookingId,$itemId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_material_color_id' => null,
            'size' => null
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->booking_qty;
        } else {
            return [];
        }
    }

    static public function getOrderBookingReQtyNoDepend($costingBookingId,$itemId)
    {
        $where = [
            'mr_cat_item_id' => $itemId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_material_color_id' => null,
            'size' => null
        ];
        $bookingDetails = MrOrderBooking::where($where);
        if($bookingDetails->count() > 0) {
            $result['bookingQty'] = $bookingDetails->sum('booking_qty');
            $result['reqQty'] = $bookingDetails->first();
            return $result;
        } else {
            return [];
        }
    }

    static public function getOrderValueQtyNoDepend($poBookingId,$costingBookingId,$itemId)
    {
        $where = [
            'mr_po_booking_id' => $poBookingId,
            'mr_cat_item_id' => $itemId,
            'mr_order_bom_costing_booking_id' => $costingBookingId,
            'mr_material_color_id' => null,
            'size' => null
        ];
        $bookingDetails = MrOrderBooking::where($where)->first();
        if(!empty($bookingDetails)) {
            return $bookingDetails->value;
        } else {
            return [];
        }
    }

    static public function getPoDetailItemExist($poId,$costingBookingId)
    {
        $where = [
            'mr_purchase_order_po_id' => $poId,
            'mr_order_bom_costing_booking_id' => $costingBookingId
        ];
        $poBookingList = PoBookingDetail::where($where);
        if($poBookingList->count() > 0) {
            $bookingListFirst = $poBookingList->first();
            if($bookingListFirst->mr_material_color_id != null || $bookingListFirst->size) {
                $bookingQty = MrOrderBooking::where([
                                'mr_po_booking_id' => $bookingListFirst->mr_po_booking_id,
                                'mr_order_bom_costing_booking_id' => $costingBookingId
                            ])->sum('req_qty');
                $reqQty = $bookingQty;
            } else {
                $reqQty = $bookingListFirst->req_qty;
            }
            $bookingQty = MrOrderBooking::where([
                                'mr_order_bom_costing_booking_id' => $costingBookingId
                            ])->sum('booking_qty');
            $resultQty = $reqQty - $bookingQty;
            if($resultQty == 0 || $resultQty < 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    static public function getOrderSupplierList($orderId)
    {
        $where = [
            'order_id' => $orderId
        ];
        $supList = [];
        $supList = DB::table('mr_order_bom_costing_booking as a')
                    ->where($where)
                    ->leftJoin('mr_supplier as b','b.sup_id','a.mr_supplier_sup_id')
                    ->groupBy('b.sup_id')
                    ->pluck('b.sup_name','b.sup_id')
                    ->toArray();
        if(!empty($supList)) {
            $result = [];
            $supExist = [];
            foreach($supList as $supId=>$supplierName){
                $where = [
                    'mr_order_entry_order_id' => $orderId
                ];
                $poBookingDetails = PoBookingDetail::where($where)->first();
                if(!empty($poBookingDetails)) {
                    $supExist = PoBooking::where(['mr_supplier_sup_id' => $supId, 'id' => $poBookingDetails->mr_po_booking_id])->first();
                    if(empty($supExist)){
                        $result[$supId] = $supplierName;
                    }
                } else {
                    $result[$supId] = $supplierName;
                }
            }
        }
        return $result;
    }

    static public function getOrderItemList($orderId,$supplierId='')
    {
        $where = [
            'order_id' => $orderId
        ];
        if(!empty($supplierId)){
            $where['mr_supplier_sup_id'] = $supplierId;
        }
        $cosBookingList = [];
        $cosBookingList = DB::table('mr_order_bom_costing_booking as a')
                    ->select([
                        'a.id as cosId',
                        'a.mr_cat_item_id',
                        'b.item_name',
                        'c.sup_name'
                    ])
                    ->where($where)
                    ->leftJoin('mr_cat_item as b', 'b.id', 'a.mr_cat_item_id')
                    ->leftJoin('mr_supplier as c', 'c.sup_id', 'a.mr_supplier_sup_id')
                    // ->pluck('b.item_name','a.id')
                    ->get()
                    ->toArray();
        $poBookingDetails = PoBookingDetail::where(['mr_order_entry_order_id' => $orderId])->pluck('mr_order_bom_costing_booking_id','id')->toArray();
        $result = [];
/*       if(empty($poBookingDetails)) {
            foreach($cosBookingList as $key=>$cosBooking) {
                $result[] = $cosBooking->sup_name.'('.$cosBooking->item_name.')';
            }
            // $result = array_column($cosBookingList,'item_name');
            // $result = $cosBookingList;
        } else {
            foreach($cosBookingList as $key=>$cosBooking) {
               if(!in_array($cosBooking->cosId,$poBookingDetails)) {
                    $result[] = $cosBooking->sup_name.'('.$cosBooking->item_name.')';
               } else {
                    $mrBooking = MrOrderBooking::where(['mr_order_bom_costing_booking_id' => $cosBooking->cosId]);
                    if($mrBooking->count() > 0) {
                        $bookingQty = self::fixedNumber($mrBooking->sum('booking_qty'),2,true);
                        $reqQty = $mrBooking->first();
                        if($reqQty->mr_material_color_id != null || $reqQty->size != null) {
                            $reqQty = MrOrderBooking::where([
                                'mr_po_booking_id' => $reqQty->mr_po_booking_id,
                                'mr_order_bom_costing_booking_id' => $cosBooking->cosId
                            ])->sum('req_qty');
                            $reqQty = self::fixedNumber($reqQty,2,true);
                        } else {
                            $reqQty = self::fixedNumber($reqQty->req_qty,2,true);
                        }
                        $percentage = self::fixedNumber(($reqQty!=0?($bookingQty/$reqQty)*100:0),2,true);
                        $result[] = $cosBooking->sup_name.' ('.$cosBooking->item_name.')'.'~'.$bookingQty.'|'.$reqQty.'|'.$percentage.'%';
                    } else {
                        $result[] = $cosBooking->sup_name.' ('.$cosBooking->item_name.')'.'~0|0|0.00%';
                    }
               }
                $result[] = $cosBooking->sup_name.' ('.$cosBooking->item_name.')'.'~0|0|0.00%';
            }
        }*/
        foreach($cosBookingList as $key=>$cosBooking) {
            //return $cosBooking;
            $mr_order_bom_costing = OrderBomCostingBooking::where(['id' => $cosBooking->cosId])->first();
            $reqQty = $mr_order_bom_costing->precost_req_qty;
            //$mr_booking_qty = PoBookingDetail::where(['mr_po_booking_id' => $cosBooking->cosId])->groupBy('mr_po_booking_id')->first();
            //return $mr_booking_qty;
            //$booking_qty = $mr_booking_qty->booking_qty;
            $booking_qty = 0;
           // $reqQty = 13;
            $result[] = $cosBooking->sup_name.' ('.$cosBooking->item_name.')'.'~'.$booking_qty.'|'.$reqQty.'|0.00%';
        }
        return $result;
    }

    static public function fixedNumber($number,$fixed=6,$decimalCheck=false)
    {
        $returnNumber = 0;
        if(is_numeric($number)) {
            $returnNumber = number_format((float)$number, $fixed, '.', '');
            if($decimalCheck == true) {
                if(fmod($returnNumber, 1) == 0.00){
                    $returnNumber = round($number, 0);
                }
            }
        }
        return $returnNumber;
    }

    static public function btn($btnName,$reset=true)
    {
        $resetBtn = '';
        if($reset) {
            $resetBtn = '
                <button class="btn btn-xs" type="reset">
                      <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                  </button>
                  &nbsp; &nbsp; &nbsp;
            ';
        }
        $btn = '
            <div class="clearfix form-actions">
              <div class="col-sm-offset-3 col-sm-9 no-padding">
                  '.$resetBtn.'
                  <button class="btn btn-success btn-xs" type="submit">
                      <i class="ace-icon fa fa-check bigger-110"></i> '.$btnName.'  &nbsp;
                  </button>
              </div>
            </div>
        ';
        return $btn;
    }

    public static function getEmpStatusName($status)
    {
        $name = '';
        if($status == 2) {
            $name = 'resign';
        } else if($status == 3) {
            $name = 'terminate';
        } else if($status == 4) {
            $name = 'suspend';
        } else if($status == 5) {
            $name = 'left';
        } else if($status == 6) {
            $name = 'maternity';
        }
        return $name;
    }


    // Rubel end

    public static function locationNameBangla($value)
    {
        return Location::getLocationNameBangla($value);
    }

    public static function unitNameBangla($value)
    {
        return Unit::getUnitNameBangla($value);
    }

    public static function employeeByAsId($associate_id)
    {
        return Employee::where('associate_id', $associate_id)->first();
    }

    public static function engToBnConvert($value)
    {
        $en = array('0','1','2','3','4','5','6','7','8','9', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',',');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯', 'জানুয়ারী', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর',',');

        return str_replace($en, $bn, $value);
    }
    public static function convertNumberToWord($num = false)
    {
        $num = str_replace(array(',', ' '), '' , trim($num));
        if(! $num) {
            return false;
        }
        $num = (int) $num;
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ( $tens < 20 ) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }

    public static function unitIdWiseName($unit)
    {
        if($unit ==1){
            $tableName="MBM GARMENTS LTD.";
        } else if($unit ==2){
            $tableName="CUTTING EDGE INDUSTRIES LTD";
        } else if($unit ==3){
            $tableName="ABSOLUTE QUALITYWEAR LTD.";
        } else if($unit ==4){
            $tableName="MBM FASHION WEAR LTD.";
        } else if($unit ==5){
            $tableName="MBM GARMENTS LTD(UNIT 1).";
        } else if($unit ==6){
            $tableName="HO";
        } else if($unit ==8){
            $tableName="CUTTING EDGE INDUSTRIES LTD (WASHING PLANT).";
        }  else{
            $tableName="";
        }
        return $tableName;
    }

    public static function unitWiseAttendanceTableName($unit)
    {
        if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
            $tableName="hr_attendance_mbm";
        } else if($unit ==2){
            $tableName="hr_attendance_ceil";
        } else if($unit ==3){
            $tableName="hr_attendance_aql";
        } else if($unit ==6){
            $tableName="hr_attendance_ho";
        } else if($unit ==8){
            $tableName="hr_attendance_cew";
        } else{
            $tableName="hr_attendance_mbm";
        }
        return $tableName;
    }

    public static function getBuyerWiseOt($unit, $asId, $month, $year, $ot)
    {

        if($unit ==1 || $unit==4 || $unit==5 || $unit==9){
            $tableName="hr_attendance_mbm";
        } else if($unit ==2){
            $tableName="hr_attendance_ceil";
        } else if($unit ==3){
            $tableName="hr_attendance_aql";
        } else if($unit ==6){
            $tableName="hr_attendance_ho";
        } else if($unit ==8){
            $tableName="hr_attendance_cew";
        } else{
            $tableName="hr_attendance_mbm";
        }
        $date = $year.'-0'.$month;
        $getAtt = DB::table($tableName)
        ->where('as_id', $asId)
        ->where('in_time', 'LIKE', $date.'%')
        ->pluck('ot_hour');
        $otHour = 0;
        if(count($getAtt) > 0){
            foreach($getAtt as $att){
                if($att > $ot){
                    $parts = explode(':',$ot);
                    if(isset($parts[1])){
                        $otMinute = $parts[0]*60 + $parts[1];
                    }else{
                        $otMinute = $parts[0]*60;
                    }
                    $att = $otMinute;
                }
                $otHour += $att;
            }
        }
        //->get();

        return $otHour;

    }



    ///PI get booking
    public static function getPiQty($booking_id)
    {
        $bookedPi =  DB::table('cm_pi_bom')
                    ->where('mr_po_booking_id', $booking_id)
                    ->sum('pi_qty');
        return $bookedPi;
    }

    public static function getPiQtyByPi($booking_id,$pi_id)
    {
        $bookedPi =  DB::table('cm_pi_bom')
                    ->where('mr_po_booking_id', $booking_id)
                    ->where('cm_pi_master_id',$pi_id)
                    ->sum('pi_qty');
        return $bookedPi;
    }

    public static function getPiItemsQty($booking_id,$item)
    {
        $bookedPi =  DB::table('cm_pi_bom')
                    ->where('mr_po_booking_id', $booking_id)
                    ->where('mr_order_booking_id',$item)
                    ->sum('pi_qty');
        return $bookedPi;
    }
    public static function getPiItemsQtyByPi($booking_id,$item,$pi_id)
    {
        $bookedPi =  DB::table('cm_pi_bom')
                    ->where('mr_po_booking_id', $booking_id)
                    ->where('mr_order_booking_id',$item)
                    ->where('cm_pi_master_id',$pi_id)
                    ->sum('pi_qty');
        return $bookedPi;
    }

    public static function getBookingItemNames($booking_id)
    {
        $booking = DB::table('mr_order_booking AS mob')
                    ->where('mob.mr_po_booking_id', $booking_id)
                    ->Join('mr_cat_item As i','i.id','mob.mr_cat_item_id')
                    ->pluck('item_name')
                    ->unique()
                    ->toArray();
        return  $booking;
    }

    public static function getOrderByBookingId($booking_id){
        $order = DB::table('mr_order_booking AS mob')
                    ->where('mob.mr_po_booking_id', $booking_id)
                    ->leftJoin('mr_order_bom_costing_booking AS bcb', 'bcb.id', 'mob.mr_order_bom_costing_booking_id')
                    ->Join('mr_order_entry AS o','o.order_id','bcb.order_id')
                    ->pluck('o.order_code')
                    ->unique()
                    ->toArray();
        return $order;
    }

    public static function getCmPiBomInfoBy($btb,$cat,$item,$cons,$color,$size){
        $where = [
                'btb.id'=>$btb,
                'b.mr_material_category_mcat_id'=>$cat,
                'b.mr_cat_item_id'=>$item,
                'b.mr_construction_id'=>$cons,
                'mob.mr_material_color_id'=>$color,
                'mob.size'=>$size
            ];
        $booking = DB::table('mr_order_booking As mob')
                    ->select(
                        "mc.clr_name",
                        "cpb.pi_qty",
                        "cpb.id AS cm_pi_bom_id",
                        'cpb.cm_pi_master_id',
                        'o.order_delivery_date',
                        "ip.unit_price"
                    )
                    ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                    ->leftJoin("mr_material_category AS c", function($join) {
                      $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
                    })
                    ->leftJoin("mr_cat_item AS i", function($join) {
                        $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                        $join->on("i.id", "=", "b.mr_cat_item_id");
                    })
                    ->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
                    ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
                    ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
                    ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
                    ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
                    ->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
                    ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                    ->leftJoin("mr_pi_item_unit_price AS ip", function($join) {
                        $join->on("ip.cm_pi_master_id", "=", "cpb.cm_pi_master_id");
                        $join->on("ip.mr_po_booking_id", "=", "cpb.mr_po_booking_id");
                        $join->on("ip.mr_cat_item_id", "=", "b.mr_cat_item_id");

                    })
                    ->LeftJoin('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                    ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                    ->where($where)
                    ->leftJoin('mr_order_entry AS o','o.order_id','b.order_id')
                    //->leftJoin("cm_invoice_pi_bom AS inv", "inv.cm_pi_bom_id", "cpb.id")
                    ->orderBy("o.order_delivery_date",'ASC')
                    ->orderBy("cpb.cm_pi_master_id",'ASC')
                    ->get();
        return $booking;
    }

    public static function getCmPiBomInfoEditBy($btb,$cat,$item,$cons,$color,$size,$invoice_id){
        $where = [
                'btb.id'=>$btb,
                'b.mr_material_category_mcat_id'=>$cat,
                'b.mr_cat_item_id'=>$item,
                'b.mr_construction_id'=>$cons,
                'mob.mr_material_color_id'=>$color,
                'mob.size'=>$size,
                'inv.cm_imp_invoice_id'=> $invoice_id
            ];

        $booking = DB::table('mr_order_booking As mob')
                    ->select(
                        "mc.clr_name",
                        "cpb.pi_qty",
                        "cpb.id AS cm_pi_bom_id",
                        'cpb.cm_pi_master_id',
                        'o.order_delivery_date',
                        'inv.id as inv_id',
                        "ip.unit_price"
                    )
                    ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                    ->leftJoin("mr_material_category AS c", function($join) {
                      $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
                    })
                    ->leftJoin("mr_cat_item AS i", function($join) {
                        $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                        $join->on("i.id", "=", "b.mr_cat_item_id");
                    })
                    ->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
                    ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
                    ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
                    ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
                    ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
                    ->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
                    ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                    ->leftJoin("mr_pi_item_unit_price AS ip", function($join) {
                        $join->on("ip.cm_pi_master_id", "=", "cpb.cm_pi_master_id");
                        $join->on("ip.mr_po_booking_id", "=", "cpb.mr_po_booking_id");
                        $join->on("ip.mr_cat_item_id", "=", "b.mr_cat_item_id");

                    })
                    ->LeftJoin('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                    ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                    ->where($where)
                    ->leftJoin('mr_order_entry AS o','o.order_id','b.order_id')
                    ->Join("cm_invoice_pi_bom AS inv", "inv.cm_pi_bom_id", "cpb.id")
                    ->orderBy("o.order_delivery_date",'ASC')
                    ->orderBy("cpb.cm_pi_master_id",'ASC')
                    ->get();
        return $booking;
    }

    public static function getInvShippedQtyBy($btb,$cat,$item,$cons,$color,$size,$invoice_id){
        $where = [
                'btb.id'=>$btb,
                'b.mr_material_category_mcat_id'=>$cat,
                'b.mr_cat_item_id'=>$item,
                'b.mr_construction_id'=>$cons,
                'mob.mr_material_color_id'=>$color,
                'mob.size'=>$size
            ];
        if($invoice_id != null){
            $where['inv.cm_imp_invoice_id']=$invoice_id;
        }
        $shipped_qty = DB::table('mr_order_booking As mob')
                    ->select(
                      "mc.clr_name",
                      "cpb.pi_qty",
                      "cpb.id AS cm_pi_bom_id",
                      'cpb.cm_pi_master_id',
                      'o.order_delivery_date'
                    )
                    ->leftJoin('mr_order_bom_costing_booking AS b','b.id','mob.mr_order_bom_costing_booking_id')
                    ->leftJoin("mr_material_category AS c", function($join) {
                      $join->on("c.mcat_id", "=", "b.mr_material_category_mcat_id");
                    })
                    ->leftJoin("mr_cat_item AS i", function($join) {
                        $join->on("i.mcat_id", "=", "b.mr_material_category_mcat_id");
                        $join->on("i.id", "=", "b.mr_cat_item_id");
                    })
                    ->leftJoin("mr_material_color AS mc", "mc.clr_id", "mob.mr_material_color_id")
                    ->leftJoin("mr_supplier AS s", "s.sup_id", "b.mr_supplier_sup_id")
                    ->leftJoin("mr_article AS a", "a.id", "b.mr_article_id")
                    ->leftJoin("mr_composition AS com", "com.id", "b.mr_composition_id")
                    ->leftJoin("mr_construction AS con", "con.id", "b.mr_construction_id")
                    ->leftJoin("mr_product_size AS sz", "sz.id","mob.size")
                    ->Join("cm_pi_bom as cpb","mob.id" ,"cpb.mr_order_booking_id")
                    ->LeftJoin('cm_pi_forwarding_details AS cd','cd.cm_pi_master_id','cpb.cm_pi_master_id')
                    ->leftJoin('cm_btb AS btb', 'btb.cm_pi_forwarding_master_id','cd.cm_pi_forwarding_master_id')
                    ->where($where)
                    ->leftJoin('mr_order_entry AS o','o.order_id','b.order_id')
                    ->Join("cm_invoice_pi_bom AS inv", "inv.cm_pi_bom_id", "cpb.id")
                    ->sum('shipped_qty');
        return $shipped_qty;
    }


    public static function getShippedQtyBy($pimaster,$pibom,$invoice_id){
        $where = [
            'cm_pi_master_id' => $pimaster,
            'cm_pi_bom_id' => $pibom
        ];
        if($invoice_id != null){
            $where['cm_imp_invoice_id']= $invoice_id;
        }
        //dd($where);
        return DB::table('cm_invoice_pi_bom')
                        ->where($where)
                        ->sum('shipped_qty');

    }

    public static function getRemQty($pi){
        $master = DB::table('cm_pi_bom')->where('cm_pi_master_id', $pi)->sum('pi_qty');
        $invoice = DB::table('cm_invoice_pi_bom')->where('cm_pi_master_id',$pi)->sum('shipped_qty');
        return round(($master-$invoice),2);

    }

    public static function salaryLeaveAdjustAsIdMonthYearWise($asId, $month, $year)
    {
        $salaryAdjust = SalaryAdjustMaster::getCheckEmployeeIdMonthYearWise($asId, $month, $year);
        $amount = 0.00;
        if($salaryAdjust != null){
            if(isset($salaryAdjust->salary_adjust)){
                foreach ($salaryAdjust->salary_adjust as $salary) {
                    $amount += $salary->amount;
                }
            }
        }

        return round($amount, 0);
    }

    public static function numberToTime($number){
        $number = round($number,1);
        $hour = explode(".", $number);
        if(isset($hour[1])){
            return $hour[0].':'.round($hour[1]*6);
        }else
            return $hour[0];
    }


    public static function numberToTimeFormat($number){
        $number = round($number,1);
        $hour = explode(".", $number);
        if(isset($hour[1])){
            $hour[1] = round($hour[1]*6);
        }else{
            $hour[1] = '00';
        }
        return $hour[0].':'.$hour[1];
    }

    public static function getLockDate(){
        return DB::table('hr_system_setting')->first()->salary_lock;
    }

    /**
     * Update Laravel Env file Key's Value
     * @param string $key
     * @param string $value
     */
    public static function envUpdate($data = array(), $type = 'set')
    {
        if (!count($data)) {
            return;
        }

        $pattern = '/([^\=]*)\=[^\n]*/';

        $envFile = base_path() . '/.env';
        $lines = file($envFile);
        $newLines = [];
        $return = '';
        foreach ($lines as $line) {
            preg_match($pattern, $line, $matches);

            if (!count($matches)) {
                $newLines[] = $line;
                continue;
            }

            if (!key_exists(trim($matches[1]), $data)) {
                $newLines[] = $line;
                continue;
            } else {
                $return = $line;
            }

            $line = trim($matches[1]) . "={$data[trim($matches[1])]}\n";
            $newLines[] = $line;
        }

        if($type == 'set') {
            $newContent = implode('', $newLines);
            file_put_contents($envFile, $newContent);
            return $return.' - <b>Change To</b> - '.$data[key($data)];
        } else {
            return $return;
        }
    }

}
