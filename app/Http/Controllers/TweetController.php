<?php
/**
 * User: pablo <fixwah@gmail.com>
 * Date: 04/09/2017
 * Time: 05:09 PM
 */

namespace App\Http\Controllers;

use App\Tweet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TweetController extends Controller
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
    public function index()
    {
        $tweets = Tweet::whereNull('deleted_at')->with('user')->get();

        return response()->json($tweets);
    }

    /**
     * Store a NEW Tweet.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);
        $feel = $request->get('feel');

        $tweet = Tweet::create([
            'body' => $request->get('body'),
            'feel' => $feel,
            'user_id' => $this->getUserId(),
            'created_at' => Carbon::now(),
        ]);

        return $this->success($tweet, 201);
    }

    private function validateRequest(Request $request)
    {

        $rules = [
            'body' => 'required',
            'feel' => 'required'
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