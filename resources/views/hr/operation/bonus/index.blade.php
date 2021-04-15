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
        /*.form-section{
            height: calc(100vh - 275px);
        }*/
        /*#appendType{
            position: absolute;
            overflow: auto;
            background: #fff;
            height: 300px;
            padding: 15px;
            margin-bottom: 30px; 
        }
        .process-btn{
            position: absolute;
            background: #fff;
            width: 100%;
            bottom: 0;
            padding-top: 15px;
        }*/
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
                    <div class="panel-heading text-center"><h6>Bonus</h6></div> 
                    <div class="panel-body">
                        <div class="row justify-content-center">
                            <div class="col-sm-7">
                                <input type="hidden" id="report_format" name="report_format">
                                <input type="hidden" id="emp_type" name="emp_type" value="all">
                                <input type="hidden" id="report_group" name="report_group">
                                <div class="form-section">
                                    
                                    <input type="hidden" name="eligible_month" id="eligible-month" value="0">
                                    <div class="row">
                                        <div class="col-sm-6 pr-0">
                                            <div class="form-group has-required has-float-label select-search-group">
                                                {{ Form::select('type_id', $bonusType, null, ['placeholder'=>'Select Bonus', 'id'=>'bonus_for', 'class'=> 'form-control', 'required'=>'required']) }}
                                                <label for="bonus_for">Bonus for </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="form-group has-required has-float-label">
                                                <input type="date" name="cut_date" id="cut_date" placeholder="Cut of Date" value="{{ date('Y-m-d') }}"  class="form-control" required>
                                                <label for="cut_date">Cut of Date </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 pr-0">
                                            <div class="form-group  has-float-label">
                                                <input type="text" name="bonus_percent" id="bonus_percent" placeholder="% of Basic"  class="form-control" >
                                                <label for="bonus_percent">OR, % of Basic </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-float-label">
                                                <input type="text" name="bonus_amount" id="bonus_amount" placeholder="Enter" class="form-control" >
                                                <label for="bonus_amount">Amount </label>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="row">
                                      <div class="col-sm-3">
                                        <div class="custom-control custom-switch">
                                          <input name="special" type="checkbox" class="custom-control-input" id="specialCheck">
                                          <label class="custom-control-label" for="specialCheck">Advanced</label>
                                        </div>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="specialsection" id="special-section">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group has-required has-float-label select-search-group">
                                                        <select name="" id="type-for" class="form-control">
                                                            <option value=""> - Select - </option>
                                                            <option value="as_department_id"> Department</option>
                                                            <option value="as_designation_id"> Designation</option>
                                                            <option value="as_section_id"> Section</option>
                                                            <option value="as_subsection_id"> Sub Section</option>
                                                            <option value="as_id"> Employee</option>
                                                        </select>
                                                        <label for="type-for">Type </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2" id="syncBtn" style="display: none">
                                                    <div class="form-group">
                                                        <button class="btn btn-outline-primary" type="button" id="sync-type">
                                                            <i class="las la-sync"></i>
                                                        </button> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="appendType"></div>
                                        
                                        </div>
                                      </div>
                                    </div>
                                    <div id="targettype"></div>
                                </div>
                                <div class="process-btn">
                                    <div class="form-group">
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
        generateBonus();
    });

    $(document).on('change','#empType', function(){
        $('#emp_type').val($(this).val());
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
           type: 'post',
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
                $('#bonus-procesor').hide();
                $('#bonus-eligible-list').html(data);
                $('.app-loader').hide();
           },
           error: function(reject)
           {
                $('.app-loader').hide();
           }
        });
    });

    
</script>
@endpush
@endsection