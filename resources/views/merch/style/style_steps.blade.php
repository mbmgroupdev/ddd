<style type="text/css">
    .steps-div{
        padding-bottom: 20px;
        border-left: 2px solid #d1d1d1;
    }
    .steps>li.active .step, .steps>li.active:before, .steps>li.complete .step, .steps>li.complete:before{
        border-color: #039e08;
    }
    .step{
        transform: rotate(-48deg);
    }
    li.a{
        text-decoration: none;
    }
    .title:hover{
        cursor: pointer;
        text-decoration: none;
    }
</style>
<div class="steps-div">
    <ul class="steps" style="margin-left: 0">
    <?php
        $active = '';
        $url = '#'; 
    ?>

    @if(isset($data['bom']))
        @php
        $active = 'class=active';
        $url = url('merch/style_bom').'/'.$id.'/edit'; 
        @endphp
    @else
        @php
            $active = '';
            $url = url('merch/style_bom').'/'.$id.'/create'; 
        @endphp
    @endif
        <li data-step="1"  {{$active}}>
            <a href="{{ $url }}" >
                <span class="step">1</span>
                <span class="title">Style BOM</span>
            </a>
        </li>

    @if(isset($data['costing']))
        @php
        $active = 'class=active';
        $url = url('merch/style_costing').'/'.$id.'/edit'; 
        @endphp
    @else
        @php 
        $active = '';
        $url = url('merch/style_costing').'/'.$id.'/create'; 
        @endphp

    @endif
    @if(!isset($data['bom']))
        @php 
            $active = '';
            $url = '#'; 
        @endphp
    @endif

        <li data-step="2" {{$active}}>
            <a href="{{ $url }}" >
                <span class="step">2</span>
                <span class="title">Style Costing</span>
            </a>
        </li>

    @if(isset($data['approval']))
        @php
        $active = 'class=active';
        $url = url('merch/style_costing').'/'.$id.'/edit';
            if($data['approval']->level == 1){
                $color = '#039e08 #039e08 #CED1D6 #CED1D6';
            }else if($data['approval']->level == 2){
                $color = '#039e08 #039e08 #039e08 #CED1D6';
            }else if($data['approval']->level == 3){
                $color = '#039e08';
            }else{
                $color = '#039e08 #CED1D6 #CED1D6 #CED1D6';
            } 
        $tooltip = 'In Level-'.$data['approval']->level.' To '.$data['approval']->name; 
        @endphp
    @else
        @php 
        $active = '';
        $tooltip = 'No Approval Request';
        $color = '#CED1D6';
        $url = url('merch/style_costing').'/'.$id.'/edit'; 
        @endphp
    @endif

    @if(!isset($data['costing']))
        @php 
            $active = '';
            $url = '#';
            $tooltip = 'Add Costing First'; 
        @endphp
    @endif

        <li data-step="3" {{$active}} >
            <a href="{{ $url }}" >
                <span class="step" style="border-color: {{$color}}; "> 3 </span>
                <span class="title" rel='tooltip' data-tooltip='{{$tooltip}}' data-tooltip-location='top'>Costing Approval</span>
            </a>
        </li>
    </ul>
</div>