<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\District;
use App\Api\V1\Controllers\BaseController;

use DB;

class SubcountyController extends BaseController
{
    //

	private function set_key($subcounty){
    	if(is_numeric($subcounty)){
			return "districts.SubCountyMFLCode"; 
		}
		else{
			return "districts.SubCountyDHISCode";
		}
    }

    public function subcounties(){
    	return DB::table('districts')->orderBy('ID')->get();
    }

    public function info($subcounty){
    	return DB::table('districts')->where('ID', $subcounty)->orWhere('SubCountyDHISCode', $subcounty)->orWhere('SubCountyMFLCode', $subcounty)->get();
    }

    public function summary($subcounty, $type, $year, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.SubCountyDHISCode as SubCountyDHISCode, districts.SubCountyMFLCode as SubCountyMFLCode, districts.name as subcounty, ' . $this->summary_query();

		$key = $this->set_key($subcounty);
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_summary')
			->select('year', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('subcounty_summary')
			->select('year', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
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

	public function hei_outcomes($subcounty, $type, $year, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.SubCountyDHISCode as SubCountyDHISCode, districts.SubCountyMFLCode as SubCountyMFLCode, districts.name as subcounty, ' . $this->hei_outcomes_query();
		
		$key = $this->set_key($subcounty);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_summary')
			->select('year', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('year', $year)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('subcounty_summary')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
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

	public function hei_validation($subcounty, $type, $year, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.SubCountyDHISCode as SubCountyDHISCode, districts.SubCountyMFLCode as SubCountyMFLCode, districts.name as subcounty, ' . $this->hei_validation_query();
		
		$key = $this->set_key($subcounty);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_summary')
			->select('year', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('year', $year)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('subcounty_summary')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
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


	public function age_breakdown($subcounty, $type, $year, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.SubCountyDHISCode as SubCountyDHISCode, districts.SubCountyMFLCode as SubCountyMFLCode, districts.name as subcounty, ' . $this->age_breakdown_query();
		
		$key = $this->set_key($subcounty);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select('year', 'month', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select('year', 'month', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
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


	public function entry_point($subcounty, $type, $year, $month=NULL){
		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.SubCountyDHISCode as SubCountyDHISCode, districts.SubCountyMFLCode as SubCountyMFLCode, districts.name as subcounty, ' . $this->entry_point_query();

		$key = $this->set_key($subcounty);

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_entrypoint')
			->select('year', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('subcounty_entrypoint')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('subcounty_entrypoint')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$d = DB::table('subcounty_entrypoint')
			->select('year', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
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

	public function mother_prophylaxis($subcounty, $type, $year, $month=NULL){
		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.SubCountyDHISCode as SubCountyDHISCode, districts.SubCountyMFLCode as SubCountyMFLCode, districts.name as subcounty, ' . $this->mother_prophylaxis_query();

		$key = $this->set_key($subcounty);

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_mprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('subcounty_mprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('subcounty_mprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$d = DB::table('subcounty_mprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
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


	public function infant_prophylaxis($subcounty, $type, $year, $month=NULL){
		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.SubCountyDHISCode as SubCountyDHISCode, districts.SubCountyMFLCode as SubCountyMFLCode, districts.name as subcounty, ' . $this->infant_prophylaxis_query();

		$key = $this->set_key($subcounty);

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_iprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('subcounty_iprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('subcounty_iprophylaxis')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$d = DB::table('subcounty_iprophylaxis')
			->select('year', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty, $key){
				if($subcounty != "0" || $subcounty != 0){
					return $query->where($key, $subcounty);
				}					
			})
			->groupBy('name')->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name', 'districts.SubCountyMFLCode', 'districts.SubCountyDHISCode', 'year')
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

	public function subcounty_sites($subcounty, $type, $year, $month=NULL){

		$data = NULL;

		$raw = 'facilitys.ID as facility_id, facilitys.facilitycode as facilityMFLCode, facilitys.DHIScode as facilityDHISCode, facilitys.name as facility, ' . $this->summary_query();

		$key = $this->set_key($subcounty);		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('site_summary')
			->select('year', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->join('districts', 'districts.ID', '=', 'facilitys.district')
			->where('year', $year)
			->where($key, $subcounty)
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
			->where('year', $year)
			->where($key, $subcounty)
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
			->where('year', $year)
			->where($key, $subcounty)
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
			->where('year', $year)
			->where($key, $subcounty)
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
