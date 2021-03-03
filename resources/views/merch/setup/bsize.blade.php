<style>
    .checkbox label, .radio label {
        font-size: 12px !important;
    }
    #myTabContent {
        border: 0 !important;
        margin-top: 5px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="col-6">
            <div class="card mt-3 tab-card">
                <div class="card-header tab-card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        @php
                            $count = 0;
                        @endphp
                        @foreach($dataGroup as $key => $dg)
                            @php
                                $count += 1;
                            @endphp
                            <li class="nav-item {{ $count==1?'active':'' }}">
                                <a class="nav-link" id="{{ $count }}-tab" data-toggle="tab" href="#{{ $count }}" role="tab" aria-controls="{{ $count }}" aria-selected="true">{{ $key }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                    @php
                        $count = 0;
                        
                    @endphp

                    @foreach($dataGroup as $key => $dg)
                        @php
                            $new_dxd = [];
                            
                            $count += 1;
                        @endphp
                        <div class="tab-pane {{ $count==1?'active':'' }}" id="{{ $count }}" role="tabpanel" aria-labelledby="{{ $count }}-tab">
                            
                            <ul class="checkbox">
                               

                                    @php
                                        $digitCount = 0;
                                    @endphp
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
                                                        <div class="col-sm-12 col-xs-12">
                                                            <li class="col-sm-2 col-xs-2" style="font-size: 12px; padding-top: 5px; ">{{ $g->size.'-'.(int)($g->size+9) }}</li>
                                                        
                                                        </div>
                                                        
                                                    @endif
                                                    <div class="col-sm-1 col-xs-1" style="background-color: #dbdbdb; padding-right: 0">
                                                       <li class='col-sm-1 col-xs-1'>
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
                                                    <li class='col-sm-1 col-xs-1'>
                                                        <input type='checkbox' class='group-input-size' name="selected_size[]" id="size_{{ $g->id }}" value="{{ $g->id }}" />
                                                       <label for="size_{{ $g->id }}">{{ $g->size }}</label>
                                                    </li>
                                                @endif
                                            @else
                                                <li class='col-sm-1 col-xs-1'>
                                                    <input type='checkbox' class='group-input-size' name="selected_size[]" id="size_{{ $g->id }}" value="{{ $g->id }}" />
                                                   <label for="size_{{ $g->id }}">{{ $g->size }}</label>
                                                </li>
                                            @endif
                                            
                                        @endif
                                    @endforeach
                                    
                                @if(!empty($new_dxd))
                                    <?php $c=0; ?>
                                    @foreach($new_dxd as $d_key=>$new_dx)
                                      <?php 
                            //$color = str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
                                     
                                               //dd($color);exit;
                                        $c++;
                                        $colors=array('#a8a8a8','#dbdbdb');
                                        for($i=0;$i<=$c;$i++)
                                        {
                                            $color=$colors[$c%2];
                                        }
                                        /*$colors = explode(',', '#9ce6ae,#d6d8db,#c3e6cb,#ffeeba,#f6ddcc,#aeb6bf,#abb2b9');

                                            shuffle($colors);
                                            for ($i = 0; $i < 1; $i++) {
                                                $color=$colors[$i];
                                            } */
                                        ?> 
                                        @if(!empty($new_dx))
                                        <div class="col-sm-12 col-xs-12 each-new-dx" style="margin-top: 9px;background-color:<?= $color ?>;">
                                            {{-- <div class="col-sm-1"></div> --}}
                                            <div class="col-sm-1 col-xs-1">                                                
                                                <li class="col-sm-12 col-xs-12" style="font-size: 15px; padding-top: 5px;">{{ $d_key }}</li>
                                            </div>
                                            <div class="col-sm-10 col-xs-10">
                                            @foreach($new_dx as $n_key=>$new_d)
                                                <li class='col-sm-2 col-xs-2' style="font-size: 12px;">
                                                    <input type='checkbox' class='group-input-size' name="selected_size[]" id="size_{{ $new_d->id }}" value="{{ $new_d->id }}" style="position: relative !important;margin-left: 3px !important;"/>
                                                    <label style="font-size: 12px;" for="size_{{ $new_d->id }}">{{ $new_d->size }}</label>
                                                </li>
                                            @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                @endif
                                <?php //dump($new_dxd);?>
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
