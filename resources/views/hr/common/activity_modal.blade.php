<div class="item_details_section">
    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
        <div class="fade-box-details fade-box">
          <div class="inner_gray clearfix">
            <div class="inner_gray_text text-center" id="heading">
             <h5 class="no_margin text-white"><span class="eName"></span>'s Yearly Activity Report - {{ date('Y')}}</h5>   
            </div>
            <div class="inner_gray_close_button">
              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
            </div>
          </div>

          <div class="inner_body" id="modal-details-content" style="display: none">
            <div class="inner_body_content">
               <div class="body_top_section d-flex justify-content-center mb-3">
                    <div>
                        <img id="image-employee" src="" style="height: 60px;">
                    </div>
                    <div class="pl-5">
                   		<p class="modal-p mb-0"><strong>Name :</strong> <b class="eName"></b></p>
                   		<p class="modal-p mb-0"><strong>Id :</strong> <b id="eId"></b></p>
                   		<p class="modal-p"><strong>Designation :</strong> <b id="eDesgination"></b></p>
                    </div>
               </div>
               <div class="body_content_section">
               	<div class="body_section" id="">
               		<table class="table table-bordered">
               			<thead>
               				<tr>
               					<th>Month</th>
               					<th>Absent</th>
               					<th>Late</th>
               					<th>Leave</th>
               					<th>Holiday</th>
               					<th>OT Hour</th>
               				</tr>
               			</thead>
               			<tbody id="body_result_section">
               				<tr>
               					<td colspan="5">
               						<img src='{{ asset("assets/img/loader-box.gif")}}' class="center-loader">
               					</td>
               				</tr>
               			</tbody>
               		</table>
               		
               	</div>
               </div>
            </div>
            <div class="inner_buttons">
              <a class="cancel_modal_button cancel_details" role="button"> Close </a>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
    var loaderModal = '<td class="text-center" colspan="6"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:50px;"></i></td>';
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
    var months    = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $(document).on('click','.yearly-activity',function(){
      $("#body_result_section").html(loaderModal);
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let name = $(this).data('ename');
        let designation = $(this).data('edesign');
        let imgsrc = $(this).data('image');
        $("#image-employee").attr('src',imgsrc);
        $(".eName").html(name);
        $("#eId").html(associateId);
        $("#eDesgination").html(designation);
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        // ajax call
        $.ajax({
            url: '/hr/reports/employee-yearly-activity-report-modal',
            type: "GET",
            data: {
                as_id: associateId
            },
            success: function(response){
                if(response.type === 'success'){
                  setTimeout(function(){
                    $("#body_result_section").html(response.value);
                  }, 1000);
                }else{
                  console.log(response);
                }
            }
        });
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "50%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });
    

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
</script>