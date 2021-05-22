@extends('hr.layout')
@section('title', 'Attendance Form')

@section('main-content')
    @push('css')
        <style>
            .view:hover, .view:hover {
                color: #ccc !important;

            }
            .view i {
                font-size: 25px;
                border: 1px solid #000;
                border-radius: 3px;
                padding: 0px 3px;
            }

            .view.active i {
                background: linear-gradient(to right, #0db5c8 0, #089bab 100%);
                color: #fff;
                border-color: #089bab;
            }

            .iq-card .iq-card-header {
                margin-bottom: 10px;
                padding: 15px 15px;
                padding-bottom: 0px;
            }

            .select2-container .select2-selection--single, .month-report {
                height: 30px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 30px !important;
            }

            table, th, td {
                border: 1px solid black;
                font-family: Tahoma, sans-serif;
                font-size: 10pt;
            }

            td {
                vertical-align: top;
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
                        <a href="#">Operation</a>
                    </li>
                    <li class="active">Attendance Form</li>
                </ul>
            </div>
            <div class="page-content">

                <div class="row">
                    <div class="col">
                        <form role="form" method="post" action="{{ url("hr/operation/attendance-form/report") }}" id="formReport">
                            @csrf
                            <div class="iq-card" id="result-section">
                                <div class="iq-card-header d-flex mb-0">
                                    <div class="iq-header-title w-100">
                                        <div class="row">
                                            <div style="width: 10%; float: left; margin-left: 15px; margin-top: 2px;">
                                                <div id="result-section-btn">
                                                    <button class="btn btn-sm btn-primary hidden-print"
                                                            onclick="printDiv('report_section')" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="Print Report"><i
                                                            class="las la-print"></i></button>

                                                </div>
                                            </div>
                                            <div class="text-center" style="width: 47%; float: left">

                                            </div>
                                            <input type="hidden" id="reportFormat" name="report_format" value="1">
                                            <div style="width: 40%; float: left">
                                                <div class="row">
                                                    <div class="col-6"></div>
                                                    <div class="col-3 p-0">
                                                        <div class="form-group has-float-label has-required ">
                                                            <input type="month"
                                                                   class="report_date form-control month-report"
                                                                   id="yearMonth" name="year_month"
                                                                   placeholder=" Month-Year" required="required"
                                                                   value="{{ $yearMonth }}" max="{{ date('Y-m') }}"
                                                                   autocomplete="off">
                                                            <label for="yearMonth">Month</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 pl-0">
                                                        <div class="text-right">
                                                            <a class="btn view no-padding clear-filter"
                                                               data-toggle="tooltip" data-placement="top" title=""
                                                               data-original-title="Clear Filter">
                                                                <i class="las la-redo-alt"
                                                                   style="color: #f64b4b; border-color:#be7979"></i>
                                                            </a>
                                                            <a class="btn view no-padding filter" data-toggle="tooltip"
                                                               data-placement="top" title=""
                                                               data-original-title="Advanced Filter">
                                                                <i class="fa fa-filter"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="iq-card-body no-padding">
                                    <div class="result-data" id="result-data">

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- /.page-content -->
        </div>
    </div>
    {{-- modal employee salary --}}

    <div class="modal right fade" id="right_modal_lg-group" tabindex="-1" role="dialog"
         aria-labelledby="right_modal_lg-group">
        <div class="modal-dialog modal-lg right-modal-width" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title=""
                       data-original-title="Back to Report">
                        <i class="las la-chevron-left"></i>
                    </a>
                    <h5 class="modal-title right-modal-title text-center" id="modal-title-right-group"> &nbsp; </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-content-result content-result" id="content-result-group">

                    </div>
                </div>

            </div>
        </div>
    </div>
    {{--  --}}
    @include('common.right-modal')
    @include('common.right-navbar')
    @push('js')
        <script src="{{ asset('assets/js/moment.min.js')}}"></script>
        <script type="text/javascript">
            $(document).ready(function (){
                $(".filter").click();
            });

        </script>
    @endpush
@endsection
