<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Partner;
use App\Http\Controllers\Controller;

use DB;

class PartnerController extends Controller
{
    //

    public function partners(){
    	return DB::table('partners')->orderBy('ID')->get();
    }

    public function info($partner){
    	return DB::table('partners')->where('ID', $partner)->orWhere('partnerDHISCode', $partner)->get();
    }

    public function summary($partner, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'partners.ID as partner_id, partners.name as partner, ' . $this->summary_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('ip_summary')
			->select(DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('ip_summary')
			->select('month', DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('ip_summary')
			->select('month', DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('ip_summary')
			->select(DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_outcomes($partner, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'partners.ID as partner_id, partners.name as partner, ' . $this->hei_outcomes_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('ip_summary')
			->select(DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('year', $year)
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('ip_summary')
			->select('month', DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('ip_summary')
			->select('month', DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('ip_summary')
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_validation($partner, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'partners.ID as partner_id, partners.name as partner, ' . $this->hei_validation_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('ip_summary')
			->select(DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('year', $year)
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('ip_summary')
			->select('month', DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('ip_summary')
			->select('month', DB::raw($raw))
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('ip_summary')
			->leftJoin('partners', 'partners.ID', '=', 'ip_summary.partner')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function age_breakdown($partner, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'partners.ID as partner_id, partners.name as partner, ' . $this->age_breakdown_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('ip_agebreakdown')
			->leftJoin('partners', 'partners.ID', '=', 'ip_agebreakdown.partner')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('ip_agebreakdown')
			->leftJoin('partners', 'partners.ID', '=', 'ip_agebreakdown.partner')
			->select('month', DB::raw($raw))
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('ip_agebreakdown')
			->leftJoin('partners', 'partners.ID', '=', 'ip_agebreakdown.partner')
			->select('month', DB::raw($raw))
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', $month)
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$data = DB::table('ip_agebreakdown')
			->leftJoin('partners', 'partners.ID', '=', 'ip_agebreakdown.partner')
			->select(DB::raw($raw))
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}


	public function entry_point($partner, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'partners.ID as partner_id, partners.name as partner, ' . $this->entry_point_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('ip_entrypoint')
			->select(DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'ip_entrypoint.entrypoint')
			->leftJoin('partners', 'partners.ID', '=', 'ip_entrypoint.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('ip_entrypoint')
			->select('month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'ip_entrypoint.entrypoint')
			->leftJoin('partners', 'partners.ID', '=', 'ip_entrypoint.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('ip_entrypoint')
			->select('month', DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'ip_entrypoint.entrypoint')
			->leftJoin('partners', 'partners.ID', '=', 'ip_entrypoint.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('ip_entrypoint')
			->select(DB::raw($raw))
			->leftJoin('entry_points', 'entry_points.ID', '=', 'ip_entrypoint.entrypoint')
			->leftJoin('partners', 'partners.ID', '=', 'ip_entrypoint.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}

	public function mother_prophylaxis($partner, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'partners.ID as partner_id, partners.name as partner, ' . $this->mother_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('ip_mprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_mprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_mprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('ip_mprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_mprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_mprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('ip_mprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_mprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_mprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('ip_mprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_mprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_mprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}


	public function infant_prophylaxis($partner, $year, $type, $month=NULL){
		$data = NULL;

		$raw = 'partners.ID as partner_id, partners.name as partner, ' . $this->infant_prophylaxis_query();

		  // Totals for the whole year
		if($type == 1){

			$data = DB::table('ip_iprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_iprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_iprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){

			$data = DB::table('ip_iprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_iprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_iprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();

		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);

			$data = DB::table('ip_iprophylaxis')
			->select('month', DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_iprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_iprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->where('month', $month)
			->groupBy('name')
			->groupBy('month')
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);

			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];


			$data = DB::table('ip_iprophylaxis')
			->select(DB::raw($raw))
			->leftJoin('prophylaxis', 'prophylaxis.ID', '=', 'ip_iprophylaxis.prophylaxis')
			->leftJoin('partners', 'partners.ID', '=', 'ip_iprophylaxis.partner')
			->where('year', $year)
			->when($partner, function($query) use ($partner){
				if($partner != 0) return $query->where('partners.ID', $partner);
			})
			->groupBy('name')->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('partners.ID', 'partners.name')
			->get();
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}
		
		return $data;

	}
}
