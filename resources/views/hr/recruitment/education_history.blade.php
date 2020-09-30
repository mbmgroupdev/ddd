<ul class="speciality-list m-0 p-0">    

    @if(count($education_data) > 0)
        @foreach($education_data as $key => $education)                              
        <li class="d-flex mb-4">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-graduation-cap"></i></a></div>
           <div class="media-support-info ml-3">
              <h6><b>{{$education->education_level_title}}</b></h6>
              <p class="mb-0"><span class="text-primary">Institute:</span> {{$education->education_institute_name}}</p>
              <p>
                <span class="text-primary">Concentration/Major/Group:</span>
                    @if(!in_array($education->education_level_id, [1,2,8]))
                        {{$education->education_major_group_concentation}}
                    @endif

                    @if(in_array($education->education_level_id, [8]))
                        {{$education->education_degree_id_2}}
                    @endif
              </p>
              <p>
                  <span class="text-primary">Year: </span>{{$education->education_passing_year}}
                  @if(in_array($education->education_result_id, [1,2,3]))
                  <span class="text-primary">Marks: </span>{{$education->education_result_marks }}
                  @endif
                  @if(in_array($education->education_result_id, [4]))
                  <span class="text-primary">CGPA: </span>{{$education->education_result_cgpa }} ({{$education->education_result_scale}})
                  @endif
              </p>
           </div>
           <div class="action-area">
               <a href=""  class="btn btn-sm btn-primary edit-education" data-edu="{{json_encode($education)}}"  data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>
               <a onclick="return confirm('are you sure?')" href="{{url('hr/recruitment/operation/education_info/delete/'.$education->id.'/'.$education->education_as_id)}}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
           </div>
        </li>
        @endforeach
    @else
        <li class="d-flex mb-4 align-items-center">
           <div class="user-img img-fluid"><a href="#" class="iq-bg-primary"><i class="las f-18 la-graduation-cap"></i></a></div>
           <div class="media-support-info ml-3">
              <h6>Education</h6>
              <p class="mb-0">No education history!</p>
            </div>
        </li>

    @endif
    
 </ul>