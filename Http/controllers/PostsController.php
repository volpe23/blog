<?php

namespace Controllers;

use Core\Models\Post;
use Core\Facades\Auth;
use Core\Request;

class PostsController
{
    public function index()
    {
        return view("posts", [
            "posts" => Post::all()
        ]);
    }

    public function create()
    {
        return view("post_create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required",
            "text" => "required"
        ]);

        Post::create([
            "title" => $validated["title"],
            "text" => $validated["text"],
            "user_id" => Auth::user()->id
        ]);

        return redirect("/posts");
    }

    public function show(string $id)
    {
        return view("post_show", [
            "post" => Post::where("id", $id)->first(),
        ]);
    }
}
