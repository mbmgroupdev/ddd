@extends('hr.layout')
@section('title', 'Bonus Set')
@section('main-content')
@push('css')
    <link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        .close-button {
            content: "X";
            background-color: rgb(229 25 4);
            transform: scale(1);
            display: block;
            border-radius: 50%;
            border: 1px solid rgb(8 155 171);
            position: absolute;
            top: -12px;
            right: 0px;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 22px;
            transition-duration: 0.4s;
            color: #fff;
            cursor: pointer;
        }
        .rule-section{
            position: relative;
        }
        .iq-header{
            border-bottom: 1px solid #ccc;
        }
        .iq-card-spacial{
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px 10px;
            margin: 10px 0px;
            position: relative;
        }
        .iq-sp-body{
            padding: 10px 5px;
        }
        .iq-sp-head{
            top: -10px;
            left: 15px;
            outline: none;
            background: #fff;
            position: absolute;
        }
        .iq-sp-head p{
            margin-bottom: 0;
            font-size: 14px;
            padding: 0px 5px;
        }
        .rule-overlay{
            width: 100%;
            height: 100%;
            position: absolute;
            background: #ffffff;
            top: 0px;
            opacity: .7;
            z-index: 4;
            border-radius: 5px;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Operation </a>
                </li>
                <li class="active">Bonus</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <form id="bonus-procesor" method="post">
                @csrf
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Bonus</h6></div> 
                    <div class="panel-body">
                        <div class="row justify-content-center">
                            <div class="col-sm-8">
                                <input type="hidden" id="report_format" name="report_format">
                                <input type="hidden" id="emp_type" name="emp_type" value="all">
                                <input type="hidden" id="pay_type" name="pay_type" value="all">
                                <input type="hidden" id="report_group" name="report_group">
                                <div class="form-section">
                                    
                                    <input type="hidden" name="eligible_month" id="eligible-month" value="0">
                                    <div class="row">
                                        <div class="col-sm-4 pr-0">
                                            <div class="custom-control custom-radio custom-control-inline"></div>
                                            <div class="form-group has-required has-float-label select-search-group">
                                                {{ Form::select('type_id', $bonusType, null, ['placeholder'=>'Select Bonus', 'id'=>'bonus_for', 'class'=> 'form-control', 'required'=>'required']) }}
                                                <label for="bonus_for">Bonus Type </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 pr-0">
                                            <div class="row">
                                                <div class="col-sm-12 pr-0">
                                                    {{-- <label>Amount Type</label><br> --}}
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                       <input type="radio" id="per_of_basic" name="bonus_amont_type" class="bonus_amont_type custom-control-input" value="percent" checked>
                                                       <label class="custom-control-label" for="per_of_basic"> % of Basic </label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                       <input type="radio" id="fixed_amount" name="bonus_amont_type" class="bonus_amont_type custom-control-input" value="fixed">
                                                       <label class="custom-control-label" for="fixed_amount"> Fixed Amount </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group  has-float-label" id="target-per-of-basic" style="margin-top: 3px;">
                                                        <input type="text" name="bonus_percent" id="bonus_percent" placeholder="% of Basic"  class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" >
                                                        <label for="bonus_percent"> % of Basic </label>
                                                    </div>
                                                    <div class="form-group has-float-label" id="target-fix-amount" style="display: none;margin-top: 3px;">
                                                        <input type="text" name="bonus_amount" id="bonus_amount" placeholder="Enter" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" >
                                                        <label for="bonus_amount">Fixed Amount </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 pr-0">
                                            <div class="custom-control custom-radio custom-control-inline"></div>
                                            <div class="form-group has-required has-float-label">
                                                <input type="date" name="cut_date" id="cut_date" placeholder="Cut of Date" value="{{ date('Y-m-d') }}"  class="form-control" required>
                                                <label for="cut_date">Bonus Date </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-sm-12"><hr class="mt-0"></div>
                                      <div class="col-sm-5">
                                        <div class="custom-control custom-switch">
                                          <input name="special" type="checkbox" class="custom-control-input" id="specialCheck">
                                          <label class="custom-control-label" for="specialCheck">Advanced</label>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="rule-section">
                                        <div class="iq-card-spacial pb-0">
                                            <div class="iq-sp-head">
                                                <p class="card-title">Special </p>
                                            </div>
                                            <div class="iq-sp-body pb-0">
                                               <div class="row">
                                                    <div class="offset-sm-3 col-sm-9">
                                                        <div class="specialsection" id="special-section">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group has-required has-float-label select-search-group">
                                                                        <select name="" id="special-type-for" class="form-control">
                                                                            <option value=""> - Select - </option>
                                                                            <option value="as_department_id"> Department</option>
                                                                            <option value="as_designation_id"> Designation</option>
                                                                            <option value="as_section_id"> Section</option>
                                                                            <option value="as_subsection_id"> Sub Section</option>
                                                                            <option value="as_id"> Employee</option>
                                                                        </select>
                                                                        <label for="special-type-for">Type </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2" >
                                                                    <div class="form-group">
                                                                        <button class="btn btn-outline-primary sync-type" data-category="special" type="button" id="special-sync-type">
                                                                            <i class="las la-sync"></i>
                                                                        </button> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="special-targettype"></div>
                                                <div id="special-appendType" class="appendType"></div>
                                            </div>
                                        </div><div class="iq-card-spacial pb-0">
                                            <div class="iq-sp-head">
                                                <p class="card-title">Partial </p>
                                            </div>
                                            <div class="iq-sp-body pb-0">
                                               <div class="row">
                                                    <div class="offset-sm-3 col-sm-9">
                                                        <div class="partialsection" id="partial-section">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group has-required has-float-label select-search-group">
                                                                        <select name="" id="partial-type-for" class="form-control">
                                                                            <option value=""> - Select - </option>
                                                                            <option value="as_department_id"> Department</option>
                                                                            <option value="as_designation_id"> Designation</option>
                                                                            <option value="as_section_id"> Section</option>
                                                                            <option value="as_subsection_id"> Sub Section</option>
                                                                            <option value="as_id"> Employee</option>
                                                                        </select>
                                                                        <label for="partial-type-for">Type </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2" >
                                                                    <div class="form-group">
                                                                        <button class="btn btn-outline-primary sync-type" data-category="partial" type="button" id="partial-sync-type">
                                                                            <i class="las la-sync"></i>
                                                                        </button> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="partial-targettype"></div>
                                                <div id="partial-appendType" class="appendType"></div>
                                            </div>
                                        </div>
                                        <div class="iq-card-spacial pb-0">
                                            <div class="iq-sp-head">
                                                <p class="card-title">Excluding </p>
                                            </div>
                                            <div class="iq-sp-body pb-0">
                                               <div class="row">
                                                    <div class="offset-sm-3 col-sm-9">
                                                        <div class="excludingsection" id="excluding-section">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group has-required has-float-label select-search-group">
                                                                        <select name="" id="excluding-type-for" class="form-control">
                                                                            <option value=""> - Select - </option>
                                                                            <option value="as_department_id"> Department</option>
                                                                            <option value="as_designation_id"> Designation</option>
                                                                            <option value="as_section_id"> Section</option>
                                                                            <option value="as_subsection_id"> Sub Section</option>
                                                                            <option value="as_id"> Employee</option>
                                                                        </select>
                                                                        <label for="excluding-type-for">Type </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2" >
                                                                    <div class="form-group">
                                                                        <button class="btn btn-outline-primary sync-type" data-category="excluding" type="button" id="excluding-sync-type">
                                                                            <i class="las la-sync"></i>
                                                                        </button> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="excluding-targettype"></div>
                                                <div id="excluding-appendType" class="appendType"></div>
                                            </div>
                                        </div>
                                        <div class="rule-overlay" id="rule-overlay"></div>
                                    </div>
                                </div>
                                <div class="process-btn">
                                    <div class="form-group pull-right">
                                        <button class="btn btn-primary" type="submit">
                                            <i class=" fa fa-check"></i> Generate
                                        </button>
                                            
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </form>
            
        </div> 
        <div id="bonus-eligible-list"></div>
    </div> 
</div> 
@include('common.right-modal')
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/js/bonus.js')}}"></script>
<script type="text/javascript">
    $(document).on('keyup','#bonus_percent', function(){
        if($(this).val() == '' || parseInt($(this).val()) < 1000){

        }else{
            $(this).val('');
            $(this).notify('Maximum value is 999');
        }
    });
    var bonus_type = @json(bonus_type_by_id());
    $(document).on('change', '#bonus_for', function(event) {
        var bonus_for = $(this).val();
        var eligibleMonth = 0;
        if(bonus_for !== '' && bonus_for !== null){
            if(bonus_type[bonus_for]){
                eligibleMonth = bonus_type[bonus_for].eligible_month;
            }
        }
        $('#eligible-month').val(eligibleMonth);
    });

    $(document).on('submit','#bonus-procesor',function(e){
        e.preventDefault();
        var r = confirm("Bonus is "+$('#bonus_percent').val()+" % of basic. Do you want to continue?");
        if (r == true) {
        
            generateBonus();
        }
    });

    $(document).on('change','#empType', function(){
        $('#emp_type').val($(this).val());
        generateBonus();
        
    });

    $(document).on('change','#paymentType', function(){
        $('#pay_type').val($(this).val());
        generateBonus();
        
    });

    $(document).on('change','#reportGroupHead', function(){
        $('#report_group').val($(this).val());
        generateBonus();
    });

    $(document).on('click','.grid_view', function(){
        generateBonus('report_format',1);
    });

    $(document).on('click','.list_view', function(){
        generateBonus('report_format',0);
    });

    function generateBonus(type = null, val = null)
    {
        
            $('.app-loader').show();

            
            // append to the report_group
            if(type == 'report_format'){$('#report_format').val(val)};

            var data = $('#bonus-procesor').serializeArray();

            $.ajax({
               url : "{{ url('hr/operation/bonus-process') }}",
               type: 'get',
               data: data,
               success: function(data)
               {
                    $('#bonus-procesor').hide();
                    $('#bonus-eligible-list').html(data);
                    $('.app-loader').hide();
               },
               error: function(reject)
               {
                    $('.app-loader').hide();
               }
            });

    }

    $(document).on('click','#back-button' ,function(){
        $('#bonus-procesor').show();
        $('#bonus-eligible-list').html('');
    });

    $(document).on('click','#approval' ,function(){
        $('.app-loader').show();
        var data = $('#bonus-procesor').serializeArray();

        $.ajax({
           url : "{{ url('hr/operation/bonus-to-aproval') }}",
           type: 'post',
           data: data,
           success: function(data)
           {
                if(data.success == 1){
                    $('#bonus-procesor').hide();
                    $('.app-loader').hide();  
                    $.notify(data.msg,'success');
                    window.location.href = "{{url('hr/operation/bonus-sheet-process')}}";
                }else{
                    $('#bonus-procesor').hide();
                    $('.app-loader').hide();  
                    $.notify(data.msg,'error');
                }
           },
           error: function(reject)
           {
                $('.app-loader').hide();
           }
        });
    });

    $(document).on('change','.bonus_amont_type', function(){
        if($(this).val() == 'percent'){
            $('#target-per-of-basic').show();
            $('#target-fix-amount').hide();
            $('#bonus_amount').val(0);
        }else{
            $('#target-per-of-basic').hide();
            $('#target-fix-amount').show();
            $('#bonus_percent').val(0);
        }

    });



    
</script>
@endpush
@endsection