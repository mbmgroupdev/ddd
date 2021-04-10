@extends('hr.layout')
@section('title', 'Unit')
@section('main-content')
	@push('css')
	@endpush
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
		<ul class="breadcrumb">
			<li>
				<i class="ace-icon fa fa-home home-icon"></i>
				<a href="#"> Human Resource </a>
			</li> 
			<li>
				<a href="#"> Library </a>
			</li>
			<li class="active"> Unit </li>
		</ul><!-- /.breadcrumb --> 
	</div>
    <div class="row">
       <div class="col-lg-2 pr-0">
           <!-- include library menu here  -->
           @include('hr.settings.library_menu')
       </div>
       <div class="col-lg-10 mail-box-detail">
       		<div class="panel panel-info">
                <div class="panel-heading">
                	<h6>
                		Unit
                		<a class="btn btn-primary pull-right" href="#list">Unit List</a>
                	</h6>
                </div> 
                <div class="panel-body">
                	<form class="form-horizontal" role="form" method="post" action="{{ url('hr/settings/unit')  }}" enctype="multipart/form-data">
                    	@csrf
	                    <div class="row">
	                    	<div class="col-sm-6">
	                    		<div class="form-group has-required has-float-label">
			                        <input type="text" id="hr_unit_name" name="hr_unit_name" placeholder="Unit name" class="form-control" required />
			                        <label class="" for="hr_unit_name" > Unit Name  </label>
			                    </div>

			                    <div class="form-group has-required has-float-label">
			                        
			                        <input type="text" id="hr_unit_short_name" name="hr_unit_short_name" placeholder="Unit short name" class="form-control"  required/>
			                        <label class="" for="hr_unit_short_name" > Unit Short Name</label>
			                    </div>

			                    <div class="form-group  has-float-label">
			                        
			                        <input type="text" id="hr_unit_name_bn" name="hr_unit_name_bn" placeholder="ইউনিটের নাম" class="form-control" />
			                        <label class="" for="hr_unit_name_bn" > ইউনিট (বাংলা) </label>
			                        
			                    </div>

			                    <div class="form-group  has-float-label">
			                        
			                        <input type="text" id="hr_unit_address" name="hr_unit_address" placeholder="Unit name" class="form-control"/>
			                        <label class="" for="hr_unit_address" > Unit Address </label>
			                    </div>

			                    <div class="form-group  has-float-label">
			                        
			                        <input type="text" id="hr_unit_address_bn" name="hr_unit_address_bn" placeholder="ইউনটের ঠিকানা(বাংলা)" class="form-control"/>
			                        <label class="" for="hr_unit_address_bn" > ইউনিট ঠিকানা (বাংলা) </label>
			                    </div>
			                    <div class="form-group  has-float-label">
			                        
			                        <input type="text" id="hr_unit_code" name="hr_unit_code" placeholder="Unit code" class="form-control" />
			                        <label class="" for="hr_unit_code"> Unit Code </label>
			                    </div>
	                    	</div>
	                    	<div class="col-sm-6">
	                    		<div class="form-group has-required file-zone">
	                    			<label for="hr_unit_logo"> Logo </label> 
	                    			<input type="file" name="hr_unit_logo" data-file-allow='["jpg", "jpeg", "png"]' autocomplete="off" required="required" class="file-type-validation"> 
	                    			<div role="alert" class="invalid-feedback">
	                    				<strong>Select a jpg/jpeg/png file</strong>
	                    			</div>
	                    			<p class="help-text">Only <strong>jpeg,png,jpg </strong>type file supported(<80kB).</p>
	                    		</div>
	                    		<div class="form-group has-required file-zone">
	                    			<label for="hr_unit_authorized_signature"> Signature </label> 
	                    			<input type="file" name="hr_unit_authorized_signature" data-file-allow='["jpg", "jpeg", "png"]' autocomplete="off" required="required" class="file-type-validation"> 
	                    			<div role="alert" class="invalid-feedback">
	                    				<strong>Select a jpg/jpeg/png file</strong>
	                    			</div>
	                    			<p class="help-text">Only <strong>jpeg,png,jpg </strong>type file supported(<80kB).</p>
	                    		</div>

			                    <div class="form-group"> 
			                        <button class="btn pull-right btn-primary" type="submit">Submit</button>
			                    </div>
	                    	</div>
	                    </div>
			                    

			                    
                </form> 
                </div>
            </div>
            <div id="list" class="panel panel-info">
                <div class="panel-body">
                	<ul class="nav nav-tabs" id="myTab-1" role="tablist">
	                    <li class="nav-item">
	                        <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="false">Active</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link" id="trash-tab" data-toggle="tab" href="#trash" role="tab" aria-controls="trash" aria-selected="false">Trash</a>
	                    </li>
	                </ul>
	                <div class="tab-content">
	                	<div class="tab-pane fade active show" id="active" role="tabpanel" aria-labelledby="active-tab">
                         
		                    <div class="table-responsive">
		                        <table id="dataTables" class="table table-striped table-bordered table-hover" >
		                         	<thead>
			                            <tr>
			                               
		                                    <th style="width: 20%;">Logo</th>
		                                    <th style="width: 20%;">Unit Name</th>
		                                    <th style="width: 20%;">Short Name</th>
		                                    <th style="width: 20%;">ইউনিট (বাংলা)</th>
		                                    <th style="width: 20%;">Unit Code</th>
		                                    <th style="width: 20%;">Signature</th>
		                                    <th style="width: 20%;">Action</th>
		                                    
			                            </tr>
		                            </thead>
		                         	<tbody>
		                         		@if($units->isNotEmpty() )
		                         		@foreach($units as $key => $unit)
		                                <tr class="datatable-action-button">
		                                    <td>
		                                    	<img src='' alt="Logo" width="80" height="30">
		                                    </td>
		                                    <td>{{ $unit->hr_unit_name??'' }}</td>
		                                    <td>{{ $unit->hr_unit_short_name??'' }}</td>
		                                    <td>{{ $unit->hr_unit_name_bn??'' }}</td>
		                                    <td>{{ $unit->hr_unit_code??'' }}</td>
		                                    <td class="relative">
		                                    	<img src='' alt="Signature" width="60" height="20">

		                                    	<!-- <ul class="iq-social-media">
	                                                <li><a href="#"><i class="ri-delete-bin-2-line"></i></a></li>
	                                                <li><a href="#"><i class="ri-mail-line"></i></a></li>
	                                                <li><a href="#"><i class="ri-file-list-2-line"></i></a></li>
	                                                <li><a href="#"><i class="ri-time-line"></i></a></li>
	                                             </ul> -->
		                                    </td>
		                                    <td>
		                                        <div class="btn-group">
		                                            {{-- <a type="button" href="{{ url('hr/setup/unit_update/'.$unit->hr_unit_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit">Edit </a> --}}
		                                            {{-- <a href="{{ url('hr/setup/unit/'.$unit->hr_unit_id) }}" type="button" class='btn btn-sm btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')">
		                                            	<i class="las la-trash"></i>
		                                            </a> --}}
		                                        </div>
		                                    </td>
		                                </tr>
		                                @endforeach
		                                @else
		                                <tr>
		                                	<td colspan="7">No items found!</td>
		                                </tr>
		                         		@endif
		                         	</tbody>
		                        </table>
		                    </div>
                      	</div>
                      	<div class="tab-pane fade" id="trash" role="tabpanel" aria-labelledby="trash-tab">
                      		<div class="table-responsive">
		                        <table id="datatable" class="table table-striped table-bordered" >
		                         	<thead>
			                            <tr>
			                               
		                                    <th style="width: 10%;">Logo</th>
		                                    <th style="width: 25%;">Unit Name</th>
		                                    <th style="width: 10%;">Short Name</th>
		                                    <th style="width: 25%;">ইউনিট (বাংলা)</th>
		                                    <th style="width: 10%;">Unit Code</th>
		                                    <th style="width: 10%;">Signature</th>
		                                    <th style="width: 10%;">Action</th>
		                                    
			                            </tr>
		                            </thead>
		                         	<tbody>
		                         		@if($trashed!= null)
			                         		@foreach($trashed as $key => $item)
			                                <tr>
			                                    <td>
			                                    	<img src='' alt="Logo" width="80" height="30">
			                                    </td>
			                                    <td>{{ $item->hr_unit_name??'' }}</td>
			                                    <td>{{ $item->hr_unit_short_name??'' }}</td>
			                                    <td>{{ $item->hr_unit_name_bn??'' }}</td>
			                                    <td>{{ $item->hr_unit_code??'' }}</td>
			                                    <td>
			                                    	<img src='' alt="Signature" width="60" height="20">


			                                    </td>
			                                    <td>
			                                        <div class="btn-group">
			                                            {{-- <a type="button" href="{{ url('hr/setup/unit_update/'.$item->hr_unit_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit">Restore </a>
			                                            <a href="{{ url('hr/setup/unit/'.$item->hr_unit_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')">
			                                            	Delete
			                                            </a> --}}
			                                        </div>

			                                    </td>
			                                </tr>
			                                @endforeach
		                                @else
		                                <tr>
		                                	<td colspan="7">No trashed item!</td>
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
	@push('js')
	@endpush
@endsection