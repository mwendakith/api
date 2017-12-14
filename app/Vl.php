<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\VlReport;

use DB;
use Excel;

class Vl extends Model
{
    //

    public function new_report($year = null){
    	echo "\n Method start at " . date('d/m/Y h:i:s a', time());
    	if($year==null){
    		$year = Date('Y');
    	}

    	ini_set("memory_limit", "-1");

    	$sql = "select count(*) as `tests`, facility, patient, labs.name as lab
				from viralsamples 
				JOIN labs on viralsamples.labtestedin=labs.ID ";

        $sql .= " where viralsamples.rcategory between 1 and 4 ";
        $sql .= " and viralsamples.flag=1 and viralsamples.repeatt=0 ";
		$sql .= " and patient != '' and patient != 'null' and patient is not null and facility != 7148 ";
		$sql .= " and year(datetested) = {$year} ";
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

		echo "\n Begin looping at " . date('d/m/Y h:i:s a', time());

		foreach ($data as $key => $value) {
			$results = DB::connection('vl')->select($get_patients, [$value->patient, $value->facility]);
			// $results = collect($results);

			$first = true;
			$max = $min = 0;
			$max_date = $min_date = null;
			$max_justification = $min_justification = 0;

			foreach ($results as $key2 => $value2) {
				$test_val = $this->check_int($value2->result);
				if($first){
					$max = $min = $test_val;
					$max_date = $min_date = $value2->datetested;
					$max_justification = $min_justification = $value2->justification;
					$first = false;
					continue;
				}

				if($test_val > $max){
					$max = $test_val;
					$max_justification = $value2->justification;
					$max_date = $value2->datetested;
				}

				if($test_val < $min){
					$min = $test_val;
					$min_justification = $value2->justification;
					$min_date = $value2->datetested;
				}
			}

			if(($max - $min) > 500){
				$return_data['lab'] = $value->lab;
				$return_data['facility'] = $value->facility;
				$return_data['patient'] = $value->patient;
				$return_data['viral_difference'] = ($max - $min);

				$return_data['max_datetested'] = $max_date;
				$return_data['min_datetested'] = $min_date;

				$return_data['max_result'] = $max;
				$return_data['min_result'] = $min;

				$return_data['max_justification'] = $max_justification;
				$return_data['min_justification'] = $min_justification;
				$i++;

			}			


		}

		echo "\n Complete looping at " . date('d/m/Y h:i:s a', time());

		Excel::create('Vl_Standard_Report', function($excel) use($return_data)  {

		    // Set sheets

		    $excel->sheet('Sheetname', function($sheet) use($return_data) {

		        $sheet->fromArray($return_data);

		    });

		})->store('csv');

		echo "\n Complete method at " . date('d/m/Y h:i:s a', time());

    }

    private function check_int($var){
    	if(is_numeric($var)){
    		return  (int) $var;
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
