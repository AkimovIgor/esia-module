<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
if (Module::isEnabled('Esia')) {
    Route::prefix('plugins/esia')->middleware(['auth', 'role:admin'])->group(function() {
        Route::get('/', 'DashboardController@index');
        Route::post('/config', 'DashboardController@saveConfig');
    });

    Route::group([], function () {
        Route::get('/esia/login/callback', 'EsiaLoginController@handleProviderCallback');

        Route::get('/esia/login', 'EsiaLoginController@redirectToProvider');

        Route::match(
            ['get', 'post'],
            '/esia/categories/prns/{oid?}',
            'EsiaController@getCategories'
        );

        Route::match(
            ['get', 'post'],
            '/esia/prns/{collection_name?}/{collection_entity_id?}/{oid?}',
            'EsiaController@getPersons'
        );

        // 7.1.1

        Route::match(
            ['get', 'post'],
            '/esia/edit/orgs/{org_id}',
            'EsiaController@updateOrganizationForm'
        );

        // 7.1.2

        Route::match(
            ['get', 'post'],
            '/esia/add/orgs/{org_id}/ctts',
            'EsiaController@addOrganizationContact'
        );

        Route::match(
            ['get', 'post'],
            '/esia/edit/organization/{org_id}/ctts/{contact_id}',
            'EsiaController@updateOrganizationContact'
        );

        Route::match(
            ['get', 'post'],
            '/esia/delete/organization/{org_id}/ctts/{contact_id}',
            'EsiaController@deleteOrganizationContact'
        );

        // 7.1.3

        Route::match(
            ['get', 'post'],
            '/esia/add/organization/{org_id}/addrs',
            'EsiaController@addOrganizationAddress'
        );

        Route::match(
            ['get', 'post'],
            '/esia/edit/organization/{org_id}/addrs/{address_id}',
            'EsiaController@updateOrganizationAddress'
        );

        Route::match(
            ['get', 'post'],
            '/esia/delete/organization/{org_id}/addrs/{address_id}',
            'EsiaController@deleteOrganizationAddress'
        );

        // 7.1.4

        Route::match(
            ['get', 'post'],
            '/esia/add/organization/{org_id}/vhls',
            'EsiaController@addOrganizationVehicle'
        );

        Route::match(
            ['get', 'post'],
            '/esia/edit/organization/{org_id}/vhls/{vehicle_id}',
            'EsiaController@updateOrganizationVehicle'
        );

        Route::match(
            ['get', 'post'],
            '/esia/delete/organization/{org_id}/vhls/{vehicle_id}',
            'EsiaController@deleteOrganizationVehicle'
        );

        // 7.2

        Route::match(
            ['get', 'post'],
            '/esia/orgs/{org_id}/invts',
            'EsiaController@getOrganizationInvites'
        );

        Route::match(
            ['get', 'post'],
            '/esia/orgs/{org_id}/invites/send',
            'EsiaController@sendOrganizationInvite'
        );

        Route::match(
            ['get', 'post'],
            '/esia/organization/{org_id}/invites/{invite_id}/delete',
            'EsiaController@deleteOrganizationInvite'
        );

        // 7.3

        Route::match(
            ['post'],
            '/esia/orgs/{org_id}/emps/{employee_id}',
            'EsiaController@updateOrganizationEmployee'
        );

        Route::match(
            ['delete'],
            '/esia/orgs/{org_id}/emps/{employee_id}/delete',
            'EsiaController@deleteOrganizationEmployee'
        );

        // 7.4

        // 7.5

        // 7.6

        Route::match(
            ['get', 'post'],
            '/esia/organization/{org_id}/branches/add',
            'EsiaController@addOrganizationBranch'
        );

        Route::match(
            ['get', 'post'],
            '/esia/organization/{org_id}/branches/{branch_id}/edit',
            'EsiaController@updateOrganizationBranch'
        );

        // 8

        Route::match(
            ['get', 'post'],
            '/esia/upd_since/{type}',
            'EsiaController@getPersonsUpdatedSince'
        );

        // 9

        Route::match(
            ['get', 'post'],
            '/esia/account/import',
            'EsiaController@importUserAccount'
        );

        // 11.1 - 11.2

        Route::get('esia/avatar/{avatar_type}', 'EsiaController@getUserAvatar');

        // 6

        Route::get('esia/categories/orgs/{oid}', 'EsiaController@getOrganizationCategories');

        Route::match(
            ['get', 'post'],
            '/esia/orgs/{org_id}/{collection_name?}/{collection_entity_id?}/{groups?}/{group_id?}/{it_sys?}',
            'EsiaController@getOrganization'
        );
        Route::get('/esia/person', 'EsiaController@getPerson');
//        Route::get('/esia/persons', 'EsiaController@getPersons');
        Route::get('/esia/config', 'EsiaController@getConfig');
        Route::get('/esia/roles', 'EsiaController@getRoles');
//        Route::get('/esia/organization/{org_id}', 'EsiaController@getOrganization');
    });
}

