@extends('hr.layout')
@section('title', '')
@section('main-content')
@push('css')
<style type="text/css">
    .select2{width: 80% !important;}
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
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Salary Processing Date </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content"> 
            
                  <!-- Display Erro/Success Message -->
                @include('inc/message')

             <div class="row" style="padding-left: 20%; padding-right: 20%;">
                    <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/default_system_setting_save')  }}" enctype="multipart/form-data">
                    {{ csrf_field() }} 
                    <div class="panel panel-info">
                        <br>
                      {{-- <div class="panel-heading"><h6> Other Setting</h6></div>  --}}
                        <div class="panel-body">
                            <div class="form-group">
                                <p class="col-sm-1"></p>
                                <label class="col-sm-4 control-label no-padding"> Salary Processing Date <span style="color: red; vertical-align: top;">&#42;</span> </label>
                                <div class="col-sm-7 no-padding">
                                    {{-- <input type="text" id="salary_lock" name="salary_lock" class="col-xs-12 datepicker" data-validation="required length custom" data-validation-length="1-50"
                                    /> --}}
                                    <?php
                                    echo "<select name='salary_lock'>";
                                        for ($i = 1; $i <=31; $i++){
                                           $sel = (isset($id->salary_lock) && ($id->salary_lock) == $i) ? ' selected="selected"' : '';
                                           echo "<option value='$i'$sel>$i</option>";   
                                        }
                                        echo "</select>";
                                    ?>
                                </div>
                                <input type="hidden" name="lock_id" id="lock_id" value="{{$id->id}}">
                            </div>
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="clearfix form-actions">
                                <div class="col-md-offset-3 col-md-6 text-center" style="padding-left: 30px;"> 
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i> Submit
                                    </button>

                                    &nbsp; &nbsp; &nbsp;
                                    <button class="btn btn-sm" type="reset">
                                        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
                                    </button>
                                </div>
                            </div>
         
                        </div>
                      </div>
                    </div>
              </div>
            </form> 

            <!--<div class="panel panel-info">
              <div class="panel-heading"><h6>List</h6></div> 
                <div class="panel-body">
                <div class="col-sm-12">
                    <table id="dataTables" class="table table-striped table-bordered" style="width: 100%;">
                            <?php $index=1; ?>
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Salary Lock</th>
                                    <th>Created at</th>
                                    <th>Updated At</th>
                                    
                                </tr>
                            </thead>
                                @foreach($system_setting as $ss)
                                <tbody>
                                        <td>{{$index++}}</td>
                                        <td>{{$ss->salary_lock}}</td>
                                        <td>{{$ss->created_at}}</td>
                                        <td>{{$ss->updated_at}}</td>
                                        
                                </tbody>
                                @endforeach
                        </table>
                </div>
            </div>
            </div>-->
        </div><!-- /.page-content -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){ 

    $('#dataTables').DataTable({
        pagingType: "full_numbers" ,
        // searching: false,
        // "lengthChange": false,
        // 'sDom': 't' 
        "sDom": '<"F"tp>'

    }); 
    
});
</script>
@endsection