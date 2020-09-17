@extends('hr.layout')
@section('title', 'Warning Notice')
@section('main-content')
@push('css')

@endpush
<div class="main-content">
  <div class="main-content-inner">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
      <ul class="breadcrumb">
        <li>
          <i class="ace-icon fa fa-home home-icon"></i>
          <a href="#">Human Resource</a>
        </li>
        <li>
          <a href="#">Operation</a>
        </li>
        <li class="active"> Warning Notice</li>
      </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col">
                <form role="form" method="get" action="{{ url('hr/operation/warning-notice') }}" class="noticeReport" id="noticeReport">
                    <div class="panel">
                        
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        {{ Form::select('associate', [Request::get('associate') => Request::get('associate')], Request::get('associate'), ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate', 'class'=> 'associates no-select col-xs-12','style', 'required'=>'required']) }}
                                        <label  for="associate"> Associate's ID </label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group has-float-label has-required select-search-group">
                                        <input type="month" class="form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                        <label  for="year"> Month </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                                    <a href="{{url('hr/reports/warning-notices')}}" class="btn btn-success pull-right" >Warning Notice List <i class="fa fa-list bigger-120"></i></a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
        </div>
        <div class="">
            @if(isset($info))
            <div class="panel panel-success" style="">
                
                <div class="panel-body">
                    <div class="row">
                        <div class="col-6">
                            {{Form::open(['url'=>'#', 'class'=>'form-horizontal'])}}
                                <div class="iq-card-body" style="border-right: 1px solid #d1d1d1;">
                                    <div class="form-group has-float-label has-required">
                                        <input type="text" name="reason" id="reason" class="form-control" placeholder="Notice Reason" required="required">
                                        <label  for="reason"> Notice Reason  </label>
                                    </div>
                                   <ul class="iq-timeline">
                                      <li>
                                         <div class="timeline-dots"></div>
                                         <h4 class="float-left mb-1">First Step</h4>
                                         <div class="d-inline-block mt-3 w-100">
                                            <div class="form-group has-float-label has-required ">
                                                <input type="date" name="first_step_date" id="first_step_date" class="form-control" required="required" value="" />
                                                <label for="first_step_date"> First Step Date </label>
                                            </div>

                                            <div class="form-group  file-zone">
                                                <label  for="file"> First Step File</label>
                                                <input type="file" id="file_upload" name="first-file" class="file-type-validation" data-file-allow='["xls","xlsx", "pdf", "doc", "docx"]' autocomplete="off" />
                                                <div class="invalid-feedback" role="alert">
                                                    <strong>Select a file</strong>
                                                </div>
                                            </div>
                                         </div>
                                      </li>
                                      <li>
                                         <div class="timeline-dots border-warning"></div>
                                         <h4 class="float-left mb-1">Second Step</h4>
                                         <div class="d-inline-block mt-3 w-100">
                                            <div class="form-group has-float-label ">
                                                <input type="date" name="second_step_date" id="second_step_date" class="form-control" required="required" value="" />
                                                <label for="second_step_date"> Second Step Date </label>
                                            </div>

                                            <div class="form-group  file-zone">
                                                <label  for="file"> Second Step File</label>
                                                <input type="file" id="file_upload" name="second-file" class="file-type-validation" data-file-allow='["xls","xlsx", "pdf", "doc", "docx"]' autocomplete="off" />
                                                <div class="invalid-feedback" role="alert">
                                                    <strong>Select a file</strong>
                                                </div>
                                            </div>
                                         </div>
                                      </li>
                                      <li>
                                         <div class="timeline-dots border-danger"></div>
                                         <h4 class="float-left mb-1">Third Step</h4>
                                         <div class="d-inline-block mt-3 w-100">
                                            <div class="form-group has-float-label ">
                                                <input type="date" name="third_step_date" id="third_step_date" class="form-control" required="required" value="" />
                                                <label for="third_step_date"> Third Step Date </label>
                                            </div>

                                            <div class="form-group  file-zone">
                                                <label  for="file"> third Step File</label>
                                                <input type="file" id="file_upload" name="third-file" class="file-type-validation" data-file-allow='["xls","xlsx", "pdf", "doc", "docx"]' autocomplete="off" />
                                                <div class="invalid-feedback" role="alert">
                                                    <strong>Select a file</strong>
                                                </div>
                                            </div>
                                         </div>
                                      </li>
                                      
                                   </ul>
                                   <div class="form-group ">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                                   </div>
                                </div>
                                
                            {{Form::close()}}
                        </div>
                        <div class="col-6">
                            <div class=" panel-info" id="basic_info_div">
                                <div class="panel-body">
                                    <div class="row">
                                        
                                        <div class="col">
                                            
                                            <div class="user-details-block" style="padding-top: 5rem;">
                                                <div class="user-profile text-center mt-0">
                                                    <img id="avatar" class="avatar-130 avatar-radius-4 img-fluid" src="{{ emp_profile_picture($info) }}">
                                                </div>
                                                <div class="text-center mt-3">
                                                 <h4><b id="name">{{ $info->as_name }}</b></h4>
                                                 <p class="mb-0" id="joined">Joined {{ $info->as_doj->diffForHumans() }}</p>
                                                 <p class="mb-0" id="designation">{{ $info->designation['hr_designation_name'] }}</p>
                                                 <p class="mb-0" >
                                                    Oracle ID: <span id="oracle_id" class="text-success">{{ $info->as_oracle_code }}</span>
                                                 </p>
                                                 <p class="mb-0" >
                                                    Associate ID: <span id="associate_id" class="text-success">{{ $info->associate_id }}</span>
                                                 </p>
                                                 <p  class="mb-0">Department: <span id="department" class="text-success">{{ $info->department['hr_department_name'] }}</span> </p>
                                                 
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div><!-- /.page-content -->
  </div>
</div>
@push('js')
<script type="text/javascript">
    function printMe1(divName)
    {
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<style>.page-header{text-align:center;}</style>');
        myWindow.document.write(document.getElementById(divName).innerHTML);
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    $(document).ready(function(){
        
        //select 2 check
        function formatState (state) {
         //console.log(state.element);
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span><img /> <span></span></span>'
            );

            var targetName = state.text;
            $state.find("span").text(targetName);
            return $state;
        };
        $('select.associates').select2({
            templateSelection:formatState,
            placeholder: 'Select Associate\'s ID',
            ajax: {
                url: '{{ url("hr/associate-search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            var oCode = '';
                            if(item.as_oracle_code !== null){
                                oCode = item.as_oracle_code + ' - ';
                            }
                            return {
                                text: oCode + item.associate_name,
                                id: item.associate_id,
                                name: item.associate_name
                            }
                        })
                    };
              },
              cache: true
            }
        });

    });
</script>
@endpush
@endsection