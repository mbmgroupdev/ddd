@extends('merch.layout')
@section('title', 'TNA Library')
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
                <li class="active"> TNA Library </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>TNA Library</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/tna_library_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="lib-action" name="tna_lib_action" placeholder="Enter TNA Name" class="form-control" autocomplete="off" />
                              <label for="lib-action" > TNA Name </label>
                            </div>

                            <div class="form-group has-required has-float-label">
                              <input type="text" id="tna-code" name="tna_lib_code" placeholder="Enter TNA Code" class="form-control" autocomplete="off" />
                              <label for="tna-code" > TNA Code </label>
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
                                  <th>Sl.</th>
                                  <th>Library Action</th>
                                  <th>TNA Code</th>
                                  <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($library as $lib)
                                  <tr id="row-{{ $lib->id }}">
                                    <td>{{ ++$i }}</td>
                                    <td id="tna-name-{{ $lib->id }}">{{ $lib->tna_lib_action }}</td>
                                    <td id="tna-code-{{ $lib->id }}">{{ $lib->tna_lib_code }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            <a type="button" class='btn btn-sm btn-primary text-white type-update' title="Update" data-tna-name="{{ $lib->tna_lib_action }}" data-tna-code="{{ $lib->tna_lib_code }}" data-id="{{ $lib->id }}" id="tna-click-{{ $lib->id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{url('merch/setup/tna_library_delete/'.$lib->id)}}" type="button" class='btn btn-xs btn-danger' onclick="return confirm('Are you sure you want to delete this Library?');" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
                            <input type="hidden" id="id" value="">
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="tna_name" placeholder="Enter TNA Name" class="form-control" autocomplete="off"/>
                              <label for="tna_name" > TNA Name </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="tna_code" placeholder="Enter TNA Code" class="form-control" autocomplete="off"/>
                              <label for="tna_code" > TNA Code </label>
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
      var name = $("#tna-click-"+id).attr('data-tna-name');
      var code = $("#tna-click-"+id).attr('data-tna-code');

      $("#popover-header").html(name);
      $("#tna_name").val(name).focus();
      $("#tna_code").val(code);

      $("#id").val(id);
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
        var id = $("#id").val();
        $.ajax({
          url : "{{ url('merch/setup/tna_library_update-ajax') }}",
          type: 'post',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          data: {
            id: $("#id").val(),
            tna_lib_action: $("#tna_name").val(),
            tna_lib_code: $("#tna_code").val()
          },
          success: function(data)
          {
            $('.app-loader').hide();
            $.notify(data.msg, data.type);
            if(data.type === 'success'){
              $("#tna-name-"+id).html($("#tna_name").val());
              $("#tna-code-"+id).html($("#tna_code").val());
              $("#tna-click-"+id).attr('data-tna-name', $("#tna_name").val());
              $("#tna-click-"+id).attr('data-tna-code', $("#tna_code").val());
              
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