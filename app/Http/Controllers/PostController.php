<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    
    public function index()
    {
        $userId = Auth::id();
        $posts = Post::where('user_id', $userId)->get();
 
        return $this->successResponse('All Post Retrive Successfully',$posts);
    }
 
    public function show($id)
    {
        $userId = Auth::id();
        $post = Post::where('id', $id)->where('user_id', $userId)->first();
 
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }
        return $this->successResponse('Post Retrive',$post);
    }
 
    public function store(Request $request)
    {
        $userId = Auth::id();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }
 
        $post = Post::create([
            'user_id' => $userId,
            'title' => $request->title,
            'description' => $request->description,
        ]);
 
        if ($post) {
            return $this->successResponse('Post Created Successfully',$post);
        } else {
            return $this->errorResponse('Error!!!!! Post not Created', 500);
        }
    }
 
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }

        $userId = Auth::id();
        $post = Post::where('id', $id)->where('user_id', $userId)->first();
 
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }
 
        $updated = $post->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
 
        if ($updated) {
            return $this->successResponse('Post Updated Successfully');
        } else {
            return $this->errorResponse('Post not updated', 500);
        }
    }
 
    public function destroy($id)
    {
        $userId = Auth::id();
        $post = Post::where('id', $id)->where('user_id', $userId)->first();
 
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }
 
        if ($post->delete()) {
            return $this->successResponse('Post deleted successfully');
        } else {
            return $this->errorResponse('Post not deleted', 500);
        }
    }

    private function successResponse($message, $data = [], $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    private function errorResponse($message, $errors = [], $statusCode = 400)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
