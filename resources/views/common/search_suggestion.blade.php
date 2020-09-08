<div class="iq-card-body em-card-body p-3">
   @if(count($employees) > 0)
   <ul class="emp-lists m-0 p-0">
      @foreach($employees as $key => $emp)
      <li class="mb-2 p-2">
         <a class="d-flex  align-items-center" href="{{url('hr/recruitment/employee/show/')}}/{{$emp->associate_id}}">
            <div class="user-img img-fluid"><img src="{{emp_profile_picture($emp)}}" alt="story-img" class="rounded-circle avatar-40"></div>
            <div class="media-support-info ml-3">
               <h6>{{$emp->as_name}}</h6>
               <p class="mb-0 font-size-12">{{$emp->associate_id}}</p>
            </div>
         </a>
      </li> 
      @endforeach                           
   </ul>
   <a href="{{ url('/search') }}/?search={{$keyword}}" class="btn btn-primary d-block mt-3"><i class="ri-add-line"></i> View All results </a>
   @else
      <p class="text-center">No result found!</p>
   @endif
</div>