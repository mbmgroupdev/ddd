<?php

namespace App\Http\Controllers\Hr\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class BuyerModeController extends Controller
{

	protected $atttable = [
		['name' => 'field_1', 'type' => 'string', 'null' => 1, 'default' => null],
		['name' => 'field_2', 'type' => 'string', 'null' => null, 'deafult' => 0]
	];

    /**
     * Create dynamic table along with dynamic fields
     *
     * @param       $table_name
     * @param array $fields
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTable($table_name, $fields = [])
    {
        // check if table is not already exists
        if (!Schema::hasTable($table_name)) {
            Schema::create($table_name, function (Blueprint $table) use ($fields, $table_name) {
                $table->increments('id');
                if (count($fields) > 0) {
                    foreach ($fields as $field) {
                    	if($field['null'] == 1 ){
	                        $table->{$field['type']}($field['name'])->nullable();	
                    	}else{
                    		$table->{$field['type']}($field['name']);
                    	}
                    }
                }
                $table->timestamps();
            });
 
            return response()->json(['message' => 'Given table has been successfully created!'], 200);
        }
 
        return response()->json(['message' => 'Given table is already existis.'], 400);
    }

    public function index(Request $request)
    {
    	
    }

    public function generate(Request $request)
    {
    	return $this->createTable('hr_test_database', $this->atttable);
    	dd('get');
    }
}
