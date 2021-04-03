<div class="">
	<div class="checkbox" id="washStoreDiv">';
		@foreach ($washCategoryList as $key => $washCategory) 
			<label class='col-sm-2' style='padding:0px;'>
		  		<span class='lbl'> {{$washCategory->category_name}}</span>";
		  		@if(count($washCategory->mr_wash_type) > 0) {
		   			<ul>
		    			@foreach($washCategory->mr_wash_type as $k => $wash) 
		     				@php
		     					$checked = '';
		      					if(!empty($selectedWash)) {
		        					$checked = in_array($wash->id, $selectedWash)!==FALSE?'checked="checked"':'';
		      					}
		      				@endphp
		      				<li style='list-style-type: none;'>
		      					<label style='padding:0px;'>
		      						<input name='washType[]' type='checkbox' class='ace' value='{{$wash->id}}' {{$checked}}>
		      						<span class='lbl'> {{$wash->wash_name}}</span>
		      					</label>
		      				</li>
		      			@endforeach
		    		</ul>
		  		@endif
		  	</label>
		@endforeach
	</div>
</div>