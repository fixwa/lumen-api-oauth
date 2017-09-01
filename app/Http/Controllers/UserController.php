<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function __construct()
    {
        /**
         * @see \App\Http\Middleware\Authenticate
         * Guarded routes: (Requires the Authentication (Bearer) header)
         */
        $this->middleware('oauth', ['except' => ['index', 'show', 'store', 'update']]);

        /**
         * @see \App\Http\Middleware\Authorize
         * @see this controller' UserController::isAuthorized() method
         *
         */
        $this->middleware('authorize:' . __CLASS__, ['except' => ['index', 'show', 'update']]);
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

    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateImage(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return $this->error("The user with {$user->id} doesn't exist", 404);
        }

        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');

            if ($photoFile->isValid()) {
                $rPath = '/uploads/users/' . $user->id . '/profile/';
                $uploadPath = base_path('/public' . $rPath);
                $fileName = 'profile.' . strtolower($photoFile->getClientOriginalExtension());

                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, $mode = 0777, true, true);
                } else if (File::exists($uploadPath . $fileName)) {
                    File::move($uploadPath . $fileName, $uploadPath . str_ireplace('profile', 'profile_' . time(), $fileName));
                }

                $filePath = $request->photo->move($uploadPath, $fileName);
                $url = env('APP_HOST') . $rPath . '/' . $fileName;
                $user->imageUrl = $url;
                $user->save();

                Log::info("Updated profile picture for User ID: [{$user->id}] for file: [{$filePath}]");
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

        if ($request->has('user_id')) {
            $user = User::find($request->get('user_id'));
        } else {
            $user = User::find($this->getUserId());
        }

        return $this->authorizeUser($request, $resource, $user);
    }
}