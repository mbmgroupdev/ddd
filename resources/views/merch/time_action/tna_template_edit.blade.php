@extends('merch.index')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li>
                    <a href="#"> Time & Action </a>
                </li>
                  
                <li class="active"> TNA Template Update</li>
            </ul><!-- /.breadcrumb --> 
        </div>


        <div class="page-content"> 
            <div class="row">
              <div class="col-sm-10 col-sm-offset-1">
                        <div class="panel panel-info">
                              <div class="panel-heading">
                                    <h6> TNA Template Update
                                     <a class="pull-right healine-panel" href="{{ url('merch/time_action/tna_template') }}" rel="tooltip" data-tooltip="TNA Template Entry/List" data-tooltip-location="left"><i class="fa fa-list"></i></a></h6>
                              </div>
                              <div class="panel-body">
                                    <!-- Display Erro/Success Message -->
                                  @include('inc/message')
                                  <form class="form-horizontal col-sm-12" role="form" method="post" action="{{ url('merch/time_action/tna_template_update')}}" enctype="multipart/form-data">
                                    {{ csrf_field() }} 

                                      <div class="form-group">
                                          <label class="col-sm-3 control-label no-padding-right" for="template_name" >Template Name<span style="color: red">&#42;</span> </label>
                                          <div class="col-sm-5">
                                               <input type="text" id="template_name" name="template_name" placeholder="Enter Text" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50" value="{{$template->tna_temp_name}}" />
                                           </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="col-sm-3 control-label no-padding-right" for="tna_code" >Buyer <span style="color: red">&#42;</span> </label>
                                            <div class="col-sm-5">
                                               {{ Form::select('buyer', $buyer, $template->mr_buyer_b_id, ['placeholder'=>'Select Buyer','id'=>'bid','class'=> 'col-xs-12', 'data-validation' => 'required']) }}

                                            </div> 
                                             <div id="msg" class="col-sm-9 pull-right" style="color: red">
                                             </div>
                                      </div>
                                      <div class="form-horizontalform-group">
                                        <h5 class="page-header">TNA Library </h5>
                                        <table class="table table-striped table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>TNA Code</th>
                                                    <th>Library Action</th>
                                                    <th>Offset Day</th>
                                                    <th>Logic</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                           <tbody>
                                            @foreach($library as $lib)
                                             <tr>
                                                <td>
                                                  <input type="text"  name="tna_lib_code[]" placeholder="Enter Text" class="col-xs-12" value="{{$lib->tna_lib_code}}" disabled="disabled"/>
                                                </td>
                                                <td>
                                                  <input type="text"  name="tna_lib_action[]" placeholder="Enter Text" class="col-xs-12" value="{{$lib->tna_lib_action}}"  disabled="disabled" />
                                                </td>
                                                <td><?php $tnalib=DB::table('mr_tna_template_to_library AS t')
                                                 ->select(
                                                          't.*'
                                                        )
                                                    ->where('t.mr_tna_library_id', $lib->id)
                                                    ->where('t.mr_tna_template_id', $template->id)     
                                                    ->first();
                                                     ?>

                                                  @if($tnalib)
                                                    <input type="text"  name="tna_lib_offset[]" placeholder="Enter Offset" class="col-xs-12 Offset" value="{{$tnalib->offset_day}}" data-validation="required length custom" data-validation-length="1-11"/>
                                                  @else 
                                                    <input type="text"  name="tna_lib_offset[]" placeholder="Enter Offset" class="col-xs-12 Offset" value="" data-validation="required length custom" data-validation-length="1-11" disabled="disabled"/>
                                                  @endif  
                                                </td>
                                                <td class="logic-box" width="25%">
                                               

                                                     @if($tnalib)
                                                       {{ Form::select('logic[]',array('DCD or FOB' => 'DCD or FOB', 'OK to Begin' => 'OK to Begin'),  $tnalib->tna_temp_logic, ['placeholder'=>'Select','class'=> 'col-xs-12 logic', ($tnalib? null : 'disabled'),'data-validation' => 'required'  ]) }}
                                                    @else
                                                       {{ Form::select('logic[]',array('DCD or FOB' => 'DCD or FOB', 'OK to Begin' => 'OK to Begin'),  null, ['placeholder'=>'Select','class'=> 'col-xs-12 logic', ($tnalib? null : 'disabled'),'data-validation' => 'required'  ]) }}
                                                    @endif
                                                  <td>
                                             
                                                   <input type="checkbox" value="{{$lib->id}}" name="tnalibrary[]" class="tnalibrary" @if($tnalib) checked @endif >

                                                   <input type="hidden" name="lib_id" value="{{$lib->id}}">

                                                </td>
                                              </tr>
                                            @endforeach
                                        </tbody>
                                        </table>
                                      </div>
                                      <input type="hidden" name="tna_id" value="{{$template->id}}">
                                      @include('merch.common.update-btn-section')                        
                                    </form>
                              </div>
                        </div>
                </div>    
                </div>  
                <!-- /.col -->
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

///Data TAble Color        
    $('#dataTables').DataTable({
        responsive: true, 
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
    });

///Is Buyer name sample type exists
  $("#sample_name").keyup(function(){  
  
        var msg = $("#msg");
        var bid = $("#bid").val();

        // Action Element list
        $.ajax({
            url : "{{ url('merch/setup/sampletypecheck') }}",
            type: 'get',
            data: {keyword : $(this).val(),b_id: bid},
            success: function(data)
            {
                if(data==1){ 
                     msg.html("This Sample Type Already exists");
                     $("#sample_name").val("");
                }
               else{ msg.html("");}

            },
            error: function()
            {
                alert('failed...');
            }
        });

    });
///

});
</script>
<script type="text/javascript">
 // Enable Logic field according to checkbox 
$("document").ready(function(){
    $("body").on("click", ".tnalibrary", function(){
        if ($(this).parent().parent().find("select").is(":disabled"))
        {
            $(this).parent().parent().find("select").prop("disabled", false);
            $(this).parent().parent().find(".Offset").prop("disabled", false);
            //$('.checkval').val("1");
        }
        else
        {
            $(this).parent().parent().find("select").prop("disabled", true);
            $(this).parent().parent().find(".Offset").prop("disabled", true);

            //$('.checkval').val("0");
        }
    });
});


</script>
@endsection

