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
        // Validasi data
        $validated = $request->validate([
            'title'   => ['required', 'max:100'],
            'content' => ['required'],
            'image'   => ['required', 'image', 'max:4096'], // Maksimum 4MB
        ]);

        $user = auth()->user();
        if (!$user) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }


        try {
            $uploadedFile = CloudinaryController::uploadImage($request->file('image'), $validated['title'], 'blog');
            $urlImage = $uploadedFile['secure_url'];
            $idImage = $uploadedFile['public_id'];
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Image upload failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $blog = Blog::create([
            'title'     => $validated['title'],
            'content'   => $validated['content'],
            'id_image'  => $idImage,
            'url_image' => $urlImage,
            'user_id'   => $user->id,
        ]);

        return BaseResource::respond(Response::HTTP_CREATED, 'Blog created successfully', new BlogResource($blog) );
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

        $res = CloudinaryController::deleteImage($blog->id_image);
        if(empty($res)){
            abort(Response::HTTP_INTERNAL_SERVER_ERROR,'Failed to delete image');
        }
        $blog->delete();

        return BaseResource::respond(Response::HTTP_OK,'Blog deleted successfully');
    }


}
