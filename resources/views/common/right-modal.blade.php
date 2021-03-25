@push('css')
  <style>
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .job_card{
      cursor: pointer;
      color: #089bab !important;
    }
  </style>
@endpush

<div class="modal right fade" id="right_modal_jobcard" tabindex="-1" role="dialog" aria-labelledby="right_modal_jobcard">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn-job" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
      <i class="las la-chevron-left"></i>
    </a>
        <h5 class="modal-title right-modal-title text-center capitalize" id="modal-title-right"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-top: 0;">
        <div class="modal-content-result" id="content-result">
          
        </div>
      </div>
      
    </div>
  </div>
</div>

@push('js')
  <script>
    var loader = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:100px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
    $(document).on('click', '.job_card', function() {
      var name = $(this).data('name');
      var associate = $(this).data('associate');
      var yearMonth = $(this).data('month-year');
      $("#modal-title-right").html(' Job Card - '+name);
      $('#right_modal_jobcard').modal('show');
      $("#content-result").html(loaderContent);
      $.ajax({
            url: "{{ url('hr/operation/partial_job_card') }}",
            data: {
                associate: associate,
                month_year: yearMonth
            },
            type: "GET",
            success: function(response){
              // console.log(response);
                if(response !== 'error'){
                  setTimeout(function(){
                    $("#content-result").html(response);
                  }, 1000);
                }else{
                  console.log(response);
                }
            }
        });
    });
  </script>
@endpush