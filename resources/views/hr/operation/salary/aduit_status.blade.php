@php
    if($salaryStatus == null){
        $date = date('Y-m-01', strtotime($input['month_year']));
        $url = 'hr/daily-activity-audit?date='.$date.'&unit='.$input['unit'].'&report_type=absent';
        $button = 'Check Attendance';
    }else{
        $url = 'hr/monthly-salary-audit?month='.$input['month_year'].'&unit='.$input['unit'];
        $button = 'Check Salary';
    }
    $link = '';
    if(Auth::user()->can('Hr Salary Generate') || Auth::user()->can('Salary Audit') || Auth::user()->can('Accounts Salary Verify') || Auth::user()->can('Management Salary Audit')){
        $link = '<a href="'.url($url).'" class="btn btn-md btn-outline-success"><i class="las la-hand-point-right"></i> '.$button.'</a>';
    }
    

@endphp

<div class="iq-card-body">
   <ul id="top-tab-list" class="p-0">
     <li class="{{ $salaryStatus == null?'':'active' }}" id="account">
        <a class="tab-link-inline">
        <i class="las la-user-shield"></i><span class="f-16">HR</span>
        </a>
     </li>
     <li id="audit" class="{{ (isset($salaryStatus) && $salaryStatus->initial_audit != null)?'active':'' }}">
        <a class="tab-link-inline">
        <i class="las la-user-shield"></i><span class="f-16">Audit</span>
        </a>
     </li>
     <li id="accounts" class="{{ (isset($salaryStatus) && $salaryStatus->accounts_audit != null)?'active':'' }}">
        <a class="tab-link-inline">
        <i class="las la-user-shield"></i><span class="f-16">Accounts</span>
        </a>
     </li>
     <li id="management" class="{{ (isset($salaryStatus) && $salaryStatus->management_audit != null)?'active':'' }}">
        <a class="tab-link-inline">
        <i class="las la-user-shield"></i><span class="f-16">Management</span>
        </a>
     </li>
  </ul>
   <form class="form">
      @if($salaryStatus == null)
        <div class="text-center">
            <h2>{{ $input['month_year'] }} Monthly Salary Not Generate!</h2>
            <h4 class="m-3">{!! $link !!}</h4>
        </div>
    @else
        @if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null)
            @if($salaryStatus->initial_audit == null)
                <div class="text-center">
                    <h2>{{ $input['month_year'] }} Monthly Salary Audit Department Not Completed!</h2>
                    <h4>{!! $link !!}</h4>
                </div>
            @elseif($salaryStatus->accounts_audit == null)
                <div class="text-center">
                    <h2>{{ $input['month_year'] }} Monthly Salary Accounts Audit Not Completed!</h2>
                    <h4>{!! $link !!}</h4>
                </div>
            @elseif($salaryStatus->management_audit == null)
                <div class="text-center">
                    <h2>{{ $input['month_year'] }} Monthly Salary Management Audit Not Completed!</h2>
                    <h4>{!! $link !!}</h4>
                </div>
            @else
                Something Wrong!
            @endif
        @endif
    @endif
   </form>
</div>