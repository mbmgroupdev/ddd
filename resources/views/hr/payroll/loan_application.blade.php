@extends('hr.layout')
@section('title', 'Loan Application')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Payroll</a>
                </li>
                <li class="active">Loan Application</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        @include('inc/message')
        <div class="panel h-min-400"> 
            <div class="panel-heading">
                <h6>
                    Loan Application

                    <a href="{{url('hr/payroll/loan_list')}}" class="btn btn-sm btn-primary pull-right">List</a>
                </h6>
            </div> 
            <div class="panel-body">  

                <form action="{{url('hr/payroll/loan')}}" method="post" class="needs-validation form" novalidate>
                @csrf
                <div class="row justify-content-center">
                    <div class="col-sm-3">
                        <input type="hidden" name="hr_la_name" id="hr_la_name"/>
                        <input type="hidden" name="hr_la_designation" id="hr_la_designation"/>
                        <input type="hidden" name="hr_la_date_of_join" id="hr_la_date_of_join"/> 
                        <div class="benefit-employee mb-3">
                            <div class="user-details-block ">
                              <div class="user-profile text-center">
                                    <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                              </div>
                              <div class="text-center mt-3">
                                 <h4><b id="user-name">Select Employee</b></h4>
                                 <p class="mb-0" id="designation">---------------------</p>
                                 <p class="mb-0" >
                                    Date of Join: <span id="as_doj">--/--/----</span></p>
                                 
                              </div>
                              {{-- <div id="loanHistory" >
                                  
                              </div> --}}
                           </div>
                        </div>
                        <div class="form-group has-float-label has-required select-search-group">
                            {{ Form::select('associate_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'associate_id', 'class'=> 'associates']) }}
                            <label  for="associate_id"> Associate's ID </label>
                        </div>
                        <div class="form-group has-required has-float-label select-search-group">
                            
                            <select name="hr_la_type_of_loan" id="hr_la_type_of_loan" class="form-control"  required="required" required-error-msg="The Type of Loan Request field is required"  >
                                <option value="">Select Type of Loan</option>
                                @foreach($types as $type)
                                <option value="{{ $type->hr_loan_type_name }}">{{ $type->hr_loan_type_name }}</option>
                                @endforeach 
                            </select>
                            <label  for="hr_la_type_of_loan">Type of Loan </label>
                        </div>
                        <div class="form-group has-required has-float-label">
                            <input name="hr_la_applied_date" type="date" id="hr_la_applied_date" placeholder="Applied Date  " class="form-control" required="required" value="{{date('Y-m-d')}}"/>
                            <label  for="hr_la_applied_date">Applied Date   </label>
                        </div>
                    </div>
                    
                    <div class="col-sm-3">
                        
                        <div class="form-group" >
                            <label for="gender"> Purpose of Loan  </label> <br>

                            <div class="control-group">
                                <div class="checkbox">
                                <label>
                                    <input name="hr_la_purpose_of_loan[]" type="checkbox" value="Education" class="ace" >
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
                    <div class="col-sm-3">
 
                        

                        <div class="form-group has-float-label has-required">
                            <input name="hr_la_applied_amount" type="text" id="hr_la_applied_amount" placeholder="Applied Amount" class="form-control" required="required " />
                            <label  for="hr_la_applied_amount"> Applied Amount  </label>
                        </div>

                        <div class="form-group has-required has-float-label">
                            <input name="hr_la_no_of_installments" type="text" id="hr_la_no_of_installments" placeholder="No. of Installments (for payment)" class="form-control" required="required" />
                            <label  for="hr_la_no_of_installments">No of Installments   </label>
                        </div>

                        
                        <div class="form-group has-float-label"  id="hiddenNote" style="display:none;">
                            <textarea name="hr_la_note" id="hr_la_note" class="form-control" placeholder="Other details"   ></textarea>
                            <label for="hr_la_note">Other Details</label>
                        </div>
                        <div class="form-group">
                            
                            <div class="custom-control custom-switch custom-switch-icon ">
                              <div class="custom-switch-inline">
                                 <input type="checkbox" class="custom-control-input" id="hr_la_status" name="hr_la_status" value="1">
                                 <label class="custom-control-label" for="hr_la_status">
                                 <span class="switch-icon-left"><i class="fa fa-check"></i></span>
                                 <span class="switch-icon-right"><i class="fa fa-check"></i></span>
                                 </label>
                                  Loan Approve
                              </div>
                           </div>
                      </div>
                        <div class="row">
                            <div id="approved-div" class="col-sm-12" style="display: none;">
                                
                                <div class="form-group has-float-label ">
                                    <input name="hr_la_approved_amount" type="text" id="hr_la_approved_amount" placeholder="Approved Amount" class="form-control"  />
                                    <label  for="hr_la_approved_amount"> Approved Amount  </label>
                                </div>  

                                <div class="form-group has-float-label">
                                    <input name="hr_la_no_of_installments_approved" type="text" id="hr_la_no_of_installments_approved" placeholder="No. of Installments (Approved)" class="form-control"  />
                                    <label  for="hr_la_no_of_installments_approved">Approved Installments   </label>
                                </div>

                                <div class="form-group has-float-label" >
                                    <textarea name="hr_la_supervisors_comment" id="hr_la_supervisors_comment" class="form-control" placeholder="Other details"   ></textarea>
                                    <label for="hr_la_supervisors_comment">Supervisor's Comment</label>
                                </div>
                            </div>
                        </div>
                         

                        
                        <div class="form-group">
                            <button class="btn btn-primary pull-right" type="submit">
                                <i class="ace-icon fa fa-check bigger-110"></i> Submit
                            </button>
                        </div>
                        
                    </div>
                </div>
                        
                {{ Form::close() }}
            </div>
        </div>
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

    $('#hr_la_status').on('click', function(){
        if(this.checked){
            $('#approved-div').toggle();
        }
        else{
            $('#approved-div').toggle();
        }
    });

 
    // retrive all information 
    $(document).on('change', ".associates", function(){
        var associate_id = $(this).val();
        $('.app-loader').show();
        $.ajax({
            url: '{{ url("hr/ess/loan_history") }}',
            dataType: 'json',
            data: {associate_id: associate_id},
            success: function(data)
            {
                    $('#avatar').attr('src',data.associate.as_pic);
                    $('#user-name').text(data.associate.as_name);
                    $('#designation').text(data.associate.hr_designation_name);
                    $('#as_doj').text(data.associate.as_doj);
                    $('#hr_la_name').val(data.associate.as_name);
                    $('#hr_la_designation').val(data.associate.hr_designation_name);
                    $('#hr_la_date_of_join').val(data.associate.as_doj);

                var html = "<table class='table table-bordered'>";
                $.each(data.loan, function(i, v){
                    html += "<tr>"+
                        "<td>"+v.hr_la_type_of_loan+"</td>"+
                        "<td>"+(v.hr_la_applied_amount).toFixed(2)+"</td>"+
                        "<td>"+(v.hr_la_purpose_of_loan.slice(0, -2))+"</td>"+
                        "<td>"+v.hr_la_updated_at+"</td>"+
                        "<td>"+v.hr_la_status+"</td>"+
                    "</tr>";
                });
                html += '</table>'; 
                $("#loanHistory").html(html);
                $('.app-loader').hide();
            },
            error: function(xhr)
            {
                $.notify('failed...','error');
                $('.app-loader').hide();
            }
        });
    });

});
</script>
@endpush
@endsection