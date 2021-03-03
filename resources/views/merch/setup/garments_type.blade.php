@extends('merch.layout')
@section('title', 'Garments Type')
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
                <li class="active"> Garments Type </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Garments Type</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/garments_type_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            <div class="form-group has-required has-float-label select-search-group">
                                
                                {{ Form::select('prd_id', $productList, null, ['placeholder'=>'Select Product Type','id'=>'prd_type_id','class'=> 'form-control', 'required']) }}
                                <label for="prd_type_id"> Product Type  </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="text" id="gmt_name" name="gmt_name" placeholder="Enter Garments Type" class="form-control" autocomplete="off" />
                                <label for="gmt_name" > Garments Type </label>
                                
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="text" id="gmt_remarks" name="gmt_remarks" placeholder="Enter Garments Remarks" class="form-control" autocomplete="off" />
                                <label for="gmt_remarks" > Remarks </label>
                                
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
                                    <th>Garment Type</th>
                                    <th>Product Type</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($garments as $garment)
                                  <tr id="row-{{ $garment->gmt_id }}">
                                    <td>{{ ++$i }}</td>
                                    <td id="gmt-name-{{ $garment->gmt_id }}">{!! $garment->gmt_name !!}</td>
                                    <td id="prd-type-name-{{ $garment->gmt_id }}">{{ $garment->prd_type_name }}</td>
                                    <td id="gmt-remarks-{{ $garment->gmt_id }}">{{ $garment->gmt_remarks }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            <a type="button" class='btn btn-sm btn-primary text-white garments-update' title="Update" data-gmtname="{{ $garment->gmt_name }}" data-id="{{ $garment->gmt_id }}" data-prdtype="{{ $garment->prd_id }}" data-gmtremark="{{ $garment->gmt_remarks }}" id="sample-click-{{ $garment->gmt_id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a>

                                            <a href="{{ url('merch/setup/garments_type_delete/'.$garment->gmt_id) }}" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                  </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="popover bs-popover-left content-section-popover popover-left" id="content-section-popover" role="tooltip" id="popover" x-placement="left" style="display:none;position:absolute;z-index:1; width: 100%; box-shadow: 0px 1px 7px 1px;">
                        <div class="arrow" style="top: 37px;"></div>
                        <h3 class="popover-header"><span id="popover-header"></span>  <i class="fa fa-close popover-close"></i></h3>
                        <div class="popover-body">
                            <br>
                            <div class="form-group has-float-label has-required select-search-group">
                              {{ Form::select('prtypeid', $productList, null, ['placeholder'=>'Select Product Type','id'=>'productTypeId','class'=> 'form-control', 'required']) }}
                              <label for="productTypeId">Product Type</label>
                            </div>
                            <input type="hidden" id="gartypeid" value="">
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="garment" placeholder="Enter Garments Type" class="form-control" autocomplete="off" />
                              <label for="garment" > Garments Type </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="garmentremark" placeholder="Enter Garments Remarks" class="form-control" autocomplete="off" />
                              <label for="garmentremark" > Garments Remarks </label>
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

    ///Data TAble Color
    $('#dataTables').DataTable({
      responsive: true,
      dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>tp",
    });

    $(document).on("click", ".garments-update", function(e) {
      var top = e.pageY-165;
      var left = e.pageX-1000;
      var id = $(this).data('id');
      
      var gmtName = $("#sample-click-"+id).attr('data-gmtname');
      var gmtRemarks = $("#sample-click-"+id).attr('data-gmtremark');
      var prdtypeid = $("#sample-click-"+id).attr('data-prdtype');

      $("#popover-header").html(gmtName);
      $("#garment").val(gmtName);
      $("#garmentremark").val(gmtRemarks);
      $("#gartypeid").val(id);
      $('#productTypeId').val(prdtypeid).trigger('change');
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
        var gmt_id = $("#gartypeid").val();
        $.ajax({
          url : "{{ url('merch/setup/garments_type_update-ajax') }}",
          type: 'post',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          data: {
            gmt_id: $("#gartypeid").val(),
            gmt_name: $("#garment").val(),
            gmt_remarks: $("#garmentremark").val(),
            prd_id: $("#productTypeId").val()
          },
          success: function(data)
          {
            // console.log(data)
            $('.app-loader').hide();
            $.notify(data.msg, data.type);
            if(data.type === 'success'){
              $("#prd-type-name-"+gmt_id).html(data.prd_type_name);
              $("#gmt-name-"+gmt_id).html($("#garment").val());
              $("#gmt-remarks-"+gmt_id).html($("#garmentremark").val());
              $("#sample-click-"+gmt_id).attr('data-gmtname', $("#garment").val());
              $("#sample-click-"+gmt_id).attr('data-gmtremark', $("#garmentremark").val());
              $("#sample-click-"+gmt_id).attr('data-prdtype', $("#productTypeId").val());
              
              setTimeout(function() {
                $("#row-"+gmt_id).addClass('highlight');
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
