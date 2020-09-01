@extends('hr.layout')
@section('title', 'Service Book')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#">Human Resource</a>
				</li>
				<li>
					<a href="#">Employee</a>
				</li>
				<li class="active"> Service Book</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content">  
            <div class="page-header row">
                <h1 class="col-sm-6">Operation<small><i class="ace-icon fa fa-angle-double-right"></i> Service Book</small></h1>
                <div class="text-right" id="newBtn"> 
                </div>
            </div>

            <div class="row">
                 @include('inc/message')
                <div class="col-xs-7">
                  <h5 class="page-header">Service Book Entry</h5>
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/operation/servicebookstore') }}" enctype="multipart/form-data"> 

                         {{ csrf_field() }} 
                        <div class="form-group">
                            <label class="col-sm-4 control-label no-padding-right" for="job_app_id"> Associate's ID <span style="color: red; vertical-align: top;">&#42;</span> :</label>
                            <div class="col-sm-8">
                                 {{ Form::select('associate_id', [Request::get('associate_id') => Request::get('associate_id')], Request::get('associate_id'),['placeholder'=>'Select Associate\'s ID', 'data-validation'=> 'required', 'id'=>'associate_id',  'class'=> 'associates no-select col-xs-10 col-sm-10']) }} 
                                
                            </div>
                        </div>
                       <div class="space-10"></div>
                       <div id="form-element"> <!---Image Fields --></div>
                       
                        <div class="clearfix form-actions no-padding-right">
                            <div class="col-md-offset-5 col-md-10" style="padding-left: 141px;"> 
                                <button class="btn btn-sm btn-success" type="submit">
                                    <i class="ace-icon fa fa-check bigger-110"></i> Add
                                </button>
                                &nbsp
                                <button class="btn btn-sm" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                </button>
                            </div>
                        </div> 
                     
                    </form>
                    <!-- PAGE CONTENT ENDS -->
                </div>
                <!-- /.col --> 
            </div>
		</div><!-- /.page-content -->
	</div>
</div>
<script type="text/javascript">
function drawNewBtn(associate_id)
{
    var url = "{{ url("") }}";
    var newUrl = "<div class=\"btn-group pull-right\">"+
        "<a href='"+url+'/hr/recruitment/employee/show/'+associate_id+"' target=\"_blank\" class=\"btn btn-sm btn-success\" title=\"Profile\"><i class=\"glyphicon glyphicon-user\"></i></a>"+ 
        "<a href='"+url+'/hr/recruitment/employee/edit/'+associate_id+"'  class=\"btn btn-sm btn-success\" title=\"Basic Info\"><i class=\"glyphicon glyphicon-bold\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/advance_info_edit/'+associate_id+"'  class=\"btn btn-sm btn-info\" title=\"Advance Info\"><i class=\"glyphicon  glyphicon-font\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/benefits?associate_id='+associate_id+"' class=\"btn btn-sm btn-primary\" title=\"Benefits\"><i class=\"fa fa-usd\"></i></a>"+
        "<a href='"+url+'/hr/ess/medical_incident?associate_id='+associate_id+"'  class=\"btn btn-sm btn-warning\" title=\"Medical Incident\"><i class=\"fa fa-stethoscope\"></i></a>"+
        "<a href='"+url+'/hr/operation/servicebook?associate_id='+associate_id+"' class=\"btn btn-sm btn-danger\" title=\"Service Book\"><i class=\"fa fa-book\"></i></a>"+
    "</div>"; 
    $("#newBtn").html(newUrl);
}
 

$(document).ready(function(){   
    // retrive all information  

    $('select.associates').select2({
        ajax: {
            url: '{{ url("hr/associate-search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { 
                    keyword: params.term
                }; 
            },
            processResults: function (data) { 
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.associate_name,
                            id: item.associate_id
                        }
                    }) 
                };
            },
            cache: true
        }
    });

    // Form Based on Emloyee Id
    var action_element = $("#form-element");
    var associate_id = '{{ request()->get("associate_id") }}';

    $(window).on("load", function(){
        if (associate_id) 
        {
            ajaxLoad(associate_id);
            drawNewBtn(associate_id);
        }
    });
    
    $("#associate_id").on("change", function(){ 
        ajaxLoad($(this).val());
        drawNewBtn($(this).val());
    });

    function ajaxLoad(associate_id){
        // Action Element list
        $.ajax({
            url : "{{ url('hr/operation/servicebookpage') }}",
            type: 'get',
            data: {associate_id},
            success: function(data)
            {
                action_element.html(data);
            },
            error: function()
            {
                alert('failed...');
            }
        });
    }

     $('#dataTables').DataTable();
    
});     
</script>
<script type="text/javascript">
    $(document).ready(function(){
        //File validation Script..........................
        // $('#page1', '#page2', '#inp_page3','#page4','#page5','#page6','#page7').change(function () {
        //     var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
        //     if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        //         $(this).next().show();
        //         // alert("Please Upload only xls/xlsx type file.");
        //         $(this).val('');
        //     }
        //     else{ 
        //             $(this).next().hide();
        //         }
        // });



        $('body').on('change','#page1',function () {
            // console.log($(this).val());
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_1').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#upload_error_1').hide();
                }
        });
        $('body').on('change','#page2',function () {
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_2').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#upload_error_2').hide();
                }
        });
        $('body').on('change','#inp_page3',function () {
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_3').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#upload_error_3').hide();
                }
        });
        $('body').on('change','#page4', function () {
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_4').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#upload_error_4').hide();
                }
        });
        $('body').on('change','#page5',function () {
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_5').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#upload_error_5').hide();
                }
        });
        $('body').on('change','#page6',function () {
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_6').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#upload_error_6').hide();
                }
        });
        $('body').on('change','#page7',function () {
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_7').show();
                // alert("Please Upload only xls/xlsx type file.");
                $(this).val('');
            }
            else{ 
                    $('#upload_error_7').hide();
                }
        }); 
    //File validation Script End......................
    });
</script>

@endsection