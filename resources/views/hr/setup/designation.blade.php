@extends('hr.layout')
@section('title', 'Designation Library')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Designation </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')

        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Designation</h6></div> 
                    <div class="panel-body">
                        <div class="hidden output"></div>
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/designation')  }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('hr_designation_emp_type', $emp_type, null, ['placeholder'=>'Select Associate Type', 'id'=>'hr_designation_emp_type', 'class'=> 'form-control', 'required'=>'required']) }}  
                                <label  for="hr_designation_emp_type"> Associate Type  </label> 
                            </div>

                            <div class="form-group has-required has-float-label">
                                <input type="text" name="hr_designation_name" placeholder="Designation Name" class="form-control" required="required" />
                                <label  for="hr_designation_name" > Designation Name  </label>
                            </div>  

                            <div class="form-group has-required has-float-label">
                                <input type="text" id="hr_designation_name_bn" name="hr_designation_name_bn" placeholder="পদের নাম" class="form-control"  />
                                <label  for="hr_designation_name_bn" > পদবী (বাংলা) </label>
                            </div>  

                            <div class="form-group has-required has-float-label">
                                <input type="text" name="hr_designation_grade" placeholder="Grade" class="form-control" required="required" />
                                <label  for="hr_designation_grade" > Grade </label>
                            </div> 
                            <div class="form-group">
                                <button class="btn btn-success" type="submit">
                                    <i class=" fa fa-check bigger-110"></i> Submit
                                </button>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body">
                    
                        
                        <table id="global-datatable"class="table table-striped table-bordered" style="display: block;width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Associate Type</th>
                                    <th style="width: 30%;">Designation Name</th>
                                    <th style="width: 30%;">পদবী (বাংলা)</th>
                                    <th style="width: 20%;">Grade</th>
                                    <th style="width: 20%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                                @foreach($designations as $designation)
                                <tr class="ui-state-default" style="cursor:move">
                                    <td>{{ $designation->hr_emp_type_name }}</td>
                                    <td>{{ $designation->hr_designation_name }}</td>
                                    <td>{{ $designation->hr_designation_name_bn }}</td>
                                    <td>{{ $designation->hr_designation_grade }}</td>
                                    <td>
                                        <input type='hidden' class="position" name='designation[{{ $designation->hr_designation_id }}]' value='{{ $designation->hr_designation_position }}'>
                                        <div class="btn-group">
                                            <a type="button" href="{{ url('hr/setup/designation_update/'.$designation->hr_designation_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('hr/setup/designation/'.$designation->hr_designation_id) }}" type="button" onclick="return confirm('Are you sure?')" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                 </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div> 
@push('js')
<script type="text/javascript">
/*$(document).ready(function(){ 

    $('#sortable').sortable({
        stop: function () {
            var inputs = $('input.position');
            var nbElems = inputs.length;
            $('input.position').each(function(id) 
            { 
                $(this).val(nbElems - id);
            });

            $.ajax({
                url: '{{ url("hr/hierarchy") }}',
                type: 'post',
                data: $('#hierarchyFrm').serialize(),
                success: function(data)
                {
                    $(".output").removeClass('hidden').addClass("alert alert-success").html(data);
                    setTimeout(function(){
                        $(".output").addClass('hidden').removeClass("alert alert-success").html('');
                    }, 3000);
                },
                error: function(xhr)
                {
                    console.log(xhr)
                }
            });
        }
    });  
 

});*/

</script>
@endpush
@endsection