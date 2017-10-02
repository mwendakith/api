<?php

namespace App\Api\Eid\V1\Controllers;

use Illuminate\Http\Request;
use App\Api\Eid\V1\Controllers\BaseController;

use DB;
use Excel;

class ReportController extends BaseController
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

    private function set_county($county){
    	if(is_numeric($county)){
			return "countys.CountyMFLCode"; 
		}
		else{
			return "countys.CountyDHISCode";
		}
    }

    private function set_subcounty($subcounty){
    	if(is_numeric($subcounty)){
			return "districts.SubCountyMFLCode"; 
		}
		else{
			return "districts.SubCountyDHISCode";
		}
    }

    public function positives_report($year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = "FacilityMFLcode, patientID, datetested";

    	$data = DB::table('patients_eid')
		->select(DB::raw($raw))
		->orderBy('FacilityMFLcode', 'desc')
		->groupBy('FacilityMFLcode', 'patientID')
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', 2)
		->where('receivedstatus', '!=', 2)
		->get();

		// return $data;

		$i = 0;
		$result = null;

		foreach ($data as $patient) {
			$d = null;

			$d = DB::table('patients_eid')
			->select(DB::raw($raw ))
			->where('FacilityMFLcode', $patient->FacilityMFLcode)
			->where('patientID', $patient->patientID)
			->where('result', 1)
			->where('datetested', '<', $patient->datetested)
			->where('receivedstatus', '!=', 2)
			->first();


			if($d != null){
				$result[$i]['FacilityMFLcode'] = $patient->FacilityMFLcode;
				$result[$i]['patientID'] = $patient->patientID;
				$result[$i]['positive_result'] = "Positive";
				$result[$i]['positive_date'] = $patient->datetested;
				$result[$i]['negative_result'] = "Negative";
				$result[$i]['negative_date'] = $d->datetested;
				$i++;
			}


		}

		Excel::create('Negative_to_Positive', function($excel) use($result)  {

		    // Set sheets

		    $excel->sheet('Sheetname', function($sheet) use($result) {

		        $sheet->fromArray($result);

		    });

		})->export('csv');

		// return $result;
    }

    public function negatives_report($year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = "FacilityMFLcode, patientID, datetested";

    	$data = DB::table('patients_eid')
		->select(DB::raw($raw))
		->orderBy('FacilityMFLcode', 'desc')
		->groupBy('FacilityMFLcode', 'patientID')
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', 1)
		->where('receivedstatus', '!=', 2)
		->get();

		$i = 0;
		$result = null;

		foreach ($data as $patient) {
			$d = null;

			$d = DB::table('patients_eid')
			->select(DB::raw($raw ))
			->where('FacilityMFLcode', $patient->FacilityMFLcode)
			->where('patientID', $patient->patientID)
			->where('result', 2)
			->where('datetested', '>', $patient->datetested)
			->where('receivedstatus', '!=', 2)
			->first();

			if($d != null){
				$result[$i]['FacilityMFLcode'] = $patient->FacilityMFLcode;
				$result[$i]['patientID'] = $patient->patientID;
				$result[$i]['positive_result'] = "Positive";
				$result[$i]['positive_date'] = $patient->datetested;
				$result[$i]['negative_result'] = "Negative";
				$result[$i]['negative_date'] = $d->datetested;
				$i++;
			}


		}

		Excel::create('Positive_to_Negative', function($excel) use($result)  {

		    // Set sheets

		    $excel->sheet('Sheetname', function($sheet) use($result) {

		        $sheet->fromArray($result);

		    });

		})->export('csv');

		// return $result;
    }

}