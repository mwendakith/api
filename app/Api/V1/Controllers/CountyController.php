<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\County;
use App\Api\V1\Controllers\BaseController;

use DB;

class CountyController extends BaseController
{
    //

    private function set_key($county){
    	if(is_numeric($county)){
			return "countys.CountyMFLCode"; 
		}
		else{
			return "countys.CountyDHISCode";
		}
    }

    public function counties(){
    	return DB::table('countys')->orderBy('ID')->get();
    }

    public function info($county){
    	return DB::table('countys')->where('ID', $county)->orWhere('CountyDHISCode', $county)->orWhere('CountyMFLCode', $county)->get();
    }

    public function summary($county, $type, $year, $month=NULL){

    	// return $county;

		$data = NULL;

		$raw = $this->county_string . $this->summary_query();

		$key = $this->set_key($county);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_summary')
			->select('year', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('county_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('county_summary')
			->select('year', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_outcomes($county, $type, $year, $month=NULL){

		$data = NULL;

		$raw = $this->county_string . $this->hei_outcomes_query();
		
		$key = $this->set_key($county);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_summary')
			->select('year', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('year', $year)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('county_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('county_summary')
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_validation($county, $type, $year, $month=NULL){

		$data = NULL;

		$raw = $this->county_string . $this->hei_validation_query();
		
		$key = $this->set_key($county);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_summary')
			->select('year', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('year', $year)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('county_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('county_summary')
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
			$data = array($temp);
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function age_breakdown($county, $type, $year, $month=NULL){

		$data = NULL;

		$raw = $this->county_string . $this->age_breakdown_query();
		
		$key = $this->set_key($county);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select('year', 'month', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select('year', 'month', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function entry_point($county, $type, $year, $month=NULL){
		$data = NULL;

		$raw = $this->county_string . $this->entry_point_query();

		$key = $this->set_key($county);

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('county_entrypoint')
			->select('year', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('county_entrypoint')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('county_entrypoint')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$d = DB::table('county_entrypoint')
			->select('year', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}

	public function mother_prophylaxis($county, $type, $year, $month=NULL){
		$data = NULL;

		$raw = $this->county_string . $this->mother_prophylaxis_query();

		$key = $this->set_key($county);

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('county_mprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('county_mprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('county_mprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$d = DB::table('county_mprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}


	public function infant_prophylaxis($county, $type, $year, $month=NULL){
		$data = NULL;

		$raw = $this->county_string . $this->infant_prophylaxis_query();

		$key = $this->set_key($county);

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('county_iprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_iprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('county_iprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_iprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('county_iprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$d = DB::table('county_iprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_iprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county, $key){
				if($county != "0" || $county != 0){
					return $query->where($key, $county);
				}
					
			})
			->groupBy('name')->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name', 'countys.CountyDHISCode', 'countys.CountyMFLCode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}

	public function county_sites($county, $type, $year, $month=NULL){

		$data = NULL;

		$raw = $this->site_string . $this->summary_query();

		
		$key = $this->set_key($county);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('site_summary')
			->select('year', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->join('districts', 'districts.ID', '=', 'facilitys.district')
			->join('countys', 'countys.ID', '=', 'districts.county')
			->where('year', $year)
			->where($key, $county)
			->orderBy('all_tests')
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('site_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->join('districts', 'districts.ID', '=', 'facilitys.district')
			->join('countys', 'countys.ID', '=', 'districts.county')
			->where('year', $year)
			->where($key, $county)
			->orderBy('facilitys.ID')
			->orderBy('site_summary.month')
			->groupBy('month')
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('site_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->join('districts', 'districts.ID', '=', 'facilitys.district')
			->join('countys', 'countys.ID', '=', 'districts.county')
			->where('year', $year)
			->where($key, $county)
			->orderBy('all_tests')
			->where('month', $month)
			->groupBy('month')
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('site_summary')
			->select('year', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->join('districts', 'districts.ID', '=', 'facilitys.district')
			->join('countys', 'countys.ID', '=', 'districts.county')
			->where('year', $year)
			->where($key, $county)
			->orderBy('all_tests')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}
}
