@extends('merch.layout')
@section('title', 'Article Construction & Composition')
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
                <li class="active"> Article Construction & Composition </li>
            </ul><!-- /.breadcrumb --> 
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h6>Article Construction & Composition</h6>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/article_store')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            
                            <div class="form-group has-required has-float-label select-search-group">
                              {{Form::select('supplier', $supplier, $supId, [ 'id' => 'supplier', 'placeholder' => 'Select Supplier', 'class' => 'form-control filter', 'required'])}}
                              <label for="supplier" > Supplier </label>
                              
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="text" id="art_name" name="art_name" placeholder="Enter Article Name " class="form-control" autocomplete="off" required="" />
                                <label for="art_name" > Article Name </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="text" id="composition" name="composition" placeholder="Enter Composition Name " class="form-control" autocomplete="off" />
                                <label for="composition" > Composition </label>
                            </div>
                            <div class="form-group has-required has-float-label">
                                <input type="text" id="art_construction" name="art_construction" placeholder="Enter Construction Name " class="form-control" autocomplete="off" />
                                <label for="art_construction" > Construction </label>
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
                                    <th width="20%">Supplier</th>
                                    <th width="20%">Article</th>
                                    <th width="20%">Construction</th>
                                    <th width="20%">Composition</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; @endphp
                                @foreach($articleList AS $article)
                                  <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $article->sup_name }}</td>
                                    <td>{{ $article->art_name }}</td>
                                    <td>{{ $article->construction_name }}</td>
                                    <td>{{ $article->comp_name }}</td>

                                    <td width="20%">
                                        <div class="btn-group">
                                            

                                            <a href="{{ url('merch/setup/article_delete/'.$article->id) }}" class='btn btn-sm btn-danger' onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
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

@endpush
@endsection

