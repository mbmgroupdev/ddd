@if(count($unit_data) > 0 )
<div class="panel">
    <div class="panel-body">
    	Submitted increment for approval 
    	@foreach($unit_data as $key => $v)
    		<span style="cursor: pointer;" class="text-primary" onclick="getApprovalData({{$key}})">{{$unit[$key]['hr_unit_short_name']}}({{$v}})</span>, 
    	@endforeach
    </div>
</div>
@endif