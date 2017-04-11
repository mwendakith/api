<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Lab;
use App\Http\Controllers\Controller;

use DB;

class LabController extends Controller
{
    //

    public function labs(){
    	return DB::table('labs')->orderBy('ID')->get();
    }

    public function info($lab){
    	return DB::table('labs')->where('ID', $lab)->get();
    }

    public function summary($lab, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'labs.ID as lab_id, labs.name as lab, ' . $this->summary_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('lab_summary')
			->select(DB::raw($raw))
			->leftJoin('labs', 'labs.ID', '=', 'lab_summary.lab')
			->where('year', $year)
			->when($lab, function($query) use ($lab){
				if($lab != 0) return $query->where('labs.ID', $lab);
			})
			->groupBy('labs.ID', 'labs.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('lab_summary')
			->select('month', DB::raw($raw))
			->leftJoin('labs', 'labs.ID', '=', 'lab_summary.lab')
			->where('year', $year)
			->when($lab, function($query) use ($lab){
				if($lab != 0) return $query->where('labs.ID', $lab);
			})
			->groupBy('month')
			->groupBy('labs.ID', 'labs.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('lab_summary')
			->select('month', DB::raw($raw))
			->leftJoin('labs', 'labs.ID', '=', 'lab_summary.lab')
			->where('year', $year)
			->when($lab, function($query) use ($lab){
				if($lab != 0) return $query->where('labs.ID', $lab);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('labs.ID', 'labs.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('lab_summary')
			->select(DB::raw($raw))
			->leftJoin('labs', 'labs.ID', '=', 'lab_summary.lab')
			->where('year', $year)
			->when($lab, function($query) use ($lab){
				if($lab != 0) return $query->where('labs.ID', $lab);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('labs.ID', 'labs.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	
}
