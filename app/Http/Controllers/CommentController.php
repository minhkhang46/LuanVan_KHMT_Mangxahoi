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
        $userId = session('id');
        $user = UserNd::find($userId);
    
        // Tạo bình luận mới
        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => $userId,
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id,
        ]);
    
        // Tạo thông báo nếu cần
        $postOwner = Post::find($request->post_id);
        if ($postOwner->id_nd !== $userId) {
            Notification::create([
                'user_id' => $postOwner->id_nd,
                'type' => 'comment',
                'data' => json_encode([
                    'message' => "{$user->name} đã bình luận bài viết của bạn.",
                    'avatar' => $user->avatar ?? 'default-avatar.png',
                    'url' => route('posts.show', ['id' => $request->post_id]),
                ]),
            ]);
        }
    
        // Trả về bình luận mới để cập nhật giao diện
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
    public function destroy($id)
    {
        // Tìm bình luận theo ID
        $comment = Comment::findOrFail($id);
    
        // Kiểm tra quyền sở hữu bình luận
        if ($comment->user_id === session('id')) {
            // Xóa bình luận
            $comment->delete();
            return response()->json(['success' => true]);  // Trả về JSON với thông báo thành công
        }
    
        // Trả về thông báo lỗi nếu người dùng không có quyền
        return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này.'], 403);
    }
    
    
    
    
    
    
    

}
