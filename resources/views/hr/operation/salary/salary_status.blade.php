
<div class="iq-card-body">

  @php $msg = ''; @endphp
  <form class="form">
    @if($salaryStatus == null)
      @php 
        $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Not Generate Yet!';
        
      @endphp
    @else
      @if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null)
          @if($salaryStatus->initial_audit == null)
            @php
             $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Audit Department';
             
            @endphp
          @elseif($salaryStatus->accounts_audit == null)
            @php
             $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Accounts Department'; 
             
            @endphp
          @elseif($salaryStatus->management_audit == null)
            @php
             $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Management Department'; 
             
            @endphp
          @endif
      @endif
      
    @endif


    <div class="text-center">
        <h2>{!! $msg !!}</h2>
    </div>
 </form>
</div>