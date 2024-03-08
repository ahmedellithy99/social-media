<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\error;

class PostController extends Controller
{

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        
        $data = $request->validated();

        DB::beginTransaction(); 
        
        $allFilePaths = [];
        try
        {
            $post = Post::create(Arr::except($data , ['attachments']));
            
            $allFilePaths = [];
            
            
            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [] ;
    
            
            foreach($files as $file)
            {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[]= $path;
                
                PostAttachment::create(
                    [
                        'post_id' => $post->id ,
                        'name' => $file->getClientOriginalName(),
                        'path'=> $path,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'user_id'=> auth()->user()->id
                    ]
                );

            }
            
            DB::commit();
        }

        catch(\Exception $e)
        {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            
            DB::rollBack();
            throw $e;
        }

        return back();
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        
        $post->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if(auth()->user()->id != $post->user->id)
        {
            return response("You don't have permission to delete this post", 403);
        }
        
        
        $post->delete();
        return back();
    }
}
