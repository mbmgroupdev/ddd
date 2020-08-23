<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Console\Commands\MySqlBackUp;
use Illuminate\Support\Carbon;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use League\Flysystem\File;
use Artisan;
use Log;
use Response, DB;
use Storage;

class DatabaseBackupController extends Controller
{
    public function index(){

    	   $filesInFolder = [];
    	   $filesInFolder = \File::files('databasebackup');     
		   // dd(sizeof($filesInFolder));
		   // $filesInFolder = array_reverse($filesInFolder);
		   $file_count = sizeof($filesInFolder);
    	return view('hr.zz_db_backup.database_backup', compact('filesInFolder', 'file_count'));
    }

    public function create(Request $request){
            // dd($request->all());
    		
        try {
        	// Artisan::call('mysql:backup');
            
			$username = \Config::get('database.connections.mysql.username');
	        $password = \Config::get('database.connections.mysql.password');
	        $dbname = \Config::get('database.connections.mysql.database');

	        // dd($password); exit;

	       $filename = 'databasebackup/'.$dbname .'_from_'.$request->from_date."_to_".$request->to_date."_on_date_". date('Y-m-d__h_i_s_a') . '.sql';
	        // dd($filename);

	        //With Pawwsord...
	        // $command = "mysqldump --no-create-info -u ".$username." -p ".$password." ".$dbname." --tables --where=\"created_at BETWEEN "."'".$request->from_date." 00:00:00' and "."'".$request->to_date." 23:59:59'\" > " . $filename;
	       
	        //Without Pawwsord...
	       // $command = "mysqldump --no-create-info -u ".$username." ".$dbname." --tables --where=\"created_at BETWEEN "."'".$request->from_date." 00:00:00' and "."'".$request->to_date." 23:59:59'\" > " . $filename;

	       if($password == ""){
	       	$load = "mysqldump --no-create-info -u ".$username." ".$dbname;
	       }
	       else{
	       	$load = "mysqldump --no-create-info -u ".$username." -p".$password."  ".$dbname;
	       }
	       // dd($load);exit;

	       // $command = "mysqldump --no-create-info -u ".$username." -p ".$password."  ".$dbname.
	       // $command = "mysqldump --no-create-info -u ".$username." ".$dbname.
	       		$command = $load.
					       	    " --ignore-table=".$dbname.".cm_agent".
								" --ignore-table=".$dbname.".cm_bank".
								" --ignore-table=".$dbname.".cm_bank_acc_no".
								" --ignore-table=".$dbname.".cm_btb".
								" --ignore-table=".$dbname.".cm_btb_asset".
								" --ignore-table=".$dbname.".cm_btb_asset_amend".
								" --ignore-table=".$dbname.".cm_category_no".
								" --ignore-table=".$dbname.".cm_file".
								" --ignore-table=".$dbname.".cm_from_date".
								" --ignore-table=".$dbname.".cm_hub".
								" --ignore-table=".$dbname.".cm_imp_invoice".
								" --ignore-table=".$dbname.".cm_inco_term".
								" --ignore-table=".$dbname.".cm_insurance".
								" --ignore-table=".$dbname.".cm_item".
								" --ignore-table=".$dbname.".cm_lc_period".
								" --ignore-table=".$dbname.".cm_lc_type".
								" --ignore-table=".$dbname.".cm_machine_inspection".
								" --ignore-table=".$dbname.".cm_machine_manufacturer".
								" --ignore-table=".$dbname.".cm_machine_type".
								" --ignore-table=".$dbname.".cm_passbook_volume".
								" --ignore-table=".$dbname.".cm_payment_type".
								" --ignore-table=".$dbname.".cm_period".
								" --ignore-table=".$dbname.".cm_pi_type".
								" --ignore-table=".$dbname.".cm_port".
								" --ignore-table=".$dbname.".cm_prc_correction".
								" --ignore-table=".$dbname.".cm_section".
								" --ignore-table=".$dbname.".cm_vessel".
								" --ignore-table=".$dbname.".cm_voyage_vessel".
								" --ignore-table=".$dbname.".com_pi_type".
								" --ignore-table=".$dbname.".conversation".
								" --ignore-table=".$dbname.".fin_asset".
								" --ignore-table=".$dbname.".fin_asset_category".
								" --ignore-table=".$dbname.".fin_asset_product".
								" --ignore-table=".$dbname.".hr_absent".
								" --ignore-table=".$dbname.".hr_area".
								" --ignore-table=".$dbname.".hr_as_basic_info".
								" --ignore-table=".$dbname.".hr_benefits".
								" --ignore-table=".$dbname.".hr_bonus_type".
								" --ignore-table=".$dbname.".hr_cost_mapping_area".
								" --ignore-table=".$dbname.".hr_cost_mapping_department".
								" --ignore-table=".$dbname.".hr_cost_mapping_floor".
								" --ignore-table=".$dbname.".hr_cost_mapping_line".
								" --ignore-table=".$dbname.".hr_cost_mapping_section".
								" --ignore-table=".$dbname.".hr_cost_mapping_sub_section".
								" --ignore-table=".$dbname.".hr_cost_mapping_unit".
								" --ignore-table=".$dbname.".hr_department".
								" --ignore-table=".$dbname.".hr_designation".
								" --ignore-table=".$dbname.".hr_designation_update_log".
								" --ignore-table=".$dbname.".hr_dis_rec".
								" --ignore-table=".$dbname.".hr_dist".
								" --ignore-table=".$dbname.".hr_education".
								" --ignore-table=".$dbname.".hr_education_degree_title".
								" --ignore-table=".$dbname.".hr_employee_bengali".
								" --ignore-table=".$dbname.".hr_events".
								" --ignore-table=".$dbname.".hr_floor".
								" --ignore-table=".$dbname.".hr_grievance_issue".
								" --ignore-table=".$dbname.".hr_grievance_steps".
								" --ignore-table=".$dbname.".hr_increment_type".
								" --ignore-table=".$dbname.".hr_interview".
								" --ignore-table=".$dbname.".hr_job_application".
								" --ignore-table=".$dbname.".hr_job_posting".
								" --ignore-table=".$dbname.".hr_late_count".
								" --ignore-table=".$dbname.".hr_late_count_customizes".
								" --ignore-table=".$dbname.".hr_letter".
								" --ignore-table=".$dbname.".hr_line".
								" --ignore-table=".$dbname.".hr_loan_application".
								" --ignore-table=".$dbname.".hr_loan_type".
								" --ignore-table=".$dbname.".hr_location".
								" --ignore-table=".$dbname.".hr_ot".
								" --ignore-table=".$dbname.".hr_other_benefit_item".
								" --ignore-table=".$dbname.".hr_outside".
								" --ignore-table=".$dbname.".hr_salary_structure".
								" --ignore-table=".$dbname.".hr_section".
								" --ignore-table=".$dbname.".hr_service_book".
								" --ignore-table=".$dbname.".hr_shift".
								" --ignore-table=".$dbname.".hr_station".
								" --ignore-table=".$dbname.".hr_subsection".
								" --ignore-table=".$dbname.".hr_unit".
								" --ignore-table=".$dbname.".hr_upazilla".
								" --ignore-table=".$dbname.".hr_without_pay".
								" --ignore-table=".$dbname.".migrations".
								" --ignore-table=".$dbname.".model_has_permissions".
								" --ignore-table=".$dbname.".model_has_roles".
								" --ignore-table=".$dbname.".mr_action_type".
								" --ignore-table=".$dbname.".mr_approval_hirarchy".
								" --ignore-table=".$dbname.".mr_article".
								" --ignore-table=".$dbname.".mr_brand".
								" --ignore-table=".$dbname.".mr_brand_contact".
								" --ignore-table=".$dbname.".mr_buyer".
								" --ignore-table=".$dbname.".mr_buyer_contact".
								" --ignore-table=".$dbname.".mr_capacity_reservation".
								" --ignore-table=".$dbname.".mr_cat_item".
								" --ignore-table=".$dbname.".mr_cat_item_uom".
								" --ignore-table=".$dbname.".mr_composition".
								" --ignore-table=".$dbname.".mr_construction".
								" --ignore-table=".$dbname.".mr_country".
								" --ignore-table=".$dbname.".mr_element".
								" --ignore-table=".$dbname.".mr_excecutive_team".
								" --ignore-table=".$dbname.".mr_excecutive_team_members".
								" --ignore-table=".$dbname.".mr_garment_type".
								" --ignore-table=".$dbname.".mr_operation".
								" --ignore-table=".$dbname.".mr_prdz_size_pallete".
								" --ignore-table=".$dbname.".mr_product_lib".
								" --ignore-table=".$dbname.".mr_product_lib_operarion".
								" --ignore-table=".$dbname.".mr_product_lib_sp_machine".
								" --ignore-table=".$dbname.".mr_product_size".
								" --ignore-table=".$dbname.".mr_product_size_group".
								" --ignore-table=".$dbname.".mr_product_type".
								" --ignore-table=".$dbname.".mr_sample_style".
								" --ignore-table=".$dbname.".mr_sample_type".
								" --ignore-table=".$dbname.".mr_season".
								" --ignore-table=".$dbname.".mr_size_pallete".
								" --ignore-table=".$dbname.".mr_special_machine".
								" --ignore-table=".$dbname.".mr_stl_sample".
								" --ignore-table=".$dbname.".mr_stl_size_group".
								" --ignore-table=".$dbname.".mr_stl_wash_type".
								" --ignore-table=".$dbname.".mr_style_image".
								" --ignore-table=".$dbname.".mr_style".
								" --ignore-table=".$dbname.".mr_style_type".
								" --ignore-table=".$dbname.".mr_sup_contact_person_info".
								" --ignore-table=".$dbname.".mr_supplier".
								" --ignore-table=".$dbname.".mr_supplier_item_type".
								" --ignore-table=".$dbname.".mr_tna_library".
								" --ignore-table=".$dbname.".mr_tna_template".
								" --ignore-table=".$dbname.".mr_tna_template_to_library".
								" --ignore-table=".$dbname.".mr_wash_category".
								" --ignore-table=".$dbname.".mr_wash_type".
								" --ignore-table=".$dbname.".password_resets".
								" --ignore-table=".$dbname.".permissions".
								" --ignore-table=".$dbname.".role_has_permissions".
								" --ignore-table=".$dbname.".roles".
								" --ignore-table=".$dbname.".st_accessories_check_points".
								" --ignore-table=".$dbname.".st_asset_issue".
								" --ignore-table=".$dbname.".st_asset_issue_item".
								" --ignore-table=".$dbname.".st_asset_item".
								" --ignore-table=".$dbname.".st_inventory_entry".
								" --ignore-table=".$dbname.".st_inventory_item_cell".
								" --ignore-table=".$dbname.".st_material_type".
								" --ignore-table=".$dbname.".st_rm_issue".
								" --ignore-table=".$dbname.".st_rm_issue_item".
								" --ignore-table=".$dbname.".st_rm_requisition".
								" --ignore-table=".$dbname.".st_rm_requisition_by_production".
								" --ignore-table=".$dbname.".st_rm_requisition_by_substore".
								" --ignore-table=".$dbname.".st_rm_requisition_item".
								" --ignore-table=".$dbname.".st_rm_requisition_production_item".
								" --ignore-table=".$dbname.".st_rm_requisition_substore_item".
								" --ignore-table=".$dbname.".st_rm_return".
								" --ignore-table=".$dbname.".st_warehouse".
								" --ignore-table=".$dbname.".st_warehouse_rack".
								" --ignore-table=".$dbname.".st_warehouse_rack_row".
								" --ignore-table=".$dbname.".st_warehouse_rack_row_colmn".
								" --ignore-table=".$dbname.".st_warehouse_rack_row_column".
								" --ignore-table=".$dbname.".uom".
								" --ignore-table=".$dbname.".user_logs".
								" --ignore-table=".$dbname.".users".
								" --ignore-table=".$dbname.".users_login_activities".

	       	   " --where=\"created_at BETWEEN "."'".$request->from_date." 00:00:00' and "."'".$request->to_date." 23:59:59'\" > " . $filename;

	        
	         // dd($command);
	        // shell_exec($command);
	        
	        //--full backup command------------------------------------------------------------------//
		        // $filename = 'databasebackup/'.$dbname .'_backup_on_'. date('Y-m-d__h-i-s_a') . '.sql';
		        // $command = 'mysqldump -u ' .$username.' '.$dbname. ' > ' . $filename;
		        // dd($command);
	        //--full backup command------------------------------------------------------------------//
	       	$rows_delete_file_name = "'".$this->fixBackslashofPath()."/databasebackup/temp/delete_query.sql'";
	       	// dd($rows_delete_file_name);exit;
	        $delete_query_generate = 	"SET @DatabaseName = \"".$dbname."\";\n". 
										"SET @ColumnName2= \"created_at BETWEEN "."'".$request->from_date." 00:00:00' and "."'".$request->to_date." 23:59:59'\";\n".
										"SELECT CONCAT('DELETE FROM ', TABLE_NAME,' WHERE ',@ColumnName2,';')\n".
										"AS ddl ".
										"INTO OUTFILE ".$rows_delete_file_name."\n".
										"FROM information_schema.TABLES\n".
										"WHERE TABLE_SCHEMA = @DatabaseName\n".
										"AND TABLE_NAME NOT IN (
												'cm_agent',
												'cm_bank',
												'cm_bank_acc_no',
												'cm_btb',
												'cm_btb_asset',
												'cm_btb_asset_amend',
												'cm_category_no',
												'cm_file',
												'cm_from_date',
												'cm_hub',
												'cm_imp_invoice',
												'cm_inco_term',
												'cm_insurance',
												'cm_item',
												'cm_lc_period',
												'cm_lc_type',
												'cm_machine_inspection',
												'cm_machine_manufacturer',
												'cm_machine_type',
												'cm_passbook_volume',
												'cm_payment_type',
												'cm_period',
												'cm_pi_type',
												'cm_port',
												'cm_prc_correction',
												'cm_section',
												'cm_vessel',
												'cm_voyage_vessel',
												'com_pi_type',
												'conversation',
												'fin_asset',
												'fin_asset_category',
												'fin_asset_product',
												'hr_absent',
												'hr_area',
												'hr_as_basic_info',
												'hr_benefits',
												'hr_bonus_type',
												'hr_cost_mapping_area',
												'hr_cost_mapping_department',
												'hr_cost_mapping_floor',
												'hr_cost_mapping_line',
												'hr_cost_mapping_section',
												'hr_cost_mapping_sub_section',
												'hr_cost_mapping_unit',
												'hr_department',
												'hr_designation',
												'hr_designation_update_log',
												'hr_dis_rec',
												'hr_dist',
												'hr_education',
												'hr_education_degree_title',
												'hr_employee_bengali',
												'hr_events',
												'hr_floor',
												'hr_grievance_issue',
												'hr_grievance_steps',
												'hr_increment_type',
												'hr_interview',
												'hr_job_application',
												'hr_job_posting',
												'hr_late_count',
												'hr_late_count_customizes',
												'hr_letter',
												'hr_line',
												'hr_loan_application',
												'hr_loan_type',
												'hr_location',
												'hr_ot',
												'hr_other_benefit_item',
												'hr_outside',
												'hr_salary_structure',
												'hr_section',
												'hr_service_book',
												'hr_shift',
												'hr_station',
												'hr_subsection',
												'hr_unit',
												'hr_upazilla',
												'hr_without_pay',
												'migrations',
												'model_has_permissions',
												'model_has_roles',
												'mr_action_type',
												'mr_approval_hirarchy',
												'mr_article',
												'mr_brand',
												'mr_brand_contact',
												'mr_buyer',
												'mr_buyer_contact',
												'mr_capacity_reservation',
												'mr_cat_item',
												'mr_cat_item_uom',
												'mr_composition',
												'mr_construction',
												'mr_country',
												'mr_element',
												'mr_excecutive_team',
												'mr_excecutive_team_members',
												'mr_garment_type',
												'mr_operation',
												'mr_prdz_size_pallete',
												'mr_product_lib',
												'mr_product_lib_operarion',
												'mr_product_lib_sp_machine',
												'mr_product_size',
												'mr_product_size_group',
												'mr_product_type',
												'mr_sample_style',
												'mr_sample_type',
												'mr_season',
												'mr_size_pallete',
												'mr_special_machine',
												'mr_stl_sample',
												'mr_stl_size_group',
												'mr_stl_wash_type',
												'mr_style_image',
												'mr_style',
												'mr_style_type',
												'mr_sup_contact_person_info',
												'mr_supplier',
												'mr_supplier_item_type',
												'mr_tna_library',
												'mr_tna_template',
												'mr_tna_template_to_library',
												'mr_wash_category',
												'mr_wash_type',
												'password_resets',
												'permissions',
												'role_has_permissions',
												'roles',
												'st_accessories_check_points',
												'st_asset_issue',
												'st_asset_issue_item',
												'st_asset_item',
												'st_inventory_entry',
												'st_inventory_item_cell',
												'st_material_type',
												'st_rm_issue',
												'st_rm_issue_item',
												'st_rm_requisition',
												'st_rm_requisition_by_production',
												'st_rm_requisition_by_substore',
												'st_rm_requisition_item',
												'st_rm_requisition_production_item',
												'st_rm_requisition_substore_item',
												'st_rm_return',
												'st_warehouse',
												'st_warehouse_rack',
												'st_warehouse_rack_row',
												'st_warehouse_rack_row_colmn',
												'st_warehouse_rack_row_column',
												'uom',
												'user_logs',
												'users',
												'users_login_activities'

												);";
			// dd($delete_query_generate);
			// $ex_name = "_from_".$request->from_date."_to_".$request->to_date."_on_date_". date('Y-m-d__h_i_s_a');							
			$ex_name = "_from_".$request->from_date."_to_".$request->to_date;							
	        Storage::disk('public')->put('databasebackup/deletequery/offload'.$ex_name.'.sql', $delete_query_generate );

	        //Process that will execute the command
	        $process = new Process($command);
	        // dd($process);
	        try {
			   $process->mustRun();
			    // dd("OK",$process->getOutput());
			} catch (ProcessFailedException $exception) {
			    // dd("Error",$exception->getMessage());
			    return Response::json($exception->getMessage());
			}

            return Response::json(1);
        
        } catch (Exception $e) {
            return Response::json($e->getMessage());
        }
    }

    //Download--------------
    public function getDownload($filename){
		// dd($filename);
	    $file = public_path(). "/databasebackup/".$filename;
	    // dd($file);
	    return Response::download($file);
	}

	//Delete a file
	public function deleteFile($filename){

		//for the insert row query file delete.............................................
		$file = public_path(). "/databasebackup/".$filename;
		//----------------------------------------------------------------------------------
		

		//for the delete_query generation sql file..........................................
		$st_pos 	= strpos($filename, 'from_');
		$plus_char 	= 29;
		$from_to 	= substr($filename, $st_pos, $plus_char);
		$find_file 	= "offload_".$from_to.".sql";
		$hasFile = $this->fixBackslashofPath().'/databasebackup/deletequery/'.$find_file;
		//-----------------------------------------------------------------------------------
		// dd($file);
 		// dd(file_exists($file));

 		//deleting the insert query file...
 		if(file_exists($file)){
	      unlink($file);
	    }
	    //deleting the delete_query generation sql file...
		if(file_exists($hasFile)){
	      unlink($hasFile);
	    }

 		return back()->with('success', 'File Deleted');
	}


	//Replacing the bslash with slash on public_path()
	public function fixBackslashofPath(){
		$path_with_slash = public_path();
		for ($i=0; $i < strlen($path_with_slash); $i++) { 
			if($path_with_slash[$i] == '\\'){
				// echo $path_with_bslash[$i];
				$path_with_slash[$i] = '/';
			}
		}
		return $path_with_slash;
	}


	//Offload the data
	public function offloadData($file_name){
		$st_pos 	= strpos($file_name, 'from_');
		$plus_char 	= 29;
		$from_to 	= substr($file_name, $st_pos, $plus_char);
		$find_file 	= "offload_".$from_to.".sql";

		// dd(public_path());
		

		// echo $path_with_slash;
		$hasFile = $this->fixBackslashofPath().'/databasebackup/deletequery/'.$find_file;
		// dd($hasFile);

		if(file_exists($hasFile)){
			// dd($hasFile);
			$command = "mysql -u root new_mbm < ".$hasFile;
			// dd($command);
			$process = new Process($command);
	        // dd($process);
	        try {
			   $process->mustRun();
			    // dd("OK",$process->getOutput());
			} catch (ProcessFailedException $exception) {
			    // dd("Error",$exception->getMessage());
			    return back()->with('error', $exception->getMessage());
			}

			$tempFile = $this->fixBackslashofPath()."/databasebackup/temp/delete_query.sql";
			if(file_exists($tempFile)){
				// dd($tempFile);
				$delete_row_command = "mysql -u root new_mbm < ".$tempFile;
				// dd($delete_row_command);
				$process = new Process($delete_row_command);
				try{
					$process->mustRun();
				}catch(ProcessFailedException $exception){
					return back()->with('error', $exception->getMessage());
				}

				unlink($tempFile);
			}
			else{
				return back()->with('error', "Delete SQL file has not created yet");	
			}
			

			return back()->with('success', "Data has been offloaded");

		}
		else{
			return back()->with('error', "Delete Query Generator file missing");
		}

		// $filesInFolder = \File::files('databasebackup/deletequery');
		// dd($file_name ,$st_pos, $from_to, $find_file, $filesInFolder);

		// for($i=0; $i<sizeof($filesInFolder); $i++) {
		// 	// echo pathinfo($filesInFolder[$i])['filename'];
		// 	$the_file = pathinfo($filesInFolder[$i])['filename'];
		// 	echo $the_file;
		// 	// if($the_file == $find_file){

		// 	// 	dd($file_name ,$st_pos, $from_to, $find_file, $filesInFolder);
		// 	// }
		// }




	}

	//Load the data
	public function loadData($file_name){
		$hasFile = $this->fixBackslashofPath().'/databasebackup/'.$file_name;
		$command = "mysql -u root new_mbm < ".$hasFile;
		$process = new Process($command);
				try{
					$process->mustRun();
				}catch(ProcessFailedException $exception){
					return back()->with('error', $exception->getMessage());
				}
		return back()->with('success', 'Data is loaded');
	}
}
