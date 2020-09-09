
@php $department = ''; @endphp
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

  @php $msg = ''; @endphp
  <form class="form">
    @if($salaryStatus == null)
      @php 
        $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Not Generate Yet!';
        $button = 'Check Attendance';
        $date = date('Y-m-01', strtotime($input['month_year']));
        $url = 'hr/daily-activity-audit?date='.$date.'&unit='.$input['unit'].'&report_type=absent';
        $department = 'HR';
      @endphp
    @else
      @if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null)
          @if($salaryStatus->initial_audit == null)
            @php
             $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Audit Department';
             $department = 'Audit'; 
            @endphp
          @elseif($salaryStatus->accounts_audit == null)
            @php
             $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Accounts Department'; 
             $department = 'Accounts'; 
            @endphp
          @elseif($salaryStatus->management_audit == null)
            @php
             $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Management Department'; 
             $department = 'Management'; 
            @endphp
          @endif
      @endif
      @php 
        $button = 'Check Salary';
        $url = 'hr/monthly-salary-audit?month='.$input['month_year'].'&unit='.$input['unit'];
      @endphp
    @endif
    @php
      $link = '';
      if(Auth::user()->can('Hr Salary Generate') || Auth::user()->can('Salary Audit') || Auth::user()->can('Accounts Salary Verify') || Auth::user()->can('Management Salary Audit')){
          $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-md btn-outline-success"><i class="las la-hand-point-right"></i> '.$button.'</a>';
      }
    @endphp

    <div class="text-center">
        <h3>{!! $msg !!}</h3>
        <br>
        <h4>{!! $link !!}</h4>
    </div>
 </form>
</div>