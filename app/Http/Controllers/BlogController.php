<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Http\Resources\BaseResourcePageable;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allowedSorts = ['asc', 'desc'];
        $allowedOrderFields = ['title', 'created_at', 'updated_at','user_id'];

        $orderBy = in_array(request('orderBy'), $allowedOrderFields) ? request('orderBy') : 'updated_at';
        $sort = in_array(request('sort'), $allowedSorts) ? request('sort') : 'desc';


        $blogs = Blog::query()
            ->when(request('title'), fn($q, $title) => $q->where('title', 'like', "%{$title}%"))
            ->orderBy($orderBy, $sort)
            ->paginate(10);

        return BaseResourcePageable::respond(Response::HTTP_OK, 'Blogs found', BlogResource::collection($blogs));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required','max:100'],
            'content'=> ['required'],
        ]);

        $user = auth()->user();
        if(!$user){
            abort(Response::HTTP_UNAUTHORIZED,'Unauthorized');
        }
        $blog = Blog::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => $user->id,
        ]);

        return BaseResource::respond(Response::HTTP_CREATED, 'Blog created successfully', new BlogResource($blog));

    }

    /**
     * Display the specified resource.
     */
    public function showById(String $blogId)
    {
        $blog = Blog::find($blogId);

        if (empty($blog)) {
            abort(Response::HTTP_NOT_FOUND, 'Blog not found');
        }

        return BaseResource::respond(Response::HTTP_OK, 'Blog found', new BlogResource($blog));
    }

    public function showBySlug(String $slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (empty($blog)) {
            abort(Response::HTTP_NOT_FOUND, 'Blog not found');
        }

        return BaseResource::respond(Response::HTTP_OK, 'Blog found', new BlogResource($blog));
    }




    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $blogId)
    {
        $validated = $request->validate([
            'title' => ['nullable','max:100'],
            'content'=> ['nullable'],
        ]);
        $blog = Blog::find($blogId);

        if(!$blog){
            abort(Response::HTTP_NOT_FOUND,'blog not found');
        }

        if($blog->user_id !== auth()->user()->id){
            abort(Response::HTTP_FORBIDDEN,'Unauthorized');
        }

        $blog->title = $validated['title'] ?? $blog->title;
        $blog->content = $validated['content'] ?? $blog->content;
        $blog->save();

        return BaseResource::respond(Response::HTTP_OK,'Blog updated successfully', new BlogResource($blog));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $blogId)
    {
        $blog = Blog::find($blogId);
        if(!$blog){
            abort(Response::HTTP_NOT_FOUND,'blog not found');
        }
        if(!$blog->user_id == auth()->user()->id){
            abort(Response::HTTP_FORBIDDEN,'Unauthorized');
        }
        $blog->delete();
        return BaseResource::respond(Response::HTTP_OK,'Blog deleted successfully');
    }
}
