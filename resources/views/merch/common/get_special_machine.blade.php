<style type="text/css">
	ul.operation-list {
	  list-style-type: none;
	}

	ul.operation-list li {
	  display: inline-block;
	  padding: 0;
	  margin: 0;
	}

	ul.operation-list input[type="checkbox"][id^="check-"] {
	  display: none;
	}

	ul.operation-list label {
	  border: 1px solid #fff;
	  padding: 10px;
	  display: block;
	  position: relative;
	  margin: 10px;
	  cursor: pointer;
	  text-align: center;
	}

	ul.operation-list label:before {
	  background-color: white;
	  color: white;
	  content: " ";
	  display: block;
	  border-radius: 50%;
	  border: 1px solid rgb(8 155 171);
	  position: absolute;
	  top: -5px;
	  left: -5px;
	  width: 25px;
	  height: 25px;
	  text-align: center;
	  line-height: 28px;
	  transition-duration: 0.4s;
	  transform: scale(0);
	}

	ul.operation-list label img {
	  height: 100px;
	  width: 100px;
	  transition-duration: 0.2s;
	  transform-origin: 50% 50%;
	}

	ul.operation-list :checked + label {
	  border-color: #ddd;
	}

	ul.operation-list :checked + label:before {
	  content: "✓";
	  background-color: rgb(8 155 171);
	  transform: scale(1);
	}

	ul.operation-list :checked + label img {
	  transform: scale(0.9);
	  /* box-shadow: 0 0 5px #333; */
	  z-index: -1;
	}
</style>
<ul class="operation-list">
	@php $selectedSpOp = $selectedSpOp??[];  @endphp
	@foreach($spMachineList as $key => $machine)
  	<li>
    	<input type="checkbox" id="check-{{$machine->opr_id}}" name="operations[]" data-content-type="{{$machine->opr_type}}" data-img-src="{{asset($machine->image)}}" data-name="{{$machine->opr_name}}" value="{{$machine->opr_id}}" @if($machine->opr_type == 1 || in_array($machine->opr_type, $selectedSpOp)) checked @endif @if($machine->opr_type == 1) disabled="disabled" @endif/>
    	<label for="check-{{$machine->opr_id}}">
    		<img src="{{asset($machine->image)}}" /><br>
    		<span>{{$machine->opr_name}}</span>
    	</label>
  	</li>
  	@endforeach
</ul>