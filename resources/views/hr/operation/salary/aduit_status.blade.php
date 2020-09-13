
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

  @php $msg = ''; $link = ''; @endphp
  <form class="form">
    @if($salaryStatus == null)
      @php 
        $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Not Generate Yet!';
        $button = 'Check Attendance';
        $date = date('Y-m-01', strtotime($input['month_year']));
        $url = 'hr/daily-activity-audit?date='.$date.'&unit='.$input['unit'].'&report_type=absent';
        $department = 'HR';
        if(Auth::user()->can('Salary Generate - HR')){
          $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-md btn-outline-success"><i class="las la-hand-point-right"></i> '.$button.'</a>';
        }
      @endphp
    @else
      @php 
        $button = 'Check Salary';
        $url = 'hr/monthly-salary-audit?month='.$input['month_year'].'&unit='.$input['unit'];
      @endphp
      @if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null)
          @if($salaryStatus->initial_audit == null)
            @php
              $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Audit Department';
              $department = 'Audit'; 
              if(Auth::user()->can('Salary Audit - Audit')){
                $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-md btn-outline-success"><i class="las la-hand-point-right"></i> '.$button.'</a>';
              }
            @endphp
          @elseif($salaryStatus->accounts_audit == null)
            @php
              $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Accounts Department'; 
              $department = 'Accounts';
              if(Auth::user()->can('Salary Verify - Accounts')){
                $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-md btn-outline-success"><i class="las la-hand-point-right"></i> '.$button.'</a>';
              } 
            @endphp
          @elseif($salaryStatus->management_audit == null)
            @php
              $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Handover On Management Department'; 
              $department = 'Management'; 
              if(Auth::user()->can('Salary Confirmation - Management')){
                $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-md btn-outline-success"><i class="las la-hand-point-right"></i> '.$button.'</a>';
              }
            @endphp
          @endif
      @else
        @php
          $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Process Completed';
           
        @endphp
      @endif
      
    @endif

    <div class="text-center">
        <h3>{!! $msg !!}</h3>
        <br>
        <h4>{!! $link !!}</h4>
    </div>
 </form>
</div>