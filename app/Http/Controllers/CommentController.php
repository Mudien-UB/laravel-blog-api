<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Http\Resources\BaseResourcePageable;
use App\Http\Resources\CommentResource;
use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function index(String $idBlog)
    {
        $blog = Blog::findOrFail($idBlog);
        $comments = $blog->comments()->paginate(20);
        return BaseResourcePageable::respond(Response::HTTP_OK, "data ditemukan", CommentResource::collection($comments));
    }

    public function store(Request $request, String $idBlog)
    {
        $user = auth()->user();
        if (!$user) {
            abort(Response::HTTP_UNAUTHORIZED, "Unauthorized");
        }

        $validated = $request->validate([
            "content" => "required",
        ]);

        $blog = Blog::findOrFail($idBlog);

        $comment = $blog->comments()->create([
            "content" => $validated["content"],
            "user_id" => $user->id,
        ]);
        
        return BaseResource::respond(Response::HTTP_CREATED, "data berhasil ditambahkan", new CommentResource($comment));
    }

    public function destroy(String $commentId)
    {
        $user = auth()->user();
        if (!$user) {
            abort(Response::HTTP_UNAUTHORIZED, "Unauthorized");
        }

        $comment = Comment::findOrFail($commentId);

        if ($comment->user->id != $user->id) {
            abort(Response::HTTP_FORBIDDEN, "Unauthorized");
        }

        $comment->delete();
        return BaseResource::respond(Response::HTTP_NO_CONTENT, "data berhasil dihapus");
    }

    public function restore(String $commentId)
    {
        $user = auth()->user();
        if (!$user) {
            abort(Response::HTTP_UNAUTHORIZED, "Unauthorized");
        }

        $comment = Comment::withTrashed()->findOrFail($commentId);

        if ($comment->user->id != $user->id) {
            abort(Response::HTTP_FORBIDDEN, "Unauthorized");
        }

        $comment->restore();
        return BaseResource::respond(Response::HTTP_OK, "data berhasil dipulihkan");
    }

    public function show(String $commentId)
    {
        $comment = Comment::findOrFail($commentId);
        return BaseResource::respond(Response::HTTP_OK, "data ditemukan", new CommentResource($comment));
    }

}
