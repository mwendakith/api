<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\National;
use App\Http\Controllers\Controller;

use DB;

class PatientController extends Controller
{
    //

    private function set_key($site){
    	if(is_numeric($site)){
			return "patients_eid.FacilityMFLcode"; 
		}
		else{
			return "patients_eid.FacilityDHIScode";
		}
    }

    public function get_results($site, $patientID){
    	$key = $this->set_key($site);
    	$query = $this->patient_query();

    	$data = DB::table('patients_eid')
		->select(DB::raw($query))
		->where('patientID', $patientID)
		->where($key, $site)
		->get();

		return $data;
    }


}