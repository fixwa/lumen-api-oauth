<?php

namespace App\Http\Controllers;

use App\User;
use App\UserAccessToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\InvalidCredentialsException;
use LucaDegasperi\OAuth2Server\Authorizer;

class AuthController extends Controller
{

    protected $authorizer;

    public function __construct(Authorizer $authorizer)
    {
        $this->middleware('oauth', ['except' => ['signup', 'signin', 'refreshToken']]);

        $this->authorizer = $authorizer;
    }

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

        $auth = $this->authorizer->issueAccessToken();

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

        try {
            $token = $this->authorizer->issueAccessToken();
        } catch (InvalidCredentialsException $exception) {
            return $this->error($exception->getMessage());
        }

        // @todo Check if we can find the user from the Authorizer somehow.
        $user = UserAccessToken::find($token['access_token'])->session->user;

        if (!$user) {
            return $this->error('Wrong email or password.');
        }

        return $this->success((object)[
            "token" => $token,
            "user_profile" => $user,
        ], 201);
    }

    public function logout(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return $this->error('Not you. Wtf.');
        }

        $this->authorizer->getAccessToken()->expire();

        return $this->success('Goodbye.');
    }

    public function deleteTokens(Request $request)
    {

    }

    public function refreshToken()
    {
        return response()->json($this->authorizer->issueAccessToken());
    }
}