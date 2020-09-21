@extends('hr.layout')
@section('title', 'Maternity Leave Application')
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
                    <a href="#"> Operation </a>
                </li>
                <li>
                    <a href="#"> Maternity Leave </a>
                </li>
                <li class="active">Medical </li>
            </ul>
        </div>

        @include('inc/message')
        <div class="panel panel-success" style="">
            <div class="panel-heading page-headline-bar">
                <h6>
                    Maternity Leave Medical
                    <a href="{{url('hr/operation/maternity-leave/list')}}" target="_blank" class="btn btn-primary pull-right" >List <i class="fa fa-list bigger-120"></i></a>
                </h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    
                    <div class="col-sm-3">        
                        <div class="user-details-block" style="padding-top: 0.5rem;">
                            <div class="user-profile text-center mt-0">
                                <img id="avatar" class="avatar-130 img-fluid" src="{{ $employee->as_pic }} " >
                            </div>
                            <div class="text-center mt-3">
                                <h4><b id="name">{{ $employee->as_name }}</b></h4>
                                <p class="mb-0" id="designation">
                                {{ $employee->hr_designation_name }}, {{$employee->hr_department_name}}</p>
                                <p class="mb-0" id="designation">
                                {{$employee->hr_unit_name}}</p>
                            </div>
                             <table style="width: 100%;" border="0">
                                 <tr>
                                     <td><i class="field-title">Oracle ID</i></td>
                                     <td class="field-data">: {{ $employee->as_oracle_code }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Associate ID</i></td>
                                     <td class="field-data">: {{ $employee->associate_id }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Name</i></td>
                                     <td class="field-data">: {{ $leave->husband_name }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Occupation</i></td>
                                     <td class="field-data">: {{ $leave->husband_occupasion }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Husband Age</i></td>
                                     <td class="field-data">: {{ $leave->husband_age }}</td>
                                 </tr>
                                 <tr>
                                     <td><i class="field-title">Total Child</i> <span class="field-data">: {{ ($leave->no_of_son + $leave->no_of_daughter) }}</span></td>
                                     <td><i class="field-title">Last Child Age</i> <span class="field-data">: {{ $leave->last_child_age }} </span></td>
                                 </tr>
                             </table>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div id="accordion" class="accordion-style panel-group">
                            
                            <a class="accordion-toggle mb-3 d-block @if($leave->medical) collapsed @endif" data-toggle="collapse" data-parent="#accordion" href="#basic-service"><span class="header-title"> Basic Checkup </span> </a>
                            
                           <div class="panel-collapse in collapse pt-3 @if(!$leave->medical)show @endif" id="basic-service">
                                <form action="{{url('hr/operation/maternity-medical-basic')}}" method="post" class="needs-validation" novalidate>
                                    @csrf
                                    @if($leave->medical)
                                    <input type="hidden" name="hr_maternity_medical_id" value="{{$leave->medical->id}}">
                                    @endif
                                    <input type="hidden" name="hr_maternity_leave_id" value="{{$leave->id}}">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group has-required has-float-label  select-search-group">
                                                @php 
                                                    $bloodgroups = array(
                                                        'A+' =>'A+',
                                                        'A-' =>'A-',
                                                        'B+' =>'B+',
                                                        'B-' =>'B-',
                                                        'AB+' =>'AB+',
                                                        'AB-' =>'AB-',
                                                        'O+' =>'O+',
                                                        'O-' => 'O-'
                                                    );

                                                    $blood = $employee->med_blood_group;
                                                    if(isset($leave->medical->blood_group)){
                                                        $blood = $leave->medical->blood_group??$blood;
                                                    }
                                                @endphp

                                                {{ Form::select('blood_group',$bloodgroups  , $blood, ['placeholder'=>'Select Blood Group', 'id'=>'blood_group', 'class'=> 'blood_group form-control', 'required'=>'required']) }}
                                                <label >Blood Group</label>
                                                
                                            </div>
                                            <div class="form-group  has-float-label ">
                                                <input id="lmp" type="date" name="lmp" class="form-control" value="{{$leave->medical->lmp??null}}" >
                                                <label for="lmp">LMP</label>
                                            </div>
                                            <div class="form-group has-required  has-float-label ">
                                                <input id="edd" type="date" name="edd" class="form-control" placeholder="Enter Anemia Problem"   min="{{date('Y-m-d')}}" required value="{{$leave->medical->edd??null}}">
                                                <label for="edd">EDD</label>
                                            </div>

                                            <div class="form-group  has-float-label ">
                                                <input id="anemia" type="text" name="anemia" class="form-control"   value="{{$leave->medical->anemia??'NAD'}}">
                                                <label for="anemia">Anemia</label>
                                            </div>
                                            <div class="form-group  has-float-label ">
                                                <input id="heart" type="text" name="heart" class="form-control"   value="{{$leave->medical->heart??'NAD'}}" placeholder="Enter Heart Problem">
                                                <label for="heart">Heart</label>
                                            </div>

                                            
                                            
                                        </div>
                                        <div class="col-sm-4">

                                            <div class="form-group  has-float-label ">
                                                <input id="lungs" type="text" name="lungs" class="form-control"   value="{{$leave->medical->lungs??'NAD'}}" placeholder="Enter Lungs Problem">
                                                <label for="lungs">Lungs</label>
                                            </div>
                                            <div class="form-group  has-float-label ">
                                                <input id="rash" type="text" name="rash" class="form-control"   value="{{$leave->medical->rash??'NAD'}}" placeholder="Enter Rash Problem">
                                                <label for="rash">Rash</label>
                                            </div>
                                            <div class="form-group  has-float-label ">
                                                <input id="rash" type="text" name="others" class="form-control"   value="{{$leave->medical->others??''}}" placeholder="Ulcer/Thrush/Others">
                                                <label for="rash">Others</label>
                                                
                                            </div>
                                            <div class="form-group  has-float-label ">
                                                <input id="past_major_diseases" type="text" name="past_major_diseases" class="form-control"   value="{{$leave->medical->past_major_diseases??''}}" placeholder="Past Major Diseases">
                                                <label for="past_major_diseases">Past Major Diseases</label>
                                                
                                            </div>
                                            
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <div class="custom-switch-22 custom-control custom-switch custom-switch-icon custom-control-inline ">
                                                 
                                                  <div class="custom-switch-inner text-left">
                                                     <input type="checkbox" class="custom-control-input" name="pregnant_complexity" value="1" @if($leave->medical && $leave->medical->pregnant_complexity == 1) checked @endif id="pregnancy_complexity" onclick="opendiv('pc-details',event)">
                                                     <label class="custom-control-label" for="pregnancy_complexity">
                                                     <span class="switch-icon-left"><i class="fa fa-check"></i></span>
                                                     <span class="switch-icon-right"><i class="fa fa-check"></i></span>
                                                     </label> &nbsp;
                                                    Pregnant Complexity
                                                  </div>
                                               </div>
                                            </div>
                                            <div id="pc-details" class="form-group  has-float-label" @if($leave->medical && $leave->medical->pregnant_complexity == 1) @else style="display:none;" @endif>
                                                <input id="pregnancy_complexity_details" type="text" name="pregnant_complexity_details" class="form-control"   value="{{$leave->medical->pregnant_complexity_details??''}}" placeholder="Prgnancy complexity details">
                                                <label for="pregnancy_complexity_details">Pregnancy Complexity Details</label>
                                            </div>

                                            <div class="form-group">
                                                <div class="custom-switch-22 custom-control custom-switch custom-switch-icon custom-control-inline ">
                                                 
                                                  <div class="custom-switch-inner text-left">
                                                     <input type="checkbox" name="operation" value="1" @if($leave->medical && $leave->medical->operation == 1) checked @endif class="custom-control-input" id="operation_c" onclick="opendiv('operation-details',event)">
                                                     <label class="custom-control-label" for="operation_c">
                                                     <span class="switch-icon-left"><i class="fa fa-check"></i></span>
                                                     <span class="switch-icon-right"><i class="fa fa-check"></i></span>
                                                     </label> &nbsp;
                                                    Operation
                                                  </div>
                                               </div>
                                            </div>
                                            <div id="operation-details" class="form-group  has-float-label" @if($leave->medical && $leave->medical->operation == 1) @else style="display:none;" @endif>
                                                <input id="operation_details" type="text" name="operation_details" class="form-control"   value="{{$leave->medical->operation_details??''}}" placeholder="Prgnancy complexity details">
                                                <label for="operation_details">Operation Details</label>
                                            </div>
                                            
                                            <div class="form-group">
                                                <div class="custom-switch-22 custom-control custom-switch custom-switch-icon custom-control-inline ">
                                                 
                                                  <div class="custom-switch-inner text-left">
                                                     <input type="checkbox" name="stl_rtl" value="1" @if($leave->medical &&  $leave->medical->stl_rtl == 1) checked @endif class="custom-control-input" id="stl_rtl" onclick="opendiv('stl_rtl-details',event)">
                                                     <label class="custom-control-label" for="stl_rtl">
                                                     <span class="switch-icon-left"><i class="fa fa-check"></i></span>
                                                     <span class="switch-icon-right"><i class="fa fa-check"></i></span>
                                                     </label> &nbsp;
                                                    STL/RTL
                                                  </div>
                                               </div>
                                            </div>
                                            <div id="stl_rtl-details" class="form-group  has-float-label" @if($leave->medical && $leave->medical->stl_rtl == 1) @else style="display:none;" @endif>
                                                <input id="stl_rtl_details" type="text" name="stl_rtl_details" class="form-control"   value="{{$leave->medical->stl_rtl_details??''}}" placeholder="Prgnancy complexity details">
                                                <label for="stl_rtl_details">STL/RTL Details</label>
                                            </div>

                                            <div class="form-group">
                                                <div class="custom-switch-22 custom-control custom-switch custom-switch-icon custom-control-inline ">
                                                 
                                                  <div class="custom-switch-inner text-left">
                                                     <input type="checkbox" class="custom-control-input" name="drug_addiction" value="1" @if($leave->medical &&  $leave->medical->drug_addiction == 1) checked @endif id="drug_addiction" onclick="opendiv('drug_addiction-details',event)">
                                                     <label class="custom-control-label" for="drug_addiction">
                                                     <span class="switch-icon-left"><i class="fa fa-check"></i></span>
                                                     <span class="switch-icon-right"><i class="fa fa-check"></i></span>
                                                     </label> &nbsp;
                                                    Drug Addiction
                                                  </div>
                                               </div>
                                            </div>
                                            <div id="drug_addiction-details" class="form-group  has-float-label" @if($leave->medical &&  $leave->medical->drug_addiction == 1) @else style="display:none;" @endif>
                                                <input id="drug_addiction_details" type="text" name="drug_addiction_details" class="form-control"   value="{{$leave->medical->drug_addiction_details??''}}" placeholder="Prgnancy complexity details">
                                                <label for="drug_addiction_details">Drug Addiction Details</label>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-switch-22 custom-control custom-switch custom-switch-icon custom-control-inline ">
                                                 
                                                  <div class="custom-switch-inner text-left">
                                                     <input type="checkbox" class="custom-control-input" id="alergy" name="alergy" value="1" @if($leave->medical &&  $leave->medical->alergy == 1) checked @endif>
                                                     <label class="custom-control-label" for="alergy">
                                                     <span class="switch-icon-left"><i class="fa fa-check"></i></span>
                                                     <span class="switch-icon-right"><i class="fa fa-check"></i></span>
                                                     </label> &nbsp;
                                                    Alergy
                                                  </div>
                                               </div>
                                            </div>
                                            <div class="form-group ">
                                                <button type="submit" class="btn btn-primary pull-right w-80">@if($leave->medical) Update @else Save @endif</button>
                                            </div>
                                        </div>
                                        <div class="col-sm-12"><hr></div>
                                    </div>
                                </form>
                           </div>
                        </div>
                        <h4 class="header-title mb-3 mt-3">Routine Checkup</h4>
                        @php $next_checkup = date('Y-m-d'); @endphp
                        @if($leave->medical)
                        <div class="iq-accordion career-style mat-style">
                            
                            @if($leave->medical->record)
                                @foreach($leave->medical->record as $key => $record)
                                    <div class="iq-accordion-block ">
                                       <div class="active-mat ">
                                          <div class="mat-container">
                                            <a class="accordion-title d-flex">
                                                <div class="rounded-div iq-bg-primary"><i class="las la-stethoscope f-18"></i></div> 
                                                <div class="media-support-info ml-3">
                                                  <h6>Checkup {{$key+1}}</h6>
                                                  <p id="line" class="mb-0">{{$record->checkup_date}}</p>
                                               </div>
                                            </a>
                                          </div>
                                       </div>
                                       <div class="accordion-details checkup-details">
                                            <form action="{{url('hr/operation/maternity-medical-record')}}" method="post" class="needs-validation" novalidate>
                                                @csrf
                                                <input type="hidden" name="hr_maternity_medical_record_id" value="{{$record->id}}">
                                                <div class="row mt-3">
                                                    <div class="col-sm-4">
                                                        <div class="form-group  has-float-label has-required">
                                                            <input type="date" id="checkup_date_{{$record->id}}" type="checkup_date" name="checkup_date" class="form-control"  value="{{$record->checkup_date}}" required> 
                                                            <label for="checkup_date">Checkup Date</label>
                                                        </div>
                                                        <div class="form-group  has-float-label has-required">
                                                            <input type="text" id="weight_{{$record->id}}" type="weight" name="weight" class="form-control"  value="{{$record->weight}}" placeholder="Enter weight" required>
                                                            <label for="weight">Weight</label>
                                                        </div>
                                                        <div class="form-group  has-float-label has-required">
                                                            <input type="text" id="bp_{{$record->id}}" type="bp" name="bp" class="form-control"  value="{{$record->bp}}" placeholder="Enter BP" required>
                                                            <label for="bp">BP</label>
                                                        </div>
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="edema_{{$record->id}}" type="edema" name="edema" class="form-control"  value="{{$record->edema??'N/A'}}">
                                                            <label for="edema">Edema</label>
                                                        </div>
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="jaundice_{{$record->id}}" type="jaundice" name="jaundice" class="form-control"  value="{{$record->jaundice??'N/A'}}">
                                                            <label for="jaundice">Jaundice</label>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="uterus_height_{{$record->id}}" type="uterus_height" name="uterus_height" class="form-control"  value="{{$record->uterus_height??'N/A'}}">
                                                            <label for="uterus_height">Uterus Height</label>
                                                        </div>
                                                        
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="baby_position_{{$record->id}}" type="baby_position" name="baby_position" class="form-control"  value="{{$record->baby_position??'N/A'}}">
                                                            <label for="baby_position">Baby Position</label>
                                                        </div>
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="baby_movement_{{$record->id}}" type="baby_movement" name="baby_movement" class="form-control"  value="{{$record->baby_movement??'N/A'}}">
                                                            <label for="baby_movement">Baby Movement</label>
                                                        </div>
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="baby_heartbeat_{{$record->id}}" type="baby_heartbeat" name="baby_heartbeat" class="form-control"  value="{{$record->baby_heartbeat??'N/A'}}">
                                                            <label for="baby_heartbeat">Baby Heartbeat</label>
                                                        </div>
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="albumine_{{$record->id}}" type="albumine" name="albumine" class="form-control"  value="{{$record->albumine??'N/A'}}">
                                                            <label for="albumine">Albumine</label>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-sm-4">
                                                        
                                                        
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="sugar_{{$record->id}}" type="sugar" name="sugar" class="form-control"  value="{{$record->sugar??'N/A'}}">
                                                            <label for="sugar">Sugar</label>
                                                        </div>
                                                        <div class="form-group  has-float-label ">
                                                            <input type="text" id="others_{{$record->id}}" type="others" name="others" class="form-control"  value="{{$record->others??'N/A'}}">
                                                            <label for="others">Others</label>
                                                        </div>
                                                        <div class="form-group  has-float-label ">
                                                            <textarea id="comments_{{$record->id}}" type="comments" name="comments" class="form-control"  placeholder="Enter doctor comments"></textarea> 
                                                            <label for="comments">Comments</label>
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-primary pull-right w-80">Update</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                       </div>
                                    </div>
                                    @php $next_checkup = $record->next_checkup_date??$next_checkup; @endphp
                                @endforeach
                            @endif
                            @if(!$leave->doctors_clearence)
                            <div class="iq-accordion-block accordion-active">
                               <div class="active-mat ">
                                  <div class="mat-container">
                                    <a class="accordion-title d-flex">
                                        <div class="rounded-div iq-bg-danger"><i class="las la-stethoscope f-18"></i></div> 
                                        <div class="media-support-info ml-3">
                                          <h6>Add Checkup</h6>
                                          <p id="line" class="mb-0">{{$next_checkup}}</p>
                                       </div>
                                    </a>
                                  </div>
                               </div>
                               <div class="accordion-details checkup-details">
                                    <form action="{{url('hr/operation/maternity-medical-record')}}" method="post" class="needs-validation" novalidate>
                                        @csrf
                                        <input type="hidden" name="hr_maternity_medical_id" value="{{$leave->medical->id}}">
                                        <div class="row mt-3">
                                            <div class="col-sm-4">
                                                <div class="form-group  has-float-label has-required">
                                                    <input type="date" id="checkup_date" type="checkup_date" name="checkup_date" class="form-control"  value="{{date('Y-m-d')}}" required> 
                                                    <label for="checkup_date">Checkup Date</label>
                                                </div>
                                                <div class="form-group  has-float-label has-required">
                                                    <input type="text" id="weight" type="weight" name="weight" class="form-control"  value="" placeholder="Enter weight" required>
                                                    <label for="weight">Weight</label>
                                                </div>
                                                <div class="form-group  has-float-label has-required">
                                                    <input type="text" id="bp" type="bp" name="bp" class="form-control"  value="" placeholder="Enter BP" required>
                                                    <label for="bp">BP</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="edema" type="edema" name="edema" class="form-control"  value="N/A">
                                                    <label for="edema">Edema</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="jaundice" type="jaundice" name="jaundice" class="form-control"  value="N/A">
                                                    <label for="jaundice">Jaundice</label>
                                                </div>
                                                
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="uterus_height" type="uterus_height" name="uterus_height" class="form-control"  value="N/A">
                                                    <label for="uterus_height">Uterus Height</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="baby_position" type="baby_position" name="baby_position" class="form-control"  value="N/A">
                                                    <label for="baby_position">Baby Position</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="baby_movement" type="baby_movement" name="baby_movement" class="form-control"  value="N/A">
                                                    <label for="baby_movement">Baby Movement</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="baby_heartbeat" type="baby_heartbeat" name="baby_heartbeat" class="form-control"  value="N/A">
                                                    <label for="baby_heartbeat">Baby Heartbeat</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="albumine" type="albumine" name="albumine" class="form-control"  value="N/A">
                                                    <label for="albumine">Albumine</label>
                                                </div>
                                                
                                            </div>
                                            <div class="col-sm-4">
                                                
                                                
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="sugar" type="sugar" name="sugar" class="form-control"  value="N/A">
                                                    <label for="sugar">Sugar</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <input type="text" id="others" type="others" name="others" class="form-control"  value="N/A">
                                                    <label for="others">Others</label>
                                                </div>
                                                <div class="form-group  has-float-label has-required">
                                                    <input type="date" id="next_checkup_date" type="next_checkup_date" name="next_checkup_date" class="form-control"  value="" required>
                                                    <label for="next_checkup_date">Next Checkup</label>
                                                </div>
                                                <div class="form-group  has-float-label ">
                                                    <textarea id="comments" type="comments" name="comments" class="form-control"  placeholder="Enter doctor comments"></textarea> 
                                                    <label for="comments">Comments</label>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary pull-right w-80">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                               </div>
                            </div>
                            @endif
                        </div>
                        @else
                            <p class="text-danger">Please complete the basic checkup first!</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@push('js')
    <script type="text/javascript">
        function opendiv(id, e){
            if (e.target.checked) {
                $('#'+id).show();
            }else{
                $('#'+id).hide();
            }
        }
    </script>
@endpush
@endsection