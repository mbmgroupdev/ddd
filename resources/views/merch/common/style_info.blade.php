<div class="wrapper center-block">
  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading active" role="tab" id="headingOne">
          <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="display: block; font-size: 13px;">
              Style Info
            </a>
          </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
          <div class="panel-body">
            <div class="row">
                <div class="col-sm-10">
                    <table class="table custom-font-table detailTable" width="50%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>Production Type</th>
                            <td>{{ (!empty($style->stl_type)?$style->stl_type:null) }}</td>
                            <th>Style Reference 1</th>
                            <td>{!! (!empty($style->stl_no)?$style->stl_no:null) !!}</td>
                            <th>Operation</th>
                            <td>{{ (!empty($operations->name)?$operations->name:null) }}</td>
                        </tr>
                        <tr>
                            <th>Buyer</th>
                            <td>{!! (!empty($style->b_name)?$style->b_name:null) !!}</td>
                            <th>SMV/PC</th>
                            <td>{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
                            <th>Special Machine</th>
                            <td>{{ (!empty($machines->name)?$machines->name:null) }}</td>
                        </tr>
                        <tr>
                            <th>Style Reference 2</th>
                            <td>{!! (!empty($style->stl_product_name)?$style->stl_product_name:null) !!}</td>
                            <th>Sample Type</th>
                            <td>{{ (!empty($samples->name)?$samples->name:null) }}</td>
                            <th>Description</th>
                            <td>{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-2">
                    <a href="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" target="_blank">
                        <img class="thumbnail" height="100px" src="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" alt=""/>
                    </a>
                </div>
            </div>
          </div>
        </div>
      </div>
        
    </div>
</div>