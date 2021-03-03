@extends('merch.layout')
@section('title', 'TNA Template')
@push('css')
  <style>
    .logic-box .select2-container{
      width: 100% !important;
    }
  </style>
@endpush
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> TNA Template </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-7">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>TNA Template</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/tna_temp_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="template-name" name="tna_temp_name" placeholder="Enter Template Name" class="form-control" autocomplete="off" />
                              <label for="template-name" > Template Name </label>
                            </div>
                            <div class="form-group has-required has-float-label select-search-group">
                                
                                {{ Form::select('mr_buyer_b_id', $buyer, null, ['placeholder'=>'Select Buyer','id'=>'bid','class'=> 'form-control', 'required']) }}
                                <label for="bid"> Buyer Name  </label>
                            </div>
                            <div id="lib" style="display: none"  class="form-horizontalform-group">
                              <h5 class="page-header">TNA Library </h5>
                              <table class="table table-striped table-bordered table-responsive">
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
                                        <input type="text"  name="tna_lib_code[]" placeholder="Enter Text" class="form-control" value="{{$lib->tna_lib_code}}" disabled="disabled"/>
                                      </td>
                                      <td>
                                        <input type="text"  name="tna_lib_action[]" placeholder="Enter Offset" class="form-control" value="{{$lib->tna_lib_action}}"  disabled="disabled" />
                                      </td>
                                      <td>
                                      <!--   <input type="text"  name="tna_lib_offset[]" placeholder="Enter Text" class="form-control Offset" value="{{$lib->tna_lib_offset}}"  disabled="disabled"/> -->
                                        <input type="text"  name="tna_lib_offset[]" placeholder="Enter Text" class="form-control Offset" value=""   data-validation="required length custom" data-validation-length="1-11" disabled="disabled"/>
                                      </td>
                                      <td class="logic-box" width="25%">
                                        {{ Form::select('logic[]',array('DCD or FOB' => 'DCD or FOB', 'OK to Begin' => 'OK to Begin'), null, ['placeholder'=>'Select','class'=> 'w-100 form-control logic', 'disabled','data-validation' => 'required']) }}
                                      <td>
                                        <input type="checkbox" value="{{$lib->id}}" name="tnalibrary[]" class="tnalibrary">
                                        
                                      </td>
                                    </tr>
                                  @endforeach
                              </tbody>
                              </table>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-outline-success" type="submit">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </div>                                 
                        </form>  
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                        <table id="global-datatable" class="table table-bordered  table-hover">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>Template Name</th>   
                                    <th>Buyer </th>   

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              @php $i=0; @endphp
                              @foreach($templates AS $template)
                                <tr id="row-{{ $template->id }}">
                                  <td>{{ ++$i }}</td>
                                  <td id="sample-name-{{ $template->id }}">{{ $template->tna_temp_name }}</td>
                                  <td id="sample-buyer-{{ $template->id }}">{!! $template->b_name !!}</td>

                                  <td width="20%">
                                      <div class="btn-group">
                                          {{-- <a type="button" class='btn btn-sm btn-primary text-white simple-update1' title="Update" data-simple="{{ $template->tna_temp_name }}" data-id="{{ $template->id }}" data-buyerid="{{ $template->mr_buyer_b_id }}" id="sample-click-{{ $template->id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a> --}}
                                          <a href="{{ url('merch/setup/tna_template_delete/'.$template->id) }}" type="button" class='btn btn-xs btn-danger' title="Delete" onclick="return confirm('Are you sure you want to delete this Action Type?');" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
                                      </div>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="popover bs-popover-left content-section-popover popover-left" id="content-section-popover" role="tooltip" id="popover" x-placement="left" style="display:none;position:absolute;z-index:1; width: 100%;">
                        <div class="arrow" style="top: 37px;"></div>
                        <h3 class="popover-header"><span id="popover-header"></span>  <i class="fa fa-close popover-close"></i></h3>
                        <div class="popover-body">
                            <br>
                            <div class="form-group has-float-label has-required select-search-group">
                              {{ Form::select('buyer_id', $buyer, null, ['placeholder'=>'Select Buyer','id'=>'buyerEdit','class'=> 'form-control', 'required']) }}
                              <label for="buyerEdit">Buyer</label>
                            </div>
                            <input type="hidden" id="sampleid" value="">
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="sample" name="tna_temp_name" placeholder="Enter Sample Type" class="form-control" autocomplete="off" />
                              <label for="sample" > Sample Type </label>
                              <div class="msg" style="color: red"></div>
                            </div>
                        </div>
                        <div class="popover-footer">
                            
                            <button type="button" class="btn btn-outline-success btn-sm sample-change-btn" data-status="2" style="font-size: 13px; margin-left: 7px; margin-bottom: 8px;"><i class="fa fa-save"></i> Save</button>
                            <button type="button" class="btn btn-outline-danger btn-sm sample-close-btn" data-status="2" style="font-size: 13px; margin-left: 7px; margin-bottom: 8px;"><i class="fa fa-close"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')

<script type="text/javascript">

$('#dataTables').DataTable({
    responsive: true,
    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
});

$("#lib").hide();

$("#bid").change(function(){

    if ($(this).val() == "" ) { 
      $("#lib").hide();    
    }
    else 
        $("#lib").show();     
});
$("document").ready(function(){
    $("body").on("click", ".tnalibrary", function(){
        if ($(this).parent().parent().find("select").is(":disabled"))
        {
            $(this).parent().parent().find("select").prop("disabled", false);
            $(this).parent().parent().find(".Offset").prop("disabled", false);
        }
        else
        {
            $(this).parent().parent().find("select").prop("disabled", true);
            $(this).parent().parent().find(".Offset").prop("disabled", true);
        }
    });
});
$(document).on("click", ".simple-update", function(e) {

  var top = e.pageY-165;
  var left = e.pageX-1000;
  var id = $(this).data('id');
  
  var name = $("#sample-click-"+id).attr('data-simple');
  var buyerid = $("#sample-click-"+id).attr('data-buyerid');

  $("#popover-header").html(name);
  $("#sample").val(name);
  $("#sampleid").val(id);

  $('#buyerEdit').val(buyerid).trigger('change');
  
  // Show context menu
  $("#content-section-popover").toggle(100).css({
     top: top + "px",
     left: left + "px"
  });
  
  return false;
});
$(document).on("click", ".popover-close, .sample-close-btn", function(e) {
    $(".content-section-popover").hide();
});

$(document).on("click", ".sample-change-btn", function(e) {
    $('.app-loader').show();
    var id = $("#sampleid").val();
    $.ajax({
      url : "{{ url('merch/setup/tna_template_update-ajax') }}",
      type: 'post',
      headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
      },
      data: {
        id: $("#sampleid").val(),
        tna_temp_name: $("#sample").val(),
        mr_buyer_b_id: $("#buyerEdit").val()
      },
      success: function(data)
      {
        $('.app-loader').hide();
        $.notify(data.msg, data.type);
        if(data.type === 'success'){
          $("#sample-buyer-"+id).html(data.buyer_name);
          $("#sample-name-"+id).html($("#sample").val());
          $("#sample-click-"+id).attr('data-simple', $("#sample").val());
          $("#sample-click-"+id).attr('data-buyerid', $("#buyerEdit").val());
          
          setTimeout(function() {
            $("#row-"+id).addClass('highlight');
            $(".content-section-popover").hide();
          }, 200);
        }
      },
      error: function(reject)
      {
         $.notify(reject, 'error');
      }
  });
});

</script>
@endpush
@endsection


