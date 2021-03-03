
<style type="text/css">
    .modal-header {
        padding: 15px;
        border-bottom: none;
        text-align: center;
    }

    .cat-title {
        background: #d1d1d1;
        font-weight: 600;
        padding: 5px 10px;
        font-size: 13px;
        border-right:1px dotted #fff;
    }
    .modal-widget {
        border: none;
    }
    .modal-widget .widget-header-small {
        min-height: 16px;
        padding: 5px 10px;
    }
    .modal-widget .widget-header{
        background: #fff!important;
        background-image: none;
        border:none;
    }
    .modal-tab-card {
        height: 250px;
        overflow-y: auto;
    }
    .modal-item{
        background:#fff;
    }
    .item-colums {
        border-left: 1px dotted #67B2DD;
        overflow-y:auto;
        height: 100%;
    }
    .col-sm-4 .column-list {
        border-left: none !important;
    }
    .modal-widget .widget-header:before {
        display: block;
        content: "";
        position: absolute;
        top: 16px;
        left: -20px;
        width: 30px;
        height: 0;
        border-top: 1px dotted #67B2DD;
        z-index: 1;
    }
    .modal-item .col-md-12{
        padding: 8px 8px 8px 0;
    }
    .modal-icon{
        font-size: 10px !important;
        border: 1px solid #d1d1d1;
        padding: 3px 4px 2px 4px;
    }
    li .checkbox{
        margin: 0;
    }
    #myTab2 li a{
        font-size: 14px;
        font-weight: 600;
    }
    .modal-widget .widget-main {
        padding: 0 10px 0 20px;
    }
    .widget-main .column-list{border-left: 1px solid #d1d1d1;}
    .modal-widget .widget-header a{
        display: block;
        color: #000;
    }
    .subcat-title{
        font-size: 13px;
    }
</style>
<div class="widget-box transparent ui-sortable-handle" id="widget-box-13">
    <div class="widget-header" style="border-bottom:none;">
        <div class="no-border">
            <ul class="nav nav-tabs" id="myTab2">
                @foreach($cat as $key => $category)
                @php
                    $active = '';
                    if($key==1){
                        $active = 'active';
                    }
                @endphp
                <li class="{{$active}}">
                    <a data-toggle="tab" href="#tab_{{$key}}" aria-expanded="false">{{$category->mcat_name}}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="widget-body">
        <div class="widget-main no-padding">
            
            <div class="tab-content padding-4">
                    
                    @foreach($catItem as $key => $subItem)
                        @php
                            $active = '';
                            if($key==1){
                                $active = 'active';
                            }
                             $name = strtolower(str_replace(" ", "_", $cat[$key]->mcat_name));

                        @endphp
                        <div id="tab_{{$key}}" class="tab-pane {{$active}}">
                            <div class="card mt-3 tab-card modal-tab-card ">
                                @php 
                                    $count=0;
                                    $div = 3;
                                @endphp
                                @foreach($subItem as $key1 => $dg)
                                    @if($key1 == null)
                                      @php $div = 2; @endphp
                                      <div class="col-sm-4 item-colums">
                                        <ul class="column-list list-unstyled" >
                                            @foreach($dg as $key2 => $item)
                                                @php 
                                                    $checked = '';
                                                    if(in_array($item->id, $existItem)){
                                                        $checked = 'checked';
                                                    }
                                                @endphp
                                                <li>
                                                    <input type="hidden" value="{{$cat[$key]->mcat_id}}"/>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input name="form-field-checkbox" class="ace ace-checkbox-2" type="checkbox" data-cat="{{$cat[$key]->mcat_id}}" id="{{$name.'_'.$item->id}}" name="$name[]" value="{{$item->id}}" {{$checked}}>
                                                            <span class="lbl"> {{$item->item_name}}</span>
                                                        </label>
                                                    </div>
                                                </li>
                                            @endforeach
                                            </ul>
                                         </div>
                                        @endif
                                    @endforeach
                                @php
                                    unset($subItem[null]);
                                    $chunkItem = round(count($subItem)/$div);
                                @endphp
                                @if(count($subItem)>0)
                                @foreach(array_chunk($subItem, $chunkItem, true) as $curta )
                                    <div class="col-sm-4 item-colums">
                                    @foreach($curta as $key3 => $dg)
                                        <div class="modal-widget widget-box collapsed">
                                            <div class="widget-header widget-header-small">
                                                <a href="#" data-action="collapse">
                                                    <i class="modal-icon ace-icon fa fa-plus" data-icon-show="fa-plus" data-icon-hide="fa-minus"></i>
                                                    <span class="subcat-title"> {{ $key3 }} </span>
                                                </a> 
                                                
                                            </div>

                                            <div class="widget-body">
                                                <div class="widget-main">
                                                    <ul class="column-list list-unstyled" >
                                                    @foreach($dg as $key2 => $item)
                                                        @php 
                                                            $checked = '';
                                                            if(in_array($item->id, $existItem)){
                                                                $checked = 'checked';
                                                            }
                                                        @endphp
                                                        <li>
                                                            <input type="hidden" value="{{$cat[$key]->mcat_id}}"/>
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input name="form-field-checkbox" class="ace ace-checkbox-2" type="checkbox" data-cat="{{$cat[$key]->mcat_id}}" id="{{$name.'_'.$item->id}}" name="$name[]" value="{{$item->id}}" {{$checked}}>
                                                                    <span class="lbl"> {{$item->item_name}}</span>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                @endforeach
                                @endif
                            </div>
                            
                        </div>
                    @endforeach
            </div>
        </div>
    </div>
</div>


