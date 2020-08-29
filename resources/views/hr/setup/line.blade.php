@extends('hr.layout')
@section('title', 'Line')
@section('main-content')
    @push('css')
    @endpush
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="#"> Human Resource </a>
            </li> 
            <li>
                <a href="#"> Library </a>
            </li>
            <li class="active"> Line </li>
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
                        Line
                        <a class="btn btn-primary pull-right" href="#list">Line List</a>
                    </h6>
                </div> 
                <div class="panel-body">
                    
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/line')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label select-search-group">
                                    {{ Form::select('hr_line_unit_id', $unitList, null, ['placeholder'=>'Select Unit Name', 'id'=>'hr_line_unit_id', 'class'=> 'form-control', 'required'=>'required']) }} 
                                    <label  for="hr_line_unit_id"> Unit Name  </label>
                                </div>


                                <div class="form-group has-required has-float-label select-search-group">
                                    <select name="hr_line_floor_id" id="hr_line_floor_id" class="form-control" required="required">
                                        <option value="">Select Floor Name </option> 
                                    </select>
                                    <label  for="hr_line_floor_id" >Floor Name  </label>\
                                </div>

                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-required has-float-label">
                                    <input type="text" name="hr_line_name" placeholder="Line Name" class="form-control"  required="required">
                                    <label  for="hr_line_name" > Line Name  </label>
                                </div>

                                
                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group has-float-label">
                                    <input type="text" id="hr_line_name_bn" name="hr_line_name_bn" placeholder="লাইনের নাম" class="form-control"  />
                                    <label  for="hr_line_name_bn" > লাইন (বাংলা) </label>
                                </div> 
                                <div class="form-group"> 
                                    <button class="btn pull-right btn-primary" type="submit">Submit</button>
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
                                <table id="global-datatable" class="table table-striped table-bordered" style="display: block;width: 100%; ">
                                    <thead>
                                        <tr>
                                            <th width="30%">Unit Name</th>
                                            <th width="30%">Floor Name</th>
                                            <th width="30%">Line Name</th>
                                            <th width="30%">লাইন (বাংলা)</th>
                                            <th width="30%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lines as $line)
                                        <tr>
                                            <td>{{ $line->hr_unit_name }}</td>
                                            <td>{{ $line->hr_floor_name }}</td>
                                            <td>{{ $line->hr_line_name }}</td>
                                            <td>{{ $line->hr_line_name_bn }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a type="button" href="{{ url('hr/setup/line_update/'.$line->hr_line_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                    <a href="{{ url('hr/setup/line/'.$line->hr_line_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
                                <table id="global-trash" class="table table-striped table-bordered" style="display: block;width: 100%; ">
                                    <thead>
                                        <tr>
                                            <th width="30%">Unit Name</th>
                                            <th width="30%">Floor Name</th>
                                            <th width="30%">Line Name</th>
                                            <th width="30%">লাইন (বাংলা)</th>
                                            <th width="30%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lines as $line)
                                        <tr>
                                            <td>{{ $line->hr_unit_name }}</td>
                                            <td>{{ $line->hr_floor_name }}</td>
                                            <td>{{ $line->hr_line_name }}</td>
                                            <td>{{ $line->hr_line_name_bn }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a type="button" href="{{ url('hr/setup/line_update/'.$line->hr_line_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                    <a href="{{ url('hr/setup/line/'.$line->hr_line_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
    var unit  = $("#hr_line_unit_id");
    var floor = $("#hr_line_floor_id")
    unit.on('change', function(){
        $.ajax({
            url : "{{ url('hr/setup/getFloorListByUnitID') }}",
            type: 'json',
            method: 'get',
            data: {unit_id: $(this).val() },
            success: function(data)
            {
                floor.html(data);
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
