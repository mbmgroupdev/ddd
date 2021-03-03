@extends('merch.layout')
@section('title', 'Season')
@push('css')
<style type="text/css">
    @media only screen and (max-width: 610px) {
        
        #dataTables{display: block; overflow-x: auto;}
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
                <li class="active"> Season </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Season</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/season_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            <div class="form-group has-required has-float-label select-search-group">
                                
                                {{ Form::select('b_id', $buyerList, null, ['placeholder'=>'Select Buyer','id'=>'b_id','class'=> 'form-control', 'required']) }}
                                <label for="b_id"> Buyer  </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="text" id="se_name" name="se_name" placeholder="Enter Season Name" class="form-control" autocomplete="off" />
                                <label for="se_name" > Season Name </label>
                                
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="month" class="form-control" id="se_mm_start" name="se_mm_start" placeholder="Start Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                <label for="se_mm_start" > Start Month-Year </label>
                                
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="month" class="form-control" id="se_mm_end" name="se_mm_end" placeholder="End Month-Year"required="required" value="{{ (request()->month_year?request()->month_year:date('Y-m') )}}"autocomplete="off" />
                                <label for="se_mm_end" > End Month-Year </label>
                                
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
                                    <th>Buyer Name</th>
                                    <th>Season Name</th>
                                    <th>Start Month-Year</th>
                                    <th>End Month-Year</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($seasons as $season)
                                  <tr id="row-{{ $season->se_id }}">
                                    <td>{{ ++$i }}</td>
                                    <td id="season-buyer-{{ $season->se_id }}">{!! $season->b_name !!}</td>
                                    <td id="season-name-{{ $season->se_id }}">{!! $season->se_name !!}</td>
                                    <td id="season-start-{{ $season->se_id }}">{{ $season->se_start }}</td>
                                    <td id="season-end-{{ $season->se_id }}">{{ $season->se_end }}</td>
                                    <td id="season-status-{{ $season->se_id }}">{{ ($season->season_status ==1)? "Active":"Inactive" }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            {{-- <a type="button" class='btn btn-sm btn-primary text-white season-update' title="Update" data-seasonname="{{ $season->se_name }}" data-id="{{ $season->se_id }}" data-buyerid="{{ $season->b_id }}" data-startmonth="{{ date('Y-m', strtotime($season->se_start)) }}" data-endmonth="{{ date('Y-m', strtotime($season->se_end)) }}" data-status="{{ $season->season_status }}" id="sample-click-{{ $season->se_id }}"><i class="ace-icon fa fa-pencil bigger-120"></i></a> --}}

                                            <a href="{{ url('merch/setup/season_delete/'.$season->se_id) }}" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
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
                              {{ Form::select('buyerid', $buyerList, null, ['placeholder'=>'Select Buyer','id'=>'buyer-id','class'=> 'form-control', 'required']) }}
                              <label for="buyer-id">Buyer</label>
                            </div>
                            <input type="hidden" id="se_id" value="">
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="season" placeholder="Enter Season Name" class="form-control" autocomplete="off" />
                              <label for="season" > Season Name </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="start-month-year" placeholder="Enter Start Month-Year" class="form-control" autocomplete="off" />
                              <label for="start-month-year" > Start Month-Year </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="end-month-year" placeholder="Enter End Month-Year" class="form-control" autocomplete="off" />
                              <label for="end-month-year" > End Month-Year </label>
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
    //auto complete placement script
    $(document).on('focus','.autocomplete_pla',function(){
        var type = $(this).data('type');
        if(type == 'season')autoTypeNo=0;

        $(this).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url : "{{ url('merch/setup/season_input') }}",
                    method: 'GET',
                    data: {
                      name_startsWith: request.term,
                      type: type,
                      b_id: $("#b_id").val()
                    },
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            var code = item.split("|");
                            return {
                                label: code[autoTypeNo],
                                value: code[autoTypeNo],
                                data : item
                            }
                        }));
                    }
                });
            },
            autoFocus: true,
            minLength: 0,
            select: function( event, ui ) {
                var names = ui.item.data.split("|");
                $(this).val(names[0]);
            }
        });

    });

    $(document).ready(function(){
       $('#dataTables').DataTable();
    });

    function selectCountry(val) {
    $("#se_name").val(val);
    $("#suggesstion-box").hide();
    }

    $(document).on("click", ".season-update", function(e) {
        var currentTop = parseInt($(this).css('top'));
        var currentLeft = parseInt($(this).css('left'));
        var top = e.pageY-165;
        var left = e.pageX-1000;
        var id = $(this).data('id');
        console.log(e);
        console.log(currentLeft);
      
        var buyerId = $("#sample-click-"+id).attr('data-buyerid');
        var seasonName = $("#sample-click-"+id).attr('data-seasonname');
        var startMonth = $("#sample-click-"+id).attr('data-startmonth');
        var endMonth = $("#sample-click-"+id).attr('data-endmonth');
        var seasonStatus = $("#sample-click-"+id).attr('data-status');
      
        $("#popover-header").html(seasonName);
        $("#season").val(seasonName);
        $("#start-month-year").val(startMonth);
        $("#end-month-year").val(endMonth);
        $("#se_id").val(id);
        $('#buyerid').val(buyerId).trigger('change');
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
        var gmt_id = $("#se_id").val();
        $.ajax({
          url : "{{ url('merch/setup/garments_type_update-ajax') }}",
          type: 'post',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          data: {
            gmt_id: $("#se_id").val(),
            gmt_name: $("#garment").val(),
            gmt_remarks: $("#garmentremark").val(),
            prd_id: $("#productTypeId").val()
          },
          success: function(data)
          {
            console.log(data)
            $('.app-loader').hide();
            $.notify(data.msg, data.type);
            if(data.type === 'success'){
              $("#season-start-"+gmt_id).html(data.prd_type_name);
              $("#season-name-"+gmt_id).html($("#garment").val());
              $("#season-status-"+gmt_id).html($("#garmentremark").val());
              $("#sample-click-"+gmt_id).attr('data-seasonname', $("#garment").val());
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
