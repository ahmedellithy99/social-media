<?php

namespace App\Http\Controllers;

use App\Http\Enums\PostReactionEnum;
use App\Http\Requests\PostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\ChatResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Chat;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\CommentNotification;
use App\Notifications\ReactionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;


use function Laravel\Prompts\error;

class PostController extends Controller
{

    /**
     * Show specific post.
     */
    public function show(Post $post)
    {
        $authUser = auth()->user();
        $authId = $authUser->id;

        // Loading certain post 
        $post->loadCount('reactions')->load([
            'comments' => function ($query) {
                $query->withCount('reactions'); // SELECT * FROM comments WHERE post_id IN (1, 2, 3...)
                // SELECT COUNT(*) from reactions
            },
        ])->loadCount('comments');

        //Notificaions 
        $notifications = $authUser->notifications;
        $countUnReads = $authUser->unReadNotifications->count();

        // Sorted Chat
        $chats = ChatResource::collection(Chat::with('lastMessage')->where('A', $authId)->orWhere('B', $authId)->get());
        $chats = $chats->toArray(request());
        usort($chats, function ($a, $b) {
            return   strtotime($b['timeOflastMessage']) - strtotime($a['timeOflastMessage']);
        });

        return Inertia::render('Post/View', [
            'post' => new PostResource($post),
            'notifications' => $notifications,
            'chats' => $chats,
            'countUnReads' => $countUnReads,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {

        $data = $request->validated();

        DB::beginTransaction();

        $allFilePaths = [];
        try {
            $post = Post::create(Arr::except($data, ['attachments']));

            $allFilePaths = [];


            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];


            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;

                PostAttachment::create(
                    [
                        'post_id' => $post->id,
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'user_id' => auth()->user()->id
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }

            DB::rollBack();
            throw $e;
        }

        return redirect(route('post.show', $post));
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
            $post->update(Arr::except($data, ['attachments', 'deleted_file_ids']));

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
        if (auth()->user()->id != $post->user->id) {
            return response("You don't have permission to delete this post", 403);
        }
        try {
            DB::beginTransaction();

            // Perform your database operations here
            $post->comments()->delete();
            $post->reactions()->delete();
            $post->delete();

            // If all operations are successful, commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // If any operation fails, rollback the transaction
            DB::rollback();

            // Handle the exception (log it, display a message, etc.)
            throw $e;
        }

        return back();
    }

    // Reacting on a Post 
    public function postReaction(Request $request, Post $post)
    {

        $data = $request->validate([
            'reaction' => [Rule::enum(PostReactionEnum::class)]
        ]);
        $user = auth()->user();

        // return response(['user_id' => $user->id , 'post_owner' => $post->user->id ] );


        $reaction = Reaction::where('reactable_id', $post->id)
            ->where('reactable_type', Post::class)
            ->where('user_id', $user->id)->first();

        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {

            $hasReaction = true;
            Reaction::create(
                [
                    'user_id' => $user->id,
                    'reactable_id' => $post->id,
                    'type' => $data['reaction'],
                    'reactable_type' => Post::class,
                ]
            );
            if ($user->id != $post->user->id) {
                Notification::send($post->user, new ReactionNotification($user->name, $user->id, $post->id, $post->body));
            }
        }

        $reactions = Reaction::where('reactable_id', $post->id)
            ->where('reactable_type', Post::class)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction
        ]);
    }

    public function postComment(Post $post, Request $request)
    {
        $data = $request->validate(['comment' => 'required']);
        $author = $post->user;
        $user = auth()->user();

        $comment = Comment::create([
            'comment' => nl2br($data['comment']),
            'user_id' => auth()->user()->id,
            'post_id' => $post->id
        ]);
        if ($author->id != $user->id) {

            Notification::send($author, new CommentNotification($user->name, $post->body, $post->id, $user->id));
        }
        return response(new CommentResource($comment), 201);
    }

    public function deleteComment(Comment $comment)
    {
        $comment->reactions()->delete();
        $comment->delete();


        return response(200);
    }

    public function commentReaction(Comment $comment, Request $request)
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(PostReactionEnum::class)]
        ]);
        $userId = auth()->user()->id;

        $reaction = Reaction::where('reactable_id', $comment->id)
            ->where('reactable_type', Comment::class)
            ->where('user_id', $userId)->first();

        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create(
                [
                    'user_id' => $userId,
                    'reactable_id' => $comment->id,
                    'type' => $data['reaction'],
                    'reactable_type' => Comment::class,
                ]
            );
        }

        $reactions = Reaction::where('reactable_id', $comment->id)
            ->where('reactable_type', Comment::class)->count();


        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction
        ]);
    }
}
