<div class="user-details-block" style="padding-top: 0;">
    @if(auth()->user()->can('Maternity Payment') || auth()->user()->hasRole('Super Admin') || auth()->user()->can('Manage Leave'))
    <a class="edit_" role="button"  data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Edit Leave Application"> <i data-toggle="modal" data-target="#right_modal_lg" class="fa fa-edit f-16"></i> </a>
    @endif
    <div class="user-profile text-center mt-0 " >
        <img id="avatar" class="avatar-130 img-fluid" src="{{ $employee->as_pic }} " >
    </div>
    <div class="text-center mt-3">
        <h4><b id="name">{{ $employee->as_name }}</b></h4>
        <p class="mb-0" id="designation">
        {{ $employee->hr_designation_name }}, {{$employee->hr_department_name}}</p>
        <p class="mb-0 field-data " id="designation">
        {{$employee->hr_unit_name}}</p>
    </div>
     <table style="width: 100%;" border="0">
         <tr>
             <td style="width: 90px;"><i class="field-title">Oracle ID</i></td>
             <td class="field-data">: {{ $employee->as_oracle_code }}</td>
         </tr>
         <tr>
             <td><i class="field-title">Associate ID</i></td>
             <td class="field-data">: {{ $employee->associate_id }}</td>
         </tr>
         <tr>
             <td><i class="field-title">Date of Join</i></td>
             <td class="field-data">: {{ $employee->as_doj->format('d-m-Y') }}</td>
         </tr>
         <tr>
             <td><i class="field-title">Age</i></td>
             <td class="field-data">: {{ $employee->as_dob->age }} Years</td>
         </tr>
         <tr>
             <td><i class="field-title">Husband Name</i></td>
             <td class="field-data">: {{ $leave->husband_name }}</td>
         </tr>
         <tr>
             <td><i class="field-title">Husband Occupation</i></td>
             <td class="field-data">: {{ $leave->husband_occupasion }}</td>
         </tr>
         <tr>
             <td><i class="field-title">Husband Age</i></td>
             <td class="field-data">: {{ $leave->husband_age }}</td>
         </tr>
         <tr>
             <td><i class="field-title">Total Child</i></td> 
             <td class="field-data">: {{ ($leave->no_of_son + $leave->no_of_daughter) }}</td>
         </tr>
     </table>
     <hr>
     <p class="text-center">
        <i class="las la-file-prescription f-18 text-success" ></i> 
        <a href="{{ asset($leave->usg_report) }}" style="    vertical-align: text-bottom;">view USG report</a>
     </p>
</div>