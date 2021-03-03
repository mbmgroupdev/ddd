@php
    $disabledClass = 'tr-disabled';
    if(isset($boms->stl_bom)||isset($boms->bom_id)){
        $disabledClass = 'tr-active';
    }

    if($item->mcat_id == 1){
        $cl = 'fab-tr';
    }else if($item->mcat_id == 2){
        $cl = 'sew-tr';
    }else if($item->mcat_id == 3){
        $cl = 'fin-tr';
    }
@endphp
<tr id="item-{{$item->id}}" class="{{$disabledClass}} {{$cl}}">
    <td>
        <input  type="hidden" name="mr_material_category_mcat_id[]" value="{{$item->mcat_id}}">
        <input type="hidden" name="style_primary_key_id[]" value="{{$boms->stl_bom??''}}">
        <input type="hidden" name="order_primary_key_id[]" value="{{$boms->bom_id??''}}">
        <input type="hidden" name="clr_id[]" value="{{$boms->clr_id??''}}">
        <input type="hidden" name="size[]" value="{{$boms->size??''}}">
        <span style="font-size: 9px;"> {{$item->mcat_name}} </span>
    </td>
    
    <td>
        <input  type="hidden" name="mr_cat_item_id[]" value="{{$item->id}}"> 
        {{$item->item_name}}
    </td>
    <td>
        @php
            if(isset($boms->depends_on)){
                $depend = $boms->depends_on;
            }else{
                $depend = $item->dependent_on;
            }

            $color_status= "";
            $size_status= "";
            $color_hidden= '';
            $size_hidden= '';


            if($depend == 1){
                $color_status= "checked";
                $size_hidden= 'name=size_depends[]';
            }
            else if($depend == 2){
                $size_status= "checked";
                $color_hidden= 'name=color_depends[]';
            }
            else if($depend == 3){
                $color_status= "checked";
                $size_status= "checked";
            }
            else{
                $color_hidden= 'name=color_depends[]';
                $size_hidden= 'name=size_depends[]';
            }
        @endphp
        <label><input name="color_depends[]" type="checkbox" value="1" data-validation-optional="true" class="ace color_depends" data-validation="checkbox_group" data-validation-qty="min1" {{$color_status}}>
            <span class="lbl">Color</span>
            <input {{ $color_hidden}} type="hidden" value="0" class="ace color_depends">
        </label>
        <label>
            <input name="size_depends[]" type="checkbox" value="2" class="ace size_depends" {{$size_status}}>
            <span class="lbl">Size</span>
            <input {{$size_hidden}} type="hidden" value="0" class="ace size_depends">
        </label>


    </td>
    
    <td>
        <input  type="text" name="item_description[]" class="form-control input-sm bg_field"  placeholder="Description" value="{{$boms->item_description??''}}"/>
    </td>
    <td>{!!$supplier!!}</td>
    <td>{!!$article!!}</td>
    <td class="comp_name">
    @if(isset($boms->stl_bom)||isset($boms->bom_id))
        {{ $boms->comp_name === null ? "N/A" : $boms->comp_name }}
    @endif
    </td>
    <td class="construction_name">
    @if(isset($boms->stl_bom)||isset($boms->bom_id))
        {{ $boms->construction_name === null ? "N/A" : $boms->construction_name }}
    @endif
    </td>
    <td>{!!$uom!!}</td>
    <td>
        <input  data-toggle="tooltip" title="{{$item->mcat_name}} > {{$item->item_name}} " type="text" name="consumption[]" class="form-control input-sm calc consumption tooltipped" data-validation="required" placeholder="Select" value="{{$boms->consumption??0}}"/ >
    </td>
    <td>
        @php
            if(isset($boms->extra_percent)){
                $extra = $boms->extra_percent;
            }else{
                $extra = 5;
            }
        @endphp
        <input  data-toggle="tooltip" title="{{$item->mcat_name}} > {{$item->item_name}} " type="text" name="extra_percent[]" class="form-control input-sm calc extra tooltipped"  placeholder="Extra" data-validation="required"  value="{{$extra}}"/>
    </td>
    <td>
        <input  type="text" class="form-control input-sm qty"  placeholder="Extra Qty" data-validation="required" readonly value="{{$boms->extra_qty??0}}"/>
    </td>
    <td>
        <input  type="text" class="form-control input-sm calc total"  placeholder="Total" data-validation="required" readonly value="{{$boms->total_value??0}}"/>
    </td>
</tr>