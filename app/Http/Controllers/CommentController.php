<?php

namespace App\Http\Controllers;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Notification;
use App\Models\UserNd;
class CommentController extends Controller
{
    public function Comments(Request $request)
    {
  

        Comment::create([
            'content' => $request->content,
            'user_id' => session('id'), // Giả sử bạn đang dùng session để lưu user_id
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id, // Nếu là trả lời bình luận
        ]);
          // Tạo thông báo nếu là bình luận trên bài viết
            $userId = session('id');
            $user = UserNd::find($userId);
            $postOwnerId = Post::find($request->post_id); // Lấy ID của người sở hữu bài viết
            $postAuthorId = $postOwnerId->id_nd;
            if ($postAuthorId !== session('id')) { // Kiểm tra để không gửi thông báo cho chính mình
                Notification::create([
                    'user_id' => $postAuthorId  , // Người nhận thông báo
                    'type' => 'comment',
                    'data' => json_encode([
                        'message' => "{$user->name} đã bình luận bài viết của bạn.",
                        'avatar' => $user->avatar ?? 'default-avatar.png',
                        'url' => route('posts.show', ['id' => $request->post_id]), // Liên kết đến bài viết
                       
                    ]),
                ]);
            }
        
        return back();
    }

    public function storeReply(Request $request, $post_id)
    {
   
        Comment::create([
            'post_id' => $post_id,
            'user_id' => $request->user_id, // Hoặc session('id')
            'content' => $request->content,
            'parent_id' => $request->parent_id, // ID bình luận cha
        ]);
        $parentComment = Comment::find($request->parent_id);
        $parentCommentAuthorId = $parentComment->user_id;
        $userId = session('id');
        $user = UserNd::find($userId);
        if ($parentCommentAuthorId !== $userId) { // Không gửi thông báo cho chính mình
            Notification::create([
                'user_id' => $parentCommentAuthorId, // Người nhận thông báo là người đã viết bình luận gốc
                'type' => 'reply',
                'data' => json_encode([
                    'message' => "{$user->name} đã trả lời bình luận của bạn.",
                    'avatar' => $user->avatar ?? 'default-avatar.png',
                    'url' => route('posts.show', ['id' => $post_id]), // Liên kết đến bài viết
                ]),
            ]);
        }
        return redirect()->back()->with('success', 'Đã gửi trả lời!');
    }

}
