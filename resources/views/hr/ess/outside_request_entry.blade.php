@extends('user.layout')
@section('title', 'User Dashboard')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<a href="#"> ESS </a>
				</li>
				<li class="active"> Outside Request</li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>ESS<small> <i class="ace-icon fa fa-angle-double-right"></i> Outside Request</small></h1>
            </div>

            <div class="col-sm-12 no-padding-left no-padding-right">
                <div class="col-sm-12 panel panel-success no-padding">
                    <div class="panel-heading"><h5>Entry</h5></div>
                        <div class="panel-body">   
                            @include('inc/message')
                            <div class="col-xs-offset-3 col-xs-6" style=" padding-top: 20px;">
                                <!-- PAGE CONTENT BEGINS -->
                                {{ Form::open(['url'=>'hr/ess/out_side_request/entry', 'class'=>'form-horizontal', 'files' => true]) }}
             
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="start_date">Date <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-7">
                                            <div class="col-sm-6 input-icon no-padding-left">
                                                <input type="text" name="start_date" id="start_date" class="datepicker col-xs-12 " placeholder="From" data-validation="required" data-validation-error-msg="The Start Date field is requested_locationired" />
                                            </div> 

                                            <div class="col-sm-6 input-icon input-icon-right no-padding-left">
                                                <input type="text" placeholder="To" name="end_date" id="end_date" class=" datepicker col-xs-12"/> 
                                            </div> 
                                        </div>
                                    </div>
             
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="requested_location">Location <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-7" style="padding-right: 20px;">
                                            {{ Form::select('requested_location', $locationList, null, ['id' => 'requested_location', 'placeholder' => 'Select Location', 'class' => 'col-xs-12', 'data-validation' => 'required', 'required'=>'required']) }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="requested_location">Type <span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-7" style="padding-right: 20px;">
                                            <select id="type" name="type" class="col-xs-12" required="required">
                                                <option value="">Select One</option>
                                                <option value="1">Full Day</option>
                                                <option value="2">1st Half</option>
                                                <option value="3">2nd Half</option>
                                            </select>
                                        </div>
                                    </div>
             
                                    <div class="form-group hide" id="place_div">
                                        <label class="col-sm-4 control-label no-padding-right" for="requested_place">Purpose<span style="color: red; vertical-align: top;">&#42;</span></label>
                                        <div class="col-sm-7"  style="padding-right: 20px;">
                                            <input type="text" name="requested_place" id="requested_place" class="col-xs-12 form-control" data-validation="required"  />
                                        </div>
                                    </div>
             
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" for="comment">Comment</label>
                                        <div class="col-sm-7"  style="padding-right: 20px;">
                                            <input type="text" name="comment" class="col-xs-12 form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label no-padding-right" ></label>
                                        <div class="col-sm-7"  style="padding-right: 20px;">
                                            <button class="btn btn-sm btn-success" type="submit">
                                                <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                            </button>

                                            &nbsp; &nbsp; &nbsp;
                                            <button class="btn btn-sm" type="reset">
                                                <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                            </button>
                                        </div>
                                    </div>


                                <!-- PAGE CONTENT ENDS -->
                            </div>
                            {{ Form::close() }}
                        </div>
                </div>    

                <div class="col-sm-12 worker-list panel panel-info no-padding">
                    <div class="panel-heading"><h5>List</h5></div>
                    <div class="panel-body">
                        <table id="dataTables" class="table table-striped table-bordered"  style="display:table;overflow-x: auto;white-space: nowrap; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Requested Location</th>
                                    <th>Type</th>
                                    <th>Purpose</th>
                                    <th>Applied on</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; ?>
                                @foreach($requestList as $out)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $out->start_date }}</td>
                                        <td>{{ $out->end_date }}</td>
                                        <td>{{ $out->location_name }}</td>
                                        <td>
                                            <?php 
                                                if($out->type == 1){
                                                    echo "Full Day";
                                                }
                                                elseif($out->type == 2){
                                                    echo "1st Half";   
                                                }
                                                elseif($out->type == 3){
                                                    echo "2nd Half";
                                                }
                                                else{
                                                    echo "";
                                                } 
                                            ?>
                                        </td>
                                        <td>{{ $out->requested_place }}</td>
                                        <td>{{ $out->applied_on }}</td>
                                        <td><?php if($out->status==0 ) printf("Applied");
                                                    else if($out->status==1 ) printf("Approved");
                                                    else printf("Rejected");
                                                      ?></td>
                                        <td>
                                            <div class="btn-group">
                                                @if($out->status == 0)
                                                    <a href="{{ url('hr/ess/out_side_request/delete/'.$out->id) }}" type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="fa fa-trash bigger-120"></i></a>
                                                @else
                                                    <a type="button" class='btn btn-xs btn-danger' data-toggle="tooltip" title="You can not delete this!" disabled><i class="fa fa-trash bigger-120"></i></a>
                                                @endif
                                                    <button type="button" class="btn btn-info btn-xs modal_button " data-toggle="modal"  data-target="#myModal" data-index ="{{ $i-2 }}" title = "Details"><i class="fa fa-list bigger-120"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal -->
                <div id="myModal" class="modal fade" role="dialog" style="border-radius: 5px !important;">
                  <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header" style="background-color: lightblue;">
                        
                        <h4 class="modal-title">
                            Details
                            <button type="button" class="close btn-xs text-right" data-dismiss="modal">&times;</button>
                        </h4>
                      </div>
                      <div class="modal-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="30%">Status</th>
                                <td id="status_val"></td>
                            </tr>
                            <tr>
                                <th width="30%">Start-End</th>
                                <td id="strat_end_val"></td>
                            </tr>
                            <tr>
                                <th width="30%">Requested Location</th>
                                <td id="location_val"></td>
                            </tr>
                            <tr>
                                <th width="30%">Type</th>
                                <td id="type_val"></td>
                            </tr>
                            <tr>
                                <th width="30%">Purpose</th>
                                <td id="purpose_val"></td>
                            </tr>
                            <tr>
                                <th width="30%">Applied on</th>
                                <td id="applied_date_val"></td>
                            </tr>
                            <tr>
                                <th width="30%">Comment</th>
                                <td id="comment_val"></td>
                            </tr>
                        </table>
                      </div>
                      <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" style="border-radius: 2px;">Close</button> --}}
                      </div>
                    </div>

                  </div>
                </div>
            </div>

		</div><!-- /.page-content -->
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        
        //Suggestion Showing...
        $( function() { 
            var tags = [ 
            "Bank", 
            "Business", 
            "Factory", 
            "Shop"
          
                /* Making a list of available tags */ 
            ]; 
            $( "#requested_place" ).autocomplete({ 
              source: tags 
                /* #tthe ags is the id of the input element 
                source: tags is the list of available tags*/ 
            }); 
          } );

        //Modal show
        // $('#modal_button').on('click', function(){
        $('body').on('click','.modal_button', function(){
            var idx = $(this).data('index');
            var data =  '<?php echo json_encode($requestList) ?>' ; 
            var parsed_data = JSON.parse(data);

            // console.log(idx,parsed_data);
            
            if(parsed_data[idx]['status'] == 0){ var txt = '<span style="color: blue;">Applied</span>';}
            else if(parsed_data[idx]['status'] == 1){ var txt = '<span style="color: darkgreen;">Approved</span>';}
            else {var txt = '<span style="color: red;">Rejected</span>';}

            $('#status_val').html(txt);
            $('#strat_end_val').text(parsed_data[idx]['start_date']+ ' to ' +parsed_data[idx]['end_date']);
            $('#location_val').text(parsed_data[idx]['location_name']);
            if(parsed_data[idx]['type'] == 0){
                var typ = "";    
            }
            else if(parsed_data[idx]['type'] == 1){
                var typ = "Full Day";
            }
            else if(parsed_data[idx]['type'] == 2){
                var typ = "1st Half";
            }
            else if(parsed_data[idx]['type'] == 3){
                var typ = "2nd Half";
            }
            $('#type_val').text(typ);
            $('#purpose_val').text(parsed_data[idx]['requested_place']);
            $('#applied_date_val').text(parsed_data[idx]['applied_on']);
            $('#comment_val').text(parsed_data[idx]['comment']);
        });

        //Date-validation
        $('#start_date').on('dp.change',function(){
            $('#end_date').val($(this).val());    
        });

        $('#end_date').on('dp.change',function(){
            var end     = new Date($(this).val());
            var start   = new Date($('#start_date').val());
            if($('#start_date').val() == '' || $('#start_date').val() == null){
                alert("Please enter Start-Date first");
                $('#end_date').val('');
            }
            else{
                if(end < start){
                    alert("Invalid!!\n Start-Date is latest than End-Date");
                    $('#end_date').val('');
                }
            }
        });

        $('#dataTables').DataTable({
            pagingType: "full_numbers" ,
            responsive: true,
            "sDom": '<"F"tp>'
        }); 

        $("#requested_location").on("change", function(){
            if($(this).val() == "Outside"){
                $('#place_div').removeClass('hide');
            }
            else{ 
                $('#place_div').addClass('hide');
            }
        });

    });
</script>




@endsection
                    