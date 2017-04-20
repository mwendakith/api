<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\Auth\\SignUpController@signUp');
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

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
    });

    

    //$api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->group(['middleware' => 'api.throttle', 'limit' => 10, 'expires' => 1], function(Router $api) {

            $api->get('hello', function() {
                return response()->json([
                    'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
                ]);
            });


            $api->group(['prefix' => 'national'], function(Router $api) {

                $api->get('summary/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\NationalController@summary');

                $api->get('hei/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\NationalController@hei_outcomes');

                $api->get('hei_validation/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\NationalController@hei_validation');

                $api->get('age_breakdown/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\NationalController@age_breakdown');

                $api->get('entry_point/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\NationalController@entry_point');

                $api->get('mprophylaxis/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\NationalController@mother_prophylaxis');

                $api->get('iprophylaxis/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\NationalController@infant_prophylaxis');
            });

            $api->group(['prefix' => 'county'], function(Router $api) {

                $api->get('counties', 'App\\Api\\V1\\Controllers\\CountyController@counties');

                $api->get('info/{county}/', 'App\\Api\\V1\\Controllers\\CountyController@info');

                $api->get('summary/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@summary');

                $api->get('hei/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@hei_outcomes');

                $api->get('hei_validation/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@hei_validation');

                $api->get('age_breakdown/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@age_breakdown');

                $api->get('entry_point/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@entry_point');

                $api->get('mprophylaxis/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@mother_prophylaxis');

                $api->get('iprophylaxis/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@infant_prophylaxis');

                $api->get('county_facilities/{county}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\CountyController@county_sites');
            });


            $api->group(['prefix' => 'subcounty'], function(Router $api) {

                $api->get('subcounties', 'App\\Api\\V1\\Controllers\\SubcountyController@subcounties');

                $api->get('info/{subcounty}/', 'App\\Api\\V1\\Controllers\\SubcountyController@info');

                $api->get('summary/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@summary');

                $api->get('hei/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@hei_outcomes');

                $api->get('hei_validation/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@hei_validation');

                $api->get('age_breakdown/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@age_breakdown');

                $api->get('entry_point/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@entry_point');

                $api->get('mprophylaxis/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@mother_prophylaxis');

                $api->get('iprophylaxis/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@infant_prophylaxis');

                $api->get('subcounty_facilities/{subcounty}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SubcountyController@subcounty_sites');
            });

            $api->group(['prefix' => 'partner'], function(Router $api) {

                $api->get('partners', 'App\\Api\\V1\\Controllers\\PartnerController@partners');

                $api->get('info/{partner}/', 'App\\Api\\V1\\Controllers\\PartnerController@info');

                $api->get('summary/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@summary');

                $api->get('hei/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@hei_outcomes');

                $api->get('hei_validation/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@hei_validation');

                $api->get('age_breakdown/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@age_breakdown');

                $api->get('entry_point/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@entry_point');

                $api->get('mprophylaxis/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@mother_prophylaxis');

                $api->get('iprophylaxis/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@infant_prophylaxis');

                $api->get('partner_facilities/{partner}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\PartnerController@partner_sites');
            });

            $api->group(['prefix' => 'lab'], function(Router $api) {

                $api->get('labs', 'App\\Api\\V1\\Controllers\\LabController@labs');

                $api->get('info/{lab}/', 'App\\Api\\V1\\Controllers\\LabController@info');

                $api->get('summary/{lab}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\LabController@summary');

            });

            $api->group(['prefix' => 'facility'], function(Router $api) {

                $api->get('facilities', 'App\\Api\\V1\\Controllers\\SiteController@sites');

                $api->get('unsupported_facilities', 'App\\Api\\V1\\Controllers\\SiteController@unsupported_sites');

                $api->get('info/{site}/', 'App\\Api\\V1\\Controllers\\SiteController@info');

                $api->get('summary/{site}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SiteController@summary');

                $api->get('hei/{site}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SiteController@hei_outcomes');

                $api->get('hei_validation/{site}/{year}/{type}/{month?}', 'App\\Api\\V1\\Controllers\\SiteController@hei_validation');

            });

        });

    //});


});
