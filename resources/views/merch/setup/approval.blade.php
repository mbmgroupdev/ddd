@extends('merch.layout')
@section('title', 'Approval Hierarchy')
@push('css')
<style type="text/css">
    @media only screen and (max-width: 767px) {
        .dataTables_wrapper .col-sm-12{width: 100%; overflow-x: auto; display: block;} 
    }
</style>
@endpush
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                <li class="active"> Approval Hierarchy </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-3 pr-0">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Approval Hierarchy</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/approval_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            
                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('type',array('Style Costing' => 'Style Costing', 'Order Costing' => 'Order Costing','Supplier Approval' => 'Supplier Approval'), null, ['placeholder'=>'Select Approval Type','id'=>'approval-type','class'=> 'form-control', 'required']) }}
                                <label for="approval-type" > Approval Type </label>
                              
                            </div>

                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('unit',$unitList, null, ['placeholder'=>'Select Unit','id'=>'unit','class'=> 'form-control', 'required']) }}
                                <label for="unit" > Unit </label>
                              
                            </div>

                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('level1', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Level 1','id'=>'level1','class'=> 'associates form-control', 'required']) }}
                                <label for="level1" > Level 1 </label>
                              
                            </div>

                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('level2', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Level 2','id'=>'level2','class'=> 'associates form-control', 'required']) }}
                                <label for="level2" > Level 2 </label>
                              
                            </div>

                            <div class="form-group has-required has-float-label select-search-group">
                                {{ Form::select('level3', [request()->get("associate_id") => request()->get("associate_id")], request()->get("associate_id"), ['placeholder'=>'Select Level 3','id'=>'level3','class'=> 'associates form-control', 'required']) }}
                                <label for="level3" > Level 3 </label>
                              
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
            <div class="col-sm-9">
                <div class="panel panel-info">
                    <div class="panel-body table-responsive"  style="margin-bottom: 5px;">
                        <table id="global-datatable" class="table table-bordered  table-hover">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Approval Type</th>
                                    <th>Unit</th>
                                    <th>Level 1</th>
                                    <th>Level 2</th>
                                    <th>Level 3</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @php $i=0; @endphp
                                @foreach($approval as $approv)
                                 
                                  <tr >
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $approv->mr_approval_type }}</td>
                                    <td>{{ $approv->unit_name }}</td>
                                    <td>{{ $getAsName[$approv->level_1]??'' }}<br/>{{ $approv->level_1 }} </td>
                                    <td>{{ $getAsName[$approv->level_2]??'' }}<br/>{{ $approv->level_2 }}</td>
                                    <td>{{ $getAsName[$approv->level_3]??'' }}<br/>{{ $approv->level_3 }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            

                                            <a href="{{ url('merch/setup/approv_delete/'.$approv->id) }}" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                  </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    
                </div>
            </div>    
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function()
        {
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
        });
    </script>
@endpush
@endsection

