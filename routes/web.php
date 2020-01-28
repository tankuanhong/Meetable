<?php

// Check whether setup has been completed and define installer routes if not
if($_ENV['APP_NAME'] == 'Meetable Installer') {

Route::get('/', 'Setup\Controller@index')->name('setup.redirect');
Route::get('/setup', 'Setup\Controller@setup')->name('setup.setup');
Route::get('/setup/db', 'Setup\Controller@database')->name('setup.database');
Route::post('/setup/db-test', 'Setup\Controller@test_database')->name('setup.test-database');
Route::get('/setup/app-settings', 'Setup\Controller@app_settings')->name('setup.app-settings');
Route::post('/setup/app-settings', 'Setup\Controller@save_app_settings')->name('setup.save-app-settings');
Route::get('/setup/auth-settings', 'Setup\Controller@auth_settings')->name('setup.auth-settings');
Route::post('/setup/auth-settings', 'Setup\Controller@save_auth_settings')->name('setup.save-auth-settings');
Route::get('/setup/save', 'Setup\Controller@save_config')->name('setup.save-config');
Route::get('/setup/database', 'Setup\Controller@create_database_error')->name('setup.create-database');

} else {

Route::get('/', 'Controller@index')->name('index');

######
## SETUP ROUTES
## If they hit reload after setup, send them back to the home page
Route::get('/setup/save', function(){ return redirect('/'); });
## Create the database here
Route::get('/setup/database', 'Setup\Controller@create_database')->name('setup.create-database');
######


Route::get('/archive', 'Controller@archive')->name('archive');

Route::get('/{year}/{month}/{slug}-{key}', 'Controller@event')->name('event');
Route::get('/{year}/{month}/{key}', 'Controller@event')->name('event-short');

Route::get('/{year}/{month}/{day}', 'Controller@index')->name('day');
Route::get('/{year}/{month}', 'Controller@index')->name('month');
Route::get('/{year}', 'Controller@index')->name('year');

Route::get('/{year}/{month}/{partial_slug}', 'Controller@find_matching_events');

Route::get('/tag/{tag}', 'Controller@tag')->name('tag');
Route::get('/tag/{tag}/archive', 'Controller@tag_archive')->name('tag-archive');
Route::get('/tags', 'Controller@tags')->name('tags');

Route::get('/local-time', 'Controller@local_time')->name('local_time');

if(env('ENABLE_WEBMENTION_RESPONSES')) {
    Route::get('/webmention', 'WebmentionController@get');
    Route::post('/webmention', 'WebmentionController@webmention')->name('webmention');
}

Route::get('/add-to-google/{event}', 'Controller@add_to_google')->name('add-to-google');

Route::get('/login', 'AuthController@login')->name('login');
Route::get('/logout', 'AuthController@logout')->name('logout');
Route::get('/auth/github', 'GitHubController@callback')->name('github-oauth-redirect');

Route::get('/img/{settings}/{image}', 'ImageController@render')
  ->where(['image' => '.+']);

Route::get('/{key}', 'Controller@event_shorturl');

Route::middleware('auth')->group(function(){

    Route::get('/new', 'EventController@new_event')->name('new-event');

    Route::post('/create', 'EventController@create_event')->name('create-event');
    Route::get('/event/{event}', 'EventController@edit_event')->name('edit-event');
    Route::post('/event/{event}/save', 'EventController@save_event')->name('save-event');
    Route::get('/event/{event}/history', 'EventController@event_history')->name('event-history');
    Route::get('/event/{event}/clone', 'EventController@clone_event')->name('clone-event');
    Route::post('/event/{event}/delete', 'EventController@delete_event')->name('delete-event');

    Route::post('/event/timezone', 'EventController@get_timezone')->name('get-timezone');

    Route::get('/event/{event}/photo', 'EventController@add_event_photo')->name('add-event-photo');
    Route::post('/event/{event}/photo', 'EventController@upload_event_photo')->name('upload-event-photo');
    Route::post('/event/{event}/photo_order', 'EventController@set_photo_order')->name('set-photo-order');

    Route::post('/event/cover_image', 'EventController@upload_event_cover_image')->name('upload-event-cover-image');

    Route::get('/event/{event}/responses', 'EventController@edit_responses')->name('edit-responses');
    Route::post('/event/{event}/responses/{response}/delete', 'EventController@delete_response')->name('delete-response');
    Route::get('/event/{event}/responses/{response}.json', 'EventController@get_response_details')->name('get-response-details');
    Route::post('/event/{event}/responses/save_alt_text', 'EventController@save_alt_text')->name('save-alt-text');

    Route::post('/event/{event}/rsvp', 'EventResponseController@save_rsvp')->name('event-rsvp');
    Route::post('/event/{event}/rsvp_delete', 'EventResponseController@delete_rsvp')->name('event-rsvp-delete');

    Route::middleware('can:manage-site')->group(function(){
        Route::get('/settings', 'SettingsController@get')->name('settings');
        Route::post('/settings/save', 'SettingsController@post')->name('settings-save');
    });

});

}
