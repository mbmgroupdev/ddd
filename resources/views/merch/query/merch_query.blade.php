@extends('merch.index')
@push('css')
<style type="text/css">
    hr {
        margin-top: 11px;
        margin-bottom: 10px;
        border-top: 1px solid #eee;
    }
    a .profile-info-row{
        width: 100%;display: block;
    }
    a .profile-info-row:first-child, a.profile-info-name:first-child  {
        display: table-cell;border-top: 1px dotted #D5E4F1;
    }
   .profile-info-name {
        width: 140px!important;
    }
   .profile-info-value{
        min-width: 140px!important;text-align: right;
    }
    p.search-title{
        margin:10px 0;
    }
    .infobox-data-number{
        display: block;
        font-size: 22px;
        margin: 2px 0 4px;
        position: relative;
        text-shadow: 1px 1px 0 rgba(0,0,0,.15);
    }
    .pricing-box:hover{
        cursor: pointer;
    }
    .fc-event-container a{margin-top: 10px; padding: 10px;}
    .modal-dialog{width: 600px!important;}
    div.dataTables_wrapper, div.dataTables_processing {
        background-color: transparent !important;
        z-index: 999 !important;
        border-color: transparent !important;
    }
    #hr_search_input {
        padding: 25px 0px;
    }
    .progress[data-percent]:after {
        color: #000 !important;
    }
    .search-result-all{margin: 0;}
    .search-result-div{}
    #searchContent .panel-info{min-height: 300px;}
    .sum-delivery, .sum-incomplete{    
        background: #f9f9f9;
        border: none;
        padding: 5px 10px;
    }
    .sum-delivery  .profile-info-name,.sum-delivery .profile-info-value{color: #2e8965;font-weight: 500;}
    .sum-incomplete .profile-info-name,.sum-incomplete  .profile-info-value{color: #d89807; font-weight: 500;}
    .sum-row{    
        background: #f1f1f1;
        border: none;
        padding: 5px 10px;
    }
    .row-sum{border-top:1px solid #f1f1f1!important; }
    .sum-row .profile-info-name,.sum-row  .profile-info-value,.row-sum .profile-info-name,.row-sum  .profile-info-value{color: #1a1a1a;}
    #dataTables{display: block; white-space: nowrap; width: 100%; overflow-x: auto;}
    .dataTables_wrapper .dataTables_processing {
        position: absolute;
        top: 30%;
        left: 50%;
        width: 30%;
        height: 80px;
        margin-left: -20%;
        margin-top: -25px;
        padding-top: 20px;
        text-align: center;
        font-size: 1.7em;
        background-color:White;
    }
    .dataTables_wrapper .dt-buttons {
        float:right;
        text-align:center;
    }
    .dataTables_length{
        float:left;
    }
    .dataTables_filter{
        display: none;
    }
    .errorparam {
        padding: 10px;
        text-align: center;
        font-size: 13px;
        color: #ff2828;
        display: block;
        border: 1px solid #ff000054;
        border-radius: 5px;
    }
    a[href]:after { content: none !important; }
    td:hover{cursor: pointer;color:#4C8FBD;}
    #dataTables  td:hover{color:#393939;}
    @media only screen and (max-width: 1280px){
        .search-result-div{width: 33.33%;}
    }
    @media only screen and (max-width: 919px) {
        #dataTables{display: block; white-space: nowrap; width: 100%; overflow-x: auto;}
    }
    @media only screen and (max-width: 767px) {
        .search-result-div{width: 50%;}
        #form_search_div{margin-top: 50px;}
        #dateDiv{margin-top: 20px; padding-left: 0px;}
        #yearDiv{margin-top: 20px; padding-left: 0px;}
        #monthDiv{margin-top: 20px; padding-left: 0px;}
        #rangeSection{margin-top: 20px;}
        #rangeMonth{margin-top: 20px;}
        #monthFromDiv{padding-left: 0px;}
        #categoryDiv{padding-left: 0px !important;padding-top: 10px; padding-bottom: 10px;}
        #typeDiv{padding-left: 0px !important;padding-top: 10px;}
        #rangeFromDiv{padding-left: 0px;}
    }
    @media only screen and (max-width: 579px) {
        .search-result-div{width: 100%;}
    }
    @media only screen and (max-width: 500px) {
        .dataTables_wrapper .dt-buttons {float: left; text-align: center;}
    }
    @media print {
        a[href]:after {
          content: " (" attr(href) ")";
        }
        .widget-color-green2 {
            border-color: #2E8965;
        }
        .widget-color-green2>.widget-header {
            background-color: #2E8965!important;
            border-color: #2E8965;
        }
        *{ color-adjust: exact; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
    *{ color-adjust: exact; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
</style>
@endpush


@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#">Query</a>
                </li>
            </ul>
        </div>

        <div class="page-content">


            <div class="row">
                <div class="col-xs-12">
                    <!-- Display Erro/Success Message -->
                    @include('inc/message')
                </div>
                @php
                    $allQueryStrings = Request::all();
                    $categorySelect = Request::get('category');
                    $typeSelect     = Request::get('type');
                    $dateInput      = Request::get('date');
                    $monthInput     = Request::get('month');
                    $yearInput      = Request::get('year');
                    $rangeFrom      = Request::get('rangeFrom');
                    $rangeTo        = Request::get('rangeTo');
                    $monthFrom      = Request::get('monthFrom');
                    $monthTo        = Request::get('monthTo');
                    $typeDisabled = 'disabled="disabled"';
                    if(!empty($monthInput) || !empty($yearInput) || !empty($rangeFrom) || !empty($rangeTo) || !empty($dateInput)|| !empty($monthTo) || !empty($monthFrom)) {
                        $typeDisabled = '';
                    }
                @endphp
                <div class="col-xs-12">
                {{ Form::open(['url'=>'#', 'method'=>'get', 'class'=>'form-horizontal', 'autocomplete'=>"off"]) }}
                    <div class="panel panel-info col-sm-12 col-xs-12" id="hr_search_input">
                        <div class="col-sm-3">
                            <label class="col-sm-5 control-label no-padding">Category <span style="color: red; vertical-align: top;">*</span> </label>
                            <div class="col-sm-7" id="categoryDiv">
                                <select name="category" id="categorySelect" class="form-control">
                                    <option value="">Please select one</option>

                                    <option value="orderbycreate" {{ Custom::sselected($categorySelect,'orderbycreate') }}>Order by Create</option>
                                    <option value="orderbydelivery" {{ Custom::sselected($categorySelect,'orderbydelivery') }}>Order by Delivery</option>
                                    <option value="team" {{ Custom::sselected($categorySelect,'team') }}>Order by Team</option>
                                    <option value="style" {{ Custom::sselected($categorySelect,'style') }}>Style</option>
                                    <option value="resv" {{ Custom::sselected($categorySelect,'resv') }}>Reservation</option>
                                    <option value="po" {{ Custom::sselected($categorySelect,'po') }}>Purchase Order</option>
                                    <option value="pi" {{ Custom::sselected($categorySelect,'pi') }}>Proforma Invoice (PI)</option>
                                    <option value="orderbooking" {{ Custom::sselected($categorySelect,'orderbooking') }}>Order Booking</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-sm-4 control-label no-padding"> Type <span style="color: red; vertical-align: top;">*</span></label>
                            <div class="col-sm-8" id="typeDiv">
                                <select name="type" id="typeSelect" class="form-control" {{ $typeDisabled }}>
                                    <option value="">Please select one</option>
                                    <option value="date" {{ Custom::sselected($typeSelect,'date') }}>Date</option>
                                    <option value="month" {{ Custom::sselected($typeSelect,'month') }}>Month</option>
                                    <option value="year" {{ Custom::sselected($typeSelect,'year') }}>Year</option>
                                    <option value="range" {{ Custom::sselected($typeSelect,'range') }}>Range</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2 extra-input" id="dateDiv" style="display: {{ $dateInput!=''?'block':'none' }};">
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" class="form-control datepicker" id="dateInput" name="dateInput" value="{{ $dateInput }}" placeholder="Select date"/>
                            </div>
                        </div>
                        <div class="col-sm-2 extra-input" id="monthDiv" style="display: {{ $monthInput!=''?'block':'none' }};">
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" class="form-control monthYearpicker" id="monthInput" name="monthInput" value="{{ $monthInput }}" placeholder="Month-Year"/>
                            </div>
                        </div>
                        <div class="col-sm-2 extra-input" id="yearDiv" style="display: {{ $yearInput!=''?'block':'none' }};">
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" class="form-control yearpicker" id="yearInput" name="yearInput" value="{{ $yearInput }}" placeholder="Select year"/>
                            </div>
                        </div>
                        <div id="rangeSection" class="extra-input" style="display: {{ $rangeFrom!=''?'block':'none' }};">
                            <div class="col-sm-2">
                                <div class="col-sm-12 col-xs-6" id="rangeFromDiv">
                                    <input type="text" class="form-control datepicker" id="rangeFrom" name="rangeFrom" placeholder="From" value="{{ $rangeFrom }}"/>
                                </div>
                            </div>
                            <div class="col-sm-2">                                
                                <div class="col-sm-12 col-xs-6">
                                    <input type="text" class="form-control datepicker" id="rangeTo" name="rangeTo" placeholder="To" value="{{ $rangeTo }}"/>
                                </div>                                
                            </div>
                        </div>
                        <div id="rangeMonth" class="extra-input" style="display: {{ $monthFrom!=''?'block':'none' }};">
                            <div class="col-sm-2">
                                <div class="col-sm-12 col-xs-6" id="monthFromDiv">
                                    <input type="text" class="form-control monthYearpicker" id="monthFrom" name="monthFrom" placeholder="From" value="{{ $monthFrom }}"/>
                                </div>
                            </div>
                            <div class="col-sm-2">                                
                                <div class="col-sm-12 col-xs-6">
                                    <input type="text" class="form-control monthYearpicker" id="monthTo" name="monthTo" placeholder="To" value="{{ $monthTo }}"/>
                                </div>                                
                            </div>
                        </div>
                        <div class="col-sm-2 text-center" id="form_search_div">
                            <button class="btn btn-info btn-sm" id="form_search" type="button" {{ $typeDisabled }}>
                                <i class="ace-icon fa fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <!-- /.row -->
                {{ Form::close() }}
                <!-- PAGE CONTENT ENDS -->
                </div>
                <div class="col-xs-12" style="display:block;height:30px;">
                    <div class="progress" id="result-process-bar" style="display: none;">
                        <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress_bar_main" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                          0%
                        </div>
                    </div>
                </div>
                <input type="hidden" value="0" id="setFlug" />
                <div class="col-xs-12">
                    <!-- <button type="button" onClick="printMe('searchContent')" class="showprint btn btn-warning btn-sm " title="Print">
                        <i class="fa fa-print"></i>
                    </button> -->
                    <div id="searchContent"></div>
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
    <script src="{{ asset('assets/js/chartjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootbox.js') }}"></script>

    <script type="text/javascript">
        $('#rangeTo, #rangeFrom').on('dp.change',function() {

            var to_date = new Date($('#rangeTo').val());
            var from_date  = new Date($('#rangeFrom').val());
            

                if(from_date > to_date){
                    //console.log(to_date,from_date);
                    alert("Error!! From Date is latest than TO Date");
                    $(this).val('');
                }

        });
        // page loader function
        function preLoader(html='searchContent'){
            //return $('#'+html).html('<center><i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:80px;margin-top:100px;"></i></center>');
        }
        // get page type
        function getPageType(queryStrings){
            var unit = queryStrings.unit;
            var view = queryStrings.view;
            if(view === 'allunit'){
                return 'unit';
            }else if(view === 'allbuyer'){
                return 'buyer';
            }else if(view === 'allorder'){
                return 'order';
            } else if(view === 'allstyle'){
                return 'style';
            }else if(view === 'reservation'){
                return 'resv';
            }else if(view === 'allpo'){
                return 'po';
            }else if(view === 'allteam'){
                return 'team';
            }else if(view === 'executive'){
                return 'executive';
            }else if(view === 'allpi'){
                return 'pi';
            }else if(view === 'allorderbooking'){
                return 'orderbooking';
            }else if(view === 'allsupplier'){
                return 'supplier';
            }
            else {
                if(typeof unit !== "undefined" && typeof buyer === "undefined") {
                    return 'buyer';
                }if(typeof unit !== "undefined" && typeof buyer !== "undefined") {
                    return 'style';
                }else {
                    return 'all';
                }
            }
        }
        // check date valid or not
        function isValidDate(dateString) {
            var regEx = /^\d{4}-\d{2}-\d{2}$/;
            if(!dateString.match(regEx)) return false;  // Invalid format
            var d = new Date(dateString);
            var dNum = d.getTime();
            if(!dNum && dNum !== 0) return false; // NaN value, Invalid date
            return d.toISOString().slice(0,10) === dateString;
        }

        $(document).ready(function() { 
            var allQueryStrings = [];
            allQueryStrings = {!! json_encode($allQueryStrings) !!};
            if(!jQuery.isEmptyObject(allQueryStrings)) {
                var category = allQueryStrings.category;
                sortTypeOption(category);
                var pageType = getPageType(allQueryStrings);
                preLoader();
                var url = getCategoryWiseUrl(allQueryStrings.category,pageType);
                //console.log(allQueryStrings.category);
                // url = '{{ url('hr/search') }}';
                // attAjaxCall(url,allQueryStrings.date);
                var obj = {
                    'category': allQueryStrings.category,
                    'type': allQueryStrings.type,
                    'date': allQueryStrings.date,
                    'unit': allQueryStrings.unit,
                    'month': allQueryStrings.month,
                    'year': allQueryStrings.year,
                    'rangeFrom': allQueryStrings.rangeFrom,
                    'rangeTo': allQueryStrings.rangeTo,
                    'view' : allQueryStrings.view,
                    'monthFrom': allQueryStrings.monthFrom,
                    'monthTo': allQueryStrings.monthTo
                }
                var errorparam='<div class="errorparam">Error! Invalid Parameter.</div>'
                // console.log(obj, url);
                if(allQueryStrings.type == 'date' && typeof allQueryStrings.date !== "undefined" && typeof allQueryStrings.attstatus === "undefined")  {
                    if(!isValidDate(allQueryStrings.date)){
                        $('#searchContent').html(errorparam);
                    } else {
                        attAjaxCall(url,obj);
                    }
                }
                else if(allQueryStrings.type == 'month' && typeof allQueryStrings.month !== "undefined") {
                    attAjaxCall(url,obj);
                }
                else if(allQueryStrings.type == 'year' && typeof allQueryStrings.year !== "undefined") {
                    attAjaxCall(url,obj);
                }
                else if(allQueryStrings.type == 'range' && ((typeof allQueryStrings.rangeFrom !== "undefined" && typeof allQueryStrings.rangeTo !== "undefined") || (typeof allQueryStrings.monthFrom !== "undefined" && typeof allQueryStrings.monthTo !== "undefined"))) {
                        attAjaxCall(url,obj);
                }
                else {
                    $('#searchContent').html(errorparam);
                }
            }
        });
        // cancle all ajax call request
        $.ajaxQ = (function(){
          var id = 0, Q = {};

          $(document).ajaxSend(function(e, jqx){
            jqx._id = ++id;
            Q[jqx._id] = jqx;
          });
          $(document).ajaxComplete(function(e, jqx){
            delete Q[jqx._id];
          });

          return {
            abortAll: function(){
              var r = [];
              $.each(Q, function(i, jqx){
                r.push(jqx._id);
                jqx.abort();
              });
              return r;
            }
          };
        })();

        // global variables
        var categorySelect = $('#categorySelect').val();
        var typeSelect  = $('#typeSelect').val();
        var dateInput   = $('#dateInput').val();
        var monthInput  = $('#monthInput').val();
        var yearInput   = $('#yearInput').val();
        var rangeFrom   = $('#rangeFrom').val();
        var rangeTo     = $('#rangeTo').val();
        var monthFrom   = $('#monthFrom').val();
        var monthTo     = $('#monthTo').val();

        //get url category wise
        function getCategoryWiseUrl(category,pageType) {
            var url = '';
            if(category == 'orderbycreate' || category == 'orderbydelivery') {
                if(pageType == 'all') {
                    url = '{{ url('merch/query/merch_order_query') }}';
                } else {
                    url = '{{ url('merch/query/merch_order_query') }}'+'_'+pageType;
                }
            }
            if(category == 'team') {
                if(pageType == 'all') {
                    url = '{{ url('merch/query/merch_team_query') }}';
                } else {
                    url = '{{ url('merch/query/merch_team_query') }}'+'_'+pageType;
                }
            }
            if(category=='style'){
                if(pageType == 'all') {
                    url = '{{ url('merch/query/merch_style_query') }}';
                } else {
                    url = '{{ url('merch/query/merch_style_query') }}'+'_'+pageType;
                }
            }
            if(category=='resv'){
                if(pageType == 'all') {
                    url = '{{ url('merch/query/merch_resv_query') }}';
                } else {
                    url = '{{ url('merch/query/merch_resv_query') }}'+'_'+pageType;
                }
            }
            if(category=='po'){
                if(pageType == 'all') {
                    url = '{{ url('merch/query/merch_po_query') }}';
                } else {
                    url = '{{ url('merch/query/merch_po_query') }}'+'_'+pageType;
                }
            }
            if(category=='pi'){
                if(pageType == 'all') {
                    url = '{{ url('merch/query/merch_pi_query') }}';
                } else {
                    url = '{{ url('merch/query/merch_pi_query') }}'+'_'+pageType;
                    console.log(url);
                }
            }  
            if(category=='orderbooking'){
                if(pageType == 'all') {
                    url = '{{ url('merch/query/merch_ob_query') }}';
                } else {
                    url = '{{ url('merch/query/merch_ob_query') }}'+'_'+pageType;
                    console.log(url);
                }
            }  
            return url;
        }

        // inisical from search from button click
        $('#form_search').on('click', function() {
            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            $('#searchContent').hide();
            categorySelect = $('#categorySelect').val();
            typeSelect  = $('#typeSelect').val();
            monthInput  = $('#monthInput').val();
            dateInput   = $('#dateInput').val();
            yearInput   = $('#yearInput').val();
            rangeFrom   = $('#rangeFrom').val();
            rangeTo     = $('#rangeTo').val();
            monthFrom   = $('#monthFrom').val();
            monthTo     = $('#monthTo').val();
            var url = getCategoryWiseUrl(categorySelect,'all');
            $.ajax({
                url: url,
                type: 'get',
                data: {
                    category: categorySelect,
                    type: typeSelect,
                    date: dateInput,
                    month: monthInput,
                    year: yearInput,
                    rangeFrom: rangeFrom,
                    rangeTo: rangeTo,
                    monthFrom: monthFrom,
                    monthTo: monthTo
                },
                success: function(res){
                    $('#searchContent').html(res.page);
                    window.history.pushState('','',res.url);
                    $('#setFlug').val(1);
                    processbar('success');
                    $('#searchContent').show();
                    // console.log(res);
                },
                error: function(){
                    console.log('error occored');
                    $('#setFlug').val(2);
                    processbar('error');
                }
            });
        });

        $(document).on('click','.search_all', function() {
            preLoader();
            $.ajaxQ.abortAll();
            categorySelect = $('#categorySelect').val();
            typeSelect  = $('#typeSelect').val();
            monthInput  = $('#monthInput').val();
            dateInput   = $('#dateInput').val();
            yearInput   = $('#yearInput').val();
            rangeFrom   = $('#rangeFrom').val();
            rangeTo     = $('#rangeTo').val();
            monthFrom   = $('#monthFrom').val();
            monthTo     = $('#monthTo').val();
            var data = {
                category: categorySelect,
                type: typeSelect,
                date: dateInput,
                month: monthInput,
                year: yearInput,
                rangeFrom: rangeFrom,
                rangeTo: rangeTo,
                monthFrom: monthFrom,
                monthTo: monthTo
            };
            //console.log(data);
            var url = getCategoryWiseUrl(categorySelect,'all');
            attAjaxCall(url, data);
        });

        $(document).on('click','.search_unit', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var url = getCategoryWiseUrl(categorySelect,'unit');
            attAjaxCall(url);
        });

        $(document).on('click','.search_country', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var url = getCategoryWiseUrl(categorySelect,'country');
            attAjaxCall(url);
        });

        $(document).on('click','.search_buyer', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit')
            };
            var url = getCategoryWiseUrl(categorySelect,'buyer');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_season', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                buyer: $(this).data('buyer')
            };
            var url = getCategoryWiseUrl(categorySelect,'season');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_order', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit'),
                buyer: $(this).data('buyer'),
                product: $(this).data('product'),
                team: $(this).data('team'),
                executive: $(this).data('executive'),
                status: $(this).data('status')
            };
            console.log(data);
            var url = getCategoryWiseUrl(categorySelect,'order');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_po', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                country: $(this).data('country')
            };
            console.log(data);
            var url = getCategoryWiseUrl(categorySelect,'po');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_pi', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit'),
                buyer: $(this).data('buyer'),
                supplier: $(this).data('supplier')
            };
            console.log(categorySelect);
            var url = getCategoryWiseUrl(categorySelect,'pi');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_supplier', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit'),
                buyer: $(this).data('buyer')
            };
            console.log(categorySelect);
            var url = getCategoryWiseUrl(categorySelect,'supplier');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_team', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit')
            };
            console.log(data);
            var url = getCategoryWiseUrl(categorySelect,'team');
            attAjaxCall(url,data);
        });
        $(document).on('click','.search_executive', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                team: $(this).data('team')
            };
            console.log(data);
            var url = getCategoryWiseUrl(categorySelect,'executive');
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_style', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                buyer: $(this).data('buyer'),
                ptype: $(this).data('ptype'),
                product: $(this).data('product')
            };
            var url = getCategoryWiseUrl(categorySelect,'style');
            console.log(data);
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_resv', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit'),
                buyer: $(this).data('buyer'),
                product: $(this).data('product')
            };
            var url = getCategoryWiseUrl(categorySelect,'resv');
            console.log(data);
            attAjaxCall(url,data);
        });

        $(document).on('click','.search_booking', function() {
            preLoader();
            $.ajaxQ.abortAll();
            var data = {
                unit: $(this).data('unit'),
                buyer: $(this).data('buyer'),
                suupplier: $(this).data('suupplier')
            };
            var url = getCategoryWiseUrl(categorySelect,'ob');
            console.log(data);
            attAjaxCall(url,data);
        });



        // global ajax call
        function attAjaxCall(url,data,resultDiv='searchContent') {
            //console.log(data);
            $("#result-process-bar").css('display', 'block');
            $('#setFlug').val(0);
            processbar(0);
            $('#searchContent').hide();
            $.ajax({
                url: url,
                type: 'get',
                data: data,
                success: function(res){
                    $('#'+resultDiv).html(res.page);
                    window.history.pushState('','',res.url);
                    //console.log(res);
                    $('#setFlug').val(1);
                    processbar('success');
                    $('#searchContent').show();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log('Error status: ' + textStatus);
                    console.log('Error message: ' + errorThrown);
                    console.log('error occored');
                    $('#setFlug').val(2);
                    processbar('error');
                }
            });
        }

        var incValue = 1;
        function processbar(percentage) {
            var setFlug = $('#setFlug').val();
            if(parseInt(setFlug) === 1){
                var percentageVaule = 99;
                $('#progress_bar_main').html(percentageVaule+'%');
                $('#progress_bar_main').css({width: percentageVaule+'%'});
                $('#progress_bar_main').attr('aria-valuenow', percentageVaule+'%');
                setTimeout(() => {
                    $("#result-process-bar").css('display', 'none');
                    percentageVaule = 0;
                    percentage = 0;
                    $('#progress_bar_main').html(percentageVaule+'%');
                    $('#progress_bar_main').css({width: percentageVaule+'%'});
                    $('#progress_bar_main').attr('aria-valuenow', percentageVaule+'%');
                }, 1000);
            }else if(parseInt(setFlug) === 2){
                console.log('error');
            }else{
                // set percentage in progress bar
                percentage = parseFloat(parseFloat(percentage) + parseFloat(incValue)).toFixed(2);
                $('#progress_bar_main').html(percentage+'%');
                $('#progress_bar_main').css({width: percentage+'%'});
                $('#progress_bar_main').attr('aria-valuenow', percentage+'%');
                if(percentage < 70 ){
                    incValue = 1;
                    // processbar(percentage);
                }else if(percentage < 80){
                    incValue = 0.8;
                }else if(percentage < 90){
                    incValue = 0.5;
                }else if(percentage < 98){
                    incValue = 0.1;
                }else{
                    percentage = 'error';
                }
                setTimeout(() => {
                    processbar(percentage);
                }, 1000);
            }
        }

        // append option in select
        function appendOption(value1) {
            if($('#typeSelect option[value='+value1+']').length == 0) {
                $('#typeSelect').append($('<option>', {
                    value: value1,
                    text: value1.charAt(0).toUpperCase() + value1.slice(1)
                }));
            }
        }

        // sort type option
        function sortTypeOption(category) {
            if(category) {
                $('#typeSelect').removeAttr('disabled');
                if(category=='orderbycreate' || category=='orderbydelivery' || category=='style' || category=='po' || category=='team' || category=='pi') {
                    appendOption('date');
                    appendOption('month');
                    appendOption('year');
                    appendOption('range');
                }
                if(category=='resv'){
                    appendOption('month');
                    appendOption('range');
                    $("#typeSelect option[value='date']").remove();
                }
            } else {
                $('#typeSelect').attr('disabled', 'disabled');
            }
        }



        /*---------custom code for ot-------*/

        /* ====== attendence search ======= */
        $('#categorySelect').on('change', function() {
            var category = $(this).val();
            sortTypeOption(category);
            $("#typeSelect").val($("#typeSelect option:first").val()).change();
            $('#form_search').attr('disabled', 'disabled');
            $('.extra-input').hide();
        });

        $('#typeSelect').on('change', function() {
            var type = $(this).val();
            var category= $('#categorySelect').val();
            $('#form_search').removeAttr('disabled');
            if(type=='month') {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').show();
                $('#monthDiv input').val('{{ date('M-Y') }}');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
                $('#rangeMonth').hide();
                $('#rangeMonth input').val('');
                
            }
            else if(type=='date') {
                $('#dateDiv').show();
                $('#dateDiv input').val('{{ date('Y-m-d') }}');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
                $('#rangeMonth').hide();
                $('#rangeMonth input').val('');
            }
            else if(type=='year') {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').show();
                $('#yearDiv input').val('{{ date('Y') }}');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
                $('#rangeMonth').hide();
                $('#rangeMonth input').val('');
            }
            else if(type=='range' && category!='resv') {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').show();
                $('#rangeMonth').hide();
                $('#rangeMonth input').val('');
            }
            else if(type=='range' && category=='resv') {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').hide();
                $('#rangeMonth').show();
                $('#rangeSection input').val('');
            }
            else {
                $('#dateDiv').hide();
                $('#dateDiv input').val('');
                $('#monthDiv').hide();
                $('#monthDiv input').val('');
                $('#yearDiv').hide();
                $('#yearDiv input').val('');
                $('#rangeSection').hide();
                $('#rangeSection input').val('');
                $('#rangeMonth').hide();
                $('#rangeMonth input').val('');
            }
        });
        function printMe(divName)
        { 
            var DocumentContainer = document.getElementById(divName);
            var WindowObject = window.open('', 'PrintWindow', 'width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes');
            WindowObject.document.writeln('<!DOCTYPE html>');
            WindowObject.document.writeln('<html><head><title></title>');
            WindowObject.document.writeln('<link rel="stylesheet" href="/assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" /><link rel="stylesheet" href="/assets/css/ace-skins.min.css" /><link rel="stylesheet" href="/assets/css/ace-rtl.min.css" /><link rel="stylesheet" href="/assets/css/bootstrap.min.css" /><style>.profile-info-row{width:100%!important;}</style>');
            WindowObject.document.writeln('</head><body>')

            WindowObject.document.writeln(DocumentContainer.innerHTML);

            WindowObject.document.writeln('</body></html>');

            WindowObject.document.close();
            WindowObject.focus();
            /*WindowObject.print();
            WindowObject.close();*/
        }
    </script>
@endpush
@endsection
