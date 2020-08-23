@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
@push('css')
    <style>
        html {
         scroll-behavior: smooth;
        }
        #load{
            width:100%;
            height:100%;
            position:fixed;
            z-index:9999;
            background:url({{asset('assets/rubel/img/loader.gif')}}) no-repeat 35% 70%  rgba(192,192,192,0.1);
            visibility: hidden;

        }
        .tbl-header{
            border: 1px solid;
            font-weight: bold;
        }
        .tbl-header th{
            border-color: #31708f;
            padding: 10px !important;
            font-size: 12px;
        }
        .grand_total{
            /*font-weight: bold;*/
            font-size: 12px;
            color: #fff;
            height: 20px;
            padding: 5px !important;
        }
        .grand_total td{
            /*font-weight: bold;*/
            font-size: 12px;
            color: #fff;
            height: 20px;
            padding: 5px !important;
        }

        tbody>tr>td{
            padding-left: 10px !important;
            padding-top: 5px !important;
            padding-bottom: 5px !important;
            padding-right: 10px !important;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Human Resource</a>
                </li>
                <li>
                    <a href="#">Reports</a>
                </li>
                <li class="active"> Attendance Summary Report</li>
            </ul><!-- /.breadcrumb -->
        </div>
        <div class="page-content">
            <div id="load"></div>
            <?php $type='attendance_2'; ?>
            @include('hr/reports/attendance_radio')
            <div class="page-header">
                <h1>Reports<small><i class="ace-icon fa fa-angle-double-right"></i> Attendance Report</small></h1>
            </div>
            <div class="row">

                <form role="form"  id="searchform" method="get" action="{{ url('hr/reports/attendance_report_2') }}">
                    <div class="col-sm-10">
                        <div class="form-group">
                            <div class="col-sm-4" style="padding-bottom: 10px;">
                                {{ Form::select('unit', $unitList, request()->unit, ['placeholder'=>'Select Unit', 'id'=>'unit',  'style'=>'width:100%', 'data-validation'=>'required', 'data-validation-error-msg'=>'The Unit field is required']) }}
                            </div>
                            <div class="col-sm-4" style="padding-bottom: 40px;">
                                <input type="text" name="date" id="date" class="datepicker col-xs-12" value="{{ request()->date }}" data-validation="required" autocomplete="off" placeholder="Y-m-d" style="height: 32px;" />
                            </div>
                            <div class="col-sm-4">
                                <button id="report" type="button" class="btn btn-primary btn-sm">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                                <div class="buttons hide" style="display: initial;">
    
                                    <button type="button" onClick="printMe('PrintArea')" class="btn btn-warning btn-sm" title="Print">
                                        <i class="fa fa-print"></i>
                                    </button>
                                    <button type="button"  id="excel"  class="showprint btn btn-success btn-sm" title="Excel"><i class="fa fa-file-excel-o" style="font-size:14px"></i>
                                    </button>
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="row" id="html-2-pdfwrapper">
                <div class="col-xs-12">
                    <div id="generate-report" style="margin:15px;">
                        
                    </div>
                    
                </div>
            </div>

        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
    const loader = '<p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p>';

    $(document).ready(function(){
        $('select.associates').select2({
            placeholder: 'Select Associate\'s ID',
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

        $('#excel').click(function(){
            var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
                    location.href=url
                return false
            })

    })
    //  Loader
    document.onreadystatechange = function () {
        var state = document.readyState
        if (state == 'interactive') {
           document.getElementById('html-2-pdfwrapper').style.visibility="hidden";
        } else if (state == 'complete') {
            setTimeout(function(){
                document.getElementById('interactive');
                document.getElementById('load').style.visibility="hidden";
                document.getElementById('html-2-pdfwrapper').style.visibility="visible";
                document.getElementById('html-2-pdfwrapper').scrollIntoView();
            },1000);
        }
    }


    function printMe(divName) {
        var style_sheet = '' +
        '<style type="text/css">' +
        'h4 {' +
            'text-align:left; page-break-before: always; padding:0.2em; margin-bottom: 30px;'
        '}' +
        '</style>';
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write(style_sheet);
        myWindow.document.write(document.getElementById(divName).innerHTML);
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    $(document).on("click",'#report', function(){ 
        var unit = $('#unit').val(),
            date = $('#date').val(),
            btn = $(this);

        if(unit && date){
            $('.buttons').addClass('hide');
            btn.attr("disabled",true);
            $("#generate-report").html(loader);
            $.ajax({
                url : "{{ url('hr/reports/get_att_summary') }}",
                type: 'post',
                data: {unit : unit, date : date},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(data)
                {
                    $("#generate-report").html(data);
                    btn.attr("disabled",false);
                    $('.buttons').removeClass('hide');
                },
                error: function()
                {
                    alert('failed...');
                    btn.attr("disabled",false);
                }
            });
        }else{
            alert('Please select unit & date!');
            $("#generate-report").html('');

        }
    });


    function attLocation(loc){
        window.location = loc;
    }

</script>
@endsection
