<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function __construct()
    {

        // @see \App\Http\Middleware\Authenticate
        // Guarded routes:
        //  $this->middleware('oauth', ['except' => ['index', 'show', 'store', 'signup', 'update']]);

        // @see \App\Http\Middleware\Authorize
        // @see this controller' isAuthorized() method
        // $this->middleware('authorize:' . __CLASS__, ['except' => ['index', 'show', 'store', 'signup', 'update']]);
    }

    public function index()
    {

        $users = User::all();
        return $this->success($users, 200);
    }

    public function store(Request $request)
    {

        $this->validateRequest($request);

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);

        return $this->success([
            "message" => "The user with with id {$user->id} has been created",
            "status" => "AUTHENTICATED",
        ], 201);
    }

    public function show($id)
    {

        $user = User::find($id);

        if (!$user) {
            return $this->error("The user with {$id} doesn't exist", 404);
        }

        return $this->success($user, 200);
    }

    public function update(Request $request)
    {

        $id = $request->get('id');

        $user = User::find($id);

        if (!$user) {
            return $this->error("The user with {$id} doesn't exist", 404);
        }

        $this->validateRequest($request);

        if ($request->has('name')) {
            $user->name = $request->get('name');
        }
        if ($request->has('email')) {
            $user->name = $request->get('email');
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
        }

        $user->save();

        return $this->success((object)[
            "message" => "The user with with id {$user->id} has been updated"
        ], 200);
    }

    public function updateImage(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->error("The user with {$userId} doesn't exist", 404);
        }

        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');

            if ($photoFile->isValid()) {
                $rPath = '/uploads/users/' . $userId . '/profile/';
                $uploadPath = base_path('/public' . $rPath);
                $fileName = 'profile.' . strtolower($photoFile->getClientOriginalExtension());

                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, $mode = 0777, true, true);
                } else if (File::exists($uploadPath . $fileName)) {
                    File::move($uploadPath . $fileName, $uploadPath . str_ireplace('profile', 'profile_' . time(), $fileName));
                }

                $request->photo->move($uploadPath, $fileName);
                $url = env('APP_HOST') . $rPath . '/' . $fileName;
                $user->imageUrl = $url;
                $user->save();
            }
        }
        return $this->success($user, 200);
    }

    public function destroy($id)
    {

        $user = User::find($id);

        if (!$user) {
            return $this->error("The user with {$id} doesn't exist", 404);
        }

        $user->delete();

        return $this->success("The user with with id {$id} has been deleted", 200);
    }

    public function validateRequest(Request $request)
    {

        $rules = [
            'name' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ];

        try {
            $this->validate($request, $rules);
        } catch (ValidationException $exception) {
            $errorObj = (object)[
                'type' => $exception->getMessage(),
                'description' => $exception->response->getContent(),
            ];
            return new JsonResponse($errorObj, 406);
        }
    }

    public function isAuthorized(Request $request)
    {

        $resource = "users";
        // $user     = User::find($this->getArgs($request)["user_id"]);

        return $this->authorizeUser($request, $resource);
    }
}