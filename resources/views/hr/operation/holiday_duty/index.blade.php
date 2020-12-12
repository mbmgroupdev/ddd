@extends('hr.layout')
@section('title', 'Holiday Duty Payment')

@section('main-content')
@php
    

@endphp
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
                <li class="active"> Holiday Duty Payment</li>
            </ul>
        </div>


        <div class="page-content"> 
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group has-float-label select-search-group">
                                <select name="unit" class="form-control capitalize select-search" id="unit" multiple>
                                    @foreach($unitList as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                              <label for="unit">Unit</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group has-float-label has-required">
                                <input type="date" class="working_date datepicker form-control" id="working_date" name="working_date" placeholder="Y-m-d" required="required" value="" autocomplete="off" />
                                <label for="working_date">Working Date</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group has-float-label has-required">
                                <input type="date" class="substitute_date datepicker form-control" id="substitute_date" name="substitute_date" placeholder="Y-m-d" required="required" value="" autocomplete="off" />
                                <label for="substitute_date">Substitute For</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button id="generate" type="button" class="btn btn-primary">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-body">
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script type="text/javascript">
        $('#working_date').on('change',function(){
            $('#substitute_date').attr('max',$('#working_date').val());    
        });


        $('#generate').on('click',function() {
            var unit = $("#unit").val();
            var substitute_date = $("#substitute_date").val();
            var working_date = $("#working_date").val();
            if(substitute_date !== '' && working_date != ''){
                $(".app-loader").show();
                $.ajax({
                    type: "get",
                    url: '{{ url("hr/operation/holiday-duty/data")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    data: {
                        unit : unit,
                        substitute_date : substitute_date,
                        working_date : working_date
                    }
                    success: function(response)
                    {
                        console.log(response);
                    },
                    error: function (reject) {
                    }
                });
            }else{
                $("#app-loader").hide();
                $.notify("Select working date & substitute date", 'error');
                
            }
        }
    </script>
@endpush
@endsection