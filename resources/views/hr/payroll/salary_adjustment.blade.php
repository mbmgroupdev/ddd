@extends('hr.layout')
@section('title', 'Salary Adjustment')

@section('main-content')
@push('css')
<link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Payroll</a>
                </li>
                <li class="active"> Salary Adjustment</li>
            </ul>
        </div>

        <div class="page-content">
            <input type="hidden" id="base_url" value="{{ url('/') }}">
            
            <div class="row">
              <div class="col-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h6>
                            @php
                                $monthYear = $input['month_year'];
                            @endphp
                            Salary Adjustment of {{ date('M Y', strtotime($input['month_year'])) }}
                            <a href='{{ url("hr/payroll/monthly-salary-adjustment-list?month_year=$monthYear")}}' class="btn btn-sm btn-outline-success pull-right"><i class="fa fa-list"></i> Salary Adjustment List</a>
                        </h6>

                    </div>

                    <div class="panel panel-info">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/payroll/monthly-salary-adjustment-store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                        <div class="panel-body">
                            <input type="hidden" name="month_year" id="month_year" value="{{ $input['month_year']??date('Y-m')}}">
                            <div class='row'>
                                <div class='col-sm-12 table-wrapper-scroll-y table-custom-scrollbar'>
                                    <table class="table table-bordered table-hover table-fixed" id="itemList">
                                        <thead>
                                            <tr class="text-center active">
                                                <th width="2%">
                                                    <button class="btn btn-sm btn-outline-success addmore" type="button"><i class="las la-plus-circle"></i></button>
                                                </th>
                                                <th width="2%">SL.</th>
                                                <th width="12%">Associate ID</th>
                                                <th>Name</th>
                                                <th>Designation</th>
                                                <th>Department</th>
                                                <th width="8%">Advance Deduct</th>
                                                <th width="8%">Cg Deduct</th>
                                                <th width="8%">Food Deduct</th>
                                                <th width="8%">Other Deduct</th>
                                                <th width="8%">Salary Add</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem1" onClick="deleteItem(this.id)">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </td>
                                                <td>1</td>
                                                <td>
                                                  <input type="text" data-type="associateid" name="associate[]" id="associate_1" class="form-control autocomplete_txt" autofocus="autofocus" autocomplete="off" >
                                                </td>
                                                <td>
                                                  <input type="text" data-type="empname" name="name[]" id="name_1" class="form-control autocomplete_txt" autocomplete="off" required>
                                                </td>
                                                <td>
                                                  <input type="text" name="designation[]" id="designation_1" class="form-control" autofocus="autofocus" autocomplete="off" readonly>
                                                </td>
                                                <td>
                                                  <input type="text" name="department[]" id="department_1" class="form-control" autofocus="autofocus" autocomplete="off" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0" value="0" name="advdeduct[]" id="advdeduct_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0" value="0" name="cgdeduct[]" id="cgdeduct_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0" value="0" name="fooddeduct[]" id="fooddeduct_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0" value="0" name="otherdeduct[]" id="otherdeduct_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0" value="0" name="salaryadd[]" id="salaryadd_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="submit-invoice invoice-save-btn">
                                        <button type="submit" class="btn btn-outline-success btn-lg text-center"><i class="fa fa-save"></i> Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
              </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/js/salary-adjust.js')}}"></script>
   <script>
        $(document).on('keypress', function(e) {
            var that = document.activeElement;
            if( e.which == 13 ) {
                if($(document.activeElement).attr('type') == 'submit'){
                    return true;
                }else{
                    e.preventDefault();
                }
            }            
        });
    </script>
@endpush
@endsection