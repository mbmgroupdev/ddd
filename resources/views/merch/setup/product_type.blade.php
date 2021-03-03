@extends('merch.layout')
@section('title', 'Product Type')
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
                <li class="active"> Product Type </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Product Type</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/product_type_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            
                            <div class="row">
                              <div class="col">
                                <div class="form-group has-required has-float-label">
                                  <input type="text" id="product" name="prd_type_name" placeholder="Enter Product Type" class="form-control" autocomplete="off" />
                                  <label for="product" > Product Type </label>
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
                                    <th>Product Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($products as $product)
                                  <tr id="row-{{ $product->prd_type_id }}">
                                    <td>{{ ++$i }}</td>
                                    <td id="producttype-name-{{ $product->prd_type_id }}">{{ $product->prd_type_name }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            <a type="button" class='btn btn-sm btn-primary text-white type-update' title="Update" data-producttype="{{ $product->prd_type_name }}" data-id="{{ $product->prd_type_id }}" id="producttype-click-{{ $product->prd_type_id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                            <a href="{{ url('merch/setup/product_type_delete/'.$product->prd_type_id) }}" type="button" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure you want to delete this Product Type?');" title="Delete"><i class="ace-icon fa fa-trash bigger-120"></i></a>
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
                            <input type="hidden" id="producttypeid" value="">
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="producttype" name="prd_type_name" placeholder="Enter Product Type" class="form-control" autocomplete="off"/>
                              <label for="producttype" > Product Type </label>
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
      var name = $("#producttype-click-"+id).attr('data-producttype');

      $("#popover-header").html(name);
      $("#producttype").val(name);
      $("#producttypeid").val(id);

      
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
        var prd_type_id = $("#producttypeid").val();
        $.ajax({
          url : "{{ url('merch/setup/product_type_update-ajax') }}",
          type: 'post',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          data: {
            prd_type_id: $("#producttypeid").val(),
            prd_type_name: $("#producttype").val(),
          },
          success: function(data)
          {
            $('.app-loader').hide();
            $.notify(data.msg, data.type);
            if(data.type === 'success'){
              $("#producttype-name-"+prd_type_id).html($("#producttype").val());
              $("#producttype-click-"+prd_type_id).attr('data-producttype', $("#producttype").val());
              
              setTimeout(function() {
                $("#row-"+prd_type_id).addClass('highlight');
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
