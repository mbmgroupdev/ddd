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
					<a href="#"> Setup </a>
				</li>
				<li class="active"> Unit </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Unit</h6></div> 
                <div class="panel-body">

                <div class="row">
                      <!-- Display Erro/Success Message -->
                    @include('inc/message')
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/unit')  }}" enctype="multipart/form-data">
                        {{ csrf_field() }} 
                    <div class="col-sm-offset-3 col-sm-6" style="padding-top: 20px;">
                        <!-- PAGE CONTENT BEGINS -->
                        <!-- <h1 align="center">Add New Employee</h1> -->

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="hr_unit_name" > Unit Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_unit_name" name="hr_unit_name" placeholder="Unit name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="hr_unit_short_name" > Unit Short Name <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_unit_short_name" name="hr_unit_short_name" placeholder="Unit short name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-20"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="hr_unit_name_bn" > ইউনিট (বাংলা) </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_unit_name_bn" name="hr_unit_name_bn" placeholder="ইউনিটের নাম" class="col-xs-12" data-validation="length" data-validation-length="0-255" data-validation-error-msg="সঠিক নাম দিন"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="hr_unit_address" > Unit Address </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_unit_address" name="hr_unit_address" placeholder="Unit name" class="col-xs-12"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="hr_unit_address_bn" > ইউনিট ঠিকানা (বাংলা) </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_unit_address_bn" name="hr_unit_address_bn" placeholder="ইউনটের ঠিকানা(বাংলা)" class="col-xs-12"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label no-padding-right" for="hr_unit_code"> Unit Code </label>
                                <div class="col-sm-8">
                                    <input type="text" id="hr_unit_code" name="hr_unit_code" placeholder="Unit code" class="col-xs-12" data-validation="length" data-validation-length="0-10"/>
                                </div>
                            </div>



                            <div class="form-group" style="padding-top: 10px;">
                                <label class="col-sm-4 col-xs-4 control-label no-padding-right no-padding-top" for="hr_unit_logo">Logo<br> <span>(jpg|jpeg|png) <br> Max Size: 200KB<br> Dimension: (148x248)px</span></label>
                                <div class="col-sm-8">

                                    <input name="hr_unit_logo" id="hr_unit_logo" type="file" 
                                    class="dropZone"
                                    data-validation="mime size dimension" data-validation-dimension="min248x148"
                                    data-validation-allowing="jpeg,png,jpg"
                                    data-validation-max-size="200kb"
                                    data-validation-error-msg-size="You can not upload images larger than 200kB"
                                    data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                                    <span id="file_upload_error" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<200kB).</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 col-xs-4 control-label no-padding-right" for="hr_unit_authorized_signature">Signature <br> <span>(jpg|jpeg|png) Max Size: 80kB<br> Dimension: (120x80)px</span></label>
                                <div class="col-sm-8">
                                    <input name="hr_unit_authorized_signature" id="hr_unit_authorized_signature" type="file" 
                                    class="dropZone"
                                    data-validation="mime size dimension" data-validation-dimension="min120x80"
                                    data-validation-allowing="jpeg,png,jpg"
                                    data-validation-max-size="80kb"
                                    data-validation-error-msg-size="You can not upload images larger than 80kB"
                                    data-validation-error-msg-mime="You can only upload jpeg, jpg or png images">
                                    <span id="file_upload_error2" class="red" style="display: none; font-size: 13px;">Only <strong>jpeg,png,jpg </strong>type file supported(<80kB).</span>
                                </div>
                            </div>

                       
                        <!-- PAGE CONTENT ENDS -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-12 col-xs-12">
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-4 col-md-4 text-center" style="padding-left: 35px;"> 
                                <button class="btn btn-sm btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>

                                &nbsp; &nbsp; &nbsp;
                                <button class="btn btn-sm" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                     </form> 
                </div>
               </div>
            </div>
            <br>
            <div class="panel panel-info">
              <div class="panel-heading"><h6>Unit List</h6></div> 
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <table id="dataTables" class="table table-striped table-bordered" style="display: block;overflow-x: auto;width: 100%;">
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
                                    @foreach($units as $unit)
                                    <tr>
                                        <td><img src='{{ url("$unit->hr_unit_logo") }}' alt="Logo" width="80" height="30"></td>
                                        <td>{{ $unit->hr_unit_name }}</td>
                                        <td>{{ $unit->hr_unit_short_name }}</td>
                                        <td>{{ $unit->hr_unit_name_bn }}</td>
                                        <td>{{ $unit->hr_unit_code }}</td>
                                        <td><img src='{{ url("$unit->hr_unit_authorized_signature") }}' alt="Signature" width="60" height="20"></td>
                                        <td>
                                            <div class="btn-group">
                                                {{-- <a type="button" href="{{ url('hr/setup/unit_update/'.$unit->hr_unit_id) }}" class='btn btn-xs btn-primary' data-toggle="tooltip" title="Edit"> <i class="ace-icon fa fa-pencil bigger-120"></i></a>
                                                <a href="{{ url('hr/setup/unit/'.$unit->hr_unit_id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="ace-icon fa fa-trash bigger-120"></i></a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

     //logo upload validation....
    $('#hr_unit_logo').on('change', function(){
        var fileExtension = ['jpeg','png','jpg'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#file_upload_error').show();
            $(this).val('');
        }
        else{
            $('#file_upload_error').hide();
        }
    });
     //signature upload validation....
    $('#hr_unit_authorized_signature').on('change', function(){
        var fileExtension = ['jpeg','png','jpg'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $('#file_upload_error2').show();
            $(this).val('');
        }
        else{
            $('#file_upload_error2').hide();
        }
    });    


    $('#dataTables').DataTable({
        pagingType: "full_numbers" ,
        // searching: false,
        // "lengthChange": false,
        // 'sDom': 't' 
        "sDom": '<"F"tp>'

    }); 
});
</script>
@endsection