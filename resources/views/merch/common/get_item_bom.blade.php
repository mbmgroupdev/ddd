@php
    $disabledClass = 'tr-disabled';
    if(isset($boms->id)){
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
        <input  type="hidden" class="form-control input-sm"  data-validation="required" value="{{$item->mcat_name}}" readonly/>
        <input  type="hidden" name="mr_material_category_mcat_id[]" value="{{$item->mcat_id}}">
        <input  type="hidden" name="id[]" value="{{$boms->id??0}}"> 
        <span style="font-size: 9px;"> {{$item->mcat_name}} </span>
    </td>
    <td>
        <input  type="hidden" class="form-control input-sm"  data-validation="required" value="{{$item->item_name}}" readonly/>
        <input  type="hidden" name="mr_cat_item_id[]" value="{{$item->id}}"> 
        {{$item->item_name}}
    </td>
    <td>
        <input  type="hidden" class="form-control input-sm"  data-validation="required" value="{{$item->item_code}}" readonly/>
        {{$item->item_code}}
    </td>
    <td>
        <input  type="text" name="item_description[]" class="form-control input-sm bg_field"  placeholder="Description" value="{{$boms->item_description??''}}"/>
    </td>
    <td>{!!$color!!}</td>
    <td>
        <input  type="text" name="size[]" class="form-control input-sm"  placeholder="Size/Width" value="{{$boms->size??''}}"/>
    </td>
    <td>{!!$supplier!!}</td>
    <td>{!!$article!!}</td>
    <td class="comp_name">
    @isset($boms->id)
        {{ $boms->comp_name === null ? "N/A" : $boms->comp_name }}
    @endisset
    </td>
    <td class="construction_name">
    @isset($boms->id)
        {{ $boms->construction_name === null ? "N/A" : $boms->construction_name }}
    @endisset
    </td>
    <td>{!!$uom!!}</td>
    <td>
        <input  data-toggle="tooltip" title="{{$item->mcat_name}} > {{$item->item_name}} " type="text" name="consumption[]" class="form-control input-sm calc consumption tooltipped" data-validation="required" placeholder="Select" value="{{$boms->consumption??0}}" onclick="this.select()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/ >
    </td>
    <td>
        @php
            if(isset($boms->extra_percent)){
                $extra = $boms->extra_percent;
            }else{
                $extra = 5;
            }
        @endphp
        <input  data-toggle="tooltip" title="{{$item->mcat_name}} > {{$item->item_name}} " type="text" name="extra_percent[]" class="form-control input-sm calc extra tooltipped"  placeholder="Extra" data-validation="required"  value="{{$extra}}" onclick="this.select()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/>
    </td>
    <td>
        <input  type="text" class="form-control input-sm qty"  placeholder="Extra Qty" data-validation="required" readonly value="{{$boms->extra_qty??0}}"/>
    </td>
    <td>
        <input  type="text" class="form-control input-sm calc total"  placeholder="Total" data-validation="required" readonly value="{{$boms->total_value??0}}"/>
    </td>
</tr>