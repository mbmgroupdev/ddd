@extends('hr.layout')
@section('title', 'Bill Type')

@section('main-content')
@push('js')
  
    <style>
        .iq-accordion-block{
            padding: 10px 0;
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
                    <a href="#">Setup</a>
                </li>
                <li class="active"> Bill Type Setting</li>
            </ul>
        </div>

        <div class="page-content"> 
          <div class="row">
             <div class="col-lg-2 pr-0">
                 <!-- include library menu here  -->
                 @include('hr.setup.bill.bill_menu')
             </div>
             <div class="col-lg-10 mail-box-detail">
                <div class="row">
                  <div class="col-sm-4">

                      <div class="panel panel-info">
                          <div class="panel-heading">
                              <h6>Bill Type</h6>
                          </div>
                          <div class="panel-body">
                              <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/bill-type')}}" enctype="multipart/form-data">
                                  {{ csrf_field() }} 
                                  @if ($errors->has('name'))
                                      <span class="help-block red block">
                                          <strong>{{ $errors->first('name') }}</strong>
                                      </span>
                                    @endif
                                  <div class="form-group has-required has-float-label">
                                    
                                    <input type="text" id="name" name="name" placeholder="Enter Name" class="form-control" autocomplete="off" value="{{ old('name') }}" autofocus />

                                    <label for="name" > Name </label>
                                    
                                  </div>
                                  
                                  <div class="form-group has-float-label">
                                    <input type="text" id="bangla-name" name="bangla_name" placeholder="Enter Bangla Name" class="form-control" value="{{ old('bangla_name') }}" autocomplete="off" />
                                    <label for="bangla-name" >Bangla Name </label>
                                    
                                  </div>
                                  
                                  <div class="form-group">
                                      <button class="btn btn-outline-success" type="submit">
                                          <i class="fa fa-save"></i> Save
                                      </button>
                                  </div>                                 
                              </form>  
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-8">
                      <div class="panel panel-info">
                          <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                              <table id="global-datatable" class="table table-bordered  table-hover">
                                  <thead>
                                      <tr>
                                          <th>SL.</th>
                                          <th width="30%">Name</th>
                                          <th width="30%">Bangla Name</th>
                                          <th width="30%">Status</th>
                                          <th width="30%">Action</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @php $i=0; @endphp
                                      @foreach($getType as $type)
                                        <tr id="row-{{ $type->id }}">
                                          <td>{{ ++$i }}</td>
                                          <td>{{ $type->name }}</td>
                                          <td>{{ $type->bangla_name }}</td>
                                          <td>{{ $type->status==1?'Active':'Inactive'}}</td>

                                          <td width="20%">
                                              <div class="btn-group">
                                                {!! Form::open(array('route'=> ['bill-type.destroy',$type->id],'method'=>'DELETE','class'=>'form_delete')) !!}
                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this type?');" class="btn btn-sm btn-outline-danger">
                                                      <i class="ace-icon fa fa-trash bigger-120"></i>
                                                    </button>
                                                {!! Form::close() !!}
                                              </div>
                                          </td>
                                        </tr>
                                      @endforeach
                                  </tbody>
                              </table>

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

  </script>
@endpush
@endsection