
@extends('merch.index')
@section('content')
@push('css')
  <style>
    .pagination {
          display: flex;
          justify-content: center;
      }
  </style>
@endpush
<div class="main-content">

    <div class="main-content-inner">
        <!-- breadcrumb -->
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li>
                <li>
                    <a href="#"> Style </a>
                </li>
                <li class="active"> Style Gallery</li>
            </ul>
        </div>
        <!-- /.breadcrumb -->

        <!-- page content -->
        <div class="page-content">
          <div class="panel panel-success">
            <div class="panel-heading">
              <h6>Gallery </h6>
            </div>

            <div class="panel-body">
              <div class="row">
                  <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div>
                      <ul class="ace-thumbnails clearfix">
                        @foreach($getStyle as $style)
                        <li>
                          <a href="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" title="{{ $style->stl_no }}" data-rel="colorbox">
                            <img width="150" height="150" alt="150x150" src="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" />
                            <div class="text">
                              <div class="inner"></div>
                            </div>
                          </a>
                        </li>

                        @foreach($style->style_image as $stlImage)
                        <li>
                          <a href="{{ asset(!empty($stlImage->image)?$stlImage->image:'assets/images/avatars/profile-pic.jpg') }}" title="{{ $style->stl_no }}" data-rel="colorbox">
                            <img width="150" height="150" alt="150x150" src="{{ asset(!empty($stlImage->image)?$stlImage->image:'assets/images/avatars/profile-pic.jpg') }}" />
                            <div class="text">
                              <div class="inner"></div>
                            </div>
                          </a>
                        </li>
                        @endforeach
                        @endforeach
                      </ul>
                    </div><!-- PAGE CONTENT ENDS -->
                    <div class="pagination">
                      {{ $getStyle->appends($_REQUEST)->render() }}
                    </div>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.page-content -->
            </div>
        </div><!-- /.page-content -->
    </div>

</div>
@push('js')
<!-- page specific plugin scripts -->
  <script src="{{ asset('assets/js/jquery.colorbox.min.js') }}"></script>
  <!-- inline scripts related to this page -->
    <script type="text/javascript">
      jQuery(function($) {
      var $overflow = '';
      var colorbox_params = {
        rel: 'colorbox',
        reposition:true,
        scalePhotos:true,
        scrolling:false,
        previous:'<i class="ace-icon fa fa-arrow-left"></i>',
        next:'<i class="ace-icon fa fa-arrow-right"></i>',
        close:'&times;',
        current:'{current} of {total}',
        maxWidth:'100%',
        maxHeight:'100%',
        onOpen:function(){
          $overflow = document.body.style.overflow;
          document.body.style.overflow = 'hidden';
        },
        onClosed:function(){
          document.body.style.overflow = $overflow;
        },
        onComplete:function(){
          $.colorbox.resize();
        }
      };

      $('.ace-thumbnails [data-rel="colorbox"]').colorbox(colorbox_params);
      $("#cboxLoadingGraphic").html("<i class='ace-icon fa fa-spinner orange fa-spin'></i>");//let's add a custom loading icon
      
      
      $(document).one('ajaxloadstart.page', function(e) {
        $('#colorbox, #cboxOverlay').remove();
       });
    })
  </script>

@endpush
@endsection
