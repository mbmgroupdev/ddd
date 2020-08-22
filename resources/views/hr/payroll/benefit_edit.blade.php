@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li> 
                <li>
                    <a href="#">Payroll </a>
                </li>
                <li class="active"> Benefit Update</li>
            </ul><!-- /.breadcrumb -->

        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Recruitment <small><i class="ace-icon fa fa-angle-double-right"></i> Benefit Update</small></h1>
            </div>

            <div class="row">
                  <!-- Display Erro/Success Message -->
                @include('inc/message')
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <!-- <h1 align="center">Add New Employee</h1> -->
                    <!-- </br> -->
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#benefit_entry" aria-expanded="true">Benefit</a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="#other_benefit_entry">Other Benefits</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="benefit_entry" class="tab-pane fade in active">
                                <form class="form-horizontal" role="form" method="post" action="{{ url('hr/payroll/benefit_edit') }}" enctype="multipart/form-data">
                                {{ csrf_field() }} 

                                    {{ Form::hidden('ben_id', $benefits->ben_id) }}
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="col-sm-6">

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_as_id"> Associate's ID  <span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_as_id" placeholder="Passport No" class="form-control" value="{{ $benefits->ben_as_id }}" readonly /> 
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_current_salary"> Gross Salary<span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_current_salary" id="ben_current_salary" placeholder="Gross Salary(tk) As Per Joining Letter" class="form-control"  value="{{$benefits->ben_current_salary}}" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Joining salary"/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_cash_amount"> CASH <span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_cash_amount" id="ben_cash_amount" placeholder="Amount Paid in Cash" class="form-control" value="{{ $benefits->ben_cash_amount }}" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid CASH amount"/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_bank_amount"> BANK <span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_bank_amount" id="ben_bank_amount" placeholder="Amount Paid in Bank"  value="{{ $benefits->ben_bank_amount }}" class="form-control" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid BANK Amount"/>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_basic"> Basic Salary</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_basic" id="ben_basic" placeholder="Basic Salary" class="form-control" value="{{ $benefits->ben_basic }}" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Basic Salary" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_house_rent"> House Rent</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_house_rent" id="ben_house_rent" placeholder="House Rent" class="form-control" value="{{ $benefits->ben_house_rent }}" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid House Rent" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_medical"> Medical</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_medical" id="ben_medical" placeholder="Medical" class="form-control" value="{{ $benefits->ben_medical }}" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Medical Allowance" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_transport"> Transportation</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_transport" id="ben_transport" value="{{ $benefits->ben_transport }}" placeholder="Transportation" class="form-control" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Transportation Allowance" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_food"> Food</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_food" id="ben_food" placeholder="Food" value="{{ $benefits->ben_food }}" class="form-control" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Food Allowance" readonly/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_food"> Fixed</label>
                                                    <div class="col-sm-8 pull-left" style="padding-top: 7px;" >
                                                        <input type="checkbox" name="fixed_check" id="fixed_check"/>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="full_salary"> Full Salary Amount</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="full_salary" id="full_salary" placeholder="Full Salary" value="" class="form-control fixed-salary"  disabled="disabled"  />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_as_id"> Associate's ID<span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        {{ Form::select('ben_as_id', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Associate\'s ID', 'id'=>'ben_as_id', 'class'=> 'associates no-select form-control', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Associate\'s ID field is required']) }}  
                                                    </div>
                                                </div>  -->

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_joining_salary_fixed"> Gross Salary<span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_joining_salary_fixed" id="ben_joining_salary_fixed" value="{{ !empty($fixedSalary->fixed_amount)?$fixedSalary->fixed_amount:null }}"  placeholder="Gross Salary(tk) As Per Joining Letter" class="form-control fixed-salary" data-validation="length number"data-validation-length="1-20" data-validation-allowing="float" data-validation-error-msg="Invalid Gross salary"  disabled="disabled"/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_cash_amount"> CASH <span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_cash_amount_fixed" id="ben_cash_amount_fixed" value="{{ !empty($fixedSalary->cash_amount)?$fixedSalary->cash_amount:null }}" placeholder="Amount Paid in Cash" class="form-control fixed-salary" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid CASH amount" disabled="disabled"/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_bank_amount"> BANK <span style="color: red">&#42;</span> </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_bank_amount_fixed" id="ben_bank_amount_fixed"  value="{{ !empty($fixedSalary->bank_amount)?$fixedSalary->bank_amount:null }}" placeholder="Amount Paid in Bank" class="form-control fixed-salary" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid BANK Amount"  disabled="disabled"/>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_basic"> Basic Salary</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_basic_fixed" id="ben_basic_fixed" value="{{ !empty($fixedSalary->  basic)?$fixedSalary->basic:null }}" placeholder="Basic Salary" value="0" class="form-control fixed-salary" data-validation="required length number" data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Basic Salary"  disabled="disabled" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_house_rent"> House Rent</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_house_rent_fixed" id="ben_house_rent_fixed" value="{{ !empty($fixedSalary->house_rent)?$fixedSalary->house_rent:null }}" placeholder="House Rent" class="form-control fixed-salary" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid House Rent" disabled="disabled" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_medical"> Medical</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_medical_fixed" id="ben_medical_fixed" placeholder="Medical" class="form-control fixed-salary" value="{{ !empty($fixedSalary->medical)?$fixedSalary->medical:$structure->medical }}" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Medical Allowance" disabled="disabled" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_transport"> Transportation</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_transport_fixed" id="ben_transport_fixed" value="{{ !empty($fixedSalary->transport)?$fixedSalary->transport:$structure->transport }}" placeholder="Transportation" class="form-control fixed-salary" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20"data-validation-error-msg="Invalid Transportation Allowance" disabled="disabled" readonly/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label no-padding-right" for="ben_food"> Food</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="ben_food_fixed" id="ben_food_fixed" placeholder="Food" value="{{ !empty($fixedSalary->food)?$fixedSalary->food:$structure->food }}" class="form-control fixed-salary" data-validation="required length number"  data-validation-allowing="float" data-validation-length="1-20" data-validation-error-msg="Invalid Food Allowance" disabled="disabled" readonly/>
                                                    </div>
                                                </div>
                                                
                                            </div> 
                                        </div>      
                                        <div class="col-sm-12">
                                           <div class="clearfix form-actions">
                                            <div class="col-md-offset-4 col-md-4 text-center"> 
                                                <button class="btn btn-sm btn-success" type="submit">
                                                    <i class="ace-icon fa fa-check bigger-110"></i> Update
                                                </button>

                                                &nbsp; &nbsp; &nbsp;
                                                <button class="btn btn-sm" type="reset">
                                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                                </button>
                                            </div>
                                           </div>
                                        </div>  
                                    </div>     
                                    
                                    <!-- /.row --> 
                                 
                                </form> 
                            </div>
                            <!-- other benefit entry -->
                            <div id="other_benefit_entry" class="tab-pane fade">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#select_item">Select Benefit Item</button>
                                <form class="form-horizontal" role="form" method="post" action="{{ url('hr/payroll/other_benefit') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }} <br>
                                    <input type="hidden" name="other_associate_id" value="{{ $benefits->ben_as_id }}">
                                    <div id="Benefit_Item_description">
                                    @if(!empty($other_bnf_data))
                                    @foreach($other_bnf_data as $data)
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <label class="col-sm-4 control-label no-padding-right" for="items">Item Name<span style="color: red">&#42;</span>  </label>
                                             <div class="col-sm-8">
                                                <input type="text" name="items[]" id="items[]" placeholder="Food" value="{{$data->benefit_name}}" class="col-xs-12" readonly/>
                                            </div>
                                            </div>
                                            <div class="col-sm-4">
                                            <label class="col-sm-4 control-label no-padding-right" for="item_description">Description<span style="color: red">&#42;</span>  </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="item_description[]" id="item_description" placeholder="Food" value="{{$data->item_description}}" class="col-xs-12" data-validation="required"/>
                                            </div>
                                            </div>
                                            <div class="col-sm-4">
                                            <label class="col-sm-4 control-label no-padding-right" for="item_amount">Amount<span style="color: red">&#42;</span>  </label>
                                            <div class="col-sm-8">
                                                <input name="item_amount[]" type="text" id="item_amount[]" placeholder="Amount" class="col-xs-12" data-validation="number required length" data-validation-length="1-11" value="{{$data->item_amount }}" data-validation="required"/>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                                <input type="hidden" name="item_id[]" value="{{$data->item_id}}">
                                        </div>
                                    @endforeach
                                    @endif

                                    </div>

                                    <div class="space-4"></div>
                                    <div class="space-4"></div>
                                    <div class="space-4"></div>
                                    <div class="clearfix form-actions">
                                        <div class="col-md-offset-4 col-md-4 text-center"> 
                                            <button class="btn btn-sm btn-success" type="submit">
                                                <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                            </button>

                                            &nbsp; &nbsp; &nbsp;
                                            <button class="btn btn-sm" type="reset">
                                                <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col -->
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="select_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <!-- <button type="button" class="btn btn-danger btn-xs pull-right" data-dismiss="modal">Close</button> -->
                <h2 class="modal-title text-center" id="myModalLabel">Other Benefit Items</h2>
            </div>
            <form class="form-horizontal" role="form" method="post" action="#" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="control-group">
                        @foreach($other_bnf_items AS $other)
                        <div class="checkbox">
                            <label>
                                <input name="selected_item[]" type="checkbox" value="{{ $other->id }}" class="ace checkbox-input" <?php if($other->chk==1) echo("checked") ?> >
                                <span class="lbl"> {{ $other->benefit_name }}</span>
                            </label>
                        </div> 
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-offset-3 col-md-9"> 
                        <button class="btn btn-info" type="button" id="modal_data" data-dismiss="modal">
                            <i class="ace-icon fa fa-check bigger-110" ></i> Done
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal End -->
<script type="text/javascript">
    $(document).ready(function(){

        $('#select_item').on('hidden.bs.modal', function (e) {
            var data= "";
            $('.checkbox-input').each(function(i, v){
                if ($(this).is(":checked"))
                {
                    var id= $(this).val();
                    console.log(id);
                    var item_name= $(this).next().text();
                    data+='<div class="form-group"><div class="col-sm-4"><label class="col-sm-4 control-label no-padding-right" for="items">Item Name<span style="color: red">&#42;</span>  </label> <div class="col-sm-8"> <input type="text" name="items[]" id="items[]" placeholder="Food" value="'+item_name+'" class="col-xs-12" readonly/> </div>  </div> <div class="col-sm-4"> <label class="col-sm-4 control-label no-padding-right" for="item_description">Description<span style="color: red">&#42;</span>  </label> <div class="col-sm-8"> <input type="text" name="item_description[]" id="item_description" placeholder="Food" value="N/A" class="col-xs-12" data-validation="required"/> </div> </div> <div class="col-sm-4"> <label class="col-sm-4 control-label no-padding-right" for="item_amount">Amount<span style="color: red">&#42;</span>  </label> <div class="col-sm-8"> <input name="item_amount[]" type="text" id="item_amount[]" placeholder="Amount" class="col-xs-12" data-validation="number required length" data-validation-length="1-11" value="0" data-validation="required"/> </div> </div> </div> <div class="form-group"> <input type="hidden" name="item_id[]" value="'+id+'"> </div>';
                }
            });
            // console.log(data);
            $("#Benefit_Item_description").html(data);
        });

        $('#ben_current_salary').on('change', function(){
            // var basic= '{{ $structure->basic }}';
            // var house= '{{ $structure->house_rent }}';
            // var medical= '{{ $structure->medical }}';
            // var trans= '{{ $structure->transport }}';
            // var food= '{{ $structure->food }}';

            var basic_percent= '{{ $structure->basic }}';
            var house= '{{ $structure->house_rent }}';
            var medical= '{{ $structure->medical }}';
            var trans= '{{ $structure->transport }}';
            var food= '{{ $structure->food }}';

            var salary= parseFloat($('#ben_current_salary').val());
            var sub =parseFloat(medical)+parseFloat(trans)+parseFloat(food);
            var basic= parseFloat((salary-sub)/basic_percent).toFixed(2);
            $('#ben_basic').val(basic);
            var house= parseFloat(salary-sub-basic).toFixed(2);
            $('#ben_house_rent').val(house);
            $('#ben_medical').val(medical);
            $('#ben_transport').val(trans);
            $('#ben_food').val(food);
        });

        $('#ben_cash_amount').on('change', function(){
            var salary= parseFloat($('#ben_current_salary').val());
            
            if(isNaN(salary)){
                alert("Please enter Current Salary first");
            }
            else{ 
                var cash= parseFloat($('#ben_cash_amount').val());
                
                if(cash>salary || cash<0)
                {
                    alert("Cash Amount Can not be greater than Salary or Negative");
                    $('#ben_cash_amount').val(salary.toFixed(2));
                    $('#ben_bank_amount').val(0);
                }
                else{
                    var bank= salary-cash;
                    $('#ben_bank_amount').val(bank.toFixed(2));
                }
            }
        });
        $('#ben_bank_amount').on('change', function(){
            var salary= parseFloat($('#ben_current_salary').val()).toFixed(2);
            
            if(isNaN(salary)){
                alert("Please enter Current Salary first");
            }
            else{ 
                var bank= parseFloat($('#ben_bank_amount').val()).toFixed(2);
                if(bank>salary || bank<0)
                {
                    alert("Cash Amount");
                    $('#ben_cash_amount').val(salary.toFixed(2));
                    $('#ben_bank_amount').val(0);
                }
                else{
                    var cash= salary-bank;
                    $('#ben_cash_amount').val(cash.toFixed(2));
                }

            }
        });
        $('#ben_current_salary').on('change', function(){
            var cash= parseFloat($('#ben_cash_amount').val()).toFixed(2);
            var bank= parseFloat($('#ben_bank_amount').val()).toFixed(2);
            if(bank>0 || cash> 0){
                $('#ben_cash_amount').val(0);
                $('#ben_bank_amount').val(0);
            }
        });

    /* For Fixed Salary  */

      $('#ben_joining_salary_fixed').on('change', function(){
           

            var basic_percent= '{{ $structure->basic }}';
            var house= '{{ $structure->house_rent }}';
            var medical= '{{ $structure->medical }}';
            var trans= '{{ $structure->transport }}';
            var food= '{{ $structure->food }}';

            var salary= parseFloat($('#ben_joining_salary_fixed').val());
            var sub =parseFloat(medical)+parseFloat(trans)+parseFloat(food);
            var basic= parseFloat((salary-sub)/basic_percent).toFixed(2);
            $('#ben_basic_fixed').val(basic);
            var house= parseFloat(salary-sub-basic).toFixed(2);
            $('#ben_house_rent_fixed').val(house);
            $('#ben_medical_fixed').val(medical);
            $('#ben_transport_fixed').val(trans);
            $('#ben_food_fixed').val(food);
        });

        $('#ben_cash_amount_fixed').on('change', function(){
            var salary= parseFloat($('#ben_joining_salary_fixed').val());
            
            if(isNaN(salary)){
                alert("Please enter Current Salary first");
            }
            else{ 
                var cash= parseFloat($('#ben_cash_amount_fixed').val());
                
                if(cash>salary || cash<0)
                {
                    alert("Cash Amount Can not be greater than Salary or Negative");
                    $('#ben_cash_amount_fixed').val(salary.toFixed(2));
                    $('#ben_bank_amount_fixed').val(0);
                }
                else{
                    var bank= salary-cash;
                    $('#ben_bank_amount_fixed').val(bank.toFixed(2));
                }
            }
        });
        $('#ben_bank_amount_fixed').on('change', function(){
            var salary= parseFloat($('#ben_joining_salary_fixed').val()).toFixed(2);
            
            if(isNaN(salary)){
                alert("Please enter Current Salary first");
            }
            else{ 
                var bank= parseFloat($('#ben_bank_amount_fixed').val()).toFixed(2);
                if(bank>salary || bank<0)
                {
                    alert("Cash Amount");
                    $('#ben_cash_amount_fixed').val(salary.toFixed(2));
                    $('#ben_bank_amount_fixed').val(0);
                }
                else{
                    var cash= salary-bank;
                    $('#ben_cash_amount_fixed').val(cash.toFixed(2));
                }

            }
        });
        $('#ben_joining_salary_fixed').on('change', function(){
            var cash= parseFloat($('#ben_cash_amount_fixed').val()).toFixed(2);
            var bank= parseFloat($('#ben_bank_amount_fixed').val()).toFixed(2);
            if(bank>0 || cash> 0){
                $('#ben_cash_amount_fixed').val(0);
                $('#ben_bank_amount_fixed').val(0);
            }
        });
    /*********************/
        
        $('form').submit(function(e){
            var salary   = parseFloat($('#ben_joining_salary').val());
            var basic    = parseFloat($('#ben_basic').val());
            var house    = parseFloat($('#ben_house_rent').val());
            var medical  = parseFloat($('#ben_medical').val());
            var transport= parseFloat($('#ben_transport').val());
            var food     = parseFloat($('#ben_food').val());
            var total_check_a;
            var total_check_b;
            var salary1   = parseFloat($('#ben_current_salary').val()); 
            

          

         total_check_a = totalSalary(salary1,basic,house,medical,transport,food);

           // If Fixed checked then call totalSalary() for fixed Salary

            var is_checked =  $("#fixed_check").is(":checked");
            if(is_checked) { 
                var salary   = parseFloat($('#ben_joining_salary_fixed').val());
                var basic    = parseFloat($('#ben_basic_fixed').val());
                var house    = parseFloat($('#ben_house_rent_fixed').val());
                var medical  = parseFloat($('#ben_medical_fixed').val());
                var transport= parseFloat($('#ben_transport_fixed').val());
                var food     = parseFloat($('#ben_food_fixed').val()); 

                total_check_b = totalSalary(salary,basic,house,medical,transport,food);
                  if( total_check_b==0){ 
                  alert("Invalid Salary Calculation"); 
                
                e.preventDefault();
            }
            }
           
            if(total_check_a==0){
                  alert("Invalid Salary Calculation");
                
                e.preventDefault();
            }
    });

// Calculate total Salary 
    function totalSalary(salary,basic,house,medical,transport,food){  
     

        var formSubmit = 1;   
        var total= parseFloat(basic+house+medical+transport+food).toFixed(2);

        var totalInt=round(total);
        var salaryInt=round(salary); 

        
        if(salary != total){
               
          formSubmit = 0;          
        }
        // return formSubmit;

           
    } 

     // If Fixed check box click then Fixed Salary inputs Enable
    
        $("#fixed_check").change(function() {
        // var prev_val=$(".fixed-salary").val();
         var fxJoiningSalary=$('#ben_joining_salary_fixed').val();
         var fxCash=$('#ben_cash_amount_fixed').val();
         var fxBank=$('#ben_bank_amount_fixed').val();
         var fxBasic=$('#ben_basic_fixed').val();
         var fxHouseRent=$('#ben_house_rent_fixed').val();
         var fxMedical=$('#ben_medical_fixed').val();
         var fxTransport=$('#ben_transport_fixed').val();
         var fxFood=$('#ben_food_fixed').val();
         var is_checked = $(this).is(":checked");
         
         if(!is_checked) {

              $('#ben_joining_salary_fixed').val(fxJoiningSalary);
              $('#ben_cash_amount_fixed').val(fxCash);
              $('#ben_bank_amount_fixed').val(fxBank);
              $('#ben_basic_fixed').val(fxBasic);
              $('#ben_house_rent_fixed').val(fxHouseRent);
              $('#ben_medical_fixed').val(fxMedical);
              $('#ben_transport_fixed').val(fxTransport);
              $('#ben_food_fixed').val(fxFood);

         }
         
         $(".fixed-salary").prop("disabled", !is_checked);
       });
    });
</script>

<script type="text/javascript">
$(document).ready(function()
{   
    $('select.associates').select2({
        placeholder: 'Select Associate\'s ID',
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    }) 
                };
          },
          cache: true
        }
    }); 

});
</script>
@endsection

 