@extends('merch.index')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Setup</a>
                </li>  
                <li class="active"> Executive Team Setup Edit</li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content"> 

            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6> Executive Team Setup Edit
                                     <a class="pull-right healine-panel" href="{{ url('merch/setup/excecutive_team_setup') }}" rel="tooltip" data-tooltip="Executive Team Setup Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                                  <!-- Display Erro/Success Message -->
                                @include('inc/message')
                                {!! Form::open(['url'=>'merch/setup/excecutive/members_update', 'class'=>'form-horizontal']) !!}
                                <input type="hidden" name="team_id" id="team_id" value="{{$exce_team->id}}">
                                <div class="col-xs-12"> 

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="unit"> Unit<span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-8" style="pointer-events: none;"> 
                                            {{ Form::select('unit_id', $units, $exce_team->unit_id, ['placeholder'=>'Select Unit Name', 'id'=>'unit_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Unit Name field is required']) }}   
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="name" > Team Name<span style="color: red; vertical-align: top;">&#42;</span>  </label>
                                        <div class="col-sm-8">
                                            <input type="text" id="team_name" name="team_name" placeholder="Team Name" class="col-xs-12" data-validation="required length custom" value="{{ $exce_team->team_name }}" data-validation-length="1-128"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="team_lead" >Team Lead<span style="color: red; vertical-align: top;">&#42;</span>  </label>
                                        <div class="col-sm-8">
                                            {!! Form::select('team_lead',$employee, $exce_team->team_lead_id, ['id' => 'team_lead', 'class' => 'team_lead col-xs-12', 'data-validation'=>'required']) !!}
                                        </div>
                                    </div>                        

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="members" >Members<span style="color: red; vertical-align: top;">&#42;</span>  </label>
                                        <div class="col-sm-8">
                                            {!! Form::select('members[]',$employee, $exce_team->members, ['id' => 'members', 'class' => 'members col-xs-12', 'data-validation'=>'required','multiple' => 'multiple']) !!}
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col -->
                                 <div class="col-xs-8 col-sm-offset-2" style="margin-bottom: 20px;">
                                     @include('merch.common.update-btn-section')
                                 </div>
                                    {!! Form::close() !!}
                              </div>
                        </div>
                </div> 
                
            </div>


        </div> {{-- page-content-end --}}
    </div> {{-- main-content-inner-end --}}
</div> {{-- main-content-end --}}

<script type="text/javascript">
    // $(document).ready(function(){   
    //     $("#unit_id").on("change", function(){  
    //          var unitval = $(this).val();     // console.log(unitval);
    //          var team_id = $('#team_id').val();
    //          var list = $("#members");
    //          var list2 = $("#team_lead");
    //          //console.log(JSON.stringify(unitval));
    //         // Element list
    //         $.ajax({
    //             url : "{{ url('merch/setup/excecutive/members_edit') }}",
    //             type: 'get',
    //             data: {
                    
    //                 unit_id:unitval,
    //                 team_id:team_id
    //             },

    //             success: function(data)
    //             {
    //                list.html(data);
    //                list2.html(data);
    //             },
    //             error: function()
    //             {
    //                 alert('failed...');
    //             }
    //         });
    //     }).change();
    // });
</script>
@endsection