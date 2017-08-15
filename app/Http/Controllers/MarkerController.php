<?php

namespace App\Http\Controllers;

use App\Marker;

use Illuminate\Http\Request;

class MarkerController extends Controller{

    public function __construct(){

        $this->middleware('oauth', ['except' => ['index', 'show']]);
        $this->middleware('authorize:' . __CLASS__, ['except' => ['index', 'show', 'store']]);
    }

    public function index(){

        $markers = Marker::all();
        return $this->success($markers, 200);
    }

    public function store(Request $request){

        $this->validateRequest($request);

        $marker = Marker::create([
            'title' => $request->get('title'),
            'content'=> $request->get('content'),
            'user_id' => $this->getUserId()
        ]);

        return $this->success("The marker with with id {$marker->id} has been created", 201);
    }

    public function show($id){

        $marker = Marker::find($id);

        if(!$marker){
            return $this->error("The marker with {$id} doesn't exist", 404);
        }

        return $this->success($marker, 200);
    }

    public function update(Request $request, $id){

        $marker = Marker::find($id);

        if(!$marker){
            return $this->error("The marker with {$id} doesn't exist", 404);
        }

        $this->validateRequest($request);

        $marker->title 		= $request->get('title');
        $marker->content 		= $request->get('content');
        $marker->user_id 		= $this->getUserId();

        $marker->save();

        return $this->success("The marker with with id {$marker->id} has been updated", 200);
    }

    public function destroy($id){

        $marker = Marker::find($id);

        if(!$marker){
            return $this->error("The marker with {$id} doesn't exist", 404);
        }

        // no need to delete the comments for the current marker,
        // since we used on delete cascase on update cascase.
        // $marker->comments()->delete();
        $marker->delete();

        return $this->success("The marker with with id {$id} has been deleted along with it's comments", 200);
    }

    public function validateRequest(Request $request){

        $rules = [
            'title' => 'required',
            'content' => 'required'
        ];

        $this->validate($request, $rules);
    }

    public function isAuthorized(Request $request){

        $resource = "markers";
        $marker     = Marker::find($this->getArgs($request)["marker_id"]);

        return $this->authorizeUser($request, $resource, $marker);
    }
}