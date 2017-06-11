<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'v1'], function(Router $api) {

        $api->group(['prefix' => 'auth'], function(Router $api) {
            $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
                $api->post('signup', 'App\\Api\\V1\\Controllers\\Auth\\SignUpController@signUp');
                $api->get('users', 'App\\Api\\V1\\Controllers\\Auth\\SignUpController@users');
                $api->get('user_types', 'App\\Api\\V1\\Controllers\\Auth\\SignUpController@user_types');
            });
		$api->get('test', 'App\\Api\\V1\\Controllers\\Auth\\SignUpController@test');
		 $api->get('test2', 'App\\Api\\V1\\Controllers\\Auth\\SignUpController@test2');
            $api->post('login', 'App\\Api\\V1\\Controllers\\Auth\\LoginController@login');

            $api->post('recovery', 'App\\Api\\V1\\Controllers\\Auth\\ForgotPasswordController@sendResetEmail');
            $api->post('reset', 'App\\Api\\V1\\Controllers\\Auth\\ResetPasswordController@resetPassword');
        });

        $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
            $api->get('protected', function() {
                return response()->json([
                    'message' => 'Access to this item is only for authenticated user. Provide a token in your request!'
                ]);
            });

            $api->get('refresh', ['middleware' => 'jwt.refresh', function() {
                    return response()->json([
                        'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                    ]);
                }
            ]);
        });

    

        // $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
            $api->group(['middleware' => 'api.throttle', 'limit' => 5, 'expires' => 1], function(Router $api) {

                $api->get('hello', function() {
                    return response()->json([
                        'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
                    ]);
                });


                $api->group(['prefix' => 'national'], function(Router $api) {

                    $api->get('summary/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\NationalController@summary');

                    $api->get('hei/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\NationalController@hei_outcomes');

                    $api->get('hei_validation/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\NationalController@hei_validation');

                    $api->get('age_breakdown/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\NationalController@age_breakdown');

                    $api->get('entry_point/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\NationalController@entry_point');

                    $api->get('mprophylaxis/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\NationalController@mother_prophylaxis');

                    $api->get('iprophylaxis/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\NationalController@infant_prophylaxis');
                });

                $api->group(['prefix' => 'county'], function(Router $api) {

                    $api->get('counties', 'App\\Api\\V1\\Controllers\\CountyController@counties');

                    $api->get('info/{county}/', 'App\\Api\\V1\\Controllers\\CountyController@info');

                    $api->get('summary/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@summary');

                    $api->get('hei/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@hei_outcomes');

                    $api->get('hei_validation/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@hei_validation');

                    $api->get('age_breakdown/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@age_breakdown');

                    $api->get('entry_point/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@entry_point');

                    $api->get('mprophylaxis/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@mother_prophylaxis');

                    $api->get('iprophylaxis/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@infant_prophylaxis');

                    $api->get('county_facilities/{county}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\CountyController@county_sites');
                });


                $api->group(['prefix' => 'subcounty'], function(Router $api) {

                    $api->get('subcounties', 'App\\Api\\V1\\Controllers\\SubcountyController@subcounties');

                    $api->get('info/{subcounty}/', 'App\\Api\\V1\\Controllers\\SubcountyController@info');

                    $api->get('summary/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@summary');

                    $api->get('hei/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@hei_outcomes');

                    $api->get('hei_validation/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@hei_validation');

                    $api->get('age_breakdown/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@age_breakdown');

                    $api->get('entry_point/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@entry_point');

                    $api->get('mprophylaxis/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@mother_prophylaxis');

                    $api->get('iprophylaxis/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@infant_prophylaxis');

                    $api->get('subcounty_facilities/{subcounty}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SubcountyController@subcounty_sites');
                });

                $api->group(['prefix' => 'partner'], function(Router $api) {

                    $api->get('partners', 'App\\Api\\V1\\Controllers\\PartnerController@partners');

                    $api->get('info/{partner}/', 'App\\Api\\V1\\Controllers\\PartnerController@info');

                    $api->get('summary/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@summary');

                    $api->get('hei/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@hei_outcomes');

                    $api->get('hei_validation/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@hei_validation');

                    $api->get('age_breakdown/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@age_breakdown');

                    $api->get('entry_point/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@entry_point');

                    $api->get('mprophylaxis/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@mother_prophylaxis');

                    $api->get('iprophylaxis/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@infant_prophylaxis');

                    $api->get('partner_facilities/{partner}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\PartnerController@partner_sites');
                });

                $api->group(['prefix' => 'lab'], function(Router $api) {

                    $api->get('labs', 'App\\Api\\V1\\Controllers\\LabController@labs');

                    $api->get('info/{lab}/', 'App\\Api\\V1\\Controllers\\LabController@info');

                    $api->get('summary/{lab}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\LabController@summary');

                });

                $api->group(['prefix' => 'facility'], function(Router $api) {

                    $api->get('facilities', 'App\\Api\\V1\\Controllers\\SiteController@sites');

                    $api->get('unsupported_facilities', 'App\\Api\\V1\\Controllers\\SiteController@unsupported_sites');

                    $api->get('info/{site}/', 'App\\Api\\V1\\Controllers\\SiteController@info');

                    $api->get('summary/{site}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SiteController@summary');

                    $api->get('hei/{site}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SiteController@hei_outcomes');

                    $api->get('hei_validation/{site}/{type}/{year}/{month?}/{year2?}/{month2?}', 'App\\Api\\V1\\Controllers\\SiteController@hei_validation');

                });

                $api->group(['prefix' => 'patient'], function(Router $api) {
                    $api->get('results/{site}/{patientID}', 'App\\Api\\V1\\Controllers\\PatientController@get_results');

                });

            });

        // });
    });
});
