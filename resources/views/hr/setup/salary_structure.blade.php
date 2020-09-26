@extends('hr.layout')
@section('title', 'Salary Structure')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Human Resource </a>
                </li> 
                <li>
                    <a href="#"> Settings </a>
                </li>
                <li class="active"> Salary Structure </li>
            </ul><!-- /.breadcrumb -->
        </div>
        @include('inc/message')
        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-info">
                  <div class="panel-heading"><h6>Salary Structure</h6></div> 
                    <div class="panel-body">
                        
                        <form class="form-horizontal" role="form" method="post" action="{{ url('hr/setup/salary_structure')  }}" enctype="multipart/form-data">
                            {{ csrf_field() }} 
                            <div class="form-group has-required has-float-label">
                                
                                <input type="text" name="basic" id="basic" placeholder="Percentage of Gross Paid as Basic Salary" class="form-control" required="required "  />
                                <label  for="basic"> Basic(% of Gross)  </label>
                                
                            </div>

                            <div class="form-group has-required has-float-label">
                                
                                <input type="text" name="medical" id="medical" placeholder="Amount Paid for Medical" class="form-control" required="required "  />
                                <label  for="medical"> Medical  </label>
                                
                            </div>
                            <div class="form-group has-required has-float-label">
                                
                                <input type="text" name="transport" id="transport" placeholder="Amount Paid for Transportation" class="form-control" required="required "  />
                                <label  for="transport"> Transportation </label>
                               
                            </div>
                            <div class="form-group has-required has-float-label">
                                
                                <input type="text" name="food" id="food" placeholder="Amount Paid for Food" class="form-control" required="required "  />
                                <label  for="food"> Food  </label>
                                
                            </div>
                            <div class="form-group">
                                <button class="btn  btn-success" type="submit">
                                    <i class=" fa fa-check bigger-110"></i> Save
                                </button>
                            </div>
                        
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-body">  
                        <table id="global-datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Basic</th>
                                        <th>Medical</th>
                                        <th>Transportation</th>
                                        <th>Food</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($current_structure AS $structure)
                                    <tr>
                                        <td>{{ ($structure->basic ? $structure->basic: null) }}</td>
                                        <td>{{ ($structure->medical ? $structure->medical: null) }}</td>
                                        <td>{{ ($structure->transport ? $structure->transport: null) }}</td>
                                        <td>{{ ($structure->food ? $structure->food: null) }}</td>
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

@endsection