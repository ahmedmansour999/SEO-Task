<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
{
    public function allPosts()
    {
        $posts = Post::orderBy('created_at', 'desc')->paginate(10);



        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'No posts found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $posts
        ], 200);
    }


    public function OnePost($id)
    {

        $post = Post::where('id', $id)->first();

        if ($post) {
            return response()->json([
                'status' => 200,
                "data" => $post
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                "data" => "not found"
            ], 404);
        }
    }


    public function createPost(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();

        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string|max:2048',
        ], [
            'title.required' => 'title is required',
            'description.required' => 'description is required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                "data" => $validate->errors()
            ], 400);
        }

        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        $post->contact_phone = $user->phone;
        $post->user_id = $user->id;

        $post->save();
        return response()->json([
            'status' => 200,
            "data" => $post
        ], 200);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string|max:2048',
        ], [
            'id.required' => 'ID is required',
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $post = Post::find($request->id);

        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found'
            ], 404);
        }

        $post->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Post updated successfully',
            'data' => $post
        ], 200);
    }

    public function destroy($id)
    {
        $post = Post::where('id', $id)->get();
        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found'
            ]);
        }
        $post->delete();
        return response()->json([
            'status' => 200,
            "message" => 'deleted successfully'
        ], 200);
    }
}
