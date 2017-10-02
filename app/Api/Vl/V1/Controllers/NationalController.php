<?php

namespace App\Api\Vl\V1\Controllers;

use App\National;
use App\Api\Vl\V1\Controllers\BaseController;

use DB;

class NationalController extends BaseController
{
    //

	public function summary($type, $year, $month=NULL, $year2=NULL, $month2=NULL){

		$data = NULL;


		$raw = $this->nat_summary_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('vl_national_summary')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->where('tat4', '!=', 0)
			->groupBy('year')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('vl_national_summary')
			->select('year', 'month', DB::raw($raw))
			->where('year', $year)
			->where('tat4', '!=', 0)
			->groupBy('month')
			->groupBy('year')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('vl_national_summary')
			->select('year', 'month', DB::raw($raw))
			->where('year', $year)
			->where('month', $month)
			->where('tat4', '!=', 0)
			->groupBy('month')
			->groupBy('year')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('vl_national_summary')
			->select('year', DB::raw($raw))
			->where('year', $year)
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->where('tat4', '!=', 0)
			->groupBy('year')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Quarter'] = $a;
				$data[$i]['Period'] = $b;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}

		}

		// For Multiple Months across years
		else if($type == 5){

			if($year > $year2) return $this->pass_error('From year is greater');
			if($year == $year2 && $month >= $month2) return $this->pass_error('From month is greater');

			$q = $this->multiple_year($year, $month, $year2, $month2);
			// return $this->pass_error($q);

			if($year == $year2 && $month < $month2){
				$d = DB::table('vl_national_summary')
				->select('year', DB::raw($raw))
				->where('year', $year)
				->where('tat4', '!=', 0)
				->whereBetween('month', [$month, $month2])
				->groupBy('year')
				->get();
			}

			if($year < $year2){
				$d = DB::table('vl_national_summary')
				->select( DB::raw($raw))
				->whereRaw($q)
				->where('tat4', '!=', 0)
				->get();

				
			}
			$desc = $this->describe_multiple($year, $month, $year2, $month2);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Period'] = $desc;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
			
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}


		return $this->check_data($data);

	}

	public function sample_type($type, $year, $month=NULL, $year2=NULL, $month2=NULL){

		$data = NULL;


		$raw = $this->sample_type_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('vl_national_sampletype')
			->select('year', DB::raw($raw))
			->leftJoin('viralsampletype', 'viralsampletype.ID', '=', 'vl_national_sampletype.sampletype')
			->where('year', $year)
			->groupBy('year')
			->groupBy('name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('vl_national_sampletype')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('viralsampletype', 'viralsampletype.ID', '=', 'vl_national_sampletype.sampletype')
			->where('year', $year)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('vl_national_sampletype')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('viralsampletype', 'viralsampletype.ID', '=', 'vl_national_sampletype.sampletype')
			->where('year', $year)
			->where('month', $month)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('vl_national_sampletype')
			->select('year', DB::raw($raw))
			->leftJoin('viralsampletype', 'viralsampletype.ID', '=', 'vl_national_sampletype.sampletype')
			->where('year', $year)
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('year')
			->groupBy('name')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);
			
			for ($i=0; $i < sizeof($d); $i++) { 
				$temp = (array) $d[$i];
				array_unshift($temp, $b, $a);
				$data[$i] = $temp;
			}

			
		}

		// For Multiple Months across years
		else if($type == 5){

			if($year > $year2) return $this->pass_error('From year is greater');
			if($year == $year2 && $month >= $month2) return $this->pass_error('From month is greater');

			$q = $this->multiple_year($year, $month, $year2, $month2);
			// return $this->pass_error($q);

			if($year == $year2 && $month < $month2){
				$d = DB::table('vl_national_sampletype')
				->select('year', DB::raw($raw))
				->leftJoin('viralsampletype', 'viralsampletype.ID', '=', 'vl_national_sampletype.sampletype')
				->where('year', $year)
				->whereBetween('month', [$month, $month2])
				->groupBy('year')
				->groupBy('name')
				->get();
			}

			if($year < $year2){
				$d = DB::table('vl_national_sampletype')
				->select( DB::raw($raw))
				->leftJoin('viralsampletype', 'viralsampletype.ID', '=', 'vl_national_sampletype.sampletype')
				->whereRaw($q)
				->groupBy('name')
				->get();

				
			}
$desc = $this->describe_multiple($year, $month, $year2, $month2);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Period'] = $desc;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
			
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}


		return $this->check_data($data);

	}

	public function regimen($type, $year, $month=NULL, $year2=NULL, $month2=NULL){

		$data = NULL;


		$raw = $this->regimen_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('vl_national_regimen')
			->select('year', DB::raw($raw))
			->leftJoin('viralprophylaxis', 'viralprophylaxis.ID', '=', 'vl_national_regimen.regimen')
			->where('year', $year)
			->groupBy('year')
			->groupBy('name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('vl_national_regimen')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('viralprophylaxis', 'viralprophylaxis.ID', '=', 'vl_national_regimen.regimen')
			->where('year', $year)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('vl_national_regimen')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('viralprophylaxis', 'viralprophylaxis.ID', '=', 'vl_national_regimen.regimen')
			->where('year', $year)
			->where('month', $month)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('vl_national_regimen')
			->select('year', DB::raw($raw))
			->leftJoin('viralprophylaxis', 'viralprophylaxis.ID', '=', 'vl_national_regimen.regimen')
			->where('year', $year)
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('year')
			->groupBy('name')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Quarter'] = $a;
				$data[$i]['Period'] = $b;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
		}

		// For Multiple Months across years
		else if($type == 5){

			if($year > $year2) return $this->pass_error('From year is greater');
			if($year == $year2 && $month >= $month2) return $this->pass_error('From month is greater');

			$q = $this->multiple_year($year, $month, $year2, $month2);
			// return $this->pass_error($q);

			if($year == $year2 && $month < $month2){
				$d = DB::table('vl_national_regimen')
				->select('year', DB::raw($raw))
				->leftJoin('viralprophylaxis', 'viralprophylaxis.ID', '=', 'vl_national_regimen.regimen')
				->where('year', $year)
				->whereBetween('month', [$month, $month2])
				->groupBy('year')
				->groupBy('name')
				->get();
			}

			if($year < $year2){
				$d = DB::table('vl_national_regimen')
				->select( DB::raw($raw))
				->leftJoin('viralprophylaxis', 'viralprophylaxis.ID', '=', 'vl_national_regimen.regimen')
				->whereRaw($q)
				->groupBy('name')
				->get();
			}

$desc = $this->describe_multiple($year, $month, $year2, $month2);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Period'] = $desc;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
			
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}


		return $this->check_data($data);

	}

	public function justification($type, $year, $month=NULL, $year2=NULL, $month2=NULL){

		$data = NULL;


		$raw = $this->justification_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('vl_national_justification')
			->select('year', DB::raw($raw))
			->leftJoin('viraljustifications', 'viraljustifications.ID', '=', 'vl_national_justification.justification')
			->where('year', $year)
			->groupBy('year')
			->groupBy('name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('vl_national_justification')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('viraljustifications', 'viraljustifications.ID', '=', 'vl_national_justification.justification')
			->where('year', $year)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('vl_national_justification')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('viraljustifications', 'viraljustifications.ID', '=', 'vl_national_justification.justification')
			->where('year', $year)
			->where('month', $month)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('vl_national_justification')
			->select('year', DB::raw($raw))
			->leftJoin('viraljustifications', 'viraljustifications.ID', '=', 'vl_national_justification.justification')
			->where('year', $year)
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('year')
			->groupBy('name')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Quarter'] = $a;
				$data[$i]['Period'] = $b;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
		}

		// For Multiple Months across years
		else if($type == 5){

			if($year > $year2) return $this->pass_error('From year is greater');
			if($year == $year2 && $month >= $month2) return $this->pass_error('From month is greater');

			$q = $this->multiple_year($year, $month, $year2, $month2);
			// return $this->pass_error($q);

			if($year == $year2 && $month < $month2){
				$d = DB::table('vl_national_justification')
				->select('year', DB::raw($raw))
				->leftJoin('viraljustifications', 'viraljustifications.ID', '=', 'vl_national_justification.justification')
				->where('year', $year)
				->whereBetween('month', [$month, $month2])
				->groupBy('year')
				->groupBy('name')
				->get();
			}

			if($year < $year2){
				$d = DB::table('vl_national_justification')
				->select( DB::raw($raw))
				->leftJoin('viraljustifications', 'viraljustifications.ID', '=', 'vl_national_justification.justification')
				->whereRaw($q)
				->groupBy('name')
				->get();
			}
			
$desc = $this->describe_multiple($year, $month, $year2, $month2);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Period'] = $desc;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
			
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}


		return $this->check_data($data);

	}

	public function gender($type, $year, $month=NULL, $year2=NULL, $month2=NULL){

		$data = NULL;


		$raw = $this->gender_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('vl_national_gender')
			->select('year', DB::raw($raw))
			->leftJoin('gender', 'gender.ID', '=', 'vl_national_gender.gender')
			->where('year', $year)
			->groupBy('year')
			->groupBy('name')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('vl_national_gender')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('gender', 'gender.ID', '=', 'vl_national_gender.gender')
			->where('year', $year)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('vl_national_gender')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('gender', 'gender.ID', '=', 'vl_national_gender.gender')
			->where('year', $year)
			->where('month', $month)
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('vl_national_gender')
			->select('year', DB::raw($raw))
			->leftJoin('gender', 'gender.ID', '=', 'vl_national_gender.gender')
			->where('year', $year)
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->groupBy('year')
			->groupBy('name')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Quarter'] = $a;
				$data[$i]['Period'] = $b;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
		}

		// For Multiple Months across years
		else if($type == 5){

			if($year > $year2) return $this->pass_error('From year is greater');
			if($year == $year2 && $month >= $month2) return $this->pass_error('From month is greater');

			$q = $this->multiple_year($year, $month, $year2, $month2);
			// return $this->pass_error($q);

			if($year == $year2 && $month < $month2){
				$d = DB::table('vl_national_gender')
				->select('year', DB::raw($raw))
				->leftJoin('gender', 'gender.ID', '=', 'vl_national_gender.gender')
				->where('year', $year)
				->whereBetween('month', [$month, $month2])
				->groupBy('year')
				->groupBy('name')
				->get();
			}

			if($year < $year2){
				$d = DB::table('vl_national_gender')
				->select( DB::raw($raw))
				->leftJoin('gender', 'gender.ID', '=', 'vl_national_gender.gender')
				->whereRaw($q)
				->groupBy('name')
				->get();
			}
			
$desc = $this->describe_multiple($year, $month, $year2, $month2);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Period'] = $desc;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
			
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}


		return $this->check_data($data);

	}

	public function age($type, $year, $month=NULL, $year2=NULL, $month2=NULL){

		$data = NULL;


		$raw = $this->age_query();
		

		// Totals for the whole year
		if($type == 1){

			$data = DB::table('vl_national_age')
			->select('year', DB::raw($raw))
			->leftJoin('agecategory', 'agecategory.ID', '=', 'vl_national_age.age')
			->where('year', $year)
			->whereRaw('(`age` = 0 OR `age` > 5 AND `age` < 12)')
			->groupBy('year')
			->groupBy('name')
			->orderBy('age')
			->get();

		}

		// For the whole year but has per month
		else if($type == 2){
			$data = DB::table('vl_national_age')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('agecategory', 'agecategory.ID', '=', 'vl_national_age.age')
			->where('year', $year)
			->whereRaw('(`age` = 0 OR `age` > 5 AND `age` < 12)')
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->orderBy('age')
			->get();
		}

		// For a particular month
		else if($type == 3){

			if($month < 1 || $month > 12) return $this->invalid_month($month);
			$data = DB::table('vl_national_age')
			->select('year', 'month', DB::raw($raw))
			->leftJoin('agecategory', 'agecategory.ID', '=', 'vl_national_age.age')
			->where('year', $year)
			->where('month', $month)
			->whereRaw('(`age` = 0 OR `age` > 5 AND `age` < 12)')
			->groupBy('month')
			->groupBy('year')
			->groupBy('name')
			->orderBy('age')
			->get();
		}

		// For a particular quarter
		// The month value will be used as the quarter value
		else if($type == 4){

			if($month < 1 || $month > 4) return $this->invalid_quarter($month);
			
			$my_range = $this->quarter_range($month);
			$lesser = $my_range[0];
			$greater = $my_range[1];

			$d = DB::table('vl_national_age')
			->select('year', DB::raw($raw))
			->leftJoin('agecategory', 'agecategory.ID', '=', 'vl_national_age.age')
			->where('year', $year)
			->where('month', '>', $lesser)
			->where('month', '<', $greater)
			->whereRaw('(`age` = 0 OR `age` > 5 AND `age` < 12)')
			->groupBy('year')
			->groupBy('name')
			->orderBy('age')
			->get();
			
			$a = "Q" . $month;
			$b = $this->quarter_description($month);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Quarter'] = $a;
				$data[$i]['Period'] = $b;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
		}

		// For Multiple Months across years
		else if($type == 5){

			if($year > $year2) return $this->pass_error('From year is greater');
			if($year == $year2 && $month >= $month2) return $this->pass_error('From month is greater');

			$q = $this->multiple_year($year, $month, $year2, $month2);
			// return $this->pass_error($q);

			if($year == $year2 && $month < $month2){
				$d = DB::table('vl_national_age')
				->select('year', DB::raw($raw))
				->leftJoin('agecategory', 'agecategory.ID', '=', 'vl_national_age.age')
				->where('year', $year)
				->whereBetween('month', [$month, $month2])
				->whereRaw('(`age` = 0 OR `age` > 5 AND `age` < 12)')
				->groupBy('year')
				->groupBy('name')
				->orderBy('age')
				->get();
			}

			if($year < $year2){
				$d = DB::table('vl_national_age')
				->select( DB::raw($raw))
				->leftJoin('agecategory', 'agecategory.ID', '=', 'vl_national_age.age')
				->whereRaw($q)
				->whereRaw('(`age` = 0 OR `age` > 5 AND `age` < 12)')
				->groupBy('name')
				->orderBy('age')
				->get();
			}
			
$desc = $this->describe_multiple($year, $month, $year2, $month2);

			for ($i=0; $i < sizeof($d); $i++) { 
				$data[$i]['Period'] = $desc;
				foreach ($d[$i] as $obj_prop => $ob_val) {
					$data[$i][$obj_prop] = $ob_val;
				}
			}
			
		}

		// Else an invalid type has been specified
		else{
			return $this->invalid_type($type);
		}


		return $this->check_data($data);

	}





}
