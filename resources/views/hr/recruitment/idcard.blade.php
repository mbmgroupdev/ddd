@extends('hr.layout')
@section('title', 'Generate ID card')
@section('main-content')
@push('css')
<style type="text/css">
	@media only screen and (max-width: 771px) {
		
		.id_info{margin-bottom: 10px;}
		.id_card_table{padding-left: 22px; padding-right: 22px;}
	}
</style>
@endpush
<div class="main-content">
	<div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                   <a href="/"><i class="ace-icon fa fa-home home-icon"></i>Human Resource</a> 
                </li>
                <li>
                    <a href="#">Recruitment</a>
                </li>
                <li class="active">ID CARD</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="panel">
        	<div class="panel-body">
               	{{ Form::open(['url'=>'', 'class'=>'row', 'id'=>'IdCard']) }}
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group has-float-label  select-search-group">
									
		                            {{ Form::select('unit', $unitList, null, ['placeholder'=>'Select Unit', 'class'=> 'form-control filter']) }} 
		                            <label for="emp_type">Unit</label> 
		                        </div>
							</div>
							<div class="col-sm-6 id_info">
								<div class="form-group has-float-label  select-search-group">
									
		                            {{ Form::select('emp_type', $employeeTypes, null, ['placeholder'=>'Select Employee Type', 'class'=> 'form-control filter']) }}  
		                            <label for="emp_type">Employee Type</label>
								</div>
							</div>
							
							<div class="col-sm-6 id_info">
								<div class="form-group has-float-label  select-search-group">
									
		    						{{ Form::select('floor', [], null, ['placeholder'=>'Select Floor', 'class'=>'form-control filter']) }}  
		    						<label for="emp_type">Floor</label>
		    					</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group has-float-label  select-search-group">
										
									{{ Form::select('line', [], null, ['placeholder'=>'Select Line', 'class'=>'form-control filter']) }} 
									<label for="emp_type">Line</label>  
								</div>
							</div>  
							<div class="col-sm-6 id_info">
								<div class="form-group has-float-label  ">
									
									<input type="date" name="doj_from" id="doj_from"  placeholder="Y-m-d (Date of Joining From)" class="datepicker form-control filter" placeholder="Date of Join From" >
									<label for="emp_type">DOJ From</label>  
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group has-float-label">
									<input type="date" name="doj_to" id="doj_to" placeholder="Y-m-d (Date of Joining To)" class="datepicker form-control filter" placeholder="Date of Join To" >
									<label for="emp_type">DOJ To</label> 
								</div> 
							</div>  
							<div class="col-sm-6">
	    						{{ Form::radio('type', 'en',true, ['id'=>'en']) }}  
								<label for="en">English</label>
	    						{{ Form::radio('type', 'bn', false, ['id'=>'bn']) }}
								<label for="bn">Bengali</label>  
								<br>
								<div class="btn-group">
	                                <button type="submit" class="btn btn-primary " type="button">
	                                     Generate
	                                </button> 
									
	                            </div>
							</div>
						</div>
					</div>

					<div class="col-sm-6 id_card_table" style="height: 400px; overflow: auto;"> 
	                    <table id="AssociateTable" class="table header-fixed1 table-compact table-bordered col-sm-12">
	                        <tehad>
	                            <tr>
	                                <th class="sticky-th"><input type="checkbox" id="checkAll"/></th>
	                                <th class="sticky-th">Associate ID</th>
	                                <th class="sticky-th">Oracle ID</th>
	                                <th class="sticky-th">Name</th>
	                            </tr>
	                            <tr>
	                                <th class="sticky-th" colspan="4" id="user_filter" style="top: 40px;"></th>
	                            </tr>
	                        </tehad>
	                        <tbody id="associateList">
								
	                        </tbody>
	                    </table>
					</div>
				{{ Form::close() }}
			</div>
        </div>

        <div class="panel">
        	<div class="panel-body">
        			
        		<div id="printBtn" style="display:inline-block;"></div>
        		<div class="main-content" >
        			<hr>
	        		<div  id="idCardPrint" style="overflow-y: auto; height:500px;width: 900px;margin: auto; "></div>
        		</div>
        		
			</div>
		</div>
	</div>
</div>          
<script type="text/javascript">
$(document).ready(function(){
	//date validation------------------------------------------

	$('#doj_from').on('change',function(){
        $('#doj_to').attr('min',$(this).val());    
    });
    $('#doj_to').on('dp.change',function(){
        var end     = $(this).val();
        var start   = $('#doj_from').val();
        if(start == '' || start == null){
            alert("Please enter From-Date first");

            $('#doj_to').val('');
        }
        else{
             if(end < start){
                alert("Invalid!!\n From-Date is latest than To-Date");

                $('#doj_to').val('');
            }
        }
    });
    //date validation end------------------------------------------


    $('body').on('click','.ck',function(){
    	  $('#idCardPrint').removeAttr('hidden');	
    	  $('html, body').animate({
          scrollTop: $('#idCardPrint').offset().top
          }, 700);
    });
	//check - uncheck
	$('#checkAll').click(function(){
	   var checked =$(this).prop('checked');
	   $('input:checkbox').prop('checked', checked);
	});  
	$('body').on('click', 'input:checkbox', function() {
	   if(!this.checked) {
	       $('#checkAll').prop('checked', false);
	   }
	   else {
	       var numChecked = $('input:checkbox:checked:not(#checkAll)').length;
	       var numTotal = $('input:checkbox:not(#checkAll)').length;
	       if(numTotal == numChecked) {
	           $('#checkAll').prop('checked', true);
	       }
	   }
	});

    //Filter User
    $("body").on("keyup", "#AssociateSearch", function() {
        var value = $(this).val().toLowerCase(); 
        $("#AssociateTable #associateList tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

	// emp_type 
	var associateList = $("#associateList");
	var user_filter   = $("#user_filter");
	var emp_type = $("select[name=emp_type]");
	var unit     = $("select[name=unit]");
	var floor    = $("select[name=floor]");
	var line     = $("select[name=line]");
	// floor list by unit
	unit.on('change', function(){
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/floor_list_by_unit") }}',
			data: {
				unit: unit.val(),
			},
			success: function(data)
			{
				floor.html(data.floorList);   
				line.html('');   
				printBtn.html('');
				idCardPrint.html('');
			},
			error:function(xhr)
			{
				console.log('Unit Failed');
			}
		});
	});

	// line list by floor & unit id
	floor.on('change', function(){
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/line_list_by_unit_floor") }}',
			data: {
				unit: unit.val(),
				floor: floor.val(),
			},
			success: function(data)
			{
				line.html(data.lineList); 
				printBtn.html('');
				idCardPrint.html('');
			},
			error:function(xhr)
			{
				console.log('Employee Type Failed');
			}
		});
	});

	// find_associate
	$("body").on('change', ".filter", function(){
		$('#idCardPrint').attr('hidden','hidden');
		$('.app-loader').show();
		$.ajax({
			url: '{{ url("hr/recruitment/employee/idcard/filter") }}',
			data: {
				emp_type: $("select[name=emp_type]").val(),
				unit: $("select[name=unit]").val(),
				floor: $("select[name=floor]").val(),
				line: $("select[name=line]").val(), 
				doj_from: $("input[name=doj_from]").val(), 
				doj_to: $("input[name=doj_to]").val() 
			},
			success: function(data)
			{
				associateList.html(data.result); 
				user_filter.html(data.filter); 
				printBtn.html('');
				idCardPrint.html('');
				$('.app-loader').hide();
			},
			error:function(xhr)
			{
				console.log('Failed');
				$('.app-loader').hide();
			}
		});
	});

	//submit 
	var IdCard = $("#IdCard");
	var idCardPrint = $("#idCardPrint");
	var printBtn = $("#printBtn");
	IdCard.on('submit', function(e){
		e.preventDefault();
		$('.app-loader').show();
    	var formdata = new FormData($(this)[0]);
    	idCardPrint.html('<center><table class"col-sm-12"><thead><th><h4>Please Wait...</th></h4></thead></table></center>');

		$.ajax({
			url  : '{{ url("hr/recruitment/employee/idcard/search") }}',
			type : $(this).attr('method'),
			dataType : 'json',
	        processData: false,
	        contentType: false,
			data : formdata,
			success:function(data)
			{
				printBtn.html(data.printbutton);
				idCardPrint.html(data.idcard).removeAttr('hidden'); 
				$('.app-loader').hide();
				$('html, body').animate({
                    scrollTop: $(idCardPrint).offset().top
                }, 2000);
			},
			error:function()
			{
				$('.app-loader').hide();
				console.log('faild')
			}
		});
	});
});


function printContent(el)
{
	var data = document.getElementById(el).innerHTML;
	var mywindow = window.open('', 'ID CARD', 'height=400,width=800');
	mywindow.document.write('<html><head><title></title>');
	mywindow.document.write('<style>@page {size: landscape; color: color;} </style>');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.focus();
	mywindow.print();
	mywindow.close();  
}
</script>
@endsection