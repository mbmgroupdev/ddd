@extends('merch.index')
@push('css')
<style type="text/css">
	.category-panel{
		border-right: 1px solid #d1d1d1; 
	}
	.category-items{	
		height: 450px;
		overflow-y: auto;
	}
	.category-title{
	    background: #e8e8e8;
	    text-align: center;
	    padding: 5px;
	    font-size: 16px;
	    font-weight: 600;
	}
	.sub-category-panel {
	    padding: 10px;
	    background: #f7f7f7;
	    margin: 5px 0;
	    cursor: pointer;
	    border-left: 2px solid #d7d7d7;
	}
	.column-list li{
	    min-height: 20px;
	    border: 1px dotted #af9797;
	    text-align: center;
	    padding: 5px;
	    margin-bottom: 5px;
	    background: #fff;
	}
	.column-list li:hover{
		cursor: move;
	}
	.fa-exchange{
	    text-align: center;
	    padding: 2px 5px;
	    border: 1px solid;
	    border-radius: 3px;
	}
	.sub-category-title {
	    background: #d8d8d8;
	    padding: 5px;
	    margin-bottom: 5px;
	    text-align: center;
	    font-weight: 600;
	}
	.pin .sub-category-title{
		display: none;
	}
	.button-area{
		padding-top: 10px;
		text-align: center;
	}
</style>
@endpush
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Merchandising </a>
                </li> 
                <li>
                    <a href="#"> Setup </a>
                </li>
                  <li>
                    <a href="#"> Materials </a>
                </li>
                <li class="active">Change Item Index</li>
            </ul>
        </div>

        <div class="page-content"> 
            <div class="row">
            	<div class="col-sm-12">
            		
	              	<div class="panel panel-info">
	              		<div class="panel-heading">
	                        <h5> <i class="ace-icon fa fa-exchange"></i> Item Tab Index</h5>
	                    </div>
	                	<div class="panel-body">
	                		@include('inc/message')
		                	<form class="form-horizontal" role="form" method="post" action="{{ url('merch/setup/item_tab_index_store') }}" enctype="multipart/form-data">
		                		{{ csrf_field() }}
		                		<div class="sortable-area row">
		                		@foreach($cat as $key => $category)
		                			@php
		                				if($key == 1){
		                					$name = 'fab-item';
		                				}else if($key == 2){
		                					$name = 'sew-item';
		                				}else if($key == 3){
		                					$name = 'fin-item';
		                				}else{
		                					$name = 'item';
		                				}

		                			@endphp

		                			<div class="col-sm-4 category-panel">
		                				<div class="category-title"> {{$category->mcat_name}} </div>
		                				<div class="category-items ui-sortable">
		                				@foreach($catItem[$key] as $key1 => $dg)
		                					@php
		                						if($key1 == ''){
		                							$pin = 'pin';
		                						}else{
		                							$pin = '';
		                						}
		                					@endphp
		                					<div class="sub-category-panel ui-sortable-handle {{$pin}}">
		                						@if($key1 != '')
			                						<div class="sub-category-title"> 
			                							{{$subcat[$key1]??''}}
			                							<input type="hidden" name="subcat[]" value="{{$key1}}">  
			                						</div>
		                						@endif
		                                        <ul class="column-list list-unstyled ui-sortable" >
		                                            @foreach($dg as $key2 => $item)
		                                            <li class="{{$name}} ui-sortable-handle" id="{{$name}}-{{$item->id}}">
		                                                <div class="item-name" id="item-{{$item->id}}" style="opacity: 1;">
		                                                	<input type="hidden" name="item_tab[]" value="{{$item->id}}"> 
		                                                    {{$item->item_name}}
		                                                </div>
		                                            </li>
		                                        	@endforeach
	                                        	</ul>
	                                        </div>	                                        
		                                @endforeach
		                                </div>
		                			</div>

		                		@endforeach
		                		</div>
		                		<div class="button-area">
									    <!-- <button class="btn btn-xs restore" type="reset">
									        <i class="ace-icon fa fa-undo bigger-110"></i> Reset
									    </button>
									      &nbsp; &nbsp; &nbsp; -->
									    <button class="btn btn-success btn-xs" type="submit">
									        <i class="ace-icon fa fa-check bigger-110"></i> Save
									    </button>
											                			
		                		</div>
		                	</form>
	                	</div>

	                </div>
            	</div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script type="text/javascript">
	/*$(document).ready(function(){
		var cachediv = $('.sortable-area').html();

		$('.restore').on('click', function(){
			$('.column-list').sortable( "destroy" );
		});
	});*/
	$('.column-list').sortable({
        connectWith: '.column-list',
		containment: "parent",
		cursor: "move",
		opacity:0.8,
		revert:true,
		tolerance:'pointer',
		start: function(event, ui) {
			ui.item.parent().css({'min-height':ui.item.height()})
		},
		update: function(event, ui) {
			ui.item.parent({'min-height':''})
		}
    });

    $('.category-items').sortable({
        connectWith: '.category-items',
        items: '> div:not(.pin)',
		containment: "parent",
		cursor: "move",
		opacity:0.8,
		revert:true,
		tolerance:'pointer',
		start: function(event, ui) {
			ui.item.parent().css({'min-height':ui.item.height()})
		},
		update: function(event, ui) {
			ui.item.parent({'min-height':''})
		}
    });
</script>
@endpush			

@endsection