<div class="row justify-content-center">
   <div class="col-sm-4">
      <div class="form-group">
         <label  for="page1">Page 1 :<span style="color: red">&#42;</span><br/><span style="font-size: 10px">(pdf|doc|docx|jpg|jpeg|png|xls|xlsx)</span></label>
            
         @if(isset($sbook->page1_url))
         <div class="align-right">
            <strong class='text-success'>{{basename(asset($sbook->page1_url))}}</strong>
           <a href="{{asset($sbook->page1_url)}}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
          <a href="{{asset($sbook->page1_url)}}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
          </div>
          @else
               <p class='text-danger'>No file found</p>
          @endif

         <input type="file" class="" name="page1" id="page1"  style="border: 0px;">
        <span id="upload_error_1" class="red" style="display: none; font-size: 12px;">You can only upload <strong>docx,doc,pdf,jpeg,png,jpg,xls,xlsx</strong> type file.</span>
    
      </div>
      <div class="form-group">
         <label  for="page2">Page 2 : <br/><span style="font-size: 10px">(pdf|doc|docx|jpg|jpeg|png|xls|xlsx) </span> </label>
         @if(isset($sbook->page2_url))
         <div class="align-right">
            <strong class='text-success'>{{basename(asset($sbook->page2_url))}}</strong>
           <a href="{{asset($sbook->page2_url)}}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
          <a href="{{asset($sbook->page2_url)}}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
          </div>
          @else
               <p class='text-danger'>No file found</p>
          @endif
        
        <input type="file" class="" name="page2" id="page2"  style="border: 0px;">
        <span id="upload_error_2" class="red" style="display: none; font-size: 12px;">You can only upload <strong>docx,doc,pdf,jpeg,png,jpg,xls,xlsx</strong> type file.</span>
            
      </div>
      <div class="form-group">
         <label  for="page3">Page 3 : <br/><span style="font-size: 10px">(pdf|doc|docx|jpg|jpeg|png|xls|xlsx) </span> </label>
        @if(isset($sbook->page3_url))
         <div class="align-right">
            <strong class='text-success'>{{basename(asset($sbook->page3_url))}}</strong>
           <a href="{{asset($sbook->page3_url)}}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
          <a href="{{asset($sbook->page3_url)}}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
          </div>
          @else
               <p class='text-danger'>No file found</p>
          @endif
        <input type="file" id="inp_page3" class="" name="page3"  style="border: 0px;">
        <span id="upload_error_3" class="red" style="display: none; font-size: 12px;">You can only upload <strong>docx,doc,pdf,jpeg,png,jpg,xls,xlsx</strong> type file.</span>
        
      </div>
      <div class="form-group">
         <label  for="page4">Page 4 :<br/> <span style="font-size: 10px">(pdf|doc|docx|jpg|jpeg|png|xls|xlsx) </span> </label>
         @if(isset($sbook->page4_url))
         <div class="align-right">
            <strong class='text-success'>{{basename(asset($sbook->page4_url))}}</strong>
           <a href="{{asset($sbook->page4_url)}}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
          <a href="{{asset($sbook->page4_url)}}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
          </div>
          @else
               <p class='text-danger'>No file found</p>
          @endif
        <input type="file" class="" name="page4" id="page4"  style="border: 0px;">
        <span id="upload_error_4" class="red" style="display: none; font-size: 12px;">You can only upload <strong>docx,doc,pdf,jpeg,png,jpg,xls,xlsx</strong> type file.</span>
        
      </div>
   </div>
   <div class="col-sm-4">
      <div class="form-group">
         <label  for="page5">Page 5 :<br/> <span style="font-size: 10px">(pdf|doc|docx|jpg|jpeg|png|xls|xlsx) </span> </label>
         @if(isset($sbook->page5_url))
         <div class="align-right">
            <strong class='text-success'>{{basename(asset($sbook->page5_url))}}</strong>
           <a href="{{asset($sbook->page5_url)}}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
          <a href="{{asset($sbook->page5_url)}}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
          </div>
          @else
               <p class='text-danger'>No file found</p>
          @endif
        <input type="file" class="" name="page5" id="page5"  style="border: 0px;">
        <span id="upload_error_5" class="red" style="display: none; font-size: 12px;">You can only upload <strong>docx,doc,pdf,jpeg,png,jpg,xls,xlsx</strong> type file.</span>
        
      </div>
      <div class="form-group">
         <label  for="page6">Page 6 :<br/> <span style="font-size: 10px">(pdf|doc|docx|jpg|jpeg|png|xls|xlsx) </span> </label>
        @if(isset($sbook->page6_url))
         <div class="align-right">
            <strong class='text-success'>{{basename(asset($sbook->page6_url))}}</strong>
           <a href="{{asset($sbook->page6_url)}}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
          <a href="{{asset($sbook->page6_url)}}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
          </div>
          @else
               <p class='text-danger'>No file found</p>
          @endif
        <input type="file" class="" name="page6" id="page6"  style="border: 0px;">
        <span id="upload_error_6" class="red" style="display: none; font-size: 12px;">You can only upload <strong>docx,doc,pdf,jpeg,png,jpg,xls,xlsx</strong> type file.</span>
        
      </div>


      <div class="form-group">
         <label  for="page7">Page 7 :<br/> <span style="font-size: 10px">(pdf|doc|docx|jpg|jpeg|png|xls|xlsx) </span> </label>
        @if(isset($sbook->page7_url))
         <div class="align-right">
            <strong class='text-success'>{{basename(asset($sbook->page7_url))}}</strong>
           <a href="{{asset($sbook->page7_url)}}" class="btn btn-xs btn-primary" target="_blank" title="View"><i class="fa fa-eye"></i> </a>
          <a href="{{asset($sbook->page7_url)}}" class="btn btn-xs btn-success" target="_blank" download title="Download"><i class="fa fa-download"></i> </a>
          </div>
          @else
               <p class='text-danger'>No file found</p>
          @endif
        <input type="file" class="" name="page7" id="page7"  style="border: 0px;">
        <span id="upload_error_7" class="red" style="display: none; font-size: 12px;">You can only upload <strong>docx,doc,pdf,jpeg,png,jpg,xls,xlsx</strong> type file.</span>
        
      </div>
      <div class="form-group">
         <button class="btn  btn-primary" type="submit">
             <i class="ace-icon fa fa-check "></i> Add
         </button>
     </div>
      <input type="hidden" name="store" value="store">
      
   </div>
</div>
      
      