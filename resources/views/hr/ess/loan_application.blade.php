@extends('user.layout')
@section('title', 'Loan Application')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="#">Ess</a>
                </li>
                <li class="active">Loan Application</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        @include('inc/message')
        <div class="row">
            <div class="col-sm-6">
                <div class="panel h-min-400"> 
                  <div class="panel-heading"><h6>Loan Application</h6></div> 
                    <div class="panel-body">  

                        {{ Form::open(['url'=>'hr/ess/loan_application', 'class'=>'form-horizontal']) }}
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="hidden" name="hr_la_name"/>
                                <input type="hidden" name="hr_la_designation"/>
                                <input type="hidden" name="hr_la_date_of_join"/> 
         
                                <div class="form-group has-required has-float-label select-search-group">
                                    
                                    <select name="hr_la_type_of_loan" id="hr_la_type_of_loan" class="form-control"  required="required" required-error-msg="The Type of Loan Request field is required"  >
                                        <option value="">Select Type of Loan</option>
                                        @foreach($types as $type)
                                        <option value="{{ $type->hr_loan_type_name }}">{{ $type->hr_loan_type_name }}</option>
                                        @endforeach 
                                    </select>
                                    <label  for="hr_la_type_of_loan">Type of Loan </label>
                                </div>

                                <div class="form-group has-float-label has-required">
                                    <input name="hr_la_applied_amount" type="text" id="hr_la_applied_amount" placeholder="Applied Amount" class="form-control" required="required " />
                                    <label  for="hr_la_applied_amount"> Applied Amount  </label>
                                </div>

                                <div class="form-group has-required has-float-label">
                                    <input name="hr_la_no_of_installments" type="text" id="hr_la_no_of_installments" placeholder="No. of Installments (for payment)" class="form-control" required="required" />
                                    <label  for="hr_la_no_of_installments">No of Installments   </label>
                                </div>

                                <input type="hidden" name="hr_la_applied_date" id="hr_la_applied_date" value="<?php echo date('Y-m-d'); ?>" class="col-xs-10 col-sm-5 " required="required"/>

                                <div class="form-group has-float-label" style="display:none;" id="hiddenNote">
                                    <textarea name="hr_la_note" id="hr_la_note" class="col-xs-10 col-sm-5" placeholder="Other details"  required="required" ></textarea>
                                    <label for="hr_la_note"></label>
                                </div>  
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>
                                </div>
                                
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" style="    margin-top: -10px;">
                                    <label for="gender"> Purpose of Loan  </label> <br>

                                    <div class="control-group">
                                        <div class="checkbox">
                                        <label>
                                            <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Education" class="ace" required="checkbox_group" required-qty="min1">
                                            <span class="lbl"> Education</span>
                                        </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Children's education" class="ace">
                                                <span class="lbl"> Children's education</span>
                                            </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Holidays/Travel" class="ace">
                                                <span class="lbl"> Holidays/Travel</span>
                                            </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Medical expenses" class="ace">
                                                <span class="lbl"> Medical expenses</span>
                                            </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Investments" class="ace">
                                                <span class="lbl"> Investments</span>
                                            </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" id="otherBox" type="checkbox" value="Other" class="ace">
                                                <span class="lbl"> Others.....</span>
                                            </label>
                                        </div>   
                                        <div class="checkbox">
                                        <label>
                                            <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Consumer durable purchase" class="ace">
                                            <span class="lbl"> Consumer durable purchase</span>
                                        </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Marriage in family" class="ace">
                                                <span class="lbl"> Marriage in family</span>
                                            </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Home improvement/Renovation of home or office" class="ace">
                                                <span class="lbl"> Home improvement/ Renovation of home or office</span>
                                            </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Loan transfer" class="ace">
                                                <span class="lbl"> Loan transfer</span>
                                            </label>
                                        </div> 
                                        <div class="checkbox">
                                            <label>
                                                <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Purchase of equipment" class="ace">
                                                <span class="lbl"> Purchase of equipment</span>
                                            </label>
                                        </div> 
                                    </div> 
                                </div>
                                
                            </div>
                        </div>
                                
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel h-min-400">
                    <div class="panel-heading"> <h6>Loan List</h6> </div>
                    <div class="panel-body" >
                        <table class="table table-bordered" style="display: block;overflow-x: auto;white-space: nowrap; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20%">Types of Loan</th>
                                    <th width="20%">Amount</th>
                                    <th width="20%">Purpose of loan</th>
                                    <th width="20%">Date</th>
                                    <th width="20%">Status</th>
                                </tr>
                            </thead>
                            <tbody id="loanHistory"> 
                            </tbody> 
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>


@push('js') 
<script type="text/javascript">
$(document).ready(function()
{ 
$('#otherBox').removeAttr('checked');
    $('#otherBox').on('click', function(){
        if(this.checked){
            $('#hiddenNote').toggle();
        }
        else{
            $('#hiddenNote').toggle();
        }
    });

 
    // retrive all information 
    var associate_id = '{{ auth()->user()->associate_id }}';
    var name         = $("input[name=hr_la_name]");
    var designation  = $("input[name=hr_la_designation]");
    var date_of_join = $("input[name=hr_la_date_of_join]");
    $(window).load( function(){
        $.ajax({
            url: '{{ url("hr/ess/loan_history") }}',
            dataType: 'json',
            data: {associate_id: associate_id},
            success: function(data)
            {
                name.val(data.associate.as_name);
                designation.val(data.associate.hr_designation_name);
                date_of_join.val(data.associate.as_doj);

                var html = "";
                $.each(data.loan, function(i, v)
                {
                    html += "<tr>"+
                        "<td>"+v.hr_la_type_of_loan+"</td>"+
                        "<td>"+(v.hr_la_applied_amount).toFixed(2)+"</td>"+
                        "<td>"+(v.hr_la_purpose_of_loan.slice(0, -2))+"</td>"+
                        "<td>"+v.hr_la_updated_at+"</td>"+
                        "<td>"+v.hr_la_status+"</td>"+
                    "</tr>";
                });
                $("#loanHistory").html(html);

            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
    });

});
</script>
@endpush
@endsection