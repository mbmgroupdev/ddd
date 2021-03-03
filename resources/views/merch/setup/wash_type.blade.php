@extends('merch.layout')
@section('title', 'Wash Type')
@push('css')
<style type="text/css">
    @media only screen and (max-width: 767px) {
        
        .dataTables_wrapper .col-sm-12{width: 100%; overflow-x: auto; display: block;}
        
    }
    
</style>
@endpush
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Wash Type </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Wash Type</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/wash_type')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            
                            <div class="row">
                              <div class="col-10">
                                <div class="form-group has-required has-float-label select-search-group">
                                  {{ Form::select('wash_category', $washCategory, null, ['placeholder'=>'Select Wash Category','id'=>'wash_category','class'=> 'form-control', 'required']) }}
                                  <label for="wash_category" > Wash Category </label>
                                  
                                </div>
                              </div>
                              <div class="col-2">
                                <button type="button" class="btn btn-sm btn-success" style="padding: 3px;" data-toggle="modal" data-target="#categoryModal">+</button>
                              </div>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="text" id="wash_name" name="wash_name" placeholder="Enter Wash Type Name" class="form-control" autocomplete="off" />
                                <label for="wash_name" > Wash Name </label>
                                
                            </div>

                            <div class="form-group has-required has-float-label">
                                <input type="text" id="process_time" name="process_time" placeholder="Enter Time in Minutes" class="form-control" autocomplete="off" />
                                <label for="process_time" > Process Time</label>
                                
                            </div>

                            <div class="form-group has-required has-float-label">
                                <input type="text" id="chemical" name="chemical" placeholder="Enter Chemical" class="form-control" autocomplete="off" />
                                <label for="chemical" > Chemical </label>
                                
                            </div>

                            <div class="form-group has-required has-float-label">
                                <input type="text" id="consumption_rate" name="consumption_rate" placeholder="Enter Consumption Rate " class="form-control" autocomplete="off" />
                                <label for="consumption_rate" > Consumption Rate </label>
                                
                            </div>
                            <div class="form-group">
                                <button class="btn btn-outline-success" type="submit">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </div>                                 
                        </form>  
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                        <table id="global-datatable" class="table table-bordered  table-hover">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Wash Category</th>
                                    <th>Wash Name</th>
                                    <th>Process Time</th>
                                    <th>Chemical</th>
                                    <th>Consumption Rate</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($washList as $wash)
                                  <tr >
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $wash->mr_wash_category->category_name }}</td>
                                    <td>{{ $wash->wash_name }}</td>
                                    <td>{{ $wash->process_time }}</td>
                                    <td>{{ $wash->chemical }}</td>
                                    <td>{{ $wash->consumption_rate }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            

                                            <a href="{{ url('merch/setup/wash_type_delete/'.$wash->id) }}" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                  </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    <!-- Modal -->
                    <div id="categoryModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                                <!-- Modal content-->

                            <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/wash_category_add')}}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Add Wash Category</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="form-group">
                                    <label class="col-sm-3 control-label no-padding-right" for="wash_category" >Category Name<span style="color: red">&#42;</span> </label>
                                    <div class="col-sm-6">
                                        <input type="text" name="wash_category" id="wash_category" placeholder="Wash Category"  class="col-xs-12" value="" data-validation="required length custom" data-validation-length="1-45"/>
                                    </div>
                                    </div>
                                    
                                    <div class="space-10"></div>

                                </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                                    <button class="btn btn-success btn-sm" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Save</button>

                                  </div>
                                </div><!--Modal content end-->
                            </form>

                        </div>
                    </div><!--Modal end-->
                    {{-- <div class="popover bs-popover-left content-section-popover popover-left" id="content-section-popover" role="tooltip" id="popover" x-placement="left" style="display:none;position:absolute;z-index:1; width: 100%; box-shadow: 0px 1px 7px 1px;">
                        <div class="arrow" style="top: 37px;"></div>
                        <h3 class="popover-header"><span id="popover-header"></span>  <i class="fa fa-close popover-close"></i></h3>
                        <div class="popover-body">
                            <br>
                            <div class="form-group has-float-label has-required select-search-group">
                              {{ Form::select('buyerid', $buyerList, null, ['placeholder'=>'Select Buyer','id'=>'buyer-id','class'=> 'form-control', 'required']) }}
                              <label for="buyer-id">Buyer</label>
                            </div>
                            <input type="hidden" id="se_id" value="">
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="Wash Type" placeholder="Enter Wash Type Name" class="form-control" autocomplete="off" />
                              <label for="Wash Type" > Wash Type Name </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="start-month-year" placeholder="Enter Start Month-Year" class="form-control" autocomplete="off" />
                              <label for="start-month-year" > Start Month-Year </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                              <input type="text" id="end-month-year" placeholder="Enter End Month-Year" class="form-control" autocomplete="off" />
                              <label for="end-month-year" > End Month-Year </label>
                            </div>
                        </div>
                        <div class="popover-footer">
                            
                            <button type="button" class="btn btn-outline-success btn-sm sample-change-btn" data-status="2" style="font-size: 13px; margin-left: 7px; margin-bottom: 8px;"><i class="fa fa-save"></i> Save</button>
                            <button type="button" class="btn btn-outline-danger btn-sm sample-close-btn" data-status="2" style="font-size: 13px; margin-left: 7px; margin-bottom: 8px;"><i class="fa fa-close"></i> Cancel</button>
                        </div>
                    </div> --}}
                </div>
            </div>    
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')

@endpush
@endsection
