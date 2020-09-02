@extends('user.layout')
@section('title', 'Outside Request')
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
        @include('inc/message')
        <div class="row">
            <div class="col-sm-3 pr-0">
        		<div class="panel">
                    <div class="panel-heading"><h6>Outside Request</h6></div>
                    <div class="panel-body">   
                        
                        {{ Form::open(['url'=>'hr/ess/out_side_request/entry', 'class'=>'form-horizontal', 'files' => true]) }}
     
                            <div class="form-group has-float-label has-required">
                                <input type="date" name="start_date" id="start_date" class="datepicker form-control " placeholder="From" required="required" />
                                <label  for="start_date">Start Date </label>
                            </div>
                            <div class="form-group has-float-label has-required">
                                <input type="date" placeholder="To" name="end_date" id="end_date" class=" datepicker form-control"/> 
                                <label  for="start_date">End Date </label>
                                    
                            </div>
     
                            <div class="form-group has-float-label has-required select-search-group">
                                {{ Form::select('requested_location', $locationList, null, ['id' => 'requested_location', 'placeholder' => 'Select Location', 'class' => 'form-control', 'required' => 'required', 'required'=>'required']) }}
                                <label  for="requested_location">Location </label>
                            </div>

                            <div class="form-group has-float-label has-required select-search-group">
                                <select id="type" name="type" class="form-control" required="required">
                                    <option value="">Select One</option>
                                    <option value="1">Full Day</option>
                                    <option value="2">1st Half</option>
                                    <option value="3">2nd Half</option>
                                </select>
                                <label  for="requested_location">Type </label>
                            </div>
     
                            <div class="form-group hide has-float-label has-required" id="place_div">
                                
                                <input type="text" name="requested_place" id="requested_place" class="form-control form-control" required="required"  />
                                <label  for="requested_place">Purpose</label>
                            </div>
     
                            <div class="form-group has-float-label has-required">
                                
                                <input type="text" name="comment" class="form-control form-control">
                                <label  for="comment">Comment</label>
                            </div>
                            <div class="form-group">
                                
                                <button class="btn  btn-primary" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                </button>

                            </div>
                        
                        {{ Form::close() }}
                    </div>
                </div>
                
            </div>
            <div class="col-sm-9">
                <div class="panel">
                    <div class="panel-heading"><h6>Outside List</h6></div>
                    <div class="panel-body">
                        <table id="global-datatable" class="table table-striped table-bordered"  style="display:table;overflow-x: auto; width: 100%;">
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
                                        <td class="text-center">
                                            @if($out->status==0 )
                                                ...
                                            @elseif($out->status==1 )
                                                <i class="las f-18 la-check-circle text-success"></i>
                                            @else
                                                <i class="las f-18 la-times-circle text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($out->status == 0)
                                                <a href="{{ url('hr/ess/out_side_request/delete/'.$out->id) }}" type="button" class='text-danger' data-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure?')"><i class="fa fa-trash bigger-120"></i></a>
                                            @endif
                                                <i data-toggle="modal"  data-target="#myModal" data-index ="{{ $i-2 }}" title = "Details" class="fa fa-list bigger-120 text-success"></i>
                                        
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
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

@push('js')
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
@endpush



@endsection
                    