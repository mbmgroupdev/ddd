@extends('hr.layout')
@section('title', 'Bonus Set')
@section('main-content')
@push('css')
    <style>
        .close-button {
            content: "X";
            background-color: rgb(229 25 4);
            transform: scale(1);
            display: block;
            border-radius: 50%;
            border: 1px solid rgb(8 155 171);
            position: absolute;
            top: -12px;
            right: 0px;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 22px;
            transition-duration: 0.4s;
            color: #fff;
            cursor: pointer;
        }
        .form-section{
            height: calc(100vh - 275px);
        }
        #appendType{
            position: absolute;
            overflow: auto;
            background: #fff;
            height: 300px;
            padding: 15px;
            margin-bottom: 30px; 
        }
        .process-btn{
            position: absolute;
            background: #fff;
            width: 100%;
            bottom: 0;
            padding-top: 15px;
        }
    </style>
@endpush
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Operation </a>
                </li>
                <li class="active">Bonus</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <form>
                <div class="panel panel-info">
                    <div class="panel-heading"><h6>Bonus</h6></div> 
                    <div class="panel-body">
                        <div class="row">
                            <div class="offset-sm-3 col-sm-6">
        
                                <div class="form-section">
                                    <div class="form-group has-required has-float-label select-search-group">
                                        {{ Form::select('type_id', $bonusType, null, ['placeholder'=>'Select Bonus', 'id'=>'bonus_for', 'class'=> 'form-control', 'required'=>'required']) }}
                                        <label for="bonus_for">Bonus for </label>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group has-float-label">
                                                <input type="text" name="bonus_amount" id="bonus_amount" placeholder="Enter" class="form-control" >
                                                <label for="bonus_amount">Amount </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group  has-float-label">
                                                <input type="text" name="bonus_percent" id="bonus_percent" placeholder="% of Basic"  class="form-control" >
                                                <label for="bonus_percent">OR, % of Basic </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group has-required has-float-label">
                                                <input type="date" name="cut_date" id="cut_date" placeholder="Cut of Date" value="{{ date('Y-m-d') }}"  class="form-control" required>
                                                <label for="cut_date">Cut of Date </label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group has-required has-float-label">
                                                <input type="number" name="eligible_month" id="eligible_month" placeholder="Enter Number of Eligible Month" value="" min="0" class="form-control" required>
                                                <label for="eligible_month">Eligible Month </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group has-required has-float-label">
                                                <input type="date" name="cut_date" id="cut_date" placeholder="Cut of Date" value="{{ date('Y-m-d') }}"  class="form-control" required>
                                                <label for="cut_date">Cut of Date </label>
                                            </div>
                                        </div>
                                        
                                    </div> --}}
                                    <div class="row">
                                      <div class="col">
                                        <div class="custom-control custom-switch">
                                          <input name="special" type="checkbox" class="custom-control-input" id="specialCheck">
                                          <label class="custom-control-label" for="specialCheck">Advanced</label>
                                        </div>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="specialsection" id="special-section">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="form-group has-required has-float-label select-search-group">
                                                        <select name="" id="type-for" class="form-control">
                                                            <option value=""> - Select - </option>
                                                            <option value="department"> Department</option>
                                                            <option value="designation"> Designation</option>
                                                            <option value="section"> Section</option>
                                                            <option value="sub_section"> Sub Section</option>
                                                            <option value="employee"> Employee</option>
                                                        </select>
                                                        <label for="type-for">Type </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="syncBtn" style="display: none">
                                                    <div class="form-group">
                                                        <button class="btn btn-outline-primary" type="button" id="sync-type">
                                                            <i class="las la-sync"></i>
                                                        </button> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="appendType"></div>
                                        
                                        </div>
                                      </div>
                                    </div>
                                    <div id="targettype"></div>
                                </div>
                                <div class="process-btn">
                                    <div class="form-group">
                                        <button class="btn btn-primary pull-right" type="submit">
                                            <i class=" fa fa-check"></i> Process
                                        </button>
                                            
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </form>
            
        </div> {{-- Page-Content-end --}}
    </div> {{-- Main-content-inner-end --}}
</div> {{-- Main-content --}}
@push('js')
<script type="text/javascript">
    $(document).on('click','#specialCheck',function(){
      if ($(this).is(":checked")) {
        $("#syncBtn").show();
        $("#appendType").show();
      }else{
        $("#syncBtn").hide();
        $("#appendType").hide();
      }
    });
    $(document).on('click', '#sync-type', function () {
        var type = $("#type-for").val();
        var typeText = $("#type-for option:selected" ).text();
        // console.log(type)
        if(type !== '' && type !== null){
            if($('#'+type).length && $('#'+type).val().length){
                $.notify(typeText+' Already Exists', 'error');
            }else{
                var typeWisePrepend = loadContent(type, typeText);
                $("#appendType").prepend(typeWisePrepend);
                $("#targettype").append('<input type="hidden" name="'+type+'" id="'+type+'" value="'+type+'">');   
            }
            

        }
    });
    function loadContent(type, typeText){
        var html = '';
        var i=$('table tr').length;
        html += '<div class="row"><div class="col-sm-12 table-wrapper-scroll-y table-custom-scrollbar"><table class="table table-bordered table-hover table-fixed" id="itemList"><button title="Remove this!" type="button" class="fa fa-close close-button" onclick="$(this).parent().remove();"></button><thead><tr class="text-center active"><th width="2%"><button class="btn btn-sm btn-outline-success addmore" type="button"><i class="las la-plus-circle"></i></button></th><th width="38%">'+typeText+' Name</th><th width="20%"> Eligible Month</th><th width="20%">Amount</th><th width="20%">Or, % of Basic</th></tr></thead><tbody><tr><td><button class="btn btn-sm btn-outline-danger delete" type="button" id="deleteItem1" onClick="deleteItem(this.id)"><i class="las la-trash"></i></button></td><td><input type="text" data-type="'+type+'" name="'+type+'[]" id="designation_1" class="form-control autocomplete_txt" autocomplete="off"></td><td><input type="number" step="any" min="0" value="0" name="special_tiffin[]" id="tiffin_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="number" step="any" min="0" value="0" name="special_tiffin[]" id="tiffin_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td><td><input type="number" step="any" min="0" value="0" name="special_dinner[]" id="dinner_1" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td></tr></tbody></table></div></div>';
            i++;
        return html;
    }
    $(document).on('focus keyup','.autocomplete_txt',function(){
        type = $(this).data('type');
        typeId = $(this).attr('id');
        console.log(type);
        // inputIdSplit = typeId.split("_");

        // if(type =='designation' )autoTypeNo=0;  
        
        // $(this).autocomplete({
        //     source: function( request, response ) {
        //         $.ajax({
        //             url : base_url+'/hr/search-designation',
        //             //dataType: "json",
        //             method: 'get',
        //             data: {
        //               keyvalue: request.term
        //             },
        //              success: function( data ) {
        //                  response( $.map( data, function( item ) {
        //                     if(type =='designation') autoTypeShow = item.name;
        //                     return {
        //                         label: item.name,
        //                         value: item.name,
        //                         data : item
        //                     }
        //                 }));
        //             }
        //         });
        //     },
        //     autoFocus: true,            
        //     minLength: 0,
        //     select: function( event, ui ) {
        //         var item = ui.item.data;                        
        //         id_arr = $(this).attr('id');
        //         id = id_arr.split("_");
        //         $('#designation_'+id[1]).val(item.designation);
        //         setTimeout(function() { 
        //             $(".addmore").click();
        //             $('#tiffin_'+id[1]).focus().select(); 
        //         }, 200);
        //     }               
        // });
    });
</script>
@endpush
@endsection