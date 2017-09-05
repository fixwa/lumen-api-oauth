<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// Home page
$app->get('/', function () use ($app) {
    return;
});

$app->get('/version', function () use ($app) {
    return $app->version();
});

// Posts
$app->get('/posts', 'PostController@index');
$app->post('/posts', 'PostController@store');
$app->get('/posts/{post_id}', 'PostController@show');
$app->put('/posts/{post_id}', 'PostController@update');
$app->patch('/posts/{post_id}', 'PostController@update');
$app->delete('/posts/{post_id}', 'PostController@destroy');

// Users
$app->get('/users/', 'UserController@index');
$app->post('/users/', 'UserController@store');
$app->get('/users/{user_id}', 'UserController@show');
$app->put('/user/profile', 'UserController@update');
$app->patch('/users/{user_id}', 'UserController@update');
$app->post('/user/profile/image', 'UserController@updateImage');
$app->delete('/users/{user_id}', 'UserController@destroy');

// Comments
$app->get('/comments', 'CommentController@index');
$app->get('/comments/{comment_id}', 'CommentController@show');

// Comment(s) of a post
$app->get('/posts/{post_id}/comments', 'PostCommentController@index');
$app->post('/posts/{post_id}/comments', 'PostCommentController@store');
$app->put('/posts/{post_id}/comments/{comment_id}', 'PostCommentController@update');
$app->patch('/posts/{post_id}/comments/{comment_id}', 'PostCommentController@update');
$app->delete('/posts/{post_id}/comments/{comment_id}', 'PostCommentController@destroy');

// Request an access token
$app->post('/oauth/access_token', function () use ($app) {
    return response()->json($app->make('oauth2-server.authorizer')->issueAccessToken());
});


$app->post('/signup', 'AuthController@signup');
$app->post('/signin', 'AuthController@signin');
$app->delete('/logout', 'AuthController@logout');
$app->delete('/tokens', 'AuthController@deleteTokens');
$app->post('/refreshtoken', 'AuthController@refreshToken');


// Markers
$app->get('/markers', 'MarkerController@index');


// Tweets
$app->get('/tweet', 'TweetController@index');
$app->post('/tweet', 'TweetController@store');
$app->get('/tweet/{id}', 'TweetController@view');
$app->put('/tweet/{id}', 'TweetController@update');
$app->delete('/tweet/{id}', 'TweetController@destroy');
/**
 * app.get(prefix_api + '/tweet', userController.apiRequestAuthorization, tweetController.getListOfTweets);
 * app.post(prefix_api + '/tweet', userController.apiRequestAuthorization, tweetController.createNewTweet);
 * app.get(prefix_api + '/tweet/:id', userController.apiRequestAuthorization, tweetController.getTweetById);
 * app.put(prefix_api + '/tweet/:id', userController.apiRequestAuthorization, tweetController.updateTweet);
 * app.delete(prefix_api + '/tweet/:id', userController.apiRequestAuthorization, tweetController.deleteTweetById);
 */