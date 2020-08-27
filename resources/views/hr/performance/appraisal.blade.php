@extends('hr.layout')
@section('title', 'Performance Appraisal')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Human Resource</a>
				</li>
                <li>Performance</li>
				<li class="active">Performance Appraisal</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content"> 
            @include('inc/message')
            <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Apprisal<a href="{{ url('hr/performance/appraisal_list')}}" class="pull-right btn  btn-primary">Apprisal list</a></h6>
                    </div> 
                    <div class="panel-body">  

                        {{ Form::open(['url'=>'hr/performance/appraisal', 'class'=>'form-horizontal']) }}
                            <div class="row justify-content-center">
                                <div class="col-sm-3">
                                    <div class="form-group has-required has-float-label  select-search-group">
                                        {{ Form::select('hr_pa_as_id', [], null, ['placeholder'=>'Select Associate\'s ID', 'id'=>'hr_pa_as_id', 'class'=> 'associates', 'required'=>'required']) }}
                                        <label  for="hr_pa_as_id"> Associate's ID</label>
                                    </div>
                                    <div class="form-group has-required has-float-label">
                                        <input type="text" id="hr_pa_as_name" placeholder="Line" class="form-control" readonly/>
                                        <label  for="hr_pa_as_name">Associate's Name</label>
                                    </div>
                                    <div class="form-group has-required has-float-label">
                                        <input type="text" id="hr_pa_department" placeholder="Line" class="form-control" readonly/>
                                        <label  for="hr_pa_department">Department</label>
                                    </div>
                                    <div class="form-group has-required has-float-label">
                                        <input type="text" id="unit_floor_line" placeholder="Line" class="form-control" readonly/>
                                        <label  for="unit_floor_line">Unit/Floor/Line</label>
                                    </div>

                                    <div class="form-group has-required has-float-label">
                                        <label  for="hr_pa_report_from"> Reporting From</label>
                                        <input type="date" name="hr_pa_report_from" id="hr_pa_report_from" class="form-control" required placeholder="Y-m-d"  />
                                    </div>
                                    <div class="form-group has-required has-float-label">
                                        <label  for="hr_pa_report_to"> Reporting To</label>
                                        <input type="date" name="hr_pa_report_to" id="hr_pa_report_to" class="form-control" required placeholder="Y-m-d" />
                                    </div>
                                    
                                </div>
                                    
                                
                                <div class="col-sm-6">
                                    <div class="user-details-block" style="border-left: 1px solid #d1d1d1;">
                                        <div class="user-profile text-center mt-0">
                                            <img id="avatar" class="avatar-130 img-fluid" src="{{ asset('assets/images/user/09.jpg') }} " onerror="this.onerror=null;this.src='{{ asset("assets/images/user/09.jpg") }}';">
                                        </div>
                                        <div class="text-center mt-3">
                                         <h4><b id="name">-------------</b></h4>
                                         <p class="mb-0" id="designation">
                                            --------------------------</p>
                                         <p class="mb-0" >
                                            Oracle ID: <span id="oracle_id" class="text-success">-------------</span>
                                         </p>
                                         <p class="mb-0" >
                                            Associate ID: <span id="associate_id" class="text-success">-------------</span>
                                         </p>
                                         <p  class="mb-0">Department: <span id="department" class="text-success">------------------------</span> </p>
                                         
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-9">
                                    <legend> <b style="font-size: 15px;"> Work Ethics </b></legend>
                                    <hr>
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_punctuality" style="text-align: left;"> Punctual To work <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_punctuality" name="hr_pa_punctuality" type="radio" class="ace" value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_punctuality" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_reasoning" style="text-align: left;"> Accepts Work Load without reasoning <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_reasoning" name="hr_pa_reasoning" type="radio" class="ace" value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_reasoning" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_job_acceptance" style="text-align: left;"> Completes given job within stipulated/acceptable time frame <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_job_acceptance" name="hr_pa_job_acceptance" type="radio" class="ace"  value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_job_acceptance" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_owner_sense" style="text-align: left;"> Sense of Ownership in all given responsibility <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_owner_sense" name="hr_pa_owner_sense" type="radio" class="ace" value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_owner_sense" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_rw_sense" style="text-align: left;">In all Interaction his/her sense of right & wrong reflects his ethical mind set <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_rw_sense" name="hr_pa_rw_sense" type="radio" class="ace" value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_rw_sense" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_idea_thought" style="text-align: left;">Does he/she accepts new ideas/chanllenges positively and thinks out of box  to acomplish <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_idea_thought" name="hr_pa_idea_thought" type="radio" class="ace" value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_idea_thought" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                   
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_coleague_interaction" style="text-align: left;">Interaction with colleagues is mostly positive <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_coleague_interaction" name="hr_pa_coleague_interaction" type="radio" class="ace"  value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_coleague_interaction" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_meet_kpi" style="text-align: left;">Was able to meet or exceed given KPIs <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_meet_kpi" name="hr_pa_meet_kpi" type="radio" class="ace" data-validation="required" value="1" />
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_meet_kpi" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                           
                            
                            <div class="row justify-content-center">
                                <div class="col-9"> 
                                    <legend ><b style="font-size: 15px;"> Reflection of Performance <span style="color: red; vertical-align: top;">&#42;</span></b></legend>
                                    <hr>
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_communication" style="text-align: left;"> Does he/she  can communicate both verbally and writing lucidly and rationall? <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_communication" name="hr_pa_communication" type="radio" class="ace" data-validation="required" value="1"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_communication" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_cause_analysis" style="text-align: left;"> In every given issues can he/she do the root cause analysis and resolve methodology? <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_cause_analysis" name="hr_pa_cause_analysis" type="radio" class="ace" data-validation="required" value="1"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_cause_analysis" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_professionality" style="text-align: left;"> Instead of working hard; is he/she is smart in discharging given job proessionally? <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_professionality" name="hr_pa_professionality" type="radio" class="ace" data-validation="required" value="1"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_professionality" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_target_set" style="text-align: left;"> Does he/she hit the target as set forth? <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_target_set" name="hr_pa_target_set" type="radio" class="ace" data-validation="required" value="1"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_target_set" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_job_interest" style="text-align: left;">Does he/she places job interest above self-interest? <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_job_interest" name="hr_pa_job_interest" type="radio" class="ace" data-validation="required" value="1"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_job_interest" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_out_perform" style="text-align: left;">Does he/she outperform  his/her colleagues of he same Dept/Sec? <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_out_perform" name="hr_pa_out_perform" type="radio" class="ace" data-validation="required" value="1"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_out_perform" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                   
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_team_work" style="text-align: left;">Is he/she helpfull to the team members and work as a team? <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_team_work" name="hr_pa_team_work" type="radio" class="ace" data-validation="required" value="1"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_team_work" type="radio" class="ace"  value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- primaty assesment -->

                            
                            <div class="row justify-content-center">
                                
                            

                                <div class="col-sm-9" style="padding-top: 20px;">
                                    <legend ><b style="font-size: 15px;">Primary Assesment<span style="color: red; vertical-align: top;">&#42;</span></b></legend>
                                    <hr>

                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_primary_assesment" name="hr_pa_primary_assesment" type="radio" class="ace" data-validation="required" value="0" />
                                                    <span class="lbl"> DOES NOT MEETS EXPECTATION</span>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input name="hr_pa_primary_assesment" type="radio" class="ace" value="1" />
                                                    <span class="lbl"> PARTIALLY MEETS EXPECATATION</span>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input name="hr_pa_primary_assesment" type="radio" class="ace" value="2" />
                                                    <span class="lbl"> MEETS EXPECTATION SATISFACTORILY </span>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input name="hr_pa_primary_assesment" type="radio" class="ace" value="3" />
                                                    <span class="lbl"> EXCEEDS SATISFACTIONS</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- professional attributes -->

                            
                            <div class="row justify-content-center">
                                
                            

                                <div class="col-sm-9" style="padding-top: 20px;">
                                    <legend ><b style="font-size: 15px;">Professional Attributes that Needs Improvement (3)</b></legend>
                                    <hr>
                                    <div class="form-group has-float-label">
                                        <input name="hr_pa_first_attribute" type="text" id="hr_pa_first_attribute" class="form-control" />
                                        <label># 1</label>                          
                                    </div>
                                    <div class="form-group has-float-label">
                                        <input name="hr_pa_second_attribute" type="text" id="hr_pa_second_attribute" class="form-control" data-validation="length" />
                                        <label># 2</label>                           
                                    </div>

                                    <div class="form-group has-float-label">
                                        <input name="hr_pa_third_attribute" type="text" id="hr_pa_third_attribute" class="form-control" />      
                                        <label># 3</label>                      
                                    </div>
                                </div>
                            </div>

                            <!-- primaty assesment -->

                            
                            <div class="row justify-content-center">
                                    
                                 
                                <div class="col-sm-9" style="padding-top: 20px;">
                                    <legend ><b style="font-size: 15px;">Final Assesment by the Appraisal</b></legend>
                                    <hr>
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_long_retention" style="text-align: left;"> Worth for long term retention <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_long_retention" name="hr_pa_long_retention" type="radio" class="ace" value="1" data-validation="required" />
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_long_retention" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_promotion_recommendation" style="text-align: left;"> Increment/Promotion recommended <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_promotion_recommendation" name="hr_pa_promotion_recommendation" type="radio" class="ace"  data-validation="required" value="1" />
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_promotion_recommendation" type="radio" class="ace" value="0" />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-7 control-label no-padding-right" for="hr_pa_replacement" style="text-align: left;"> Needs to be replaced <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                        <div class="col-sm-5">
                                            <div class="radio">
                                                <label>
                                                    <input id="hr_pa_replacement" name="hr_pa_replacement" type="radio" class="ace" value="1" data-validation="required"/>
                                                    <span class="lbl"> Yes</span>
                                                </label>
                                                <label>
                                                    <input name="hr_pa_replacement" type="radio" class="ace" value="0"  />
                                                    <span class="lbl"> No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- remarks -->

                            
                            <div class="row justify-content-center">
                                
                            

                                <div class="col-sm-9" style="padding-top: 20px;">
                                    <legend ><b style="font-size: 15px;">Remarks</b></legend>
                                    <hr>
                                    <div class="form-group has-float-label">
                                        <input name="hr_pa_remarks_dept_head" type="text" id="hr_pa_remarks_dept_head" placeholder="Dept. Head Remarks" class="form-control"/>                         
                                        <label  for="hr_pa_remarks_dept_head"  > Dept.  Head </label>
                                    </div>

                                    <div class="form-group has-float-label">
                                        <input name="hr_pa_remarks_hr_head" type="text" id="hr_pa_remarks_hr_head" placeholder="HR Head Remarks" class="form-control" />
                                        <label  for="hr_pa_remarks_hr_head"> HR Head </label>
                                    </div>

                                    <div class="form-group has-float-label">
                                        <input name="hr_pa_remarks_incharge" type="text" id="hr_pa_remarks_incharge" placeholder="Factory In Charge Comments" class="form-control" />                          
                                        <label  for="hr_pa_remarks_incharge" > Factory In Charge </label>
                                    </div>

                                    <div class="form-group has-float-label">
                                        <input name="hr_pa_remarks_ceo" type="text" id="hr_pa_remarks_ceo" placeholder="CEO's Remarks" class="form-control" />                        
                                        <label  for="hr_pa_remarks_ceo" > CEO </label>
                                    </div>
                                   
                                </div>
                            </div>
                        
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <button class="btn pull-right btn-success" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
            </div>   
        </div>
    </div><!-- /.page-content -->
</div>
@push('js')
<script type="text/javascript">
$(document).ready(function()
{
    function formatState (state) {
        //console.log(state.element);
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "/user/pages/images/flags";
        var $state = $(
        '<span><img /> <span></span></span>'
        );
        // Use .text() instead of HTML string concatenation to avoid script injection issues
        var targetName = state.name;
        $state.find("span").text(targetName);
        // $state.find("img").attr("src", baseUrl + "/" + state.element.value.toLowerCase() + ".png");
        return $state;
    };

    $('select.associates').select2({
        templateSelection:formatState,
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
                            text: $("<span><img src='"+(item.as_pic ==null?'/assets/images/avatars/profile-pic.jpg':item.as_pic)+"' height='50px' width='auto'/> " + item.associate_name + "</span>"),
                            id: item.associate_id,
                            name: item.associate_name
                        }
                    }) 
                };
          },
          cache: true
        }
    });



    // retrive all information 
    var name       = $("#hr_pa_as_name");
    var department = $("#hr_pa_department");
    var line       = $("#unit_floor_line");
    $('body').on('change', '.associates', function(){
        $.ajax({
            url: '{{ url("hr/associate") }}',
            dataType: 'json',
            data: {associate_id: $(this).val()},
            success: function(data)
            {
                console.log(data);
                name.val(data.as_name);
                department.val(data.hr_department_name);
                line.val(data.unit_floor_line);
                $('#associate_id').text(data.associate_id);
                $('#oracle_id').text(data.as_oracle_code);
                $('#name').text(data.as_name);
                $('#department').text(data.hr_department_name);
                $('#designation').text(data.hr_designation_name);
                $('#doj').text(data.as_doj);
                if(data.as_pic == null){
                    if(data.as_gender == 'Male'){
                        $('#avatar').attr('src',url+'/assets/images/user/09.jpg');   
                    }
                    else{
                        $('#avatar').attr('src',url+'/assets/images/user/1.jpg');   
                    }
                }
                else{
                    $('#avatar').attr('src', url+data.as_pic);   
                }
            },
            error: function(xhr)
            {
                alert('failed...');
            }
        });
    });


    
    //date validation------------------
    $('#hr_pa_report_from').on('dp.change',function(){
        $('#hr_pa_report_to').val($('#hr_pa_report_from').val());    
    });

    $('#hr_pa_report_to').on('dp.change',function(){
        var end     = new Date($(this).val());
        var start   = new Date($('#hr_pa_report_from').val());
        if($('#hr_pa_report_from').val() == '' || $('#hr_pa_report_from').val() == null){
            alert("Please enter From-Date first");
            $('#hr_pa_report_to').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n From-Date is latest than To-Date");
                $('#hr_pa_report_to').val('');
            }
        }
    });
    //date validation end---------------

});
</script>
@endpush
@endsection