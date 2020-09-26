<div class="user-details-block benefit-employee">
      <div class="user-profile text-center mt-0">
      		@if($info->as_pic)
            	<img id="avatar" class="avatar-130 img-fluid" src="{{ asset($info->as_pic) }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
            @else
            	<img id="avatar" class="avatar-130 img-fluid" src="{{ asset("assets/images/user/09.jpg") }}" onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
            @endif
      </div>
      <div class="text-center mt-3">
         <h4><b id="user-name">{{$info->as_name}}</b></h4>
        <p class="mb-0" id="designation">
            Associate ID: {{$info->associate_id}}</p>
        <p class="mb-0" id="designation">
            Oracle ID: {{$info->as_oracle_code}}</p>
         
      </div>
</div>
<h5 class="center">Leave log {{date('Y')}}</h5>
	<?php 
		$display='';
		if($info->as_gender =='Male')
		{$display='display:none;';} 
	?>
@if(!empty($leaves)) 
	<table class="table table-bordered table-stripped" >
		<thead>
		<tr>
			<th >Leave Type</th>
			<th >Total</th>
			<th >Taken</th>
			<th >Due</th>
		</tr>	
		</thead>
		<tbody>
		<tr>
			<th >Casual</th>
			<td >10</td>
			<td >{{ (!empty($leaves->casual)?$leaves->casual:0) }}</td>
			<td >{{ (10-$leaves->casual) }}</td>
		</tr>
		<tr>
			<th >Earned</th>
			<td > {{$earnedLeaves[date('Y')]['earned']}} </td>
			<td > {{$earnedLeaves[date('Y')]['enjoyed']}} </td>
			<td > {{$earnedLeaves[date('Y')]['remain']}} </td>
		</tr>
		<tr>
			<th >Sick</th>
			<td >14</td>
			<td >{{ (!empty($leaves->sick)?$leaves->sick:0) }}</td>
			<td >{{ (14-$leaves->sick) }}</td>
		</tr>
		<tr>
			<th >Special</th>
			<td > - </td>
			<td >{{ (!empty($leaves->special)?$leaves->special:0) }}</td>
			<td > - </td>
		</tr>
		<tr style="{{$display}}">
			<th >Maternity</th>
			<td >112</td>
			<td >{{ (!empty($leaves->maternity)?$leaves->maternity:0) }}</td>
			<td >{{ (112-$leaves->maternity) }}</td>
		</tr>
		</tbody>
		@if($info->as_gender=='Male')
		<tfoot>
			<tr style="    background: #efefef;"> 
				<th >Subtotal</th>
				<td >{{ (14)+(10)+(112)+($earnedLeaves[date('Y')]['earned'])-112 }}</td>
				<td> {{(!empty($leaves->maternity)?$leaves->maternity:0)+(!empty($leaves->sick)?$leaves->sick:0)+($earnedLeaves[date('Y')]['enjoyed']) + (!empty($leaves->casual)?$leaves->casual:0)}}</td>
				<td >{{ (10-$leaves->casual)+($earnedLeaves[date('Y')]['remain'])+(14-$leaves->sick)+(112-$leaves->maternity)-112 }}</td>
			</tr>
		</tfoot>
		@else
		<tfoot>
			<tr style="    background: #efefef;"> 
				<th >Subtotal</th>
				<td >{{ (14)+(10)+(112)+($earnedLeaves[date('Y')]['earned']) }}</td>
				<td> {{(!empty($leaves->maternity)?$leaves->maternity:0)+(!empty($leaves->sick)?$leaves->sick:0)+($earnedLeaves[date('Y')]['enjoyed']) + (!empty($leaves->casual)?$leaves->casual:0)}}</td>
				<td >{{ (10-$leaves->casual)+($earnedLeaves[date('Y')]['remain'])+(14-$leaves->sick)+(112-$leaves->maternity) }}</td>
			</tr>
		</tfoot>
		@endif
		
	</table>

@else 

	<table class="table table-bordered table-stripped" >
		<thead>
		<tr>
			<th >Leave Type</th>
			<th >Total</th>
			<th >Taken</th>
			<th >Due</th>
		</tr>	
		</thead>
		<tbody>
		<tr>
			<th >Casual</th>
			<td >10</td>
			<td >0</td>
			<td >10</td>
		</tr>
		<tr>
			<th >Earned</th>
			<td >{{$earnedLeaves[date('Y')]['remain']}}</td>
			<td >0</td>
			<td >{{$earnedLeaves[date('Y')]['remain']}}</td>
		</tr>
		<tr>
			<th >Sick</th>
			<td >14</td>
			<td >0</td>
			<td >14</td>
		</tr>
		<tr>
			<th >Special</th>
			<td > - </td>
			<td >0</td>
			<td > - </td>
		</tr>
		<tr style="{{$display}}">
			<th >Maternity</th>
			<td >112</td>
			<td >0</td>
			<td >112</td>
		</tr>
		</tbody>
		@if($info->as_gender=='Male')
		<tfoot>
			<tr style="background: #efefef;"> 
				<th >Subtotal</th>
				<td >{{(136+$earnedLeaves[date('Y')]['remain']-112)}}</td>
				<td >0</td>
				<td >{{(136+$earnedLeaves[date('Y')]['remain']-112)}}</td>
			</tr>
		</tfoot>
		@else
		<tfoot>
			<tr style="background: #efefef;"> 
				<th >Subtotal</th>
				<td >{{(136+$earnedLeaves[date('Y')]['remain'])}}</td>
				<td >0</td>
				<td >{{(136+$earnedLeaves[date('Y')]['remain'])}}</td>
			</tr>
		</tfoot>
		@endif
		
	</table>
@endif
		

 