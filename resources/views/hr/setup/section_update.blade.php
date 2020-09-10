@extends('hr.layout')
@section('title', 'Section '.$section->hr_section_name)
@section('main-content')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Human Resource </a>
            </li> 
            <li>
                <a href="#"> Library </a>
            </li>
            <li class="active"> Section </li>
        </ul><!-- /.breadcrumb --> 
    </div>
    <div class="row">
       <div class="col-lg-2 pr-0">
           <!-- include library menu here  -->
           @include('hr.settings.library_menu')
       </div>
       <div class="col-lg-10 mail-box-detail">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h6>
                        Section : {{ $section->hr_section_name }}
                        <a class="btn btn-primary pull-right" href="#list">Section List</a>
                    </h6>
                </div> 
                <div class="panel-body">
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/section_update')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <input type="hidden" name="hr_section_id" value="{{ $section->hr_section_id}}"> 
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('hr_section_area_id', $areaList, $section->hr_section_area_id, ['placeholder' => 'Select Area Name', 'class' => 'form-control no-select', 'id'=>'hr_section_area_id', 'required'=>'required']) }}
                                    <label  for="hr_section_area_id" > Area Name </label>
                                </div>

                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('hr_section_department_id', $departmentList, $section->hr_section_department_id, ['placeholder' => 'Select Department Name', 'class' => 'form-control', 'id'=>'hr_section_department_id', 'required'=>'required']) }}
                                    <label  for="hr_section_department_id" >Department Name </label>
                                </div>


                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_section_name" id="hr_section_name" placeholder="Section Name" class="form-control" required="required" value="{{ $section->hr_section_name }}"/>
                                    <label  for="hr_section_name" > Section Name </label>
                                </div>

                                <div class="form-group  has-float-label">
                                    <input type="text" name="hr_section_name_bn" id="hr_section_name_bn" placeholder="সেকশনের নাম" class="form-control" value="{{ $section->hr_section_name_bn }}"/>
                                    <label  for="hr_section_name_bn" > সেকশন (বাংলা) </label>
                                </div>

                                
                                
                            </div>
                            <div class="col-sm-4"> 
                                <div class="form-group has-float-label">
                                    <input type="text" id="hr_section_code" name="hr_section_code" placeholder="Section code" class="form-control" value="{{ $section->hr_section_code }}">
                                    <label  for="hr_section_code"> Section Code </label>
                                </div>
                                <div class="form-group"> 
                                    <button class="btn pull-right btn-primary" type="submit">Update</button>
                                </div>
                                
                            </div>
                        </div>    
                            
                    </form> 
                </div>
            </div>
            <div id="list" class="panel panel-info">
                <div class="panel-body">
                    <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="false">Active</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash" role="tab" aria-controls="trash" aria-selected="false">Trash</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="active" role="tabpanel" aria-labelledby="active-tab">
                         
                            <div class="table-responsive">
                                <table id="global-datatable" class="table table-striped table-bordered" style="display: block;width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">Area Name</th>
                                            <th style="width: 20%;">Department Name</th>
                                            <th style="width: 20%;">Section Name</th>
                                            <th style="width: 30%;">সেকশন (বাংলা)</th>
                                            <th style="width: 10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sections as $section)
                                        <tr>
                                            <td>{{ $section->hr_area_name }}</td>
                                            <td>{{ $section->hr_department_name }}</td>
                                            <td>{{ $section->hr_section_name }}</td>
                                            <td>{{ $section->hr_section_name_bn }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a type="button" href="{{ url('hr/setup/section_update/'.$section->hr_section_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                    <a href="{{ url('hr/setup/section/'.$section->hr_section_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                            <div class="table-responsive">
                                <table id="global-trash" class="table table-striped table-bordered" style="display: block;width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">Area Name</th>
                                            <th style="width: 20%;">Department Name</th>
                                            <th style="width: 20%;">Section Name</th>
                                            <th style="width: 30%;">সেকশন (বাংলা)</th>
                                            <th style="width: 10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trashed as $section)
                                        <tr>
                                            <td>{{ $section->hr_area_name }}</td>
                                            <td>{{ $section->hr_department_name }}</td>
                                            <td>{{ $section->hr_section_name }}</td>
                                            <td>{{ $section->hr_section_name_bn }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a type="button" href="{{ url('hr/setup/section_update/'.$section->hr_section_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                    <a href="{{ url('hr/setup/section/'.$section->hr_section_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

       </div>
    </div>
@push('js')
<script type="text/javascript">
$(document).ready(function(){
    var area    = $("#hr_section_area_id");
    var department = $("#hr_section_department_id")
    area.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getDepartmentListByAreaID') }}",
            type: 'json',
            method: 'get',
            data: {area_id: $(this).val() },
            success: function(data)
            {
                department.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    });
});
</script>
@endpush
@endsection
