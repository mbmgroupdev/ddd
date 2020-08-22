@extends('hr.layout')
@section('title', '')
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
                <li class="active">List of Unit Change</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="page-header">
                <h1>Opeartion<small><i class="ace-icon fa fa-angle-double-right"></i>List of Unit Change</small></h1>
            </div>

            <div class="row">
                {{-- <a href="{{url('hr/operation/employee_unit_change')}}" class="btn btn-info btn-sm pull-right" style="margin-bottom: 10px;margin-right: 12px;">Unit Change Entry</a> --}}
                <div class="col-sm-12">
                    <table id="unit_change_table" class="table table-striped table-bordered"> 
                        <thead>
                            <tr>
                                 <th colspan="5" class="align-center" style="background-color: darkgrey;border-right-width: 0px;"><h5>Employee Unit Change List</h5></th>
                                 <th colspan="2" class="align-center" style="background-color: darkgrey;padding-left: 0px;padding-right: 0px;border-left-width: 0px;">
                                    <a href="{{url('hr/operation/employee_unit_change')}}"  class="btn btn-sm btn-info" style=" width: 200px; ">Unit Change Entry</a>
                                 </th>
                            </tr>
                            <tr>
                                <th style="width: 20%;">Employee</th>
                                <th style="width: 20%;">Previous Unit</th>
                                <th style="width: 20%;">Changed Unit</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Salary</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="unit_change_table_body">
                            
                        </tbody>
                    </table>
                </div>
            </div> {{-- row-end --}}


        </div> {{-- page-content-end --}}
    </div> {{-- main-content-inner-end --}}
</div> {{-- main-content-end --}}




@endsection
