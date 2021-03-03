@extends('merch.index')
@push('css')
<style type="text/css">
    .select2-container--default .select2-results__option[aria-disabled=true] {
            color: tan;
        }

</style>
@endpush
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Setup</a>
                </li>  
                <li class="active"> Executive Team Setup </li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content"> 

            <div class="row">
             <div class="panel panel-default">
                <div class="panel-body">
                      <!-- Display Erro/Success Message -->
                    @include('inc/message')
                    <div class="row no-padding no-margin">
                         <div class="panel panel-success">
                              <div class="panel-heading">
                              <h6>Executive Team Setup</h6>
                              </div>
                              <div class="panel-body">
                                  {!! Form::open(['url'=>'merch/setup/excecutive/members_save', 'class'=>'form-horizontal']) !!}
                                    <div class="col-sm-8 col-sm-offset-2 no-padding">

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="unit"> Unit<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                            <div class="col-sm-8"> 
                                                {{ Form::select('unit_id', $units, null, ['placeholder'=>'Select Unit Name', 'id'=>'unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}   
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="name" > Team Name<span style="color: red; vertical-align: top;">&#42;</span>  </label>
                                            <div class="col-sm-8">
                                                <input type="text" id="team_name" name="team_name" placeholder="Team Name" class="col-xs-12" data-validation="required length custom" value="{{ old('team_name') }}" data-validation-length="1-128"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="team_lead" >Team Lead<span style="color: red; vertical-align: top;">&#42;</span>  </label>
                                            <div class="col-sm-8">
                                                {!! Form::select('team_lead',[], old('team_lead'), ['id' => 'team_lead', 'class' => 'team_lead col-xs-12', 'data-validation'=>'required']) !!}
                                            </div>
                                        </div>                        

                                        <div class="form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="members" >Members<span style="color: red; vertical-align: top;">&#42;</span>  </label>
                                            <div class="col-sm-8">
                                                {!! Form::select('members[]',[], old('members'), ['id' => 'members', 'class' => 'members col-xs-12', 'data-validation'=>'required','multiple' => 'multiple']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-xs-8 col-sm-offset-2" style="margin-bottom: 20px;     margin-left: 22%;">
                                        @include('merch.common.save-btn-section')
                                    </div>
                                {!! Form::close() !!}
                              </div>
                        </div>
                    </div>

                    {{-- <div class="col-sm-6 no-padding-right"> --}}
                         <div class="panel panel-info">
                              <div class="panel-heading">
                              <h6>Executive Team List</h6>
                              </div>
                              <div class="panel-body">
                                  <table id="dataTables" class="table table-striped table-bordered"  style="margin-top: 20px !important;">
                                        <thead>
                                            <tr>
                                                <th>SI.</th>
                                                <th>Unit</th>
                                                <th>Team Name</th>
                                                <th>Team Lead</th>                                    
                                                <th>Team Members</th>                                    
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($exce_team)
                                                @foreach($exce_team as $team)
                                                <tr>
                                                    <td style="vertical-align: middle;" >{{ $loop->index+1 }}</td>
                                                    <td style="vertical-align: middle;">{{ $team->hr_unit_name }}</td>
                                                    <td style="vertical-align: middle;">{{ $team->team_name }}</td>
                                                    <td style="vertical-align: middle;">{{ $team->team_leader }}</td>
                                                    <td style="vertical-align: middle;">
                                                        @foreach($team->members as $member)
                                                            {{ $member->team_member }}<br>
                                                        @endforeach 
                                                    </td>

                                                    <td style="vertical-align: middle;">
                                                        <div class="btn-group">
                                                            <a type="button" href="{{ url('merch/setup/excecutive/edit_team/'.$team->id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                            <a href="{{ url('merch/setup/excecutive/delete_team/'.$team->id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                                        </div>
                                                    </td>

                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                               </table>
                              </div>
                        </div>
                    {{-- </div> --}}
                </div>
            </div>
  
          </div>
                 
        </div><!-- /.page-content -->
    </div>
</div>  
<script type="text/javascript">


  
$(document).ready(function(){   

    $('#dataTables').DataTable();

    $("#unit_id").on("change", function(){  
  
         var unitval = $(this).val();     // console.log(unitval);

         var list = $("#members");
         var list2 = $("#team_lead");
         //console.log(JSON.stringify(unitval));

        // Element list
        $.ajax({
            url : "{{ url('merch/setup/excecutive/members') }}",
            type: 'get',
            data: {
            
                unit_id:unitval
            },

            success: function(data)
            {
               list.html(data);
               list2.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });

    });

});
</script>
@endsection