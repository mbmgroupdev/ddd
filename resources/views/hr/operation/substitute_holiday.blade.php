@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#">Operation</a>
                </li>
                <li class="active">Substitude Holidays</li>
            </ul><!-- /.breadcrumb --> 
        </div>
        <div class="page-content">
            <?php $type = 'substitute_holiday'; ?>
            @include('hr/reports/operations_radio')
            <div class="row">
            <div class="page-header">
                <h1>Operations<small> <i class="ace-icon fa fa-angle-double-right"></i>Substitute Holidays</small></h1>
            </div>
            <div class="panel panel-success col-sm-10 col-sm-offset-1 no-padding"> 
                <div class="panel-heading">
                    <h6>Substitute Holidays Entry</h6>
                </div>
                <div class="panel-body">
                    @include('inc/message')
                    {{Form::open(['url'=>'hr/operation/substitute_holiday_save', 'class'=>'form-horizontal' ])}}
                    <div class="row">
                        <div class="col-sm-12 ">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="col-sm-12" style="margin-top: 15px;">
                                        <div class="form-group">
                                            <label class="col-sm-4" for="employee_id">Emlpoyee</label>
                                            <div class="col-sm-8">
                                            {{Form::select('employee_id', $employees, null, ['placeholder'=>'Select Employee','id'=>'employee_id','class'=> 'form-control', 'data-validation'=>'required'])}}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4" for="">No. of Holidays</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="col-xs-12" name="holiday_count" id="holiday_count" placeholder="Enter Number" data-validation ="required" style="height: auto;">

                                                <input type="hidden" name="today" id="today" value="{{date('Y-m-d')}}" disabled="disabled">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-warning  col-sm-5" id="section_2" hidden="hidden" >
                                    <div class="panel-body">
                                        <div class="form-group" id="d1" hidden="hidden">
                                            <label class="col-sm-4" for="">Date 1</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="date[]" id="date_1" class="col-xs-12 datepicker" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>

                                        <div class="form-group" id="d2" hidden="hidden">
                                            <label class="col-sm-4" for="">Date 2</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="date[]" id="date_2" class="col-xs-12 datepicker" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>

                                        <div class="form-group" id="d3" hidden="hidden">
                                            <label class="col-sm-4" for="">Date 3</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="date[]" id="date_3" class="col-xs-12 datepicker" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>

                                        <div class="form-group" id="d4" hidden="hidden">
                                            <label class="col-sm-4" for="">Date 4</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="date[]" id="date_4" class="col-xs-12 datepicker" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>

                                        <div class="form-group" id="d5" hidden="hidden">
                                            <label class="col-sm-4" for="">Date 5</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="date[]" id="date_5" class="col-xs-12 datepicker" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>

                                        <div class="form-group" id="d6" hidden="hidden">
                                            <label class="col-sm-4" for="">Date 6</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="date[]" id="date_6" class="col-xs-12 datepicker" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>  
                                    </div>
                                    </div>
                                </div>

                                

                                
                            <div class="row" id="save_button" hidden="hidden">
                                <button type="submit" class="pull-right btn btn-sm btn-primary" style="border-radius: 2px;">Save</button>
                            </div>
                            
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            </div>


        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#holiday_count').on('change',function(){
            var number = $(this).val();
            if(number>0){
                document.getElementById("save_button").removeAttribute('hidden');
                document.getElementById("section_2").removeAttribute('hidden');
                for(var i=1; i<=number; i++){;
                    document.getElementById("d"+i).removeAttribute('hidden');
                }
            }
        }).change();
        //Stop click event of Datepicker
        // $('#date_1, #date_2, #date_3, #date_4, #date_5, #date_6').datepicker({useCurrent: false}); 
        // 

        $('#date_1').on('dp.change', function(){
            var today = new Date($('#today').val());
            var date = new Date($('#date_1').val());

            if(date < today){
                alert("Date is older than Today");
                $('#date_1').val('');
            }
            if(
                (date - new Date($('#date_2').val()) ) === 0 ||
                (date - new Date($('#date_3').val()) ) === 0 ||
                (date - new Date($('#date_4').val()) ) === 0 ||
                (date - new Date($('#date_5').val()) ) === 0 ||
                (date - new Date($('#date_6').val()) ) === 0 
                ){
                alert("Already Entered");
                $('#date_1').val('');
            }
        });
        //------------------------------------------
        $('#date_2').on('dp.change', function(){
            var today = new Date($('#today').val());
            var date = new Date($('#date_2').val());

            if(date < today){
                alert("Date is older than Today");
                $('#date_2').val('');
            }
            // console.log((date - new Date($('#date_1').val()) ) === 0);
            if(
                (date - new Date($('#date_1').val()) ) === 0 ||
                (date - new Date($('#date_3').val()) ) === 0 ||
                (date - new Date($('#date_4').val()) ) === 0 ||
                (date - new Date($('#date_5').val()) ) === 0 ||
                (date - new Date($('#date_6').val()) ) === 0 
                ){
                alert("Already Entered");
                $('#date_2').val('');
            }
        });
        //--------------------------------------------
        $('#date_3').on('dp.change', function(){
            var today = new Date($('#today').val());
            var date = new Date($('#date_3').val());

            if(date < today){
                alert("Date is older than Today");
                $('#date_3').val('');
            }
            if(
                (date - new Date($('#date_1').val()) ) === 0 ||
                (date - new Date($('#date_2').val()) ) === 0 ||
                (date - new Date($('#date_4').val()) ) === 0 ||
                (date - new Date($('#date_5').val()) ) === 0 ||
                (date - new Date($('#date_6').val()) ) === 0 
                ){
                alert("Already Entered");
                $('#date_3').val('');
            }
        });
        //----------------------------------------------
        $('#date_4').on('dp.change', function(){
            var today = new Date($('#today').val());
            var date = new Date($('#date_4').val());

            if(date < today){
                alert("Date is older than Today");
                $('#date_4').val('');
            }
            if(
                (date - new Date($('#date_1').val()) ) === 0 ||
                (date - new Date($('#date_3').val()) ) === 0 ||
                (date - new Date($('#date_2').val()) ) === 0 ||
                (date - new Date($('#date_5').val()) ) === 0 ||
                (date - new Date($('#date_6').val()) ) === 0 
                ){
                alert("Already Entered");
                $('#date_4').val('');
            }
        });
        //---------------------------------------------
        $('#date_5').on('dp.change', function(){
            var today = new Date($('#today').val());
            var date = new Date($('#date_5').val());

            if(date < today){
                alert("Date is older than Today");
                $('#date_5').val('');
            }
            if(
                (date - new Date($('#date_1').val()) ) === 0 ||
                (date - new Date($('#date_3').val()) ) === 0 ||
                (date - new Date($('#date_4').val()) ) === 0 ||
                (date - new Date($('#date_2').val()) ) === 0 ||
                (date - new Date($('#date_6').val()) ) === 0 
                ){
                alert("Already Entered");
                $('#date_5').val('');
            }
        });
        //------------------------------------------------
        $('#date_6').on('dp.change', function(){
            var today = new Date($('#today').val());
            var date = new Date($('#date_6').val());

            if(date < today){
                alert("Date is older than Today");
                $('#date_6').val('');
            }
            if(
                (date - new Date($('#date_1').val()) ) === 0 ||
                (date - new Date($('#date_3').val()) ) === 0 ||
                (date - new Date($('#date_4').val()) ) === 0 ||
                (date - new Date($('#date_5').val()) ) === 0 ||
                (date - new Date($('#date_2').val()) ) === 0 
                ){
                alert("Already Entered");
                $('#date_6').val('');
            }
        });
    });
// Radio button action
  function attLocation(loc){
    window.location = loc;
   }
</script>
@endsection