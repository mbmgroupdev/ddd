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

        @include('inc/message')
		<div class="panel">  
            <div class="panel-heading">
                <h6>Service Book</h6>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="post" action="{{ url('hr/operation/servicebookstore') }}" enctype="multipart/form-data"> 

                    {{ csrf_field() }} 
                    <div class="row justify-content-center">
                        <div class="col-sm-3">
                            <div class="form-group has-required has-float-label select-search-group mt-3">
                                {{ Form::select('associate_id', [Request::get('associate_id') => Request::get('associate_id')], Request::get('associate_id'),['placeholder'=>'Select Associate\'s ID', 'required'=> 'required', 'id'=>'associate_id',  'class'=> 'associates form-control']) }} 
                                <label for="job_app_id"> Associate's ID </label>
                                    
                            </div>
                        </div>
                    </div>
                   <div id="form-element"> <!---Image Fields --></div>
                   
                     
                 
                </form>
            </div>
		</div>
	</div>
</div>
@push('js')
<script type="text/javascript">
function drawNewBtn(associate_id)
{
    var url = "{{ url("") }}";
    var newUrl = "<div class=\"btn-group pull-right\">"+
        "<a href='"+url+'/hr/recruitment/employee/show/'+associate_id+"' target=\"_blank\" class=\"btn btn-sm btn-success\" title=\"Profile\"><i class=\"glyphicon glyphicon-user\"></i></a>"+ 
        "<a href='"+url+'/hr/recruitment/employee/edit/'+associate_id+"'  class=\"btn btn-sm btn-success\" title=\"Basic Info\"><i class=\"glyphicon glyphicon-bold\"></i></a>"+
        "<a href='"+url+'/hr/recruitment/operation/advance_info_edit/'+associate_id+"'  class=\"btn btn-sm btn-info\" title=\"Advance Info\"><i class=\"glyphicon  glyphicon-font\"></i></a>"+
        "<a href='"+url+'/hr/payroll/employee-benefit?associate_id='+associate_id+"' class=\"btn btn-sm btn-primary\" title=\"Benefits\"><i class=\"fa fa-usd\"></i></a>"+
        "<a href='"+url+'/hr/ess/medical_incident?associate_id='+associate_id+"'  class=\"btn btn-sm btn-warning\" title=\"Medical Incident\"><i class=\"fa fa-stethoscope\"></i></a>"+
        "<a href='"+url+'/hr/operation/servicebook?associate_id='+associate_id+"' class=\"btn btn-sm btn-danger\" title=\"Service Book\"><i class=\"fa fa-book\"></i></a>"+
    "</div>"; 
    $("#newBtn").html(newUrl);
}
 

$(document).ready(function(){   
    
    var action_element = $("#form-element");
    var associate_id = '{{ request()->get("associate_id") }}';

    $(window).on("load", function(){
        if (associate_id) 
        {
            drawNewBtn(associate_id);
            ajaxLoad(associate_id);
        }
    });
    
    $("#associate_id").on("change", function(){ 
        drawNewBtn($(this).val());
        ajaxLoad($(this).val());
    });

    function ajaxLoad(associate_id){
        $('.app-loader').show();
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
        $('.app-loader').hide();
    }

    
});     
</script>
<script type="text/javascript">
    $(document).ready(function(){


        $('body').on('change','#page1',function () {
            // console.log($(this).val());
            var fileExtension = ['pdf','doc','docx','jpg','jpeg','png','xls','xlsx'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                $('#upload_error_1').show();
                $.notify("Please Upload only xls/xlsx type file.", 'error');
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
                $.notify("Please Upload only xls/xlsx type file.", 'error');
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
                $.notify("Please Upload only xls/xlsx type file.", 'error');
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
                $.notify("Please Upload only xls/xlsx type file.", 'error');
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
                $.notify("Please Upload only xls/xlsx type file.", 'error');
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
                $.notify("Please Upload only xls/xlsx type file.", 'error');
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
                $.notify("Please Upload only xls/xlsx type file.", 'error');
                $(this).val('');
            }
            else{ 
                    $('#upload_error_7').hide();
                }
        }); 
    });
</script>
@endpush
@endsection