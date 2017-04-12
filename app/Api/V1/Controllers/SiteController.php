<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Facility;
use App\Http\Controllers\Controller;

use DB;

class SiteController extends Controller
{
    //

    private function set_key($site){
    	if(is_numeric($site)){
			return "facilitys.facilitycode"; 
		}
		else{
			return "facilitys.DHIScode";
		}
    }

	public function unsupported_sites(){
    	$raw = '';

    	return DB::table('facilitys')
			->select('facilitys.ID as facility_id', 'facilitys.facilitycode as SiteMFLCode', 'facilitys.DHIScode as SiteDHISCode', 'facilitys.name as site', 'districts.ID as subcounty_id', 'districts.name as subcounty', 'countys.ID as county_id', 'countys.name as county')
			->join('districts', 'districts.ID', '=', 'facilitys.district')
			->join('countys', 'countys.ID', '=', 'districts.county')
			->where('facilitys.partner', 0)
			->get();
    }


    public function sites(){
    	return DB::table('facilitys')
    	->select('facilitys.ID as facility_id', 'facilitys.facilitycode as facilityMFLCode', 'facilitys.DHIScode as facilityDHISCode', 'facilitys.name as facility')->orderBy('ID')
    	->get();
    }

    public function info($site){
    	$raw = '';

    	return DB::table('facilitys')
			->select('facilitys.ID as facility_id', 'facilitys.facilitycode as facilityMFLCode', 'facilitys.DHIScode as facilityDHISCode', 'facilitys.name as site', 'districts.ID as subcounty_id', 'districts.name as subcounty', 'countys.ID as county_id', 'countys.name as county', 'partners.ID as partner_id', 'partners.name as partner')
			->join('districts', 'districts.ID', '=', 'facilitys.district')
			->join('countys', 'countys.ID', '=', 'districts.county')
			->join('partners', 'partners.ID', '=', 'facilitys.partner')
			->where('facilitys.ID', $site)
			->orWhere('facilitycode', $site)
			->orWhere('DHIScode', $site)
			->get();
    }

    public function summary($site, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'facilitys.ID as facility_id, facilitys.facilitycode as facilityMFLCode, facilitys.DHIScode as facilityDHISCode, facilitys.name as facility, ' . $this->summary_query();

		$key = $this->set_key($site);
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('site_summary')
			->select('year', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->where('year', $year)
			->when($site, function($query) use ($site, $key){
				if($site != "0" || $site != 0){
					return $query->where($key, $site);
				}
				else{
					return $query->orderBy('all_tests')->limit(100);
				}					
			})
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('site_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->where('year', $year)
			->when($site, function($query) use ($site, $key){
				if($site != "0" || $site != 0){
					return $query->where($key, $site);
				}
				else{
					return $query->orderBy('all_tests')->limit(100);
				}					
			})
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
			->where('year', $year)
			->when($site, function($query) use ($site, $key){
				if($site != "0" || $site != 0){
					return $query->where($key, $site);
				}
				else{
					return $query->orderBy('all_tests')->limit(100);
				}					
			})
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

			$data = DB::table('site_summary')
			->select('year', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->where('year', $year)
			->when($site, function($query) use ($site, $key){
				if($site != "0" || $site != 0){
					return $query->where($key, $site);
				}
				else{
					return $query->orderBy('all_tests')->limit(100);
				}					
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			$temp = (array) $data[0];
			array_unshift($temp, $b, $a);
			$data = array($temp);
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_outcomes($site, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'facilitys.ID as facility_id, facilitys.facilitycode as facilityMFLCode, facilitys.DHIScode as facilityDHISCode, facilitys.name as facility, ' . $this->hei_outcomes_query();
		
		$key = $this->set_key($site);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('site_summary')
			->select('year', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('lost_to_follow_up')->limit(100);
				}
			})
			->where('year', $year)
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('site_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->where('year', $year)
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('lost_to_follow_up')->limit(100);
				}
			})
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
			->where('year', $year)
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('lost_to_follow_up')->limit(100);
				}
			})
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

			$data = DB::table('site_summary')
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('lost_to_follow_up')->limit(100);
				}
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();

			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			$temp = (array) $data[0];
			array_unshift($temp, $b, $a);
			$data = array($temp);
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	public function hei_validation($site, $year, $type, $month=NULL){

		$data = NULL;

		$raw = 'facilitys.ID as facility_id, facilitys.facilitycode as facilityMFLCode, facilitys.DHIScode as facilityDHISCode, facilitys.name as facility, ' . $this->hei_validation_query();
		
		$key = $this->set_key($site);

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('site_summary')
			->select('year', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('confirmed_pos')->limit(100);
				}
			})
			->where('year', $year)
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('site_summary')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->where('year', $year)
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('confirmed_pos')->limit(100);
				}
			})
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
			->where('year', $year)
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('confirmed_pos')->limit(100);
				}
			})
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

			$data = DB::table('site_summary')
			->leftJoin('facilitys', 'facilitys.ID', '=', 'site_summary.facility')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->when($site, function($query) use ($site){
				if($site != 0) return $query->where('facilitys.ID', $site);

				else{
					return $query->orderBy('confirmed_pos')->limit(100);
				}
			})
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('facilitys.ID', 'facilitys.name', 'facilitys.facilitycode', 'facilitys.DHIScode', 'year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			$temp = (array) $data[0];
			array_unshift($temp, $b, $a);
			$data = array($temp);
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}

		
		return $data;

	}

	
}
