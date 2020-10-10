<div class="col-sm-12">
     <p>Total selected: <b>{{count($data)}}</b></p>
</div>
@foreach($data as $info)
<div class="col-sm-3 mb-2">
	<div class="iq-info-box d-flex ">
       <div class="info-image mr-3">
          <img src="{{emp_profile_picture($info)}}" class="img-fluid" alt="image-box">
       </div>
       <div class="info-text">
          <strong>{{ $info->associate_id}}</strong><br>
          <span>{{ $info->as_name}}</span><br>
          <span><i>Shift:</i> {{ $info->shift['hr_shift_name']??'' }}</span><br>
          <span><i>Line:</i> {{ $info->line['hr_line_name']??'' }}</span><br>
          <span><i>Floor:</i> {{ $info->floor['hr_floor_name']??'' }}</span>
       </div>
    </div>
    <div class="separator"></div>
</div>
@endforeach