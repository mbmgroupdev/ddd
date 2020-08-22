@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        .progress[data-percent]:after{
            color: #000;
        }
        .form-group {
            padding: 14px 0px;
        }
        span.select2 {
            width: 100% !important;
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
                <li>Performance</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Performance 
                @if(!empty($approvalComplete))
                    <span class="label label-success arrowed-in arrowed-in-right">Approved</span>
                @endif
                </h1>
            </div>

            <div class="widget-box">
                <div class="widget-body">
                    <div class="widget-main">
                        <div id="fuelux-wizard-container" class="no-steps-container">
                            <div>
                                <ul class="steps" style="margin-left: 0">
                                    <li data-step="1" class="active">
                                        <span class="step">1</span>
                                        <span class="title">Performance</span>
                                    </li>

                                    <li data-step="2">
                                        <span class="step">2</span>
                                        <span class="title">Increment</span>
                                    </li>
                                </ul>
                            </div>

                            <hr>

                            <div class="step-content pos-rel">
                                <div class="step-pane active" data-step="1">
                                    <div class="row">
                                        <!-- Display Erro/Success Message -->
                                        @include('inc/message')


                                        {{ Form::open(['url'=>'hr/reports/emp_performance_save', 'class'=>'form-horizontal']) }}
                                        <div class="col-xs-12">
                                            <div class="col-xs-6" style=" padding: 10px 0px  10px 5px;">
                                                    <h2>Employee Information</h2>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label no-padding-right" for="associate_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="col-xs-10 col-sm-11" name="associate_id" id="associate_id" value="{{$associateId}}" readonly="readonly" />
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label no-padding-right" for="hr_pa_as_name"> Associate's Name <span style="color: red; vertical-align: top;">&#42;</span></label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="hr_pa_as_name" value="{{$employeeDetails->as_name}}" class="col-xs-10 col-sm-11" placeholder="Associate's Name"  data-validation="required" readonly/>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label no-padding-right" for="hr_pa_department"> Department <span style="color: red; vertical-align: top;">&#42;</span></label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="hr_pa_department" value="{{$employeeDetails->department['hr_department_name']}}" placeholder="Department" class="col-xs-10 col-sm-11" data-validation="required" readonly/>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label no-padding-right" for="unit_floor_line">Unit/Floor/Line</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" id="unit_floor_line" value="{{'Unit: '.$employeeDetails->unit['hr_unit_name']}} {{'Floor: '.$employeeDetails->floor['hr_floor_name']}}. {{'Line: '.$employeeDetails->line['hr_line_name']}}" placeholder="Line" class="col-xs-10 col-sm-11" readonly/>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="date" value="{{$date}}">
                                            </div>
                                            <div class="col-xs-6" style=" padding: 10px 0px  10px 5px;">
                                                <h2>Employee Performance (1 Year)</h2>
                                                <hr>
                                                @php
                                                    $presentPercentage = 0;
                                                    $absentPercentage = 0;
                                                    $halfPercentage = 0;
                                                    $latePercentage = 0;
                                                    $leavePercentage = 0;
                                                    $persent = $getPresentOT->present!=null?$getPresentOT->present:0;
                                                    $totalDays = $persent+$leaveCount+$absentCount;
                                                    if($persent != 0 && $totalDays != 0) {
                                                        $presentPercentage = round(($persent*100)/$totalDays);
                                                    }
                                                    if($absentCount != 0 && $totalDays != 0) {
                                                        $absentPercentage = round(($absentCount*100)/$totalDays);
                                                    }
                                                    if($leaveCount != 0 && $totalDays != 0) {
                                                        $leavePercentage = round(($leaveCount*100)/$totalDays);
                                                    }
                                                    if($lateCount != 0 && $persent != 0) {
                                                        $latePercentage = round(($lateCount*100)/$persent);
                                                    }
                                                    if($halfCount != 0 && $persent != 0) {
                                                        $halfPercentage = round(($halfCount*100)/$persent);
                                                    }
                                                @endphp
                                                <div class="progress pos-rel" data-percent="Present: {{$presentPercentage}}% ({{$getPresentOT->present}})" width="100%">
                                                    <div class="progress-bar progress-bar-success" style="width:{{$presentPercentage}}%;"></div>
                                                </div>
                                                <div class="progress pos-rel" data-percent="Absent: {{$absentPercentage}}% ({{$absentCount}})" width="100%">
                                                    <div class="progress-bar progress-bar-warning" style="width:{{$absentPercentage}}%;"></div>
                                                </div>
                                                <div class="progress pos-rel" data-percent="Late: {{$latePercentage}}% ({{$lateCount}})" width="100%">
                                                    <div class="progress-bar progress-bar-info" style="width:{{$latePercentage}}%;"></div>
                                                </div>
                                                <div class="progress pos-rel" data-percent="Leave: {{$leavePercentage}}% ({{$leaveCount}})" width="100%">
                                                    <div class="progress-bar progress-bar-danger" style="width:{{$leavePercentage}}%;"></div>
                                                </div>
                                                <div class="progress pos-rel" data-percent="Half Day: {{$halfPercentage}}% ({{$halfCount}})" width="100%">
                                                    <div class="progress-bar progress-bar-warning" style="width:{{$halfPercentage}}%;"></div>
                                                </div>
                                                <div id="accordion" class="accordion-style1 panel-group">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false">
                                                                    <i class="bigger-110 ace-icon fa fa-angle-right" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i>
                                                                    &nbsp;Disciplinary Records 
                                                                    @if(!empty($disciplinaryList))
                                                                        <span class="label label-danger arrowed-in arrowed-in-right pull-right">Found</span>
                                                                    @else
                                                                        <span class="label label-success arrowed-in arrowed-in-right pull-right">Not Found</span>
                                                                    @endif
                                                                </a>
                                                            </h4>
                                                        </div>

                                                        <div class="panel-collapse collapse" id="collapseOne" aria-expanded="false" style="height: 0px;">
                                                            <div class="panel-body">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Griever Id</th>
                                                                            <th>Reason</th>
                                                                            <th>Action</th>
                                                                            <th>Discussed Date</th>
                                                                            <th>Date of Execution</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @if(!empty($disciplinaryList))
                                                                            @foreach($disciplinaryList as $desciplinary)
                                                                                <tr>
                                                                                    <td>{{$desciplinary->dis_re_griever_id}}</td>
                                                                                    <td>{{$desciplinary->issue}}</td>
                                                                                    <td>{{$desciplinary->step}}</td>
                                                                                    <td>{{$desciplinary->dis_re_discussed_date}}</td>
                                                                                    <td>
                                                                                        @php
                                                                                            $start= (!empty($desciplinary->dis_re_doe_from)? (date('d-M-y',strtotime($desciplinary->dis_re_doe_from))):null);
                                                                                            $to= (!empty($desciplinary->dis_re_doe_to)? (date('d-M-y', strtotime($desciplinary->dis_re_doe_to))):null);
                                                                                            $date_of_execution= $start. " <b>to</b> ".$to;
                                                                                            echo $date_of_execution;
                                                                                        @endphp
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="5" class="text-center">No Data Found</td>
                                                                            </tr>
                                                                        @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <textarea name="comments" id="" cols="30" rows="5" class="form-control" placeholder="Commente"></textarea>
                                            </div>
                                            @if(empty($approvalComplete))
                                                <button type="submit" id="rejectBtn" name="reject" value="reject" class="btn btn-sm btn-warning pull-right">
                                                    <i class="ace-icon fa fa-close"></i>
                                                    Reject
                                                </button>
                                            @endif
                                        </div>
                                        <!-- PAGE CONTENT ENDS -->
                                    </div>
                                </div>

                                <div class="step-pane" data-step="2">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <input type="hidden" value="{{!empty($incrementData)?$incrementData->id:''}}" name="incrementid">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="applied_date"> Applied Date </label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="applied_date" id="applied_date" class="datepicker col-xs-12 filter" placeholder="Enter Date" {{!empty($incrementData)?'value='.$incrementData->applied_date.' disabled':''}}>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="effective_date"> Effective Date </label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="effective_date" id="effective_date" class="datepicker col-xs-12 filter" placeholder="Enter Date" {{!empty($incrementData)?'value='.$incrementData->effective_date.' disabled':''}}>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="increment_amount"> Increment Amount/Percentage  </label>
                                                <div class="col-sm-5">
                                                    <input type="text" name="increment_amount" id="increment_amount" placeholder="Increment Amount/Percentage" class="col-xs-12" {{!empty($incrementData)?'value='.$incrementData->increment_amount.' disabled':''}}>
                                                </div>
                                                <div class="col-sm-3">
                                                    <select class="no-select col-xs-12" id="amount_type" name="amount_type" {{!empty($incrementData)?'disabled':''}}>
                                                        <option value="1" {{!empty($incrementData)?Custom::sselected($incrementData->amount_type,1):''}}>Increased Amount</option>
                                                        <option value="2" {{!empty($incrementData)?Custom::sselected($incrementData->amount_type,2):''}}>Percent</option>
                                                    </select>
                                                </div>
                                            </div>

                                            @if(empty($approvalComplete))
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label no-padding-right" for="increment_amount"> Forward  </label>
                                                    <div class="col-sm-8">
                                                        <select class="col-xs-12 select2" id="forward" name="forward">
                                                            <option value="">No Forward</option>
                                                            @if(!empty($positionList))
                                                                @foreach($positionList as $k=>$position)
                                                                    <option value="{{$position->associate_id}}">{{$position->as_name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="increment_amount"> History List  </label>
                                                <div class="col-sm-8">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Date</th>
                                                                <th>Submit By</th>
                                                                <th>Submit To</th>
                                                                <th>Comments</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(!empty($approvalHistory))
                                                                @foreach($approvalHistory as $k=>$approval)
                                                                    <tr>
                                                                        <td>{{$k+1}}</td>
                                                                        <td>{{$approval->date}}</td>
                                                                        <td>{{$approval->submit_by}}</td>
                                                                        <td>{{$approval->submit_to}}</td>
                                                                        <td>{{$approval->comments}}</td>
                                                                        <td>
                                                                            @php
                                                                                if($approval->status == 0) {
                                                                                    echo 'Reject';
                                                                                } else if($approval->status == 1) {
                                                                                    echo 'Forwarded';
                                                                                } else if($approval->status == 2) {
                                                                                    echo 'Approved';
                                                                                }
                                                                            @endphp
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="6" class="text-center"></td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="wizard-actions">
                            <button type="button" id="prevBtn" class="btn btn-prev">
                                <i class="ace-icon fa fa-arrow-left"></i>
                                Back
                            </button>

                            <button type="button" id="nextBtn" class="btn btn-info btn-next" data-last="Finish">
                                Next
                                <i class="ace-icon fa fa-arrow-right icon-on-right"></i>
                            </button>
                        </div>
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div>
            {{ Form::close() }}
        </div>
    </div><!-- /.page-content -->
</div>
<script src="http://ace.jeka.by/assets/js/wizard.min.js"></script>
<script src="http://ace.jeka.by/assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
    jQuery(function($) {
        var $validation = false;
        $('#fuelux-wizard-container')
        .ace_wizard({
            //step: 2 //optional argument. wizard will jump to step "2" at first
            //buttons: '.wizard-actions:eq(0)'
        })
        .on('actionclicked.fu.wizard' , function(e, info){
            if(info.direction == 'next' || info.direction == 1) {
                @if(!empty($approvalComplete))
                    $('#nextBtn').attr('data-last','Already Approved');
                @endif
            } else {
                @if(!empty($approvalComplete))
                    $('#nextBtn').html('Next<i class="ace-icon fa fa-arrow-right icon-on-right"></i>');
                @endif
            }
            console.log(info);
        })
        .on('finished.fu.wizard', function(e) {
            var appDate = $('#applied_date').val();
            var effDate = $('#effective_date').val();
            var incAmount = $('#increment_amount').val();
            if(appDate != '' && effDate != '' && incAmount != '') {
                // console.log(appDate,effDate);
                @if(empty($approvalComplete))
                    $('#nextBtn').attr('type','submit');
                @endif
            } else {
                alert('Please fill all field properly.');
            }
        }).on('stepclick.fu.wizard', function(e){
            //e.preventDefault();//this will prevent clicking and selecting steps
        });
        $('#modal-wizard-container').ace_wizard();
        $('#modal-wizard .wizard-actions .btn[data-dismiss=modal]').removeAttr('disabled');
    })
</script>
@endsection