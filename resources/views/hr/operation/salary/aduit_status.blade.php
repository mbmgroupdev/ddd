
@php $department = ''; $designation = designation_by_id();  @endphp
<style type="text/css">
    .iq-bg-danger.tab-link-inline i {
        background: #ec8886 !important;
    }
    #top-tab-list li i {
        height: 40px;
        width: 40px;
        line-height: 40px;
    }
</style>
<div class="iq-card-body ">
  <div class="row justify-content-center">
      <div class="col-sm-10">
        
         <ul id="top-tab-list" class="p-0">
             <li class="" id="account">
                @php
                    $hr = '';
                    $accounts = '';
                    $audit = '';
                    $management = '';
                    if($salaryStatus){
                        $hr = $salaryStatus->hr();
                        $audit = $salaryStatus->audit();
                        $accounts = $salaryStatus->accounts();
                        $management = $salaryStatus->management();
                    }
                @endphp
                <a class="tab-link-inline {{ $salaryStatus == null?'iq-bg-danger':'iq-bg-primary' }}" data-toggle="tooltip" title="@if($hr) - Authorised by {{$hr->name}} <br> {{$designation[$hr->employee->as_designation_id]['hr_designation_name']??''}} @endif">
                <i class="las la-fingerprint"></i>
                <span class="f-16">HR
                    
                </span>

                </a>
             </li>
             <li id="audit" class="">
                <a class="tab-link-inline {{ (isset($salaryStatus) && $salaryStatus->initial_audit != null)?'iq-bg-primary':'iq-bg-danger' }}" data-toggle="tooltip" title="@if($audit) - Authorised by {{$audit->name}} <br> {{$designation[$audit->employee->as_designation_id]['hr_designation_name']??''}} @endif">
                <i class="las la-clipboard-check"></i><span class="f-16">Audit</span>
                </a>
             </li>
             <li id="accounts" class="">
                <a class="tab-link-inline {{ (isset($salaryStatus) && $salaryStatus->accounts_audit != null)?'iq-bg-primary':'iq-bg-danger' }}" data-toggle="tooltip" title="@if($accounts) - Authorised by {{$accounts->name}} <br> {{$designation[$accounts->employee->as_designation_id]['hr_designation_name']??''}} @endif">
                <i class="las la-coins"></i><span class="f-16">Accounts</span>
                </a>
             </li>
             <li id="management" class="">
                <a class="tab-link-inline {{ (isset($salaryStatus) && $salaryStatus->management_audit != null)?'iq-bg-primary':'iq-bg-danger' }}" data-toggle="tooltip" title="@if($management) - Authorised by {{$management->name}} <br> {{$designation[$management->employee->as_designation_id]['hr_designation_name']??''}} @endif">
                <i class="las la-user-check"></i><span class="f-16">Management</span>
                </a>
             </li>
          </ul>
      </div>
  </div>

  @php $msg = ''; $link = ''; $icon = 'la-exclamation-circle'; $status = 'danger'; @endphp
  <form class="form">
    @if($salaryStatus == null)
      @php 
        $icon = 'la-fingerprint'; $status = 'danger';
        
        $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> has not generated yet!';
        $button = 'Check Attendance';
        $date = date('Y-m-01', strtotime($input['month_year']));
        $url = 'hr/daily-activity-audit?date='.$date.'&unit='.$input['unit'].'&report_type=absent';
        $department = 'HR';
        if(Auth::user()->can('Salary Generate - HR')){
          $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-xs btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a>';
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
              $icon = 'la-clipboard-check'; $status = 'success';
              $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> is now at Audit Department for verification';
              $department = 'Audit'; 
              if(Auth::user()->can('Salary Audit - Audit')){
                $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-xs btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a>';
              }
            @endphp
          @elseif($salaryStatus->accounts_audit == null)
            @php
              $icon = 'la-coins'; $status = 'success';
              $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> is now at Accounts Department'; 
              $department = 'Accounts';
              if(Auth::user()->can('Salary Verify - Accounts')){
                $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-xs btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a>';
              } 
            @endphp
          @elseif($salaryStatus->management_audit == null)
            @php
              $icon = 'la-user-check'; $status = 'success';
              $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> is waiting for Management confirmation'; 
              $department = 'Management'; 
              if(Auth::user()->can('Salary Confirmation - Management')){
                $link = '<a href="'.url($url.'&audit='.$department).'" class="btn btn-xs btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a>';
              }
            @endphp
          @endif
      @else
        @php
          $icon = 'la-check-circle'; $status = 'success';
          $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Process has been Completed. ';
          if(Auth::user()->can('Salary Confirmation - Management')){
                $msg .= '<a href="'.url("hr/operation/salary-sheet").'"> Disburse Salary </a>';
           }
        @endphp
      @endif
      
    @endif

    <div class="text-center">
        <br><br>
        <i class="las {{$icon}} f-100 text-{{$status}}"></i>
        <br><br>
        <p style="font-size: 14px;">{!! $msg !!}</p>
        <br>
        <h4>{!! $link !!}</h4>
    </div>
 </form>
</div>