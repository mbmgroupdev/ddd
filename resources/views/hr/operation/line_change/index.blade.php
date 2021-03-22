@extends('hr.layout')
@section('title', 'Line Change')

@section('main-content')
@push('css')
<link href="{{ asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
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
                <li class="active">Line Change</li>
                <li class="top-nav-btn">
                    <a class="btn btn-primary btn-sm pull-right" href="{{ url('hr/reports/line-changes') }}"><i class="fa fa-list"></i> List Of Line Change</a>
                </li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content">
            <input type="hidden" id="base_url" value="{{ url('/') }}">
            
            <div class="row">
              <div class="col-12">
                <div class="panel panel-success">

                    <div class="panel panel-info">
                        <form class="form-horizontal" role="form" id="lineChangeForm">
                            {{ csrf_field() }} 
                        <div class="panel-body">
                            
                            <div class='row'>
                                <div class='col-sm-12 table-wrapper-scroll-y table-custom-scrollbar'>
                                    <table class="table table-bordered table-hover table-fixed" id="itemList">
                                        <thead>
                                            <tr class="text-center active">
                                                <th width="2%">
                                                    <button class="btn btn-sm btn-outline-success" id="addRow" type="button"><i class="las la-plus-circle"></i></button>
                                                </th>
                                                <th width="2%">SL.</th>
                                                <th width="12%">Associate ID</th>
                                                <th width="15%">Name</th>
                                                <th width="15%">Designation</th>
                                                <th width="8%">Start Date</th>
                                                <th width="8%">Unit</th>
                                                <th width="12%">Line</th>
                                                <th width="12%">Floor</th>
                                                <th width="8%">End Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem1" onClick="deleteItem(this.id)">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </td>
                                                <td>1</td>
                                                <td>
                                                  <input type="text" data-type="associateid" name="associate[]" id="associate_1" class="form-control autocomplete_txt" autofocus="autofocus" autocomplete="off" required>
                                                  <input type="hidden" name="asid[]" id="asid_1">
                                                </td>
                                                <td>
                                                  <input type="text" data-type="empname" name="name[]" id="name_1" class="form-control autocomplete_txt" autocomplete="off" readonly>
                                                  <input type="hidden" name="default_line[]" id="defaultline_1">
                                                </td>
                                                <td>
                                                  <input type="text" name="designation[]" id="designation_1" class="form-control" autofocus="autofocus" autocomplete="off" readonly>
                                                </td>
                                                <td>
                                                    <input type="date" name="start_date[]" id="startdate_1" class="form-control start-date" required="required" value="">
                                                </td>
                                                <td>
                                                    <select name="unit[]" id="unit_1" class="unit form-control">
                                                        @foreach($unitList as $key => $unit)
                                                        <option value="{{ $key }}">{{ $unit }}</option>
                                                        @endforeach
                                                    </select>
                                                    
                                                </td>
                                                <td>
                                                    <input type="text" name="line[]" id="line_1" class="form-control changesNo" autocomplete="off" onClick="this.select()">
                                                    <input type="hidden" name="lineid[]" id="lineid_1">
                                                    <input type="hidden" name="exist_id[]" id="existid_1">
                                                </td>
                                                <td>
                                                    <input type="text" name="floor[]" id="floor_1" class="form-control" autocomplete="off" readonly>
                                                    <input type="hidden" name="floorid[]" id="floorid_1">
                                                </td>
                                                
                                                <td>
                                                    <input type="date" name="end_date[]" id="enddate_1"  class=" form-control" placeholder="End Date" >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="submit-invoice invoice-save-btn">
                                        <button type="button" id="lineChangeFormBtn" onClick="activityProcess()" class="btn btn-outline-success btn-lg text-center"><i class="fa fa-save"></i> Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
              </div>
            </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
<script src="{{ asset('assets/js/moment.min.js')}}"></script>

<script>
    $(document).on('keypress', function(e) {
        var that = document.activeElement;
        if( e.which == 13 ) {
            if($(document.activeElement).attr('type') == 'submit'){
                return true;
            }else{
                e.preventDefault();
            }
        }            
    });
    //adds extra table rows
    var i=$('table tr').length;
    $(document).on('click','#addRow',function(){
        // check exists empty item
        var lastId = i-1;
        var lastItem = $("#associate_"+lastId).val();
        if(lastItem){
            html = '<tr id="itemRow_'+i+'">';
            html += '<td><button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem'+i+'" onClick="deleteItem(this.id)"><i class="las la-trash"></i></button></td>';
            html += '<td>'+i+'</td>';
            html += '<td><input type="text" data-type="associateid" name="associate[]" id="associate_'+i+'" class="form-control autocomplete_txt" autofocus="autofocus" autocomplete="off" ><input type="hidden" name="asid[]" id="asid_'+i+'"></td>';
            html += '<td><input type="text" data-type="empname" name="name[]" id="name_'+i+'" class="form-control autocomplete_txt" autocomplete="off" readonly><input type="hidden" name="default_line[]" id="defaultline_'+i+'"></td>';
            html += '<td><input type="text" name="designation[]" id="designation_'+i+'" class="form-control " autofocus="autofocus" autocomplete="off" readonly></td>';
            html += '<td><input type="date" name="start_date[]" id="startdate_'+i+'" class="start-date form-control " required="required" value=""></td>';
            html += '<td><select name="unit[]" id="unit_'+i+'" class="unit form-control"> @foreach($unitList as $key => $unit)<option value="{{ $key }}">{{ $unit }}</option>@endforeach </select></td>';
            html += '<td><input type="text" name="line[]" id="line_'+i+'" class="form-control changesNo" autocomplete="off" onClick="this.select()"><input type="hidden" name="lineid[]" id="lineid_'+i+'"><input type="hidden" name="exist_id[]" id="existid_'+i+'"></td>';
            html += '<td><input type="text" name="floor[]" id="floor_'+i+'" class="form-control" autocomplete="off" readonly><input type="hidden" name="floorid[]" id="floorid_'+i+'"></td>';
            html += '<td><input type="date" name="end_date[]" id="enddate_'+i+'"  class=" form-control" placeholder="End Date" ></td>';
            html += '</tr>';
            $('table').append(html);
            $('#associate_'+i).focus();
            i++;
        }else{
            $('#associate_'+lastId).focus();
        }
        
    });

    function deleteItem(itemId) {
        $("#"+itemId).parent().parent().remove();
    }

    var base_url = $("#base_url").val();

    //auto-complete script
    $(document).on('focus keyup','.autocomplete_txt',function(){
        type = $(this).data('type');
        typeId = $(this).attr('id');
        // console.log(typeId);
        inputIdSplit = typeId.split("_");

        if(type =='associateid' )autoTypeNo=0;
        if(type =='empname' )autoTypeNo=1;  
        
        $(this).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url : base_url+'/hr/operation/line-change-get-employee',
                    //dataType: "json",
                    method: 'get',
                    data: {
                      keyvalue: request.term,
                      type: type
                    },
                     success: function( data ) {
                         response( $.map( data, function( item ) {
                            if(type =='associateid') autoTypeShow = item.associate;
                            if(type =='empname') autoTypeShow = item.name;
                            return {
                                label: autoTypeShow+' - '+item.name,
                                value: autoTypeShow,
                                data : item
                            }
                        }));
                    }
                });
            },
            autoFocus: true,            
            minLength: 0,
            select: function( event, ui ) {
                // console.log(ui.item.data);
                var item = ui.item.data;                        
                id_arr = $(this).attr('id');
                id = id_arr.split("_");
                $('#associate_'+id[1]).val(item.associate);
                $('#asid_'+id[1]).val(item.as_id);
                $('#name_'+id[1]).val(item.name);
                $('#designation_'+id[1]).val(item.designation);
                $('#defaultline_'+id[1]).val(item.default_line);
                $('#unit_'+id[1]).val(item.unit_id);
                $('#line_'+id[1]).val(item.line_name);
                $('#existid_'+id[1]).val(item.exist_id);
                $('#lineid_'+id[1]).val(item.line_id);
                $('#floor_'+id[1]).val(item.floor_name);
                $('#floorid_'+id[1]).val(item.floor_id);
                $('#startdate_'+id[1]).val(item.start_date);
                $('#enddate_'+id[1]).val(item.end_date);
                
                setTimeout(function() { $('#startdate_'+id[1]).focus().select(); }, 200);
                $("#addRow").click();
            }               
        });
    });

    // $(document).on('change','.start-date', function(){
    //     typeId = $(this).attr('id');
    //     id = typeId.split("_");

    //     $.ajax({
    //         type: "get",
    //         url: '{{ url("/hr/operation/date-wise-line-floor") }}',
    //         data: {
    //             associate: $('#associate_'+id[1]).val(),
    //             date: $('#startdate_'+id[1]).val()
    //         },
    //         success: function(response)
    //         {
    //           if(response.type === 'success'){
    //             item = response.value;
    //             $('#unit_'+id[1]).val(item.unit_id);
    //             $('#line_'+id[1]).val(item.line_name);
    //             $('#lineid_'+id[1]).val(item.line_id);
    //             $('#floor_'+id[1]).val(item.floor_name);
    //             $('#floorid_'+id[1]).val(item.floor_id);
    //             $('#startdate_'+id[1]).val(item.start_date);
    //             $('#enddate_'+id[1]).val(item.end_date);
    //             $('#existid_'+id[1]).val(item.exist_id);
    //             setTimeout(function() { $('#line_'+id[1]).focus().select(); }, 200);
    //           }else{
    //             $.notify(response.msg, 'error');
    //           }
    //         },
    //         error: function (reject) {
    //           $.notify(reject, 'error');
    //         }
    //     });
    // });
    $(document).on('change','.unit', function(){
        typeId = $(this).attr('id');
        id = typeId.split("_");
        $('#line_'+id[1]).val('');
        $('#lineid_'+id[1]).val('');
        $('#floor_'+id[1]).val('');
        $('#floorid_'+id[1]).val('');
    });

    $(document).on('focus keyup','.changesNo', function(){
        type = $(this).data('type');
        typeId = $(this).attr('id');
        inputIdSplit = typeId.split("_");
        unit = $("#unit_"+inputIdSplit[1]).val();
        
        $(this).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url : base_url+'/hr/operation/unit-wise-line-floor',
                    //dataType: "json",
                    method: 'get',
                    data: {
                      keyvalue: request.term,
                      unit: unit
                    },
                     success: function( data ) {
                         response( $.map( data, function( item ) {
                            
                            return {
                                label: item.hr_line_name,
                                value: item.hr_line_name,
                                data : item
                            }
                        }));
                    }
                });
            },
            autoFocus: true,            
            minLength: 0,
            select: function( event, ui ) {
                // console.log(ui.item.data);
                var item = ui.item.data;                        
                id_arr = $(this).attr('id');
                id = id_arr.split("_");
                $('#line_'+id[1]).val(item.hr_line_name);
                $('#lineid_'+id[1]).val(item.hr_line_id);
                $('#floor_'+id[1]).val(item.hr_floor_name); 
                $('#floorid_'+id[1]).val(item.hr_floor_id);
                
                setTimeout(function() { $('#enddate_'+id[1]).focus().select(); }, 200);
            }               
        });
    });

    function activityProcess() {
      
        var form = $("#lineChangeForm");

        $.ajax({
            type: "post",
            url: '{{ url("/hr/operation/ajax-line-changes") }}',
            data: form.serialize(), // serializes the form's elements.
            success: function(response)
            {
                // console.log(response);
                var flag = 0;
                if(response.type === 'success'){
                    for (var key in response.message) {
                        var value = response.message[key];
                        $.notify(value, {
                        type: 'error',
                        delay: 500
                     });
                        flag = 1;
                    }

                    setTimeout(function() {
                       window.location.href=base_url+'/hr/reports/line-changes';
                    }, 500);
                }
                if(flag === 0){
                    $.notify(response.msg, 'success');
                }
            },
            error: function (reject) {
              $.notify(reject, 'error');
            }
        });
    }


</script>
@endpush
@endsection