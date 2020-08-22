<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4 class="modal-title" id="lineEmpTitle">
		{{$associate_id}}
	</h4>
</div>
<div class="modal-body" id="outSideEmpBody" style="height: 300px; overflow: auto;">
	<div class="table-responsive">
        <table class="table table-striped table-bordered" >
            <thead>
                <tr>
                    <th>Sl. No</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Changed Floor</th>
                    <th>Changed Line</th>
                </tr>
            </thead>
            <tbody>
            	@if(!empty($changes))
            		@php $count=0; @endphp
            		@foreach ($changes as $key => $change)
                	@php $count++; @endphp
                    <tr>
                		<td> <span class="label label-green arrowed-right">{{$count}}</span> </td>
                		<td>{{$change->start_date}}</td>
                		<td>{{$change->start_date}}</td>
                		<td>{{$change->hr_floor_name}}</td>
                		<td>{{$change->hr_line_name}}</td>
                	</tr>
                	@endforeach
					
				@else
					<tr>
						<td colspan="5" class="text-center">No Data Found</td>
					</tr>
				@endif
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>