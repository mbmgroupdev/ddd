@extends('hr.layout')
@section('title', 'Monthly Salary')

@section('main-content')
@push('js')
    <style>
        #top-tab-list li a {
            padding: 5px 15px;
            cursor: default;
        }
        div.text-center b{
            font-size: 20px;
        }
        .mh-410{
            max-height: 410px;
            overflow: auto;
        }
        .min-h-415{
            min-height: 415px;
        }
        .font-italic{
            font-style: italic;
        }
        #top-tab-list {
          margin: 0 -10px 20px !important;
        }
        #top-tab-list li a {
            border-radius: 10px !important;
            -webkit-border-radius: 10px !important;
        }
        #content-result .panel .panel-body .loader-p{
            margin-top: 20% !important;
        } 
        .modal-h3{
            line-height: 1;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active"> Monthly Salary Generate</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
                <div class="col-12">
                    <form class="" role="form" id="unitWiseSalary"> 
                        <div class="panel">
                            
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required select-search-group">
                                            <select name="unit" class="form-control capitalize select-search" id="unit" required="">
                                                <option selected="" value="">Choose...</option>
                                                @foreach($units as $key => $unit)
                                                <option value="{{ $unit->hr_unit_id }}" @if(isset(request()->unit) && request()->unit == $unit->hr_unit_id) selected @endif>{{ $unit->hr_unit_name }}</option>
                                                @endforeach
                                            </select>
                                          <label for="unit">Unit</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group has-float-label has-required">
                                          <input type="month" class="report_date form-control" id="month" name="month_year" placeholder=" Month-Year"required="required" value="{{ (request()->month?request()->month:date('Y-m', strtotime('-1 month'))) }}"autocomplete="off" />
                                          <label for="month">Month</label>
                                        </div>
                                    </div> 
                                    <div class="col-3">
                                        <div class="form-group">
                                          <button onclick="generate()" class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="unitFromBtn"><i class="fa fa-save"></i> Generate</button>
                                        </div>
                                    </div>  
                                </div>
                                
                            </div>
                        </div>
                        
                    </form>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col ">
                    <div id="result-process-bar" style="display: none;">
                        <div class="" id="result-data"></div>
                    </div>
                    
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<div class="modal right fade" id="right_modal_lg" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Previous ">
            <i class="las la-chevron-left"></i>
        </a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result" id="content-result">
            
        </div>
      </div>
      
    </div>
  </div>
</div>
@push('js')

<script>
    var loaderModal = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:10px;" class="loader-p"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    @if(request()->month != null && request()->unit != null)
        generate();
    @endif 
    // generate salary sheet
    function generate() {
        $("#result-process-bar").show();
        $('#result-data').html('<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>');
        var form = $("#unitWiseSalary");
        var unit = $("#unit").val();
        var month = $("#month").val();
        if(unit !== '' && month !== ''){
            
            $.ajax({
                type: "get",
                url: '{{ url("hr/operation/salary-generate")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                data: form.serialize(), // serializes the form's elements.
                success: function(response)
                {
                    // console.log(response)
                    if(response !== 'error'){
                        $("#result-data").html(response);
                    }
                },
                error: function (reject) {
                    console.log(reject);
                }
            });
        }else{
            $("#result-process-bar").hide();
            if(unit !== null){
                $.notify("Please Select Unit", 'error');
            }
            if(month !== null){
                $.notify("Please Select Month", 'error');
            }
        }
    }

    function selectedGroup(id){
        
        $("#modal-title-right").html('Audit Details');
        $('#right_modal_lg').modal('show');
        $("#content-result").html(loaderModal);
        $.ajax({
            url: '/hr/reports/salary-fail-audit-history/'+id,
            type: "GET",
            success: function(response){
                // console.log(response);
                if(response !== 'error'){
                    setTimeout(function(){
                        $("#content-result").html(response);
                    }, 1000);
                }else{
                    console.log(response);
                }
            }
        });

    }

</script>
@endpush
@endsection