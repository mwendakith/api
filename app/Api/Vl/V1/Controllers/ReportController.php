<?php

namespace App\Api\Vl\V1\Controllers;

use Illuminate\Http\Request;
use App\Api\Vl\V1\Controllers\BaseController;

use DB;

class ReportController extends BaseController
{
    //

    private function set_key($site){
    	if(is_numeric($site)){
			return "patients.FacilityMFLcode"; 
		}
		else{
			return "patients.FacilityDHIScode";
		}
    }

    private function set_county($county){
    	if(is_numeric($county)){
			return "countys.CountyMFLCode"; 
		}
		else{
			return "countys.CountyDHISCode";
		}
    }

    private function set_subcounty($subcounty){
    	if(is_numeric($subcounty)){
			return "districts.SubCountyMFLCode"; 
		}
		else{
			return "districts.SubCountyDHISCode";
		}
    }

    public function nation_report($year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = $this->report_string;

    	$data = DB::table('patients')
		->select(DB::raw($raw))
		->orderBy('FacilityMFLcode', 'desc')
		->groupBy('FacilityMFLcode', 'patientID')
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', '>', 1000)
		->where('receivedstatus', '!=', 2)
		->get();

		return $this->format_return($data);
    }


    public function county_report($county, $year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = $this->report_string;
    	$key = $this->set_county($county);

    	$data = DB::table('patients')
		->select(DB::raw($raw))
		->join('facilitys', 'facilitys.facilitycode', '=', 'patients.FacilityMFLcode')
		->join('districts', 'districts.ID', '=', 'facilitys.district')
		->join('countys', 'countys.ID', '=', 'districts.county')
		->orderBy('FacilityMFLcode', 'desc')
		->groupBy('FacilityMFLcode', 'patientID')
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', '>', 1000)
		->where($key, $county)
		->where('receivedstatus', '!=', 2)
		->get();

		return $this->format_return($data);
    }

    public function subcounty_report($subcounty, $year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = $this->report_string;
    	$key = $this->set_subcounty($subcounty);

    	$data = DB::table('patients')
		->select(DB::raw($raw))
		->join('facilitys', 'facilitys.facilitycode', '=', 'patients.FacilityMFLcode')
		->join('districts', 'districts.ID', '=', 'facilitys.district')
		->orderBy('FacilityMFLcode', 'desc')
		->groupBy('FacilityMFLcode', 'patientID')
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', '>', 1000)
		->where($key, $subcounty)
		->where('receivedstatus', '!=', 2)
		->get();

		return $this->format_return($data);
    }

    public function partner_report($partner, $year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = $this->report_string;

    	$data = DB::table('patients')
		->select(DB::raw($raw))
		->join('facilitys', 'facilitys.facilitycode', '=', 'patients.FacilityMFLcode')
		->orderBy('FacilityMFLcode', 'desc')
		->groupBy('FacilityMFLcode', 'patientID')
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', '>', 1000)
		->where('facilitys.partner', $partner)
		->where('receivedstatus', '!=', 2)
		->get();

		return $this->format_return($data);
    }

    public function site_report($site, $year=null, $month=null){
    	if($year==null){
    		$year = Date('Y');
    	}

    	$raw = $this->report_string;
    	$key = $this->set_key($site);

    	$data = DB::table('patients')
		->select(DB::raw($raw))
		->orderBy('FacilityMFLcode', 'desc')
		->groupBy('FacilityMFLcode', 'patientID')
		->whereYear('datetested', $year)
		->when($month, function($query) use ($month){
			if($month != null || $month != 0){
				return $query->whereMonth('datetested', $month);
			}
		})
		->where('result', '>', 1000)
		->where($key, $site)
		->where('receivedstatus', '!=', 2)
		->get();

		return $this->format_return($data);
    }

    public function format_return(&$data){

    	if ($data->isEmpty()) { return $this->pass_error("No data found"); }
    	
    	$raw = $this->report_string;
    	$i = 0;
		$result = null;

		foreach ($data as $patient) {
			$d = null;

			$d = DB::table('patients')
			->select(DB::raw($raw ))
			->where('FacilityMFLcode', $patient->FacilityMFLcode)
			->where('patientID', $patient->patientID)
			->where('result', '<', 1000)
			->where('datetested', '<', $patient->datetested)
			->where('receivedstatus', '!=', 2)
			->first();

			if($d != null){
				$result[$i]['FacilityMFLcode'] = $patient->FacilityMFLcode;
				$result[$i]['patientID'] = $patient->patientID;
				$result[$i]['not_suppressed_result'] = $patient->result;
				$result[$i]['not_suppressed_date'] = $patient->datetested;
				$result[$i]['suppressed_result'] = $d->result;
				$result[$i]['suppressed_date'] = $d->datetested;
				$i++;
			}

		}

		return $result;
    }


}