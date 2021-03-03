<style>
    .checkbox label, .radio label {
        font-size: 12px !important;
    }
    #myTabContent {
        border: 0 !important;
    }
</style>
<div class="">
    <div class="row">
        <div class="col">
            <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                <div class="iq-card-header d-flex justify-content-between">
                   <div class="iq-header-title">
                      <h4 class="card-title">Size Group</h4>
                   </div>
                   <div class="iq-card-header-toolbar d-flex align-items-center">
                      <ul class="nav nav-pills" id="myTab" role="tablist">
                        @php
                            $slcount = 0;
                        @endphp
                        @foreach($dataGroup as $key1 => $aw)
                            @php
                                $slcount += 1;
                            @endphp
                            <li class="nav-item ">
                                <a class="nav-link {{ $slcount==1?'active':'' }}" id="ia{{ $slcount }}-tab" data-toggle="tab" href="#ia{{ $slcount }}" role="tab" aria-controls="{{ $slcount }}" aria-selected="true">{{ $key1 }}</a>
                            </li>
                        @endforeach
                        
                      </ul>
                   </div>
                </div>
                <div class="iq-card-body pt-0 pb-0">
                   <div class="tab-content" id="myTabContent">
                        @php $count = 0; @endphp
                        @foreach($dataGroup as $key => $dg)
                            @php
                                $new_dxd = [];
                                $count += 1;
                            @endphp
                            <div class="tab-pane fade {{ $count==1?'active show':'' }}" id="ia{{ $count }}" role="tabpanel" aria-labelledby="ia{{ $count }}-tab">
                                
                                <ul class="checkbox">
                                    <div class="">
                                        @php $digitCount = 0; @endphp
                                        <div class="row">
                                        @foreach($dg as $k=>$g)
                                            @php
                                                if(in_array($key, ['DxD','D/D','D/L','DL'])) {
                                                    if($key=='DL'){
                                                        list($second_size,$first_size) = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $g->size);
                                                    } else {
                                                        list($first_size,$second_size) = explode($key[1], $g->size);
                                                    }
                                                   
                                                    $new_dxd[$first_size][] = $g;
                                                }
                                            @endphp
                                            
                                            @if(!in_array($key, ['DxD','D/D','D/L','DL']))
                                                @if(in_array($key, ['L','Digit']))
                                                    @if($key == 'Digit')                                
                                                        @if($digitCount == 0)
                                                            <div class="col-sm-12 row p-0">
                                                                <li class="col-sm-2 col-xs-2" style="font-size: 11px; padding-top: 5px; ">{{ $g->size.'-'.(int)($g->size+9) }}</li>
                                                            
                                                            </div>
                                                            
                                                        @endif
                                                        <div class="col-sm-2 col-xs-2" style="background-color: #dbdbdb; padding-right: 0">
                                                           <li class=''>
                                                            <input type='checkbox' class='group-input-size' name="selected_size[]" id="size_{{ $g->id }}" value="{{ $g->id }}" />
                                                           <label for="size_{{ $g->id }}">{{ $g->size }}</label>
                                                           </li> 
                                                        </div>
                                                        
                                                        @php
                                                            $digitCount++;
                                                        @endphp
                                                        @if($digitCount == 10)
                                                            @php
                                                                $digitCount = 0;
                                                            @endphp
                                                        @endif
                                                    @else
                                                        <li class='col-sm-2 col-xs-2 pr-0'>
                                                            <input type='checkbox' class='group-input-size' name="selected_size[]" id="size_{{ $g->id }}" value="{{ $g->id }}" />
                                                           <label for="size_{{ $g->id }}">{{ $g->size }}</label>
                                                        </li>
                                                    @endif
                                                @else
                                                    <li class='col-sm-2 col-xs-2 pr-0'>
                                                        <input type='checkbox' class='group-input-size' name="selected_size[]" id="size_{{ $g->id }}" value="{{ $g->id }}" />
                                                       <label for="size_{{ $g->id }}">{{ $g->size }}</label>
                                                    </li>
                                                @endif
                                                
                                            @endif

                                        @endforeach
                                         </div>    
                                        @if(!empty($new_dxd))
                                            <?php $c=0; ?>
                                            @foreach($new_dxd as $d_key=>$new_dx)
                                              <?php 
                                    
                                                $c++;
                                                $colors=array('#a8a8a8','#dbdbdb');
                                                for($i=0;$i<=$c;$i++)
                                                {
                                                    $color=$colors[$c%2];
                                                }
                                                ?> 
                                                @if(!empty($new_dx))
                                                <div class="row each-new-dx" style="margin-top: 9px;background-color:<?= $color ?>;">
                                                    {{-- <div class="col-sm-1"></div> --}}
                                                    <div class="col-sm-2 col-xs-2 pr-0">                                                
                                                        <li class="col-sm-12 col-xs-12 p-0" style="font-size: 15px; padding-top: 5px;">{{ $d_key }}</li>
                                                    </div>
                                                    <div class="col-sm-10 row col-xs-12">
                                                    @foreach($new_dx as $n_key=>$new_d)
                                                        <li class='col-sm-2 col-xs-2 pr-0' style="font-size: 11px;">
                                                            <input type='checkbox' class='group-input-size' name="selected_size[]" id="size_{{ $new_d->id }}" value="{{ $new_d->id }}" style="position: relative !important;"/>
                                                            <label style="font-size: 11px;" for="size_{{ $new_d->id }}">{{ $new_d->size }}</label>
                                                        </li>
                                                    @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </ul>
                            </div>
                        @endforeach
                      
                   </div>
                </div>
             </div>
            
        </div>
    </div>
</div>
