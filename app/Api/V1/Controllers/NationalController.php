<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\National;
use App\Http\Controllers\Controller;

use DB;

class NationalController extends Controller
{
    //

	public function summary($year, $type, $month=NULL){

		$data = NULL;


		$raw = $this->summary_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('national_summary')->select(DB::raw($raw))->where('year', $year)->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('national_summary')->select('month', DB::raw($raw))->where('year', $year)->groupBy('month')->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('national_summary')->select('month', DB::raw($raw))->where('year', $year)->where('month', $month)->groupBy('month')->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('national_summary')->select(DB::raw($raw))->where('year', $year)->where('month', '>', $lesser)->where('month', '<', $greater)->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_outcomes($year, $type, $month=NULL){

		$data = NULL;

		$raw = $this->hei_outcomes_query();		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('national_summary')->select(DB::raw($raw))->where('year', $year)->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('national_summary')->select('month', DB::raw($raw))->where('year', $year)->groupBy('month')->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('national_summary')->select('month', DB::raw($raw))->where('year', $year)->where('month', $month)->groupBy('month')->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('national_summary')->select(DB::raw($raw))->where('year', $year)->where('month', '>', $lesser)->where('month', '<', $greater)->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_validation($year, $type, $month=NULL){

		$data = NULL;

		$raw = $this->hei_validation_query();		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('national_summary')->select(DB::raw($raw))->where('year', $year)->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('national_summary')->select('month', DB::raw($raw))->where('year', $year)->groupBy('month')->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('national_summary')->select('month', DB::raw($raw))->where('year', $year)->where('month', $month)->groupBy('month')->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('national_summary')->select(DB::raw($raw))->where('year', $year)->where('month', '>', $lesser)->where('month', '<', $greater)->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function age_breakdown($year, $type, $month=NULL){

		$data = NULL;

		$raw = $this->age_breakdown_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('national_agebreakdown')->select(DB::raw($raw))->where('year', $year)->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('national_agebreakdown')->select('month', DB::raw($raw))->where('year', $year)->groupBy('month')->get();
		}

		// For a particular month
		else if($type == 3){
			$data = DB::table('national_agebreakdown')->select('month', DB::raw($raw))->where('year', $year)->where('month', $month)->groupBy('month')->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('national_agebreakdown')->select(DB::raw($raw))->where('year', $year)->where('month', '>', $lesser)->where('month', '<', $greater)->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function entry_point($year, $type, $month=NULL){
		$data = NULL;

		$raw = $this->entry_point_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('national_entrypoint')->select(DB::raw($raw))->leftJoin('entry_points', 'entry_points.ID', '=', 'national_entrypoint.entrypoint')->where('year', $year)->groupBy('name')->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('national_entrypoint')->select('month', DB::raw($raw))->leftJoin('entry_points', 'entry_points.ID', '=', 'national_entrypoint.entrypoint')->where('year', $year)->groupBy('name')->groupBy('month')->get();

		}

		// For a particular month
		else if($type == 3){

			$data = DB::table('national_entrypoint')->select('month', DB::raw($raw))->leftJoin('entry_points', 'entry_points.ID', '=', 'national_entrypoint.entrypoint')->where('year', $year)->where('month', $month)->groupBy('name')->groupBy('month')->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('national_entrypoint')->select(DB::raw($raw))->leftJoin('entry_points', 'entry_points.ID', '=', 'national_entrypoint.entrypoint')->where('year', $year)->groupBy('name')->where('month', '>', $lesser)->where('month', '<', $greater)->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}

	public function mother_prophylaxis($year, $type, $month=NULL){
		$data = NULL;

		$raw = $this->mother_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('national_mprophylaxis')->select(DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_mprophylaxis.prophylaxis')->where('year', $year)->groupBy('name')->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('national_mprophylaxis')->select('month', DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_mprophylaxis.prophylaxis')->where('year', $year)->groupBy('name')->groupBy('month')->get();

		}

		// For a particular month
		else if($type == 3){

			$data = DB::table('national_mprophylaxis')->select('month', DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_mprophylaxis.prophylaxis')->where('year', $year)->where('month', $month)->groupBy('name')->groupBy('month')->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('national_mprophylaxis')->select(DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_mprophylaxis.prophylaxis')->where('year', $year)->groupBy('name')->where('month', '>', $lesser)->where('month', '<', $greater)->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}


	public function infant_prophylaxis($year, $type, $month=NULL){
		$data = NULL;

		$raw = $this->infant_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('national_iprophylaxis')->select(DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_iprophylaxis.prophylaxis')->where('year', $year)->groupBy('name')->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('national_iprophylaxis')->select('month', DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_iprophylaxis.prophylaxis')->where('year', $year)->groupBy('name')->groupBy('month')->get();

		}

		// For a particular month
		else if($type == 3){

			$data = DB::table('national_iprophylaxis')->select('month', DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_iprophylaxis.prophylaxis')->where('year', $year)->where('month', $month)->groupBy('name')->groupBy('month')->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('national_iprophylaxis')->select(DB::raw($raw))->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'national_iprophylaxis.prophylaxis')->where('year', $year)->groupBy('name')->where('month', '>', $lesser)->where('month', '<', $greater)->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}

}
