@push('css')
	<style>
		th, td { white-space: nowrap; }
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
        /*making place holder custom*/
	    input::-webkit-input-placeholder {
	        color: #827979;
	        font-weight: bold;
	        font-size: 12px;
	    }
	    input:-moz-placeholder {
	        color: #827979;
	        font-weight: bold;
	        font-size: 12px;
	    }
	    input:-ms-input-placeholder {
	        color: #827979;
	        font-weight: bold;
	        font-size: 12px;
	    }
	    .DTFC_RightBodyLiner, .DTFC_LeftBodyLiner{
	    	width: 178px !important;
    		height: 431px !important;
	    }
        div.DTFC_LeftWrapper table.dataTable, div.DTFC_RightWrapper table.dataTable {
        margin-bottom: 0 !important;}
        .DTFC_LeftBodyLiner table{margin: 0 !important}
        /*.DTFC_LeftBodyLiner{max-height: 282px !important;}*/
        .DTFC_RightBodyLiner table{margin: 0 !important}
        /*.DTFC_RightBodyLiner{max-height: 282px !important;}*/
        table.DTFC_Cloned thead,table.DTFC_Cloned tfoot{background-color:white}div.DTFC_Blocker{background-color:white}div.DTFC_LeftWrapper table.dataTable,div.DTFC_RightWrapper table.dataTable{margin-bottom:0;z-index:2}div.DTFC_LeftWrapper table.dataTable.no-footer,div.DTFC_RightWrapper table.dataTable.no-footer{border-bottom:none}
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}">
@endpush


@push('js')
	<script src="{{ asset('assets/js/dataTables.fixedColumns.min.js')}}"></script>
@endpush