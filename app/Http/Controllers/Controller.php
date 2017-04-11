<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    protected function pass_message($message){
    	return response()
            ->json([
                'error' => 500,
                'message' => 'Month ' . $message . ' is invalid. Value must be between 1 and 12.'
            ]);
    }

    protected function invalid_month($month){
		return response()
            ->json([
                'error' => 500,
                'message' => 'Month ' . $month . ' is invalid. Value must be between 1 and 12.'
            ]);
	}

	protected function invalid_quarter($quarter){
		return response()
            ->json([
                'error' => 500,
                'message' => 'Quarter ' . $quarter . ' is invalid. Value must be between 1 and 4.'
            ]);
	}

	protected function invalid_type($type){
		return response()
            ->json([
                'error' => 500,
                'message' => 'Type ' . $type . ' is invalid. Value must be between 1 and 4. 1 is for the total for the whole year. 2 is for the year with data grouped by month. 3 is for the a particular month. 4 for a particular quarter.'
            ]);
	}

	protected function quarter_range($month){
		$greater;
		$lesser;

		switch ($month) {
			case 1:
				$greater = 4;
				$lesser = 0;
				break;
			case 2:
				$greater = 7;
				$lesser = 3;
				break;
			case 3:
				$greater = 10;
				$lesser = 6;
				break;
			case 4:
				$greater = 13;
				$lesser = 9;
				break;
			
			default:
				// return $this->invalid_quarter($month);
				break;
		}

		return array($lesser, $greater);
	}

	protected function summary_query(){
		return 'SUM(alltests) as all_tests,
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
	}

	protected function hei_validation_query(){
		return 'SUM(validation_confirmedpos) as confirmed_pos,
		 SUM(validation_repeattest) as repeat_test, 
		 SUM(validation_viralload) as viral_load,
		  SUM(validation_adult) as adult, 
		  SUM(validation_unknownsite) as unknown_facility';
	}

	protected function hei_outcomes_query(){
		return 'SUM(ltfu) as lost_to_follow_up,
		 SUM(dead) as dead, 
		 SUM(adult) as adult,
		  SUM(transout) as transferred_out, 
		  SUM(other) as other';
	}

	protected function age_breakdown_query(){
		return 'SUM(nodatapos) as no_data_pos,
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
	}


	protected function entry_point_query(){
		return 'SUM(pos) as positives, 
		 SUM(neg) as negatives,
		  entry_points.name';
	}

	protected function mother_prophylaxis_query(){
		return 'SUM(pos) as positives, 
		 SUM(neg) as negatives,
		  prophylaxis.name';
	}

	protected function infant_prophylaxis_query(){
		return 'SUM(pos) as positives, 
		 SUM(neg) as negatives,
		  prophylaxis.name';
	}


}
