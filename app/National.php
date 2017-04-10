<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class National extends Model
{
    //

    public function summary($year, $type, $month=NULL){

		$data = NULL;

		$raw = 'SUM(alltests) as all_tests,
		 SUM(pos) as positives, 
		 SUM(neg) as negatives,
		  SUM(redraw) as redraws, 
		  SUM(firstdna) as first_DNA_PCR, 
		  SUM(confirmdna) as repeat_confirmatory_PCR, 
		  AVG(sitessending) as sites_sending, 
		  AVG(medage) as median_age, 
		  SUM(rejected) as rejected,
		   SUM(actualinfants) as infants, 
		   SUM(actualinfantsPOS) as infants_positive,
		    SUM(infantsless2m) as infants_less_2m, SUM(adults) as adults, 
		    SUM(adultsPOS) as adults_positive,
		     AVG(tat1) as collection_to_lab_receipt, 
		     AVG(tat2) as lab_receipt_to_testing, 
		     AVG(tat3) as tested_to_dispatch, 
		     AVG(tat4) as collection_to_dispatch';
		

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

		$raw = 'SUM(ltfu) as lost_to_follow_up,
		 SUM(dead) as dead, 
		 SUM(adult) as adult,
		  SUM(transout) as transferred_out, 
		  SUM(other) as other';
		

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

		$raw = 'SUM(nodatapos) as no_data_pos,
		 SUM(nodataneg) as no_data_neg, 
		 SUM(sixweekspos) as six_weeks_pos, 
		 SUM(sixweeksneg) as six_weeks_neg, 
		 SUM(sevento3mpos) as seven_to_3m_pos, 
		 SUM(sevento3mneg) as seven_to_3m_neg, 
		 SUM(threemto9mpos) as three_to_9m_pos, 
		 SUM(threemto9mneg) as three_to_9m_neg, 
		 SUM(ninemto18mpos) as nine_to_18m_pos, 
		 SUM(ninemto18mneg) as nine_to_18m_neg, 
		 SUM(above18mpos) as above_18m_pos, 
		 SUM(above18mneg) as above_18m_neg';
		

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

		$raw = 'SUM(national_entrypoint.pos) as positives, 
		 SUM(national_entrypoint.neg) as negatives,
		  entry_points.name';

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

		$raw = 'SUM(national_mprophylaxis.pos) as positives, 
		 SUM(national_mprophylaxis.neg) as negatives,
		  prophylaxis.name';

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

		$raw = 'SUM(national_iprophylaxis.pos) as positives, 
		 SUM(national_iprophylaxis.neg) as negatives,
		  prophylaxis.name';

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
