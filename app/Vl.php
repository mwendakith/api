<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Mail\EidReport;

use DB;
use Excel;

class Vl extends Model
{
    //

    public function new_report($year = null){
    	ini_set("memory_limit", "-1");

    	$sql = "select count(*) as `tests`, facility, patient, labs.name as lab
				from viralsamples 
				JOIN labs on viralsamples.labtestedin=labs.ID ";

        $sql .= " where viralsamples.rcategory between 1 and 4 ";
        $sql .= " and viralsamples.flag=1 and viralsamples.repeatt=0 ";
		$sql .= " and patient != '' and patient != 'null' and patient is not null and facility != 7148 ";
		$sql .= " and year(datetested) = 2017 ";
		$sql .= " group by facility, patient ";
		$sql .= " having tests > 1 ";

		$get_patients = "SELECT datetested, month(datetested) AS test_month, result, justification 
							FROM viralsamples 
							WHERE viralsamples.rcategory BETWEEN 1 and 4 
							and viralsamples.flag=1 and viralsamples.repeatt=0 
							and year(datetested) = 2017 
							and patient = ? and facility = ? ";

		$data = DB::connection('vl')->select($sql);

		$return_data = null;
		$i = 0;

		foreach ($data as $key => $value) {
			$results = DB::connection('vl')->select($get_patients, [$value['patient'], $value['facility']]);
			// $results = collect($results);

			$first = true;
			$max = $min = 0;
			$max_date = $min_date = null;
			$max_justification = $min_justification = 0;

			foreach ($results as $key2 => $value2) {
				if($first){
					$max = $min = $this->check_int($value2->result);
					$max_date = $min_date = $value2->datetested;
					$max_justification = $min_justification = $value2->justification;
					$first = false;
					continue;
				}

				if($value2->result > $max){
					$max = $value2->result;
					$max_justification = $value2->justification;
					$max_date = $value2->datetested;
				}

				if($value2->result < $min){
					$min = $value2->result;
					$min_justification = $value2->justification;
					$min_date = $value2->datetested;
				}
			}

			if(($max - $min) > 500){
				$return_data['lab'] = $value->lab;
				$return_data['facility'] = $value->facility;
				$return_data['patient'] = $value->patient;

				$return_data['max_datetested'] = $maxdate;
				$return_data['min_datetested'] = $mindate;

				$return_data['max_result'] = $max;
				$return_data['min_result'] = $min;

				$return_data['max_justification'] = $max_justification;
				$return_data['min_justification'] = $min_justification;
				$i++;

			}			


		}



		Excel::create('Vl_Standard_Report', function($excel) use($return_data)  {

		    // Set sheets

		    $excel->sheet('Sheetname', function($sheet) use($return_data) {

		        $sheet->fromArray($return_data);

		    });

		})->store('csv');

    }

    private function check_int($var){
    	if(is_numeric($var)){
    		return $var;
    	}
    	else{
    		return 0;
    	}
    }

    public function send_report(){
    	$mail_array = array('joelkith@gmail.com', 'tngugi@gmail.com', 'jbatuka@usaid.gov');
    	$up = new VlReport;
    	Mail::to($mail_array)->send($up);
    }
}
