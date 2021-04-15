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
                    <a href="#"> Operation </a>
                </li>
                <li class="active">Bonus Process</li>
            </ul><!-- /.breadcrumb --> 
        </div>

        <div class="page-content"> 
            <div class="row">
                @foreach($unitBonus as $bonus)
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="iq-card @if($bonus->approved_at != null)  bg-primary text-white @endif">
                            <div class="iq-card-body border text-center rounded">
                               <span class="font-size-16 text-uppercase">{{ $bonusType[$bonus->bonus_type_id]['bonus_type_name']??'' }} - {{ $bonus->bonus_year }}</span>
                               <h2 class="mb-4 display-3 font-weight-bolder "><small class="font-size-14 text-muted">{{ $unit[$bonus->unit_id]['hr_unit_name']??''}}</small></h2>
                               <ul class="list-unstyled line-height-4 mb-0">
                                  
                               </ul>
                               <a href='{{ url("hr/operation/bonus-sheet-process-for-approval?bonus_sheet=$bonus->id")}}' class="btn btn-primary mt-5">Get Sheet</a>
                            </div>
                        </div>
                    </div>
                @endforeach
              {{-- <div class="col-lg-3 col-md-6 col-sm-12">
                 <div class="iq-card bg-primary text-white">
                    <div class="iq-card-body border text-center rounded">
                       <span class="font-size-16 text-uppercase">Basic</span>
                       <h2 class="mb-4 display-3 font-weight-bolder text-white">$99<small class="font-size-14 text-white-50">/ Month</small></h2>
                       <ul class="list-unstyled line-height-4 mb-0 ">
                          <li>Lorem ipsum dolor sit amet</li>
                          <li>Consectetur adipiscing elit</li>
                          <li>Integer molestie lorem at massa</li>
                          <li>Facilisis in pretium nisl aliquet</li>
                          <li>Nulla volutpat aliquam velit</li>
                       </ul>
                       <a href="#" class="btn btn-light btn-block mt-5">Start Starter</a>
                    </div>
                 </div>
              </div> --}}
              
           </div>
            
        </div> 
    </div> 
</div> 
@push('js')

@endpush
@endsection