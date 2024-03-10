<?php

namespace App\Http\Controllers;

use App\Http\Enums\PostReactionEnum;
use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $data = $request->validated();
            $post->update(Arr::except($data , ['attachments' , 'deleted_file_ids']));

            $deleted_ids = $data['deleted_file_ids'] ?? []; // 1, 2, 3, 4

            $attachments = PostAttachment::query()
                ->where('post_id', $post->id)
                ->whereIn('id', $deleted_ids)
                ->get();

            foreach ($attachments as $attachment) {
                $attachment->delete();
            }

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];
            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $e;
        }

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

    public function postReaction(Request $request ,Post $post)
    {
        $data =$request->validate([
            'reaction' => [Rule::enum(PostReactionEnum::class)]
        ]);

        $userId = auth()->user()->id;
        
        $reaction = Reaction::where('post_id' , $post->id)->where('user_id' , $userId )->first();

        if($reaction)
        {
            $hasReaction = false;
            $reaction->delete();
        }
        else
        {   
            $hasReaction = true;
            Reaction::create(
                [
                    'user_id' => $userId,
                    'post_id' => $post->id,
                    'type' => $data['reaction']
                ]
            );
        }

        $reactions = Reaction::where('post_id' , $post->id)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction
        ]);

    }

    public function postComment(Post $post , Request $request)
    {
        $data = $request->validate(['comment' => 'required']);
        
        $comment = Comment::create([
            'comment' => nl2br($data['comment']),
            'user_id' => auth()->user()->id,
            'post_id' => $post->id
        ]);

        return response(new CommentResource($comment) , 201);
    }


}
