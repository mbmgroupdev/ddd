@extends('merch.layout')
@section('title', 'Sample Type')
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
                <li class="active"> Sample Type </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Sample Type</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/sampletypestore')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            <div class="form-group has-required has-float-label select-search-group">
                                
                                {{ Form::select('buyer', $buyer, null, ['placeholder'=>'Select Buyer','id'=>'bid','class'=> 'form-control', 'required']) }}
                                <label for="bid"> Buyer Name  </label>
                            </div>
                            <div class="row">
                              <div class="col-10">
                                <div class="form-group has-required has-float-label">
                                  <input type="text" id="sample0" name="sample_name[]" placeholder="Enter Sample Type" class="form-control" autocomplete="off" />
                                  <label for="sample0" > Sample Type </label>
                                  <div class="msg" style="color: red"></div>
                                </div>
                              </div>
                              <div class="col-2">
                                <button type="button" class="btn btn-xs btn-outline-success AddBtn_bu">+</button>
                              </div>
                            </div>
                            <div id="addSample"></div>
                            
                            <div class="form-group">
                                <button class="btn btn-outline-success" type="submit">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </div>                                 
                        </form>  
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                        <table id="global-datatable" class="table table-bordered  table-hover">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>Buyer Name</th>
                                    <th>Sample Type</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              @php $i=0; @endphp
                                @foreach($sample as $type)
                                  <tr id="row-{{ $type->sample_id }}">
                                    <td>{{ ++$i }}</td>
                                    <td id="sample-buyer-{{ $type->sample_id }}">{!! $type->b_name !!}</td>
                                    <td id="sample-name-{{ $type->sample_id }}">{{ $type->sample_name }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            <a type="button" class='btn btn-sm btn-primary text-white simple-update' title="Update" data-simple="{{ $type->sample_name }}" data-id="{{ $type->sample_id }}" data-buyerid="{{ $type->b_id }}" id="sample-click-{{ $type->sample_id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('merch/setup/sampletypedelete/'.$type->sample_id) }}" type="button" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure you want to delete this Sample Type?');" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
                              <input type="text" id="sample" name="sample_name" placeholder="Enter Sample Type" class="form-control" autocomplete="off" />
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

var i=1;
$(".AddBtn_bu").on('click',function(){
  html = '<div class="row"><div class="col-10"><div class="form-group has-required has-float-label">';
  html += '<input type="text" id="sample'+i+'" name="sample_name[]" placeholder="Enter Sample Type" class="form-control sample_name"/>';
  html += '<label for="sample'+i+'"> Sample Type </label>';
  html += '<div class="msg" style="color: red"></div>';
  html += '</div></div><div class="col-2">';
  html += '<button type="button" class="btn btn-xs btn-outline-danger RemoveBtn_bu">-</button></div></div>';
  
  $('#addSample').append(html);
  $('#sample'+i).focus();
  i++;
});

$('body').on('click', '.RemoveBtn_bu', function(){
    $(this).parent().parent().remove();
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
    var sample_id = $("#sampleid").val();
    $.ajax({
      url : "{{ url('merch/setup/sampletypeupdate-ajax') }}",
      type: 'post',
      headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
      },
      data: {
        sample_id: $("#sampleid").val(),
        sample_name: $("#sample").val(),
        b_id: $("#buyerEdit").val()
      },
      success: function(data)
      {
        $('.app-loader').hide();
        $.notify(data.msg, data.type);
        if(data.type === 'success'){
          $("#sample-buyer-"+sample_id).html(data.buyer_name);
          $("#sample-name-"+sample_id).html($("#sample").val());
          $("#sample-click-"+sample_id).attr('data-simple', $("#sample").val());
          $("#sample-click-"+sample_id).attr('data-buyerid', $("#buyerEdit").val());
          
          setTimeout(function() {
            $("#row-"+sample_id).addClass('highlight');
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
