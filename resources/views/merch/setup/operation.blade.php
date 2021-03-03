@extends('merch.layout')
@section('title', 'Operation')
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
                <li class="active"> Operation </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Operation</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/operationstore')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            
                            <div class="row">
                              <div class="col">
                                <div class="form-group has-required has-float-label">
                                  <input type="text" id="product" name="opr_name" placeholder="Enter Operation Name" class="form-control" autocomplete="off" />
                                  <label for="product" > Operation </label>
                                  <div class="msg" style="color: red"></div>
                                </div>
                              </div>
                              
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
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                        <table id="global-datatable" class="table table-bordered  table-hover">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Operation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($operations as $operation)
                                  <tr id="row-{{ $operation->opr_id }}">
                                    <td>{{ ++$i }}</td>
                                    <td id="operation-name-{{ $operation->opr_id }}">{{ $operation->opr_name }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            <a type="button" class='btn btn-sm btn-primary text-white type-update' title="Update" data-operation="{{ $operation->opr_name }}" data-id="{{ $operation->opr_id }}" id="operation-click-{{ $operation->opr_id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('merch/setup/operationdelete/'.$operation->opr_id) }}" type="button" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure you want to delete this Operation?');" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
                            <input type="hidden" id="operationid" value="">
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="operation" name="opr_name" placeholder="Enter Operation" class="form-control" autocomplete="off"/>
                              <label for="operation" > Product Type </label>
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
      $('#dataTables').DataTable({
          responsive: true,
          dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
      });
    });

    $(document).on("click", ".type-update", function(e) {

      var top = e.pageY-165;
      var left = e.pageX-1000;
      var id = $(this).data('id');
      var name = $("#operation-click-"+id).attr('data-operation');

      $("#popover-header").html(name);
      $("#operation").val(name);
      $("#operationid").val(id);

      
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
        var opr_id = $("#operationid").val();
        $.ajax({
          url : "{{ url('merch/setup/operationupdate-ajax') }}",
          type: 'post',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          data: {
            opr_id: $("#operationid").val(),
            opr_name: $("#operation").val(),
          },
          success: function(data)
          {
            $('.app-loader').hide();
            $.notify(data.msg, data.type);
            if(data.type === 'success'){
              $("#operation-name-"+opr_id).html($("#operation").val());
              $("#operation-click-"+opr_id).attr('data-operation', $("#operation").val());
              
              setTimeout(function() {
                $("#row-"+opr_id).addClass('highlight');
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
    $(document).mouseup(function(e) 
    {
        var content = $(".content-section-popover");
        if (!content.is(e.target) && content.has(e.target).length === 0) 
        {
            content.hide();
        }
        
    });
    </script>
@endpush
@endsection