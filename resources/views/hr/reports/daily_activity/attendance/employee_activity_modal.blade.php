<div class="item_details_section">
    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
        <div class="fade-box-details fade-box">
          <div class="inner_gray clearfix">
            <div class="inner_gray_text text-center" id="heading">
             <h5 class="no_margin text-white">Employee Yearly Activity Report - {{ date('Y')}}</h5>   
            </div>
            <div class="inner_gray_close_button">
              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
            </div>
          </div>

          <div class="inner_body" id="modal-details-content" style="display: none">
            <div class="inner_body_content">
               <div class="body_top_section">
               		<h3 class="text-center modal-h3"><strong>Name :</strong> <b id="eName"></b></h3>
               		<h3 class="text-center modal-h3"><strong>Id :</strong> <b id="eId"></b></h3>
               		<h3 class="text-center modal-h3"><strong>Designation :</strong> <b id="eDesgination"></b></h3>
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