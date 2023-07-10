<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    public function index()
    {
        $posts = auth()->user()->posts;
 
        return $this->successResponse('All Post Retrive Successfully',$posts);
    }
 
    public function show($id)
    {
        $post = auth()->user()->posts()->find($id);
 
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }
 
        return $this->successResponse('Post Retrive',$post);
    }
 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }
 
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
 
        if (auth()->user()->posts()->save($post)) {
            return $this->successResponse('Post Created Successfully',$post);
        } else {
            return $this->errorResponse('Post not added', 500);
        }
    }
 
    public function update(Request $request, $id)
    {
        $post = auth()->user()->posts()->find($id);
 
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }
 
        $updated = $post->update($request->all());
 
        if ($updated) {
            return $this->successResponse('Post Updated Successfully');
        } else {
            return $this->errorResponse('Post cannot be updated', 500);
        }
    }
 
    public function destroy($id)
    {
        $post = auth()->user()->posts()->find($id);
 
        if (!$post) {
            return $this->errorResponse('Post not found', 404);
        }
 
        if ($post->delete()) {
            return $this->successResponse('Post deleted successfully');
        } else {
            return $this->errorResponse('Post cannot be deleted', 500);
        }
    }

    private function successResponse($message, $data = [], $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    private function errorResponse($message, $errors = [], $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
}
