<?php

/*
|--------------------------------------------------------------------------
| MERCHANDISING
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'merchandising','namespace' => 'Merch'], function(){
	Route::get('/', 'DashboardController@index');
});

/*
*--------------------------------------------------------------
* Style
*--------------------------------------------------------------
*/
Route::group(['prefix' => 'merch/style','namespace' => 'Merch\Style'], function(){
	Route::get('style_new', 'NewStyleController@showForm');
	Route::post('check-style-no', 'NewStyleController@checkStlNo');
	Route::get('sample_season', 'NewStyleController@getSampleByBuyer');
	Route::get('remove_image/', 'NewStyleController@removeGalleryImage');
	Route::post('style_store', 'NewStyleController@store');
	Route::get('style_list', 'NewStyleController@showList');
	Route::get("delete/{id}", "NewStyleController@styleDelete");
	Route::get('sample_garments', 'NewStyleController@garmentsList');

	Route::get('style_list_data', 'NewStyleController@getData');
	Route::get('style_new_edit/{stl_id}', 'NewStyleController@styleDevelopmentEditForm');
	Route::post('style_update', 'NewStyleController@styleUpdate');

	// Route::get('style_copy/{stl_id}', 'NewStyleController@styleCopyForm');

	Route::get('style_copy/{stl_id}', 'NewStyleController@styleNewCopyForm');
	Route::post('style_copy_store', 'NewStyleController@storeNewCopy');
	Route::get('style_copy_search', 'NewStyleController@styleNewCopySearchForm');

	Route::get('create_bulk', 'NewStyleController@styleBulkForm');
	// Route::get('style_copy_search', 'NewStyleController@styleCopySearchForm');
	Route::get('style_profile/{stl_id}', 'NewStyleController@getStyleProfile');

	// Route::get('find_bulk', 'NewStyleController@styleFindBulk');

	Route::post('bulk_store', 'NewStyleController@storeBulk');

	Route::get('product', 'NewStyleController@productList');
	Route::get('season', 'NewStyleController@seasonList');
	Route::get('wash', 'NewStyleController@washList');
	Route::get('fetchsizegroup/{buyerid}/{p_type}', 'NewStyleController@fetchSizeGroup');
	Route::post('fetchwashgroup', 'NewStyleController@fetchWashGroup');
	Route::get('sizegroup', 'NewStyleController@sizegroupList');
	Route::get('buyerlist', 'NewStyleController@buyerList');
	Route::get('get_brands_data', 'NewStyleController@getBrandsData');

	Route::get('get_sz_grp_modal_data', 'NewStyleController@getSzGrpModalData');
	Route::get('get_sz_grp_details', 'NewStyleController@getSzGrpDetails');
	Route::get('fetchspecialmechines', 'NewStyleController@fetchspecialmechines');

	Route::get('style-gallery', 'NewStyleController@styleGallery');

	// BOM
	Route::get('bom/{id}', 'BOMController@show');
	Route::get('bom-ajax-store', 'BOMController@ajaxStore');
});

/*
*--------------------------------------------------------------
* STYLE BOM
*--------------------------------------------------------------
*/
Route::group(['prefix' => 'merch/style_bom','namespace' => 'Merch\StyleBOM'], function(){
	Route::get("/", "StyleBomController@showList");
	Route::get("/style_bom_data", "StyleBomController@getListData");
	Route::get("/{id}/create", "StyleBomController@showForm");
	Route::get("/get_item_data", "StyleBomController@getItemData");
	Route::get("/get_article_by_supplier", "StyleBomController@article");
	Route::get("/get_composition_by_supplier", "StyleBomController@composition");
	Route::get("/get_construction_by_supplier", "StyleBomController@construction");
	Route::post("/{id}/store", "StyleBomController@store");
	Route::get("/new_article", "StyleBomController@createArticle");
	Route::get("/new_composition", "StyleBomController@createComposition");
	Route::get("/new_construction", "StyleBomController@createConstruction");
	Route::get("/{id}/edit", "StyleBomController@editForm");
	Route::post("/{id}/update", "StyleBomController@update");
	Route::get("/{id}/delete", "StyleBomController@destroy");
	Route::post("/newcolorstore", "StyleBomController@saveNewColor");

	Route::get("/get_composition_by_article", "StyleBomController@compositionByArticle");
	Route::post('/ajax_save_supplier', 'StyleBomController@ajaxSaveSupplier');
	Route::post('/ajax_save_bom_info', 'StyleBomController@ajaxSaveBomInfo');
	Route::post('/ajax_update_bom_info', 'StyleBomController@ajaxUpdateBomInfo');

});

/*
*--------------------------------------------------------------
* Search
*--------------------------------------------------------------
*/
Route::group(['prefix' => 'merch/search','namespace' => 'Merch\Search'], function(){
	Route::get('ajax-item-search', 'AjaxSearchController@item');
	Route::get('ajax-supplier-article-search', 'AjaxSearchController@article');

});

/*
*--------------------------------------------------------------
* Setup
*--------------------------------------------------------------
*/
Route::group(['prefix' => 'merch/setup','namespace' => 'Merch\Setup'], function(){
	
	//Sample type
	Route::get('sampletype', 'SampleController@sampleType');
	Route::post('sampletypestore', 'SampleController@sampletypeStore');
	Route::get('sampletypedelete/{clr_id}', 'SampleController@sampletypeDelete');
	Route::get('sampletypedit/{clr_id}', 'SampleController@sampletypeEdit');
	Route::post('sampletypeupdate', 'SampleController@sampletypeUpdate');
	Route::post('sampletypeupdate-ajax', 'SampleController@sampletypeUpdateAjax');
	Route::get('sampletypecheck', 'SampleController@sampletypeCheck');

	//Product Size
	Route::get('productsize', 'ProductsizeController@productSize');
	Route::get('productsize_brand_generate', 'ProductsizeController@brandGenerate');
	Route::post('productsizestore', 'ProductsizeController@productSizeStore');
	Route::get('sizegroupsave', 'ProductsizeController@sizegroupSave');
	Route::get('productsizedelete/{prdsz_id}', 'ProductsizeController@productSizeDelete');
	Route::get('productsizedit/{prdsz_id}', 'ProductsizeController@productSizeEdit');
	Route::post('productsizeupdate', 'ProductsizeController@productSizeUpdate');

	// Buyer
	Route::get('buyer_info', 'BuyerController@showForm');
	Route::match(['get','post'],'buyer_info_store', 'BuyerController@buyerInfoStore');
	Route::post('ajax_save_buyer', 'BuyerController@ajaxSaveBuyer');
	Route::get('buyerinfo_listdata', 'BuyerController@buyerinfoListData');
	Route::get('buyer_profile/{buyer_id}', 'BuyerController@getBuyerProfile');
	Route::get('buyer_info_edit/{b_id}', 'BuyerController@buyerUpdate');
	Route::post('update','BuyerController@buyerUpdateAction');
	Route::get('buyerdelete/{b_id}', 'BuyerController@buyerDelete');

	//product Type
	Route::get("product_type", "ProductTypeController@showForm");
	Route::post("product_type_store", "ProductTypeController@store");
	Route::get("product_type_edit/{id}", "ProductTypeController@edit");
	Route::post("product_type_update", "ProductTypeController@update");
	Route::post("product_type_update-ajax", "ProductTypeController@updateAjax");
	Route::get("product_type_delete/{id}", "ProductTypeController@destroy");

	//Garments Type
	Route::get("garments_type", "GarmentsTypeController@showForm");
	Route::post("garments_type_store", "GarmentsTypeController@store");
	Route::get("garments_type_edit/{id}", "GarmentsTypeController@edit");
	Route::post("garments_type_update", "GarmentsTypeController@update");
	Route::post("garments_type_update-ajax", "GarmentsTypeController@updateAjax");
	Route::get("garments_type_delete/{id}", "GarmentsTypeController@destroy");

	//Season Type
	Route::get("season", "SeasonController@showForm");
	Route::post("season_store", "SeasonController@store");
	Route::get("season_edit/{id}", "SeasonController@edit");
	Route::post("season_update", "SeasonController@update");
	Route::get("season_delete/{id}", "SeasonController@destroy");
	Route::get('season_input', 'SeasonController@searchSeason');

	//Supplier
	Route::get('supplier', 'SupplierController@showForm');
	Route::get('supplier_data', 'SupplierController@getData');
	Route::post('supplier', 'SupplierController@saveData');
	Route::post('ajax_save_supplier', 'SupplierController@ajaxSaveSupplier');
	Route::get('supplier_delete/{sup_id}', 'SupplierController@SupplierDelete');
	Route::get('supplier_edit/{sup_id}', 'SupplierController@SupplierEdit');
	Route::post('supplier_update', 'SupplierController@SupplierUpdate');

	//Materials item
	Route::get('item', 'MaterialController@itemForm');
	Route::get('item_data', 'MaterialController@itemData');
	Route::post('item_store', 'MaterialController@itemStore');
	Route::post('item_store_ajax', 'MaterialController@itemStoreAjax');
	Route::post('main_category_store', 'MaterialController@mainCategoryStore');
	Route::get('main_category_delete/{mcat_id}', 'MaterialController@mainCategoryDelete');
	Route::get('item_edit_ajax/{mcat_id}', 'MaterialController@itemEditAjax');
	Route::get('item_edit/{mcat_id}', 'MaterialController@itemEdit');
	Route::post('item_update', 'MaterialController@itemUpdate');
	Route::get('item_delete/{mcat_id}', 'MaterialController@itemDelete');
	Route::get('get_material_sub_cat_name_suggestion', 'MaterialController@getMaterialSubcategorySuggestion');

	//Materials Color
	Route::get('color', 'MaterialController@color');
	Route::post('colorstore', 'MaterialController@colorStore');
	Route::get('colordelete/{clr_id}', 'MaterialController@colorDelete');
	Route::get('coloredit/{clr_id}', 'MaterialController@colorEdit');
	Route::post('colorupdate', 'MaterialController@colorUpdate');

	// article 
	Route::get('article/{sup_id}', 'ArticleController@articleForm');
	Route::get('article', 'ArticleController@articleForm');
	Route::post('article_store', 'ArticleController@articleStore');
	Route::get('article_edit/{type}/{a_id}', 'ArticleController@articleEdit');
	// Route::get('composition_edit/{a_id}', 'ArticleController@compositionEdit');
	// Route::get('construction_edit/{a_id}', 'ArticleController@constructionEdit');
	Route::post('article_update', 'ArticleController@articleUpdate');
	Route::get('article_delete/{art_id}', 'ArticleController@articleDelete');

	// operation
	Route::get('operation', 'OperationController@operation');
	Route::post('operationstore', 'OperationController@operationStore');
	Route::get('operationdelete/{op_id}', 'OperationController@operationDelete');
	Route::get('operationedit/{op_id}', 'OperationController@operationEdit');
	Route::post('operationupdate', 'OperationController@operationUpdate');
	Route::post('operationupdate-ajax', 'OperationController@operationUpdateAjax');

	//Special Machine
	Route::get('spmachine', 'SpmachineController@spmachine');
	Route::post('spmachinestore', 'SpmachineController@spmachineStore');
	Route::get('spmachinedelete/{spmachine_id}', 'SpmachineController@spmachineDelete');
	Route::get('spmachineedit/{spmachine_id}', 'SpmachineController@spmachineEdit');
	Route::post('spmachineupdate', 'SpmachineController@spmachineUpdate');
	Route::post('spmachineupdate-ajax', 'SpmachineController@spmachineUpdateAjax');

	//Wash Category
	Route::get('wash_category','WashCategoryController@showForm');
	Route::post('wash_category_save','WashCategoryController@saveForm');
	Route::get('wash_category_edit/{id}','WashCategoryController@editForm');
	Route::post('wash_category_update','WashCategoryController@updateForm');
	Route::post('wash_category_update-ajax','WashCategoryController@updateAjax');
	Route::get('wash_category_delete/{id}','WashCategoryController@deleteEntry');

	//Wash Type
	Route::get('wash_type','WashTypeController@showForm');
	Route::post('wash_type','WashTypeController@saveForm');
	Route::get('wash_type_edit/{id}','WashTypeController@editForm');
	Route::post('wash_type_update','WashTypeController@updateForm');
	Route::get('wash_type_delete/{id}','WashTypeController@deleteWash');
	Route::post('wash_category_add','WashTypeController@saveWashCategory');

	// TNA Library
	Route::get('tna_library', 'TimeActionController@timeActionForm');
	Route::post('tna_library_store', 'TimeActionController@libraryStore');
	Route::get('library_edit/{id}', 'TimeActionController@libraryEdit');
	Route::post('library_update', 'TimeActionController@libraryUpdate');
	Route::post('tna_library_update-ajax', 'TimeActionController@libraryUpdateAjax');
	Route::get('tna_library_delete/{id}', 'TimeActionController@libraryDelete');

	// TNA Template
	Route::get('tna_template', 'TimeActionController@templateForm');
	Route::post('tna_temp_store', 'TimeActionController@templateStore');
	Route::get('tna_template_edit/{id}', 'TimeActionController@templateEdit');
	Route::post('tna_template_update', 'TimeActionController@templateUpdate');
	Route::post('tna_template_update-ajax', 'TimeActionController@templateUpdateAjax');
	Route::get('tna_template_delete/{id}', 'TimeActionController@templateDelete');

	//Approval Hierarchy
	Route::get('approval', 'ApprovalController@showForm');
	Route::post('approval_store', 'ApprovalController@approvalStore');
	Route::get('approval_edit/{id}', 'ApprovalController@approvalEdit');
	Route::post('approval_update', 'ApprovalController@approvalUpdate');
	Route::get('approv_delete/{id}','ApprovalController@deleteApprov');

});



Route::group(['middleware' => ['role:Super Admin|merchandiser|merchandising_executive']], function () {

Route::get('merch/dashboard','Merch\DashboardController@index');


//Brand
Route::get('merch/setup/brand', 'Merch\Setup\BuyerController@brand');
//Brand Store
Route::post('merch/setup/brandstore', 'Merch\Setup\BuyerController@brandStore');
//Brand Update
Route::get('merch/setup/brandupdate/{b_id}', 'Merch\Setup\BuyerController@brandUpdate');
Route::post('merch/setup/brandUpdateAction','Merch\Setup\BuyerController@brandUpdateAction');
//Brand delete
Route::get('merch/setup/brand_delete/{br_id}', 'Merch\Setup\BuyerController@brandDelete');

//drawable items
Route::get('merch/setup/item_tab_index', 'Merch\Setup\MaterialController@itemTabIndex');
Route::post('merch/setup/item_tab_index_store', 'Merch\Setup\MaterialController@storeItemTabIndex');


//Article & Dimension Booking
Route::get('merch/setup/articledimension', 'Merch\Setup\articledimensionController@article');




//Operation

Route::get('merch/style/fetchoperations', 'Merch\Setup\OperationController@fetchOperations');
// end


Route::get('merch/setup/sub_cat_by_main_cat', 'Merch\Setup\MaterialController@getSubCatByMainCat');
Route::get('merch/setup/item_code', 'Merch\Setup\MaterialController@itemCode');

//Materials Color & Size
Route::get('merch/setup/colorsize', 'Merch\Setup\MaterialController@colorSize');
Route::get('merch/setup/size', 'Merch\Setup\MaterialController@sizeForm');
Route::post('merch/setup/size', 'Merch\Setup\MaterialController@sizeStore');
Route::get('merch/setup/size_delete/{sz_id}', 'Merch\Setup\MaterialController@sizeDelete');
Route::get('merch/setup/size_edit/{sz_id}', 'Merch\Setup\MaterialController@sizeEdit');
Route::post('merch/setup/size_update', 'Merch\Setup\MaterialController@sizeUpdate');
Route::get('merch/setup/size_code', 'Merch\Setup\MaterialController@sizeCode');


//Article & Dimension Booking

Route::get('merch/setup/comp_delete/{com_id}', 'Merch\Setup\ArticleController@compositionDelete');
Route::get('merch/setup/cons_delete/{cons_id}', 'Merch\Setup\ArticleController@constructionDelete');

Route::get('merch/setup/size_by_item', 'Merch\Setup\ArticleController@getSizeByItem');
Route::get('merch/setup/size_by_item', 'Merch\Setup\ArticleController@getSizeByItem');
Route::get('merch/setup/article_by_supllier', 'Merch\Setup\ArticleController@getArticleBySupplier');
Route::get('merch/setup/composition_by_article', 'Merch\Setup\ArticleController@getCompByArticle');
Route::get('merch/setup/add_new_article', 'Merch\Setup\ArticleController@addNewByArticle');
Route::get('merch/setup/add_new_composition', 'Merch\Setup\ArticleController@addNewComposition');


//Executive Team Setup
Route::get('merch/setup/excecutive_team_setup', 'Merch\Setup\ExcecutiveTeamSetupController@index');
Route::get('merch/setup/excecutive/members', 'Merch\Setup\ExcecutiveTeamSetupController@memberList');
Route::get('merch/setup/excecutive/members_edit', 'Merch\Setup\ExcecutiveTeamSetupController@memberListEdit');
Route::post('merch/setup/excecutive/members_save', 'Merch\Setup\ExcecutiveTeamSetupController@StoreExcecutiveTeam');
Route::get('merch/setup/excecutive/edit_team/{id}', 'Merch\Setup\ExcecutiveTeamSetupController@editTeam');
Route::get('merch/setup/excecutive/delete_team/{id}', 'Merch\Setup\ExcecutiveTeamSetupController@deleteTeam');
Route::post('merch/setup/excecutive/members_update', 'Merch\Setup\ExcecutiveTeamSetupController@updateTeam');




/*
*--------------------------------------------------------------
* STYLE BOM Costing
*--------------------------------------------------------------
*/
Route::get("merch/style_costing", "Merch\StyleCosting\StyleCostingController@showList");
Route::post("merch/style_costing_data", "Merch\StyleCosting\StyleCostingController@getListData");
Route::get("merch/style_costing/{id}/create", "Merch\StyleCosting\StyleCostingController@showForm");
Route::post("merch/style_costing/{id}/create", "Merch\StyleCosting\StyleCostingController@store");
Route::get("merch/style_costing/{id}/edit", "Merch\StyleCosting\StyleCostingController@editForm");
Route::get("merch/style_costing/{id}/print", "Merch\StyleCosting\StyleCostingController@editFormPrint");
Route::post("merch/style_costing/{id}/edit", "Merch\StyleCosting\StyleCostingController@update");
#----------------- Capacity Reservation by MATI --------------#
#-------------------------------------------------------------#
Route::get('merch/reservation/reservation','Merch\Reservation\ReservationController@showForm');
Route::post('merch/reservation/reservation','Merch\Reservation\ReservationController@storeData');
Route::get('merch/reservation/reservation_list','Merch\Reservation\ReservationController@getReservationList');
Route::get('merch/reservation/reservation_list_data','Merch\Reservation\ReservationController@getReservationListData');
Route::get('merch/reservation/reservation_edit/{res_id}','Merch\Reservation\ReservationController@resEdit');
Route::post('merch/reservation/reservation_update','Merch\Reservation\ReservationController@resUpdate');
Route::get('merch/orders/order_list','Merch\Orders\OrderController@orderList');
Route::post('merch/orders/order_list_data','Merch\Orders\OrderController@orderListData');
Route::get("merch/orders/delete/{id}", "Merch\Orders\OrderController@orderDelete");

Route::get('merch/orders/order_entry/{res_id}', 'Merch\Orders\OrderController@orderEntry');
Route::get('merch/orders/order_entry_direct', 'Merch\Orders\OrderController@orderEntryDirect');
Route::get('merch/orders/order_quantity_direct', 'Merch\Orders\OrderController@orderQuantityDirect');

Route::post('merch/orders/order_entry_direct_save', 'Merch\Orders\OrderController@orderStoreDirect');

Route::post('merch/orders/order_entry', 'Merch\Orders\OrderController@orderStore');
Route::get('merch/orders/order_edit/{order_id}', 'Merch\Orders\OrderController@orderEdit');
Route::post('merch/orders/order_update', 'Merch\Orders\OrderController@orderUpdate');
Route::get('merch/orders/season_style', 'Merch\Orders\OrderController@styleList');
Route::get('merch/orders/season_style_direct', 'Merch\Orders\OrderController@styleListdirect');

Route::get('merch/order_edit/get_port_country_wise', 'Merch\Orders\OrderController@getCountryPorts');

Route::get('merch/orders/order_copy/{order_id}', 'Merch\Orders\OrderController@orderCopyForm');
Route::post('merch/orders/order_copy/{order_id}', 'Merch\Orders\OrderController@orderCopyStore');
Route::get('merch/orders/purchase_order/{order_id}', 'Merch\Orders\OrderController@poEntry');
Route::get('merch/orders/sub_style_generate', 'Merch\Orders\OrderController@generateSubStyle');
Route::post('merch/orders/purchase_order_store', 'Merch\Orders\OrderController@poStore');
Route::get('merch/orders/purchase_order_delete/{order_id}/{po_id}', 'Merch\Orders\OrderController@poDelete');


Route::get('merch/orders/get_size_list', 'Merch\Orders\OrderController@getSizeList');
Route::post('merch/orders/order_po_store', 'Merch\Orders\OrderController@poStoreWithBreakdown');
Route::get('merch/orders/get_selected_colors', 'Merch\Orders\OrderController@getSelectedColors');

Route::get('merch/orders/po/{order_id}/{po_id}/delete', 'Merch\Orders\OrderController@deletePO');

Route::get('merch/orders/get_po_edit_options', 'Merch\Orders\OrderController@getPoEditOptions');
//
/*
*--------------------------------------------------------------
* Time and Action
*--------------------------------------------------------------
*/





// TNA Order
Route::get('merch/time_action/tna_order_list',
	'Merch\TimeAction\TnaOrderController@tnaOrderList');
Route::post('merch/time_action/tna_order_list_data',
	'Merch\TimeAction\TnaOrderController@tnaOrderListData');
Route::get('merch/time_action/tna_order', 'Merch\TimeAction\TnaOrderController@orderForm');
Route::get('merch/orders/po_edit_modal_data', 'Merch\Orders\OrderController@poEdit');
Route::get('merch/time_action/tna_order_edit/{id}',
	'Merch\TimeAction\TnaOrderController@tnaOrderEdit');
Route::post('merch/time_action/tna_order_update',
	'Merch\TimeAction\TnaOrderController@tnaOrderUpdate');
Route::get('merch/time_action/tna_order_delete/{id}',
	'Merch\TimeAction\TnaOrderController@tnaOrderDelete');
//TNA Status
Route::get('merch/time_action/tna_status', 'Merch\TimeAction\TnaStatusController@tnaShowStatus');
//
Route::post('merch/orders/purchase_order_update', 'Merch\Orders\OrderController@poUpdate');
Route::get('merch/time_action/tna_generate1', 'Merch\TimeAction\TnaOrderController@tnaGenerate1');
Route::get('merch/time_action/tna_generate', 'Merch\TimeAction\TnaOrderController@tnaGenerate');
Route::get('merch/time_action/templates_list', 'Merch\TimeAction\TnaOrderController@templatesList');


Route::post('merch/time_action/tna_generate_store', 'Merch\TimeAction\TnaOrderController@tnaGenerateStore');

//Proforma Invoice
Route::get('merch/proforma_invoice/', 'Merch\ProformaInvoice\ProformaInvoiceController@showList');
Route::get('merch/proforma_invoice/form', 'Merch\ProformaInvoice\ProformaInvoiceController@showForm');
Route::get('merch/proforma_invoice/getbookinglist', 'Merch\ProformaInvoice\ProformaInvoiceController@getBookingList');
Route::get('merch/proforma_invoice/getbookingitem', 'Merch\ProformaInvoice\ProformaInvoiceController@getBookingItem');
Route::get('merch/proforma_invoice/edit/{id}', 'Merch\ProformaInvoice\ProformaInvoiceController@edit');
Route::get('merch/proforma_invoice/delete/{id}', 'Merch\ProformaInvoice\ProformaInvoiceController@delete');
Route::get('merch/proforma_invoice/view/{id}', 'Merch\ProformaInvoice\ProformaInvoiceController@view');
Route::post('merch/proforma_invoice/update', 'Merch\ProformaInvoice\ProformaInvoiceController@update');
Route::post('merch/proforma_invoice/store', 'Merch\ProformaInvoice\ProformaInvoiceController@store');
Route::get('merch/proforma_invoice/getPIListData', 'Merch\ProformaInvoice\ProformaInvoiceController@getPIListData');
Route::post('merch/proforma-invoice/check-pi-no', 'Merch\ProformaInvoice\ProformaInvoiceController@checkPi');

//Costing History
Route::get('merch/costing-compare/', 'Merch\Costing\CostingController@showList');
Route::get('merch/costing-compare/{id}', 'Merch\Costing\CostingController@orderWiseCosting');
Route::post('merch/costing/list-data', 'Merch\Costing\CostingController@listData');


#-------------------------------------------------------------#
Route::get("merch/order_bom", "Merch\OrderBOM\OrderBomController@showList");
Route::post("merch/order_bom_data", "Merch\OrderBOM\OrderBomController@getListData");
Route::get("merch/order_bom/{id}/create", "Merch\OrderBOM\OrderBomController@showForm");
Route::get("merch/order_bom/{id}/create/{po_id}", "Merch\OrderBOM\OrderBomController@showForm");
Route::get("merch/order_bom/{id}/preview", "Merch\OrderBOM\OrderBomController@previewOrder");
Route::get("merch/order_bom/get_item_data", "Merch\OrderBOM\OrderBomController@getItemData");
Route::post("merch/order_bom/{id}/store", "Merch\OrderBOM\OrderBomController@storeData");
Route::post("merch/order_bom/{id}/store/{po_id}", "Merch\OrderBOM\OrderBomController@storeData");
Route::get("merch/order_bom/get_article_by_supplier", "Merch\OrderBOM\OrderBomController@article");


#-------------------------------------------------------------#
Route::get("merch/order_costing", "Merch\OrderCosting\OrderCostingController@showList");
Route::post("merch/order_costing_data", "Merch\OrderCosting\OrderCostingController@getListData");
Route::get("merch/order_costing/{id}/create", "Merch\OrderCosting\OrderCostingController@showForm");
Route::get("merch/order_costing/{id}/create/{po_id}", "Merch\OrderCosting\OrderCostingController@showForm");
Route::post("merch/order_costing/{id}/create", "Merch\OrderCosting\OrderCostingController@store");
Route::get("merch/order_costing/{id}/edit", "Merch\OrderCosting\OrderCostingController@editForm");
Route::get("merch/order_costing/{id}/print", "Merch\OrderCosting\OrderCostingController@editFormPrint");
Route::post("merch/order_costing/{id}/edit", "Merch\OrderCosting\OrderCostingController@update");


#-------------------------------------------------------------#
Route::get("merch/order_booking", "Merch\OrderBooking\OrderBookingController@showList");
Route::post("merch/order_booking_data", "Merch\OrderBooking\OrderBookingController@getListData");
Route::get("merch/order_booking/{id}/create", "Merch\OrderBooking\OrderBookingController@showForm");
Route::post("merch/order_booking/{id}/create", "Merch\OrderBooking\OrderBookingController@store");
Route::get("merch/order_booking/{id}/edit", "Merch\OrderBooking\OrderBookingController@editForm");


#-------------------------------------------------------------#
Route::get("merch/order_po_booking", "Merch\OrderBooking\OrderPoBookingController@showList");
Route::get("merch/order_po_booking/getSupOrderList", "Merch\OrderBooking\OrderPoBookingController@getSupOrderList");
Route::get("merch/order_po_booking/showForm", "Merch\OrderBooking\OrderPoBookingController@showForm");
Route::get("merch/order_po_booking/getPoOrderItem", "Merch\OrderBooking\OrderPoBookingController@getPoOrderItem");
Route::post("merch/order_po_booking/store", "Merch\OrderBooking\OrderPoBookingController@store");
Route::post("merch/order_po_booking/update", "Merch\OrderBooking\OrderPoBookingController@update");
Route::get("merch/order_po_booking/getPoOrderInfo", "Merch\OrderBooking\OrderPoBookingController@getPoOrderInfo");
Route::get("merch/order_po_booking/getPoOrderListData", "Merch\OrderBooking\OrderPoBookingController@getPoOrderListData");
Route::get("merch/order_po_booking/edit/{poBookingId}", "Merch\OrderBooking\OrderPoBookingController@poBookingEdit");
Route::get("merch/order_po_booking/confirm/{poBookingId}", "Merch\OrderBooking\OrderPoBookingController@confirm");
Route::post("merch/order_po_booking/confirm_store/{poBookingId}", "Merch\OrderBooking\OrderPoBookingController@confirmStore");

Route::get('merch/order_profile','Merch\OrderProfile\ProfileController@index');
Route::get('merch/orders/order_profile_data','Merch\OrderProfile\ProfileController@orderProfileData');
Route::get('merch/orders/order_profile_show/{id}','Merch\OrderProfile\ProfileController@orderProfileShow');
Route::get('merch/orders/order_profile_pdf/{id}','Merch\OrderProfile\ProfileController@orderProfilePdf');

/*-------------------order color and size breakdown-------------*/
Route::get('merch/order_breakdown','Merch\OrderBreakdown\OrderBreakDownController@index');
Route::get('merch/getOrder','Merch\OrderBreakdown\OrderBreakDownController@orderData');
Route::get('merch/order_breakdown/show/{id}','Merch\OrderBreakdown\OrderBreakDownController@show');
Route::post('merch/order_breakdown_store','Merch\OrderBreakdown\OrderBreakDownController@store');
Route::get('merch/order_breakdown_edit/{id}','Merch\OrderBreakdown\OrderBreakDownController@edit');
Route::post('merch/order_breakdown_update','Merch\OrderBreakdown\OrderBreakDownController@update');

Route::get('merch/order_booking_edit/{id}','Merch\OrderBooking\OrderBookingController@edit');
Route::post('merch/order_booking_update','Merch\OrderBooking\OrderBookingController@update');

// order bom details
Route::resource('order-bom-details', "Merch\OrderBOM\OrderBomDetailsController");
Route::get('merch/order_bom/single-order-details-info', "Merch\OrderBOM\OrderBomDetailsController@orderIdItemIdWise");
Route::get('merch/order_bom/item-wise-size-group', "Merch\OrderBOM\OrderBomDetailsController@itemWiseSizeGroup");
Route::get('merch/order_bom/item-wise-placement', "Merch\OrderBOM\OrderBomDetailsController@itemWisePlacement");

//MERCHANDISING reporting
Route::get('merch/report/report_view', "Merch\Report\ReportController@getReport");


//Query
Route::group(['prefix' => 'merch/query/','namespace' => 'Merch\Query'], function(){

	Route::get('/', 'QueryController@merchQuery');
	//order
	Route::get('merch_order_query', 'OrderQueryController@merchOrderQuery');
	Route::get('merch_order_query_unit', 'OrderQueryController@merchOrderQueryUnit');
	Route::get('merch_order_query_buyer', 'OrderQueryController@merchOrderQueryBuyer');
	Route::get('merch_order_query_order', 'OrderQueryController@merchOrderQueryOrder');
	Route::get('merch_order_query_listorder', 'OrderQueryController@merchOrderQueryListOrder');
	//style
	Route::get('merch_style_query', 'StyleQueryController@merchStyleQuery');
	Route::get('merch_style_query_buyer', 'StyleQueryController@merchStyleQueryBuyer');
	Route::get('merch_style_query_style', 'StyleQueryController@merchStyleQueryStyle');
	Route::get('merch_style_query_liststyle', 'StyleQueryController@merchStyleQueryListStyle');
	//reservation
	Route::get('merch_resv_query', 'ResvQueryController@merchResvQuery');
	Route::get('merch_resv_query_unit', 'ResvQueryController@merchResvQueryUnit');
	Route::get('merch_resv_query_buyer', 'ResvQueryController@merchResvQueryBuyer');
	Route::get('merch_resv_query_resv', 'ResvQueryController@merchResvQueryResv');
	Route::get('merch_resv_query_listresv', 'ResvQueryController@merchResvQueryListResv');

	//purchase order
	Route::get('merch_po_query', 'PoQueryController@merchPoQuery');
	Route::get('merch_po_query_country', 'PoQueryController@merchPoQueryCountry');
	Route::get('merch_po_query_po', 'PoQueryController@merchPoQueryPo');
	Route::get('merch_po_query_listpo', 'PoQueryController@merchPoQueryListPo');

	//order by team
	Route::get('merch_team_query', 'TeamQueryController@merchTeamQuery');
	Route::get('merch_team_query_unit', 'TeamQueryController@merchTeamQueryUnit');
	Route::get('merch_team_query_team', 'TeamQueryController@merchTeamQueryTeam');
	Route::get('merch_team_query_executive', 'TeamQueryController@merchTeamQueryExecutive');
	Route::get('merch_team_query_order', 'TeamQueryController@merchTeamQueryOrder');
	Route::get('merch_team_query_listorder', 'TeamQueryController@merchTeamQueryListOrder');

	//pi
	Route::get('merch_pi_query', 'PiQueryController@merchPiQuery');
	Route::get('merch_pi_query_unit', 'PiQueryController@merchPiQueryUnit');
	Route::get('merch_pi_query_buyer', 'PiQueryController@merchPiQueryBuyer');
	Route::get('merch_pi_query_supplier', 'PiQueryController@merchPiQuerySupplier');
	Route::get('merch_pi_query_pi', 'PiQueryController@merchPiQueryPi');
	Route::get('merch_pi_query_listpi', 'PiQueryController@merchPiQueryListPi');

	//order booking
	Route::get('merch_ob_query', 'BookingQueryController@merchBookingQuery');
	Route::get('merch_ob_query_unit', 'BookingQueryController@merchBookingQueryUnit');
	Route::get('merch_ob_query_buyer', 'BookingQueryController@merchBookingQueryBuyer');
	Route::get('merch_ob_query_supplier', 'BookingQueryController@merchBookingQuerySupplier');
	Route::get('merch_ob_query_ob', 'BookingQueryController@merchBookingQueryBooking');
	Route::get('merch_ob_query_listob', 'BookingQueryController@merchBookingQueryListBooking');


});




Route::get('merch/report/report_view/tna_due_report', "Merch\Report\TimeActionDueReportController@tnaDueViewReport");
Route::get('merch/report/tna_report_ajax_call', "Merch\Report\TimeActionDueReportController@tnaDueReportResult");
});



//sales contract merch

Route::get('merch/sales_contract/sales_contract_entry', 'Merch\Salescontract\SalesContractController@entryForm');
Route::get('merch/sales_contract/getcontractlist', 'Merch\Salescontract\SalesContractController@getContractList');
Route::post('merch/sales_contract/sales_contract_store', 'Merch\Salescontract\SalesContractController@salesStore');
Route::get('merch/sales_contract/getorderlist', 'Merch\Salescontract\SalesContractController@getOrderList');
Route::get('merch/sales_contract/getorderlist_for_amend', 'Merch\Salescontract\SalesContractController@getOrderListForAmendment');
Route::get('merch/sales_contract/sales_contract_list', 'Merch\Salescontract\SalesContractController@salesContractList');
Route::get('merch/sales_contract/sales_contract_get_data', 'Merch\Salescontract\SalesContractController@getData');
Route::get('merch/sales_contract/sales_contract_edit/{id}', 'Merch\Salescontract\SalesContractController@edit');
Route::post('merch/sales_contract/sales_contract_update', 'Merch\Salescontract\SalesContractController@salesUpdate');

Route::get('merch/sales_contract/sales_contract_delete/{id}','Merch\Salescontract\SalesContractController@salesDelete');

Route::get('merch/sales_contract/amendment/{id}','Merch\Salescontract\SalesContractController@amendmentForm');
Route::post('merch/sales_contract/amendment','Merch\Salescontract\SalesContractController@amendmentStore');


//pi to file
Route::get('merch/pi_to_file','Merch\PiToFile\PiToFileController@showForm');
Route::get('merch/pi_to_file/pi_bom_info','Merch\PiToFile\PiToFileController@piBomInfo');
Route::post('merch/pi_to_file/update','Merch\PiToFile\PiToFileController@updatePiToFile');



//PO BOM .............
Route::get('merch/orders/po_bom/{po_id}/{order_id}/{clr_id}/view', 'Merch\POOrderBOM\POOrderBOMController@showFormPOBOM');
Route::post('merch/po_bom/store', 'Merch\POOrderBOM\POOrderBOMController@storePOData');
Route::get('merch/orders/po_bom_list', 'Merch\POOrderBOM\POOrderBOMController@poBOMList');
Route::post('merch/orders/po_bom_list_data', 'Merch\POOrderBOM\POOrderBOMController@poBOMListData');
//PO BOM Costing
// Route::get("merch/orders/po_costing/{po_id}/{order_id}/create", "Merch\POOrderBOM\POOrderCostingController@");
Route::get("merch/orders/po_costing/{po_id}/{order_id}/{clr_id}/edit", "Merch\POOrderBOM\POOrderCostingController@editForm");
Route::post("merch/orders/po_costing/store", "Merch\POOrderBOM\POOrderCostingController@updatePOCosting");
