<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\PostCollection;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends BaseController
{
    public function showPosts(Request $request){
        $postSearch = $request->query->get('title');
        if($postSearch){
            $posts = Post::where('title', 'LIKE', "%$postSearch%")->get();
        }else{
            $posts = Post::with(['categories', 'author'])->get();
        }

        $data = PostCollection::collection($posts);

        return $this->sendResponse($data, 'Posts retrieved successfully');
    }

    public function showPostById($id){
        $post = Post::with(['categories', 'author'])->findOrFail($id);

        if(!$post){
            return $this->sendError('Post not found.');
        }

        $data = new PostCollection($post);

        return $this->sendResponse($data, 'Post retrieved successfully');
    }
}
