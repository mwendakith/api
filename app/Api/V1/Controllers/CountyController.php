<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\County;
use App\Http\Controllers\Controller;

use DB;

class CountyController extends Controller
{
    //

    public function counties(){
    	return DB::table('countys')->orderBy('ID')->get();
    }

    public function info($county){
    	return DB::table('countys')->where('ID', $county)->orWhere('CountyDHISCode', $county)->orWhere('CountyMFLCode', $county)->get();
    }

    public function summary($county, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'countys.ID as county_id, countys.name as county, ' . $this->summary_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_summary')
			->select(DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_summary')
			->select('month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('county_summary')
			->select('month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('county_summary')
			->select(DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_outcomes($county, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'countys.ID as county_id, countys.name as county, ' . $this->hei_outcomes_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_summary')
			->select(DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('year', $year)
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_summary')
			->select('month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('county_summary')
			->select('month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('county_summary')
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_validation($county, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'countys.ID as county_id, countys.name as county, ' . $this->hei_validation_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_summary')
			->select(DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('year', $year)
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_summary')
			->select('month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('county_summary')
			->select('month', DB::raw($raw))
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('county_summary')
			->leftJoin('countys', 'countys.ID', '=', 'county_summary.county')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function age_breakdown($county, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'countys.ID as county_id, countys.name as county, ' . $this->age_breakdown_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select('month', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select('month', DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('county_agebreakdown')
			->leftJoin('countys', 'countys.ID', '=', 'county_agebreakdown.county')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function entry_point($county, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'countys.ID as county_id, countys.name as county, ' . $this->entry_point_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('county_entrypoint')
			->select(DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('county_entrypoint')
			->select('month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			$data = DB::table('county_entrypoint')
			->select('month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('county_entrypoint')
			->select(DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'county_entrypoint.entrypoint')
			->leftJoin('countys', 'countys.ID', '=', 'county_entrypoint.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}

	public function mother_prophylaxis($county, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'countys.ID as county_id, countys.name as county, ' . $this->mother_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('county_mprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('county_mprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			$data = DB::table('county_mprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('county_mprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_mprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}


	public function infant_prophylaxis($county, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'countys.ID as county_id, countys.name as county, ' . $this->infant_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('county_iprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_iprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('county_iprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_iprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			$data = DB::table('county_iprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_mprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('county_iprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'county_iprophylaxis.prophylaxis')
			->leftJoin('countys', 'countys.ID', '=', 'county_iprophylaxis.county')
			->where('year', $year)
			->when($county, function($query) use ($county){
				if($county != 0) return $query->where('countys.ID', $county);
			})
			->groupBy('name')->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('countys.ID', 'countys.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}
}
