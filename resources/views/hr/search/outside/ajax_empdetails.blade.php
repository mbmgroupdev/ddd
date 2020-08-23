<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4 class="modal-title" id="outSideEmpTitle">
		@php
			if(!empty($dataList)) {
				echo $dataList[0]['basic']['as_name'];
			}
		@endphp
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
                    <th>Total Count</th>
                    <th>Purpose</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
            	@if(!empty($dataList))
					@foreach($dataList as $k=>$data)
						<tr>
							<td>{{$k+1}}</td>
							<td>{{$data['start_date']}}</td>
							<td>{{$data['end_date']}}</td>
							<td>{{\Carbon\Carbon::parse($data['end_date'])->diffInDays($data['start_date'])+1}}</td>
							<td>{{$data['requested_place']}}</td>
							<td>{{$data['comment']}}</td>
						</tr>
					@endforeach
				@else
					<tr>
						<td colspan="6" class="text-center">No Data Found</td>
					</tr>
				@endif
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>