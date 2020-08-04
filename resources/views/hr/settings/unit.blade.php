@extends('hr.layout')
@section('title', 'Unit')
@section('main-content')
	@push('css')
	@endpush
	<div class="row">
		<!-- start message area-->

        <div class="col-sm-12">
        	<div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                   <div class="iq-header-title">
                      <h4 class="card-title">All Unit</h4>
                   </div>
                   <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#libraryAdd">
                       Add Unit
                   </button>
                </div>
                <div class="iq-card-body">
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
		                        <table id="datatable" class="table table-striped table-bordered" >
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
		                                <tr>
		                                    <td>
		                                    	<img src='' alt="Logo" width="80" height="30">
		                                    </td>
		                                    <td>{{ $unit->hr_unit_name??'' }}</td>
		                                    <td>{{ $unit->hr_unit_short_name??'' }}</td>
		                                    <td>{{ $unit->hr_unit_name_bn??'' }}</td>
		                                    <td>{{ $unit->hr_unit_code??'' }}</td>
		                                    <td>
		                                    	<img src='' alt="Signature" width="60" height="20">
		                                    </td>
		                                    <td>
		                                        <div class="btn-group">
		                                            <a type="button" href="{{ url('hr/setup/unit_update/'.$unit->hr_unit_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit">Edit </a>
		                                            <a href="{{ url('hr/setup/unit/'.$unit->hr_unit_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')">
		                                            	Trash
		                                            </a>
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
			                                            <a type="button" href="{{ url('hr/setup/unit_update/'.$item->hr_unit_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit">Restore </a>
			                                            <a href="{{ url('hr/setup/unit/'.$item->hr_unit_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')">
			                                            	Delete
			                                            </a>
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
	<!-- add unit modal -->
	<!--  -->
	<div class="modal fade " id="libraryAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalCenterTitle">Add Unit</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/unit')  }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    	<div class="col-sm-6">
                    		<div class="form-group">
		                        <label class="" for="hr_unit_name" > Unit Name <span class="text-red">&#42;</span> </label>
		                        <input type="text" id="hr_unit_name" name="hr_unit_name" placeholder="Unit name" class="form-control" data-validation="required length custom" data-validation-length="1-128"/>
		                    </div>

		                    <div class="form-group">
		                        <label class="" for="hr_unit_short_name" > Unit Short Name <span class="text-red">&#42;</span> </label>
		                        
		                        <input type="text" id="hr_unit_short_name" name="hr_unit_short_name" placeholder="Unit short name" class="form-control" data-validation="required length custom" data-validation-length="1-20"/>
		                    </div>

		                    <div class="form-group">
		                        <label class="" for="hr_unit_name_bn" > ইউনিট (বাংলা) </label>
		                        
		                        <input type="text" id="hr_unit_name_bn" name="hr_unit_name_bn" placeholder="ইউনিটের নাম" class="form-control" data-validation="length" data-validation-length="0-255" data-validation-error-msg="সঠিক নাম দিন"/>
		                        
		                    </div>

		                    <div class="form-group">
		                        <label class="" for="hr_unit_address" > Unit Address </label>
		                        
		                        <input type="text" id="hr_unit_address" name="hr_unit_address" placeholder="Unit name" class="form-control"/>
		                    </div>

		                    <div class="form-group">
		                        <label class="" for="hr_unit_address_bn" > ইউনিট ঠিকানা (বাংলা) </label>
		                        
		                        <input type="text" id="hr_unit_address_bn" name="hr_unit_address_bn" placeholder="ইউনটের ঠিকানা(বাংলা)" class="form-control"/>
		                    </div>
                    	</div>
                    	<div class="col-sm-6">
                    		<div class="form-group">
		                        <label class="" for="hr_unit_code"> Unit Code </label>
		                        
		                        <input type="text" id="hr_unit_code" name="hr_unit_code" placeholder="Unit code" class="form-control" data-validation="length" data-validation-length="0-10"/>
		                    </div>

		                    <div class="form-group" >
		                        <label class="" for="hr_unit_logo">Logo<br> <span>(jpg|jpeg|png) <br> Max Size: 200KB<br> Dimension: (148x248)px</span></label>
		                        
		                        <input name="hr_unit_logo" id="hr_unit_logo" type="file" 
		                            class="dropZone"
		                            data-validation="mime size dimension" data-validation-dimension="min248x148"
		                            data-validation-allowing="jpeg,png,jpg"
		                            data-validation-max-size="200kb"
		                            data-validation-error-msg-size="You can not upload images larger than 200kB"
		                            data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
		                        <p id="file_upload_error" class="red" >Only <strong>jpeg,png,jpg </strong>type file supported(<200kB).</p>
		                    </div>

		                    <div class="form-group">
		                        <label class=" " for="hr_unit_authorized_signature">Signature <br> <span>(jpg|jpeg|png) Max Size: 80kB<br> Dimension: (120x80)px</span></label>
		                        
		                        <input name="hr_unit_authorized_signature" id="hr_unit_authorized_signature" type="file" 
		                            class="dropZone"
		                            data-validation="mime size dimension" data-validation-dimension="min120x80"
		                            data-validation-allowing="jpeg,png,jpg"
		                            data-validation-max-size="80kb"
		                            data-validation-error-msg-size="You can not upload images larger than 80kB"
		                            data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
		                        <p id="file_upload_error2" class="red" >Only <strong>jpeg,png,jpg </strong>type file supported(<80kB).</p>
		                    </div>

		                    <div class="form-group"> 
		                        <button class="btn btn-sm btn-success" type="submit">Submit</button>

		                        <button class="btn btn-sm" type="reset">Reset
		                        </button>
		                    </div>
                    	</div>
                    </div>
		                    

		                    
                </form> 
            </div>
         </div>
      </div>
    </div>
	@push('js')
	@endpush
@endsection