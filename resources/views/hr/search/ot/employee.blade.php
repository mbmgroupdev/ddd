<style>
    .fc-event-container a{margin-top: 10px; padding: 10px;}
</style>
<br>
<div class="panel panel-info col-sm-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category'] }}" data-type="{{ $request1['type'] }}"> MBM Group </a>
            </li>
            <li>
                <a href="#" class="search_unit"> All Unit </a>
            </li>
            @if(isset($request2['as_unit_id']))
                <li>
                    <a href="#" class="search_area" data-unit="{{ $request2['as_unit_id'] }}">
                        {{ $data['unit']->hr_unit_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_area_id']))
                <li>
                     <a href="#" class="search_dept" data-area="{{ $request2['as_area_id'] }}">
                        {{ $data['area']->hr_area_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_department_id']))
                <li>
                    <a href="#" class="search_floor" data-department="{{ $request2['as_department_id'] }}">
                        {{ $data['department']->hr_department_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_floor_id']))
                <li>
                    <a href="#" class="search_section" data-floor="{{ $request2['as_floor_id'] }}">
                        {{ $data['floor']->hr_floor_name }}
                    </a>
                </li>
            @endif
            @if(isset($request2['as_section_id']))
                <li>
                    <a href="#" class="search_subsection" data-section="{{ $request2['as_section_id'] }}">
                        {{ $data['section']->hr_section_name }}
                    </a>
                </li>
            @endif
            @if(isset($data['subsection']))
                <li>
                    {{ $data['subsection']->hr_subsec_name }}
                </li>
            @endif
            <li class="active"> Employee </li>
            <li>{{ $info->associate_id }}</li>
        </ul><!-- /.breadcrumb -->

    </div>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row">
          <div class=" col-sm-4">
              <div class="profile-user-info no-margin">
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Name </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->as_name}} </span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Associate Id </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->associate_id }} </span>
                        </div>
                    </div>
                </div>
          </div>
          <div class=" col-sm-4">
                <div class="profile-user-info no-margin">
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Designation </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->hr_designation_name }} </span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Department </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->hr_department_name }} </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" col-sm-4">
                <div class="profile-user-info no-margin">
                    <div class="profile-info-row">
                        <div style="" class="profile-info-name"> Unit </div>
                        <div class="profile-info-value align-left">
                            <span> {{ $info->hr_unit_name }} </span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name align-left"> Status</div>
                        <div class="profile-info-value">
                            <span>@if($info->as_status == 1) Active @else Inactive @endif </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="space-20"></div>
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <h3 class="text-center">Monthly OT Calander</h3>
                <hr>
                {!! $calendar->calendar() !!}
                {!! $calendar->script() !!}
            </div>
        </div>
    </div>
</div>