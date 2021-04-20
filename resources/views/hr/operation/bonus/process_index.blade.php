@extends('hr.layout')
@section('title', 'Bonus Process')
@section('main-content')
@push('css')
    
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
                    <a href="#"> Payroll </a>
                </li>
                <li class="active">Bonus Process</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="row">
              @if(count($unitBonus) > 0)
              @foreach($unitBonus as $bonus)
                  <div class="col-lg-3 col-md-6 col-sm-12">
                      <div class="iq-card @if($bonus->approved_at != null)  bg-primary text-white @endif">
                          <div class="iq-card-body border text-center rounded">
                             <span class="font-size-16 text-uppercase">{{ $bonusType[$bonus->bonus_type_id]['bonus_type_name']??'' }} - {{ $bonus->bonus_year }}</span>
                             <h2 class=" display-3 font-weight-bolder "><small class="font-size-14 text-muted @if($bonus->approved_at != null) text-white @endif">{{ $unit[$bonus->unit_id]['hr_unit_name']??''}}</small></h2>
                             <div class="text-left">
                                <u><b>Rules</b></u>
                                <ul class="list-unstyled line-height-2 mb-0">
                                    @if($bonus->amount != null && $bonus->amount > 0)
                                    <li class="@if($bonus->approved_at != null) text-white @endif"> Bonus Amount : {{ $bonusType[$bonus->bonus_type_id]['eligible_month'] }}</li>
                                    @else
                                    <li class="@if($bonus->approved_at != null) text-white @endif"> Basic : {{ $bonus->percent_of_basic }}%</li>
                                    @endif
                                    <li class="@if($bonus->approved_at != null) text-white @endif"> Last Eligible Date : {{ \Carbon\Carbon::parse($bonus->cutoff_date)->subMonths($bonusType[$bonus->bonus_type_id]['eligible_month'])->toDateString()  }}</li>
                                    <li class="@if($bonus->approved_at != null) text-white @endif"> Bonus Date : {{ $bonus->cutoff_date }}</li>
                                </ul>
                              </div>
                              @if($bonus->approved_at != null)
                                <p>Authorized by <br>
                                     &nbsp; &nbsp; -  {{ $getUser[$bonus->approved_by]->name?? ''}}
                                </p>
                                @if(auth()->user()->can('Bonus Sheet'))
                                <a href='{{ url("hr/payroll/bonus-disburse")}}' class="btn btn-dark mt-2"Get Disburse</a>
                                @endif
                              @else
                                @if(auth()->user()->can('Bonus Approval'))
                                <a href='{{ url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id")}}' class="btn btn-primary mt-5">Get Process</a>
                                @else
                                <a class="btn btn-primary mt-5">Pending for Approval</a>
                                @endif
                              @endif
                          </div>
                      </div>
                  </div>
              @endforeach
              @else
              <div class="col">
                <div class="panel w-100">
                  <div class="panel-body">
                      <h4 class="text-center">No Process Found!</h4>
                  </div>
                </div>
              </div>
              @endif
           </div>
            
        </div> 
    </div> 
</div> 
@push('js')

@endpush
@endsection