@push('css')
  <style>
    .navbar-modal{width: 260px !important; box-shadow: -2px 0px 6px 1px; }
    .custom-control-label {line-height: 18px;}
    .group-checkbox{margin-top: 5px; margin-left: 5px;}
  </style>
@endpush
<div class="modal right fade" id="right_modal_navbar" tabindex="-1" role="dialog" aria-labelledby="right_modal_navbar">
  <div class="modal-dialog modal-lg navbar-modal" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back">
          <i class="las la-chevron-left"></i>
        </a>
        <h5 class="modal-title right-modal-title text-center" id="navbar-title-right"> &nbsp; </h5>
      {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button> --}}
    </div>
    <div class="modal-body">
      <div class="modal-content-result" id="content-result">
        <form id="filterForm">
          <div class="filter-section">
            @yield('right-nav-top')
            <div class="form-group mb-2">
              <label for="" class="m-0 fwb"><h5>Unit <input type='checkbox' id="unit" class="unit-group group-checkbox bg-primary" checked onclick="checkAllGroup(this)" /></h5></label>
              <hr class="mt-2">
              <div class="row">
                @foreach(unit_by_id()->chunk(2) as $unitList)
                  <div class="col pr-0">
                    @foreach($unitList as $unit)
                    <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                      <input type="checkbox" name="unit[]" class="custom-control-input bg-primary unit" value="{{ $unit['hr_unit_id'] }}" id="unit-{{ $unit['hr_unit_id'] }}" checked>
                      <label class="custom-control-label" for="unit-{{ $unit['hr_unit_id'] }}"> {{ $unit['hr_unit_short_name'] }}</label>
                    </div>
                    @endforeach
                  </div>
                @endforeach
              </div>
            </div>
            <div class="form-group mb-2">
              <label for="" class="m-0 fwb"><h5>Location <input type='checkbox' id="location" class="location-group group-checkbox bg-primary" checked onclick="checkAllGroup(this)" /></h5></label>
              <hr class="mt-2">
              <div class="row">
                @foreach(location_by_id()->chunk(3) as $locationList)
                  <div class="col pr-0">
                    @foreach($locationList as $location)
                    <div class="custom-control custom-checkbox custom-checkbox-color-check location-group">
                      <input type="checkbox" name="location[]" class="custom-control-input bg-primary location" value="{{ $location['hr_location_id'] }}" id="location-{{ $location['hr_location_id'] }}" checked>
                      <label class="custom-control-label" for="location-{{ $location['hr_location_id'] }}"> {{ $location['hr_location_short_name'] }}</label>
                    </div>
                    @endforeach
                  </div>
                @endforeach
              </div>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="area" class="form-control capitalize select-search" id="area">
                  <option selected="" value="">Choose Area...</option>
                  @foreach(area_by_id() as $key => $area)
                  <option value="{{ $key }}">{{ $area['hr_area_name'] }}</option>
                  @endforeach
              </select>
              <label for="area">Area</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="department" class="form-control capitalize select-search" id="department">
                  <option selected="" value="">Choose Department...</option>
                  @foreach(department_by_id() as $key => $department)
                  <option value="{{ $key }}">{{ $department['hr_department_name'] }}</option>
                  @endforeach
              </select>
              <label for="department">Department</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="section" class="form-control capitalize select-search " id="section">
                  <option selected="" value="">Choose Section...</option>
                  @foreach(section_by_id() as $key => $section)
                  <option value="{{ $key }}">{{ $section['hr_section_name'] }}</option>
                  @endforeach
              </select>
              <label for="section">Section</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="subSection" class="form-control capitalize select-search" id="subSection">
                  <option selected="" value="">Choose Sub Section...</option>
                  @foreach(subSection_by_id() as $key => $subSection)
                  <option value="{{ $key }}">{{ $subSection['hr_subsec_name'] }}</option>
                  @endforeach
              </select>
              <label for="subSection">Sub Section</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="floor_id" class="form-control capitalize select-search" id="floor_id" >
                  <option selected="" value="">Choose Floor...</option>
                  @foreach(floor_by_id() as $key => $floor)
                  <option value="{{ $key }}">{{ $floor['hr_floor_name'] }}</option>
                  @endforeach
              </select>
              <label for="floor_id">Floor</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="line_id" class="form-control capitalize select-search" id="line_id" >
                  <option selected="" value="">Choose Line...</option>
                  @foreach(line_by_id() as $key => $line)
                  <option value="{{ $key }}">{{ $line['hr_line_name'] }}</option>
                  @endforeach
              </select>
              <label for="line_id">Line</label>
            </div>
            <hr class="mt-2">
            <div class="form-group has-float-label select-search-group">
              <select name="otnonot" class="form-control capitalize select-search" id="otnonot" >
                  <option selected="" value="">Choose...</option>
                  <option value="0">Non-OT</option>
                  <option value="1">OT</option>
              </select>
              <label for="otnonot">OT/Non-OT</label>
            </div>
            <hr class="mt-2">
            
            <div class="form-group mb-2">
              <label for="" class="m-0 fwb"><h5>Employee Status </h5></label>
              <hr class="mt-2">
              <div class="row">
                <div class="col pr-0">
                  <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                    <input type="checkbox" name="emp_status[]" class="custom-control-input bg-primary sta" value="1" id="sta-1" checked >
                    <label class="custom-control-label" for="sta-1">Active</label>
                  </div>
                  
                  <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                    <input type="checkbox" name="emp_status[]" class="custom-control-input bg-primary sta" value="6" id="sta-6" >
                    <label class="custom-control-label" for="sta-6">Maternity</label>
                  </div>
                </div>
                <div class="col pr-0">
                  <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                    <input type="checkbox" name="emp_status[]" class="custom-control-input bg-primary sta" value="5" id="sta-5" >
                    <label class="custom-control-label" for="sta-5">Left</label>
                  </div>
                  <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                    <input type="checkbox" name="emp_status[]" class="custom-control-input bg-primary sta" value="2" id="sta-2" >
                    <label class="custom-control-label" for="sta-2">Resign</label>
                  </div>
                  <div class="custom-control custom-checkbox custom-checkbox-color-check ">
                    <input type="checkbox" name="emp_status[]" class="custom-control-input bg-primary sta" value="3" id="sta-3" >
                    <label class="custom-control-label" for="sta-3">Terminate</label>
                  </div>
                </div>
                
              </div>
            </div>
            @yield('right-nav')
            
            <hr class="mt-2">
            <div class="form-group">

              <button class="btn btn-primary nextBtn btn-lg pull-right filterBtnSubmit" type="button" ><i class="fa fa-filter"></i> Filter</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>

@push('js')
<script src="{{ asset('assets/js/moment.min.js')}}"></script>
<script src="{{ asset('assets/js/advance-filter.js')}}"></script>
<script type="text/javascript">
  let section = @json(section_by_id());
  let subSection = @json(subSection_by_id());
  let department = @json(department_by_id());
</script>
@endpush
