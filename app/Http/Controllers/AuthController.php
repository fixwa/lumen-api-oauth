<?php

namespace App\Http\Controllers;

use App\User;
use App\UserAccessToken;
use App\UserSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * Creates a new User and returns an authorization Token as well.
     * Example Post Request params:
     *  {
     *   "email":"fixwah@gmail.com",
     *   "password":"12345678",
     *   "name": "Pablito",
     *   "client_id": "id0",
     *   "client_secret":"secret0",
     *   "grant_type": "password"
     *   }
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(Request $request)
    {
        $this->validateRequest($request);

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'imageUrl' => 'http://laburen.com/default_user.png'
        ]);

        $request->offsetSet('username', $request->get('email'));

        $auth = app('oauth2-server.authorizer')->issueAccessToken();

        return $this->success([
            "token" => $auth,
            "user_profile" => $user
        ], 201);
    }

    /**
     * Returns a valid token and user information on success.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signin(Request $request)
    {
        $rules = [
            'username' => 'required|email',
            'password' => 'required'
        ];

        try {
            $this->validate($request, $rules);
        } catch (ValidationException $exception) {
            $errorObj = (object)[
                'type' => $exception->getMessage(),
                'description' => $exception->response->getContent(),
            ];
            return $this->error($errorObj);
        }

        $user = null;
        /* @var $authorizer \LucaDegasperi\OAuth2Server\Authorizer */
        $authorizer = app('oauth2-server.authorizer');
        $auth = $authorizer->issueAccessToken();

        $user = UserAccessToken::find($auth['access_token'])->session->user;

        return $this->success([
            "token" => $auth,
            "user_profile" => $user
        ], 201);
    }
}