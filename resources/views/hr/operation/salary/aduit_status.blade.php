
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
    blockquote {
      text-indent: 9px;
      border-left: 2px solid #cecece;
    }
    @supports ( hanging-punctuation: first) {
      blockquote {
        text-indent: 0;
        hanging-punctuation: first;
      }
    }

    blockquote::before {
        content: open-quote;
    }
    blockquote::after {
        content: close-quote;
    }
    blockquote {
        quotes: "“" "”" "‘" "’";
    }
    .media-support-info h6 {
      font-size: 13px;
    }
    #top-tab-list li a {
      background: #eff7f8;
      color: #3c3c3c;
    }
    .audit-stauts-de{
      position: absolute;
      padding: 15px 30px;
      bottom: 0;
    }
</style>
<div class="row">
  <div class="col-sm-9">
    <div class="iq-card min-h-415">
      <div class="iq-card-body ">


        @php $msg = ''; $link = ''; $icon = 'la-exclamation-circle'; $status = 'danger'; @endphp
        @php 
              
          $button = 'Process Salary';
          $url = 'hr/monthly-salary-audit?month='.$input['month_year'].'&unit='.$input['unit'];
        @endphp
        <form class="form">
          @if($salaryStatus == null)
            @php 
              $icon = 'la-fingerprint'; $status = 'danger';
              
              $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> has not generated yet!';
              /*$button = 'Check Attendance';
              $date = date('Y-m-01', strtotime($input['month_year']));
              $url = 'hr/daily-activity-audit?date='.$date.'&unit='.$input['unit'].'&report_type=absent';*/
              $department = 'HR';
              if(Auth::user()->can('Salary Generate - HR')){
                $link = '<h3><a href="'.url($url.'&audit='.$department).'" class="btn btn-lg btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a></h3>';
              }
            @endphp
          @else
            
            @if($salaryStatus->initial_audit == null || $salaryStatus->accounts_audit == null || $salaryStatus->management_audit == null)
                @if($salaryStatus->initial_audit == null)
                  @php
                    $icon = 'la-clipboard-check'; $status = 'success';
                    $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> is now at Audit Department for verification';
                    $department = 'Audit'; 
                    if(Auth::user()->can('Salary Audit - Audit')){
                      $link = '<h3><a href="'.url($url.'&audit='.$department).'" class="btn btn-lg btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a></h3>';
                    }
                  @endphp
                @elseif($salaryStatus->accounts_audit == null)
                  @php
                    $icon = 'la-coins'; $status = 'success';
                    $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> is now at Accounts Department'; 
                    $department = 'Accounts';
                    if(Auth::user()->can('Salary Verify - Accounts')){
                      $link = '<h3><a href="'.url($url.'&audit='.$department).'" class="btn btn-lg btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a></h3>';
                    } 
                  @endphp
                @elseif($salaryStatus->management_audit == null)
                  @php
                    $icon = 'la-user-check'; $status = 'success';
                    $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> is waiting for Management confirmation'; 
                    $department = 'Management'; 
                    if(Auth::user()->can('Salary Confirmation - Management')){
                      $link = '<h3><a href="'.url($url.'&audit='.$department).'" class="btn btn-lg btn-primary"><i class="las la-hand-point-right"></i> '.$button.'</a></h3>';
                    }
                  @endphp
                @endif
            @else
              @php
                $icon = 'la-check-circle'; $status = 'success';
                $msg = 'Monthly Salary Of <b>'.date('M Y', strtotime($input['month_year'])).'</b> Process has been Completed. ';
                if(Auth::user()->can('Salary Confirmation - Management')){
                      $link = '<h3><a class="btn btn-sm btn-success" href="'.url("hr/operation/salary-sheet").'"><i class="fa fa-check"></i> Disburse Salary </a></h3>';
                 }
              @endphp
            @endif
            
          @endif

          <div class="text-center">
              
              <i class="las {{$icon}} f-80 text-{{$status}}"></i>
              <br>
              <p style="font-size: 14px;">{!! $msg !!}</p>
             
              <h4>{!! $link !!}</h4>
              <br>
          </div>
       </form>
       <div class="row justify-content-center">
            <div class="col audit-stauts-de">
              
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
                      <a class="tab-link-inline {{ $salaryStatus == null?'iq-bg-danger':'iq-bg-primary' }}" data-toggle="tooltip" title="">
                      <i class="las la-fingerprint"></i>
                      <span class="f-16">HR
                          
                      </span>

                      </a>
                      @if($hr)
                      <a class="text-right">
                        <span> Authorized by <hr>-  {{$hr->name}} <br> {{$designation[$hr->employee->as_designation_id]['hr_designation_name']??''}} </span>
                      </a>
                      @endif
                   </li>
                   <li id="audit" class="">
                      <a class="tab-link-inline {{ (isset($salaryStatus) && $salaryStatus->initial_audit != null)?'iq-bg-primary':'iq-bg-danger' }}" data-toggle="tooltip" title="">
                      <i class="las la-clipboard-check"></i><span class="f-16">Audit</span>
                      </a>
                      @if($audit)
                      <a class="text-right">
                        <span> Authorized by <hr>-  {{$audit->name}} <br> {{$designation[$audit->employee->as_designation_id]['hr_designation_name']??''}} </span>
                      </a>
                      @endif
                   </li>
                   <li id="accounts" class="">
                      <a class="tab-link-inline {{ (isset($salaryStatus) && $salaryStatus->accounts_audit != null)?'iq-bg-primary':'iq-bg-danger' }}" data-toggle="tooltip" title="">
                      <i class="las la-coins"></i><span class="f-16">Accounts</span>
                      </a>
                      @if($accounts)
                      <a class="text-right">
                        <span> Authorized by <hr>-  {{$accounts->name}} <br> {{$designation[$accounts->employee->as_designation_id]['hr_designation_name']??''}} </span>
                      </a>
                      @endif
                   </li>
                   <li id="management" class="">
                      <a class="tab-link-inline {{ (isset($salaryStatus) && $salaryStatus->management_audit != null)?'iq-bg-primary':'iq-bg-danger' }}" data-toggle="tooltip" title="">
                      <i class="las la-user-check"></i><span class="f-16">Management</span>
                      </a>
                      @if($management)
                      <a class="text-right">
                        <span> Authorized by <hr>-  {{$management->name}} <br> {{$designation[$management->employee->as_designation_id]['hr_designation_name']??''}} </span>
                      </a>
                      @endif
                   </li>
                </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-3 pl-0">
    <div class="iq-card min-h-415">
      <div class="iq-card-body mh-410">
        <ul class="speciality-list m-0 p-0">
          @if(count($auditHistory) > 0)
          @foreach($auditHistory as $history)
            @if($history->status == 1)
            <li class="d-flex mb-4 align-items-center">
             <div class="user-img img-fluid">
                 <a href="#" class="iq-bg-success" @if($history->stage == 2) onClick="selectedGroup({{$history->id}})" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Audit History" @endif>
                    <i class="las f-18 la-check-circle"></i>
                 </a>
              </div>
             <div class="media-support-info ml-3">
                
                <h6>
                  @if($history->stage == 1)
                  HR
                  @elseif($history->stage == 2)
                  Audit
                  @elseif($history->stage == 3)
                  Accounts
                  @elseif($history->stage == 4)
                  Management
                  @endif
                  <small class="float-right mt-1">{{ $history->created_at }}</small>
                </h6>
                <h6 class="capitalize">- {{ $history->user['name'] }}</h6>
                @if($history->comment != null)
                <span class="mb-0">
                <blockquote>{{ $history->comment }}</blockquote>
                </span>
                @endif
             </div>
            </li>
            @else
            <li class="d-flex mb-4 align-items-center">
             <div class="user-img img-fluid">
                 <a href="#" class="iq-bg-danger" @if($history->stage == 2) onClick="selectedGroup({{$history->id}})" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Audit History" @endif>
                    <i class="las f-18 la-times-circle"></i>
                 </a>
              </div>
              <div class="media-support-info ml-3">
                <h6>
                  @if($history->stage == 1)
                  HR
                  @elseif($history->stage == 2)
                  Audit
                  @elseif($history->stage == 3)
                  Accounts
                  @elseif($history->stage == 2)
                  Management
                  @endif
                  <small class="float-right mt-1 font-italic">{{ $history->created_at }}</small>
                </h6>
                <h6 class="capitalize">- {{ $history->user['name'] }}</h6>
                @if($history->comment != null)
                <span class="mb-0">
                <blockquote>{{ $history->comment }}</blockquote>
                </span>
                @endif
             </div>
            </li>
            @endif
          @endforeach
          @endif
       </ul>
      </div>
    </div>
  </div>
</div>

