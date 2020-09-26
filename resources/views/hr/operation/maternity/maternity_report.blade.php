@extends('hr.layout')
@section('title', 'Maternity Leave Report')
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
                    <a href="#"> Report </a>
                </li>
                <li class="active">Maternity Leave </li>
                <li class="top-nav-btn">
                    <a href="{{url('hr/operation/maternity-leave/list')}}" target="_blank" class="btn btn-primary pull-right" >List <i class="fa fa-list bigger-120"></i></a>
                </li>
            </ul>
        </div>
        <div class="panel">
            <div class="panel-body">
                <table>
                    <tr>
                        <th>Sl</th>
                        <th>Photo</th>
                        <th>Associate ID</th>
                        <th>Name & Phone</th>
                        <th>Designation</th>
                        <th>Department</th>     
                        <th>Floor</th>
                        <th>Line</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td><img src="{{ $employee->as_pic }}" class='small-image' onError='this.onerror=null;this.src="/assets/images/avatars/avatar2.png"' style="height: 40px; width: auto;"></td>
                        <td>{{ $employee->associate_id }}</td>
                        <td>
                            <b>{{ $employee->as_name }}</b>
                            <p>{{ $employee->as_contact }}</p>
                        </td>
                        <td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
                        <td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
                        <td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
                        <td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
                        <td>
                            <a class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" rel='tooltip' data-tooltip-location='top' data-tooltip='Yearly Activity Report' ><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
              <div class="iq-card" id="result-section">
                <div class="iq-card-header d-flex mb-0">
                   <div class="iq-header-title w-100">
                      <div class="row">
                        <div class="col-3">
                          <h4 class="card-title capitalize inline">
                              <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('result-data')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
                              
                            </h4>
                        </div>
                        <div class="col-6 text-center">
                          <div id="head-arrow">
                            <h4 class="card-title capitalize inline">
                              <a class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month" >
                                <i class="las la-chevron-left"></i>
                              </a>
                              <b class="f-16" id="result-head"> </b>
                              <a class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Month" >
                                <i class="las la-chevron-right"></i>
                              </a>
                            </h4>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="row">
                            <div class="col-7 pr-0">
                              <div class="format">
                                <div class="form-group has-float-label select-search-group mb-0">
                                    <?php
                                        $type = ['as_unit_id'=>'N/A','as_line_id'=>'Line','as_floor_id'=>'Floor','as_department_id'=>'Department','as_designation_id'=>'Designation'];
                                    ?>
                                    {{ Form::select('report_group_select', $type, 'as_line_id', ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                                    <label for="reportGroupHead">Report Format</label>
                                </div>
                              </div>
                            </div>
                            <div class="col-5 pl-0">
                              <div class="text-right">
                                <a class="btn view grid_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                                  <i class="las la-th-large"></i>
                                </a>
                                <a class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                                  <i class="las la-list-ul"></i>
                                </a>
                                
                              </div>
                            </div>
                          </div>
                          
                          
                        </div>
                      </div>
                   </div>
                </div>
                <div class="iq-card-body no-padding">
                  <div class="result-data" id="result-data">
                    
                  </div>
                </div>
             </div>
              
            </div>
        </div>
    </div>
</div>
@endsection