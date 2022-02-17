<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Str;


class PostController extends Controller
{
    protected $validationRule = [
        "title" => "required|string|max:100",
        "content" => "required",
        "published" => "sometimes|accepted",
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        
        return view("admin.posts.index", compact("posts"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.posts.create");
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate($this->validationRule);
        
        $data = $request->all();

        $newPost = new Post();
        $newPost->title = $data["title"];
        $newPost->content = $data["content"];
        $newPost->published = isset($data["published"]);

        
        $slug = Str::of($newPost->title)->slug("-");
        $count = 1;
        
        while(Post::where("slug", $slug)->first() ) {
            $slug = Str::of($newPost->title)->slug("-")."-{$count}";
            $count++;
            
        }
        $newPost->slug = $slug;

        $newPost->save();

        return redirect()->route("posts.show", $newPost->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view("admin.posts.show", compact("post"));
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view("admin.posts.edit", compact("post"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate($this->validationRule);


        $data = $request->all();

        if($post->title != $data['title']) {
            $post->title = $data['title'];
            $slug = Str::of($post->title)->slug("-");
            if ($slug != $post->slug) {
                $count = 1;
                while (Post::where("slug", $slug)->first() ) {
                    $slug = Str::of($post->title)->slug("-") . "-{$count}";
                    $count++;
                }
                $post->slug = $slug;
            }
        }

        $post->content = $data["content"];
        $post->publisched = isset($data["published"]);

        $post->save();

        return redirect()->route("posts.show", $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route("posts.index");
    }
}
