<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\EidReport;

use DB;
use Excel;

class Eid extends Model
{
    //
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

		})->store('csv');

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

		})->store('csv');

		// return $result;
    }

    public function send_report(){
    	$mail_array = array('joelkith@gmail.com', 'tngugi@gmail.com', 'jbatuka@usaid.gov');
    	$up = new EidReport;
    	Mail::to($mail_array)->send($up);
    }
}
