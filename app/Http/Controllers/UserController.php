<?php 

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller{

	public function __construct(){

		$this->middleware('oauth', ['except' => ['index', 'show', 'store', 'signup']]);
		$this->middleware('authorize:' . __CLASS__, ['except' => ['index', 'show', 'store', 'signup']]);
	}

	public function index(){

		$users = User::all();
		return $this->success($users, 200);
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
            'password'=> Hash::make($request->get('password')),
            'imageUrl' => 'http://laburen.com/default_user.png'
        ]);

        $request->offsetSet('username', $request->get('email'));

        $auth = app('oauth2-server.authorizer')->issueAccessToken();

        return $this->success([
            "token" => $auth,
            "user_profile"=> $user
        ], 201);
	}

	public function store(Request $request){

		$this->validateRequest($request);

		$user = User::create([
		            'name' => $request->get('name'),
					'email' => $request->get('email'),
					'password'=> Hash::make($request->get('password'))
				]);

		return $this->success([
            "message" => "The user with with id {$user->id} has been created",
            "status" => "AUTHENTICATED",
        ], 201);
	}

	public function show($id){

		$user = User::find($id);

		if(!$user){
			return $this->error("The user with {$id} doesn't exist", 404);
		}

		return $this->success($user, 200);
	}

	public function update(Request $request, $id){

		$user = User::find($id);

		if(!$user){
			return $this->error("The user with {$id} doesn't exist", 404);
		}

		$this->validateRequest($request);

		$user->email 		= $request->get('email');
		$user->password 	= Hash::make($request->get('password'));

		$user->save();

		return $this->success("The user with with id {$user->id} has been updated", 200);
	}

	public function destroy($id){

		$user = User::find($id);

		if(!$user){
			return $this->error("The user with {$id} doesn't exist", 404);
		}

		$user->delete();

		return $this->success("The user with with id {$id} has been deleted", 200);
	}

	public function validateRequest(Request $request){

		$rules = [
            'name' => 'required|min:5',
			'email' => 'required|email|unique:users', 
			'password' => 'required|min:6'
		];

		try {
            $this->validate($request, $rules);
        } catch (ValidationException $exception) {
		    $errorObj = (object) [
		        'type' => $exception->getMessage(),
                'description' => $exception->response->getContent(),
            ];
		    return new JsonResponse($errorObj, 406);
        }
	}

	public function isAuthorized(Request $request){

		$resource = "users";
		// $user     = User::find($this->getArgs($request)["user_id"]);

		return $this->authorizeUser($request, $resource);
	}
}