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
            font-size: 22px;
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
                <div class="col h-min-400">
                    <div id="result-process-bar" style="display: none;">
                        <div class="iq-card">
                            <div class="iq-card-body">
                                <div class="" id="result-data">
                                    <div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')

<script>
    @if(request()->month != null && request()->unit != null)
        generate();
    @endif 
    // generate salary sheet
    function generate() {
        
        var form = $("#unitWiseSalary");
        var unit = $("#unit").val();
        var month = $("#month").val();
        if(unit !== '' && month !== ''){
            $("#result-process-bar").show();
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

</script>
@endpush
@endsection