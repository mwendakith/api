<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\District;
use App\Http\Controllers\Controller;

use DB;

class SubcountyController extends Controller
{
    //
    public function subcounties(){
    	return DB::table('districts')->orderBy('ID')->get();
    }

    public function info($subcounty){
    	return DB::table('districts')->where('ID', $subcounty)->orWhere('SubCountyDHISCode', $subcounty)->orWhere('SubCountyMFLCode', $subcounty)->get();
    }

    public function summary($subcounty, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.name as subcounty, ' . $this->summary_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_summary')
			->select(DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_summary')
			->select('month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_summary')
			->select('month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('subcounty_summary')
			->select(DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_outcomes($subcounty, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.name as subcounty, ' . $this->hei_outcomes_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_summary')
			->select(DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('year', $year)
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_summary')
			->select('month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_summary')
			->select('month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('subcounty_summary')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_validation($subcounty, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.name as subcounty, ' . $this->hei_validation_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_summary')
			->select(DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('year', $year)
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_summary')
			->select('month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_summary')
			->select('month', DB::raw($raw))
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('subcounty_summary')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_summary.subcounty')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function age_breakdown($subcounty, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.name as subcounty, ' . $this->age_breakdown_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select('month', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select('month', DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('subcounty_agebreakdown')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_agebreakdown.subcounty')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function entry_point($subcounty, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.name as subcounty, ' . $this->entry_point_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_entrypoint')
			->select(DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('subcounty_entrypoint')
			->select('month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('subcounty_entrypoint')
			->select('month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('subcounty_entrypoint')
			->select(DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'subcounty_entrypoint.entrypoint')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_entrypoint.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}

	public function mother_prophylaxis($subcounty, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.name as subcounty, ' . $this->mother_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_mprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('subcounty_mprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('subcounty_mprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('subcounty_mprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_mprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_mprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}


	public function infant_prophylaxis($subcounty, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'districts.ID as subcounty_id, districts.name as subcounty, ' . $this->infant_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('subcounty_iprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('subcounty_iprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('subcounty_iprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('subcounty_iprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'subcounty_iprophylaxis.prophylaxis')
			->leftJoin('districts', 'districts.ID', '=', 'subcounty_iprophylaxis.subcounty')
			->where('year', $year)
			->when($subcounty, function($query) use ($subcounty){
				if($subcounty != 0) return $query->where('districts.ID', $subcounty);
			})
			->groupBy('name')->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('districts.ID', 'districts.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}
}
