<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\EidReport;

use DB;
use Excel;
// use Carbon/Carbon;

class Eid extends Model
{
    //
    public function positives_report($year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = "samples.ID, samples.patient, samples.facility, labs.name as lab, facilitys.name as facility, samples.pcrtype,  datetested";

    	$data = DB::connection('eid')
		->table("samples")
		->select(DB::raw($raw))
		->join('facilitys', 'samples.facility', '=', 'facilitys.ID')
		->join('labs', 'samples.labtestedin', '=', 'labs.ID')
		->orderBy('samples.facility', 'desc')
		->groupBy('patient', 'samples.facility') 
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', 2)
		->where('samples.repeatt', 0)
		->where('samples.Flag', 1)
		->where('samples.eqa', 0)
		->get();

		// return $data;

		$i = 0;
		$result = null;

		foreach ($data as $patient) {
			$d = null;

	    	$d = DB::connection('eid')
			->table("samples")
			->select(DB::raw($raw))
			->join('facilitys', 'samples.facility', '=', 'facilitys.ID')
			->join('labs', 'samples.labtestedin', '=', 'labs.ID')
			->where('samples.facility', $patient->facility)
			->where('patient', $patient->patient)
			->where('datetested', '<', $patient->datetested)
			->where('result', 1)
			->where('samples.repeatt', 0)
			->where('samples.Flag', 1)
			->where('samples.eqa', 0)
			->first();

			if($d != null){
				$result[$i]['laboratory'] = $patient->lab;
				$result[$i]['facility'] = $patient->facility;
				$result[$i]['patient_id'] = $patient->patient;
				$result[$i]['negative_sample_id'] = $d->ID;
				$result[$i]['negative_date'] = $d->datetested;
				$result[$i]['positive_sample_id'] = $patient->ID;
				$result[$i]['positive_date'] = $patient->datetested;
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

    	$raw = "samples.ID, samples.patient, samples.facility, labs.name as lab, facilitys.name as facility, samples.pcrtype,  datetested";

    	$data = DB::connection('eid')
		->table("samples")
		->select(DB::raw($raw))
		->join('facilitys', 'samples.facility', '=', 'facilitys.ID')
		->join('labs', 'samples.labtestedin', '=', 'labs.ID')
		->orderBy('samples.facility', 'desc')
		->groupBy('patient', 'samples.facility') 
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', 1)
		->where('samples.repeatt', 0)
		->where('samples.Flag', 1)
		->where('samples.eqa', 0)
		->get();

		// return $data;

		$i = 0;
		$result = null;

		foreach ($data as $patient) {
			$d = null;

	    	$d = DB::connection('eid')
			->table("samples")
			->select(DB::raw($raw))
			->join('facilitys', 'samples.facility', '=', 'facilitys.ID')
			->join('labs', 'samples.labtestedin', '=', 'labs.ID')
			->where('samples.facility', $patient->facility)
			->where('patient', $patient->patient)
			->where('datetested', '<', $patient->datetested)
			->where('result', 2)
			->where('samples.repeatt', 0)
			->where('samples.Flag', 1)
			->where('samples.eqa', 0)
			->first();

			if($d != null){
				$result[$i]['laboratory'] = $patient->lab;
				$result[$i]['facility'] = $patient->facility;
				$result[$i]['patient_id'] = $patient->patient;

				$result[$i]['negative_sample_id'] = $patient->ID; 
				$result[$i]['negative_date'] = $patient->datetested;

				$result[$i]['positive_sample_id'] = $d->ID;
				$result[$i]['positive_date'] =  $d->datetested;
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



  //   public function negatives_report($year=null, $month=null){
  //   	if($year==null){
  //   		$year = Date('Y');
  //   	}

  //   	$raw = "FacilityMFLcode, patientID, datetested";

  //   	$data = DB::table('patients_eid')
		// ->select(DB::raw($raw))
		// ->orderBy('FacilityMFLcode', 'desc')
		// ->groupBy('FacilityMFLcode', 'patientID')
		// ->whereYear('datetested', $year)
		// ->when($month, function($query) use ($month){
		// 	if($month != null || $month != 0){
		// 		return $query->whereMonth('datetested', $month);
		// 	}
		// })
		// ->where('result', 1)
		// ->where('receivedstatus', '!=', 2)
		// ->get();

		// $i = 0;
		// $result = null;

		// foreach ($data as $patient) {
		// 	$d = null;

		// 	$d = DB::table('patients_eid')
		// 	->select(DB::raw($raw ))
		// 	->where('FacilityMFLcode', $patient->FacilityMFLcode)
		// 	->where('patientID', $patient->patientID)
		// 	->where('result', 2)
		// 	->where('datetested', '>', $patient->datetested)
		// 	->where('receivedstatus', '!=', 2)
		// 	->first();

		// 	if($d != null){
		// 		$result[$i]['FacilityMFLcode'] = $patient->FacilityMFLcode;
		// 		$result[$i]['patientID'] = $patient->patientID;
		// 		$result[$i]['positive_result'] = "Positive";
		// 		$result[$i]['positive_date'] = $patient->datetested;
		// 		$result[$i]['negative_result'] = "Negative";
		// 		$result[$i]['negative_date'] = $d->datetested;
		// 		$i++;
		// 	}


		// }

		// Excel::create('Positive_to_Negative', function($excel) use($result)  {

		//     // Set sheets

		//     $excel->sheet('Sheetname', function($sheet) use($result) {

		//         $sheet->fromArray($result);

		//     });

		// })->store('csv');

		// // return $result;
  //   }

    public function report($year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = "samples.patient, samples.facility, labs.name as lab, view_facilitys.name as facility, samples.pcrtype,  datetested";

    	// $data = 

    	$positives = DB::connection('eid')
		->table("samples")
		->select(DB::raw($raw))
		->join('view_facilitys', 'samples.facility', '=', 'view_facilitys.ID')
		->join('labs', 'samples.labtestedin', '=', 'labs.ID')
		->orderBy('facility', 'desc')
		->groupBy('patient', 'facility') 
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', 2)
		->where('samples.repeatt', 0)
		->where('samples.Flag', 1)
		->where('samples.eqa', 0)
		->get();

	}

    public function send_report(){
    	$mail_array = array('joelkith@gmail.com', 'tngugi@gmail.com', 'jbatuka@usaid.gov');
    	$up = new EidReport;
    	Mail::to($mail_array)->send($up);
    }
}
