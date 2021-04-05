<div class="row " style="padding-left: 15px;" >
	@foreach($operationList as $key => $operation)
	<div class="col-sm-3 text-center pr-2 pl-0">
		<div class="opr-item">
			<img style="width:50px;" src="{{asset($operation->image)}}">
			<br>
			<span>{{$operation->opr_name}}</span>
		</div>
	    <input type="hidden" name="opr_id[]" value="{{$operation->opr_id}}">
	    <input type="hidden" name="opr_type[]" value="{{$operation->opr_type}}">
	</div>
	@endforeach
</div>