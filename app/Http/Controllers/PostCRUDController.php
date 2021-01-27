<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 

class PostCRUDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['posts'] = Post::orderBy('id', 'desc')->paginate(5);
        return view('posts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required|mimes:jpg,png,gif,svg|max:2048',
            'description' => 'required',
        ]);

        // $path = $request->file('image')->store('public/images');
        $name = time(). '_' .$request->title. '.' . $request->file('image')->getClientOriginalExtension();

        $post = new Post;
        $post->title = $request->title;
        $post->description = $request->description;

        if($request->hasFile('image')) {
            $post->image = $name;
            $path = $request->file('image')->move('images', $name);
        }

        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('posts.show', compact(['posts']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        // dd(Post::find($post));
        return view('posts.edit', compact(['post']));
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
        // dd(Post::find($post));
        // dd(File::exists());
        // dd(File::exists(public_path().'/images/'.$post->image));
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        
        // $post = Post::find($id);
        if($request->hasFile('image')){
            $request->validate([
              'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);

            $loc = public_path().'/images/'.$post->image;
            if(File::exists($loc)) {
                File::delete($loc);
            }
            
            $name = time(). '_' .$request->title. '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->move('images', $name);
            $post->image = $name;
        }

        $post->title = $request->title;
        $post->description = $request->description;
        $post->save();
    
        return redirect()->route('posts.index')
                        ->with('success','Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $loc = public_path().'/images/'.$post->image;
        if(file_exists($loc)) {
            File::delete($loc);
            // unlink($loc);
        }
        // dd(file_exists($loc));
        // dd(public_path().'/images/'.$post->image);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post has been deleted successfully');
    }
}
