@php $emp_count = employee_count(); @endphp
<div class="row">
   <div class="col-sm-12 flex justify-content-between mb-3">
      <div class="iq-card icon-card">
         <div class="iq-card-body text-center">
            <div class="doc-profile">
               <i class="img-fluid icon-70 las la-user-tie"></i>
            </div>
            <div class="iq-doc-info">
               <h4> Active</h4>
               <p class="mb-0">{{$emp_count['active']}}</p>
            </div>
         </div>
      </div>
      <div class="iq-card icon-card">
         <div class="iq-card-body text-center">
            <div class="doc-profile">
               <i class="img-fluid icon-70 las la-file-prescription"></i>
            </div>
            <div class="iq-doc-info">
               <h4> Maternity</h4>
               <p class="mb-0">{{$emp_count['maternity']}}</p>
            </div>
         </div>
      </div>
      <div class="iq-card icon-card">
         <div class="iq-card-body text-center">
            <div class="doc-profile">
               <i class="img-fluid icon-70 las la-male"></i>
            </div>
            <div class="iq-doc-info">
               <h4> Male</h4>
               <p class="mb-0">{{$emp_count['males']}}</p>
            </div>
         </div>
      </div>
      <div class="iq-card icon-card">
         <div class="iq-card-body text-center">
            <div class="doc-profile">
               <i class="img-fluid icon-70 las la-female"></i>
            </div>
            <div class="iq-doc-info">
               <h4> Female</h4>
               <p class="mb-0">{{$emp_count['females']}}</p>
            </div>
         </div>
      </div>
      
      <div class="iq-card icon-card">
         <div class="iq-card-body text-center">
            <div class="doc-profile">
               <i class="img-fluid icon-70 las la-stopwatch"></i>
            </div>
            <div class="iq-doc-info">
               <h4> OT</h4>
               <p class="mb-0">{{$emp_count['ot']}}</p>
            </div>
         </div>
      </div>
      <div class="iq-card icon-card">
         <div class="iq-card-body text-center">
            <div class="doc-profile">
               <i class="img-fluid icon-70 las la-clock "></i>
            </div>
            <div class="iq-doc-info">
               <h4> Non-OT</h4>
               <p class="mb-0">{{$emp_count['non_ot']}}</p>
            </div>
         </div>
      </div>
      <div class="iq-card icon-card">
         <div class="iq-card-body text-center">
            <div class="doc-profile">
               <i class="img-fluid icon-70 las la-calendar-day"></i>
            </div>
            <div class="iq-doc-info">
               <h4> Today's Join</h4>
               <p class="mb-0">{{$emp_count['todays_join']}}</p>
            </div>
         </div>
      </div>
   </div>
</div>