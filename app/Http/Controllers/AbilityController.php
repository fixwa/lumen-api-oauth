<?php
/**
 * User: pablo <fixwah@gmail.com>
 * Date: 04/09/2017
 * Time: 05:09 PM
 */

namespace App\Http\Controllers;

use App\Tweet;
use App\UserAbility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbilityController extends Controller
{

    public function __construct()
    {
        $this->middleware('oauth');
    }

    /**
     * List of all Tweets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $userId = $this->getUserId();

        $abilities = UserAbility::whereNull('deleted_at')
            ->where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($abilities);
    }

    /**
     * Store a NEW User Ability.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $tweet = UserAbility::create([
            'name' => $request->get('name'),
            'user_id' => $this->getUserId(),
            'created_at' => Carbon::now(),
        ]);

        return $this->success($tweet, 201);
    }

    private function validateRequest(Request $request)
    {

        $rules = [
            'name' => 'required'
        ];

        $this->validate($request, $rules);
    }

    /**
     * Returns ONE Tweet.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Request $request, $id)
    {
        $tweet = Tweet::where('id', $id)->with('user')->first();

        if (!$tweet) {
            return $this->error("The Tweet with {$id} doesn't exist", 404);
        }

        return $this->success($tweet, 200);
    }

    /**
     * Updates Tweet.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $tweet = Tweet::where('id', $id)->with('user')->first();

        if (!$tweet) {
            return $this->error("The Tweet with {$id} doesn't exist", 404);
        }

        $this->validateRequest($request);

        $tweet->body = $request->get('body');
        $tweet->feel = $request->get('feel');

        $tweet->save();

        return $this->success($tweet, 200);
    }

    /**
     * Deletes one Tweet.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $tweet = Tweet::where('id', $id)->with('user')->first();

        if (!$tweet) {
            return $this->error("The Tweet with {$id} doesn't exist", 404);
        }

        $tweet->delete();

        return $this->success("Deleted", 200);
    }
}