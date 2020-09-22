<style type="text/css">


    .lib-male{background-color:#288402!important;}
    .lib-female{background-color:#b95907!important;}
    .lib-absent{background-color:#e9e9e9!important;}
</style>
<div class="panel panel-info col-sm-12">
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                <a href="#" class="search_all" data-category="{{ $request1['category']??'' }}" data-type="{{ $request1['type']??'' }}"> MBM Group </a>
            </li>
            <li>
                <a href="#" class="search_unit"> All Unit </a>
            </li>
            
            <li>
                 Line
            </li>
        </ul><!-- /.breadcrumb -->
        <a href="#" id="printButton" class="btn btn-info pull-right" onclick='printDiv({{json_encode($line_list)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        <div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="row">
                <div class="col-sm-12">
                    <ul class="color-bar">
                       <li><span class="color-label lib-male"></span><span class="lib-label">  Male</span></li>
                       <li><span class="color-label lib-female"></span><span class="lib-label"> Female</span></li>
                       <li><span class="color-label lib-absent"></span><span class="lib-label"> Absent</span></li>
                    </ul>
                </div>
            	<div class="col-sm-12">

                    <table class="table no-border">
                        <tbody>
                            @foreach($line_list as $k=>$line)
                                <tr class="no-border">
                                    <td class="no-border" style="border-top:none;">
                                        <a href="#" class="search_section" data-line="{{ $line->hr_line_id }}">
                                            {{ $line->hr_line_name }}
                                        </a>
                                    </td>
                                    <td class="no-border" style="width:80%;border-top:none;">
                                        <div class="progress">
                                        @if(isset($lineEmpCount[$line->hr_line_id]['percent_male']))
                                          <div class="lib-male progress-bar" role="progressbar" style="width: {{$lineEmpCount[$line->hr_line_id]['percent_male']}}%" aria-valuenow="{{$lineEmpCount[$line->hr_line_id]['percent_male']}}" aria-valuemin="0" aria-valuemax="100" title="Male present {{$lineEmpCount[$line->hr_line_id]['present_male']}}">{{$lineEmpCount[$line->hr_line_id]['percent_male']}}%</div>
                                        @endif
                                        @if(isset($lineEmpCount[$line->hr_line_id]['percent_female']))

                                          <div class="lib-female progress-bar" role="progressbar" style="width: {{$lineEmpCount[$line->hr_line_id]['percent_female']}}%;" aria-valuenow="{{$lineEmpCount[$line->hr_line_id]['percent_female']}}" aria-valuemin="0" aria-valuemax="100" title="Feale present {{$lineEmpCount[$line->hr_line_id]['present_female']}}">{{$lineEmpCount[$line->hr_line_id]['percent_female']}}%</div>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
				</div>
            </div>
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
@php
    $rangeFrom = date('Y-m-d');
    $rangeTo = date('Y-m-d');
    if($request1['type'] == 'date'){
        $rangeFrom = $request1['date'];
        $rangeTo = $request1['date'];
    }
@endphp
<script>
    $(document).ready(function(){
        $('.after-load span').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-30"></i>');
    });
    


    
</script>