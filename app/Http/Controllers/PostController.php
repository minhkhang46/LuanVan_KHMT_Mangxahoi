<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\UserNd;
use App\Models\Friend;
use App\Models\Notification;
use App\Models\Messager;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Group;
class PostController extends Controller
{
    public function posts(Request $request)
    {
        // Xử lý tải lên file hình ảnh nếu có
        if ($request->hasFile('images')) {
            $imagePath = $request->file('images')->store('image', 'public');
        } else {
            $imagePath = 'default/image.png'; // Đường dẫn tới hình ảnh mặc định nếu người dùng không tải lên hình ảnh
        }
    
        // Xử lý tải lên file đính kèm nếu có
        if ($request->hasFile('files')) {
            $attachmentPath = $request->file('files')->store('file', 'public');
            // dd($attachmentPath); // Kiểm tra đường dẫn sau khi lưu
        } else {
            $attachmentPath = null;
        }
    
        // Tạo bài đăng mới
        $post = Post::create([
            'id_nd' => $request->id_nd,
            'noidung' => $request->noidung,
            'group_id' => $request->group_id,
            'images' => $imagePath, // Lưu đường dẫn hình ảnh vào cơ sở dữ liệu
            'files' => $attachmentPath, // Lưu đường dẫn file đính kèm vào cơ sở dữ liệu
            'topic' => $request->topic,
            'regime' => $request->regime === 'Bạn bè' ? 1 : 0,
        ]);
    
        $userId = session('id');
        $user = UserNd::find($userId);
    
        // Kiểm tra xem bài đăng đã được tạo thành công hay không
        if ($post && $user) {
            // Lấy danh sách bạn bè của người dùng đã tạo bài đăng
            $friendIds = Friend::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhere('friend_id', $userId);
            })
            ->where('status', 1) // Chỉ lấy các bạn bè đã chấp nhận
            ->pluck('user_id')
            ->merge(Friend::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhere('friend_id', $userId);
            })
            ->where('status', 1)
            ->pluck('friend_id'))
            ->unique();
    
            // Gửi thông báo cho tất cả bạn bè ngoại trừ người tạo bài đăng
            foreach ($friendIds as $friendId) {
                if ($friendId != $userId) { // Loại bỏ người tạo bài đăng khỏi danh sách bạn bè
                    $friend = UserNd::find($friendId);
                    $postUrl = route('posts.show', ['id' => $post->id]);
                    // dd($postUrl);
                    Notification::create([
                        'user_id' => $friendId,
                        'type' => 'new_post',
                        'data' => json_encode([
                            'message' => "{$user->name} đã đăng một bài viết mới.",
                            'avatar' => $user->avatar ?? 'default-avatar.png',
                            'url' => $postUrl,
                        
                        ]),
                        'read_at' => 0, // Đánh dấu thông báo là chưa đọc
                    ]);
                }
            }
    
            return redirect()->back()->with('success', 'Tạo bài đăng thành công!');

        }
    }
    
    
    public function show($id)
    {
        $userId = session('id');
        
        $post = Post::findOrFail($id); // Tìm bài đăng theo ID
        $user = UserNd::find($post->id_nd);
        $group = Group::find($post->group_id);
        $groupName = $group ? $group->name : 'Không có group';
        $groupImg = $group ? $group->image : 'Không có group';
     
        // Đếm số thông báo chưa đọc
        $notificationCount = Notification::where('user_id', $userId)
            ->where('read_at', 0)
            ->count();
    
        // Đếm số tin nhắn chưa đọc
        $newMessagesCount = Messager::where('receiver_id', $userId)
            ->where('is_read', 0)
            ->count();
    
        // Kiểm tra xem người dùng đã thích bài viết chưa
        $post->is_liked = Like::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->exists();
    
        // Đếm số lượt thích
        $post->likes_count = Like::where('post_id', $post->id)->count();

        // Lấy danh sách người dùng đã thích bài viết
        $likedUserIds = Like::where('post_id', $post->id)->pluck('user_id');
        $likes = UserNd::whereIn('id', $likedUserIds)->get(['name', 'avatar', 'id']);
        $postsWithComments = []; 
        $posts = Post::orderBy('created_at', 'desc')->get();
        foreach ($posts as $p) {
            // Đếm số bình luận cho bài viết
            $commentCount = Comment::where('post_id', $p->id)->count();
            
            // Lấy các bình luận và thông tin người dùng cho bài viết
            $comments = Comment::where('post_id', $p->id)->get();
            $commentsWithUser = [];
            
            foreach ($comments as $comment) {
                $user_comment = UserNd::where('id', $comment->user_id)->first(['name','id', 'avatar',  'phone', 'email', 'cv']);
                $commentsWithUser[] = [
                    'id' => $comment->id,
                    'comment_content' => $comment->content,
                
                    'phone' => $user_comment->phone,
                    'cv' => $user_comment->cv,
                    'user_name' => $user_comment ? $user_comment->name : 'Người dùng không xác định',
                    'user_id' => $user_comment ? $user_comment->id : 'Người dùng không xác định',
                    'user_avatar' => $user_comment ? $user_comment->avatar : null,
                    'created_at' => $comment->created_at,
                    'parent_id'=>$comment->parent_id
                ];
            }
        
            // Lưu thông tin bài viết, số lượng bình luận và bình luận vào mảng
            $postsWithComments[$p->id] = [
                'post' => $post, // Thêm thông tin bài viết
                'comment_count' => $commentCount,
                'comments' => $commentsWithUser,
            ];
        }
        // Trả về view với bài đăng, thông tin người dùng, thông báo và tin nhắn chưa đọc
        return view('post', compact('post', 'notificationCount', 'newMessagesCount', 'user', 'likes','postsWithComments','groupName', 'groupImg'));
    }
    

   public function toggleLike(Request $request)
{
    $userId = session('id');
    $postId = $request->input('post_id');
    
    $post = Post::find($postId);
    $user = UserNd::find($userId);
    
    // Kiểm tra nếu người dùng đã thích bài viết
    $like = Like::where('user_id', $userId)
                ->where('post_id', $postId)
                ->first();
    
    if ($like) {
        // Nếu đã thích, xóa lượt thích
        $like->delete();
        $isLiked = false;
    } else {
        // Nếu chưa thích, thêm lượt thích
        Like::create([
            'user_id' => $userId,
            'post_id' => $postId,
        ]);
        $isLiked = true;
        
        // Tạo thông báo khi thêm lượt thích
        $postAuthorId = $post->id_nd;

        // Tạo thông báo cho tác giả bài viết, nhưng không gửi thông báo cho người dùng hiện tại
        if ($postAuthorId != $userId) { // Kiểm tra xem người thực hiện hành động có phải là tác giả không
            $user = UserNd::find($userId);

            Notification::create([
                'user_id' => $postAuthorId, // Thông báo cho tác giả bài viết
                'type' => 'like_added',
                'data' => json_encode([
                    'message' => "{$user->name} đã thích bài viết của bạn.",
                    'avatar' => $user->avatar ?? 'default-avatar.png',
                    'url' => route('posts.show', ['id' => $postId]), // Liên kết đến bài viết
                ]),
                'read_at' => 0, // Đánh dấu thông báo là chưa đọc
            ]);
        }
    }
    
    // Cập nhật số lượt thích
    $likesCount = Like::where('post_id', $postId)->count();

    return response()->json(['is_liked' => $isLiked, 'likes_count' => $likesCount]);
}
// hàm xóa bài đăng
public function destroy($id)
{
    $post = Post::find($id);

    if ($post) {
        // Kiểm tra xem người dùng hiện tại có quyền xóa bài đăng hay không
        if ($post->id_nd == session('id')) {
            $post->delete();
            return redirect()->back()->with('success', 'Bài đăng đã được xóa thành công.');
        } else {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa bài đăng này.');
        }
    }

    return redirect()->back()->with('error', 'Bài đăng không tồn tại.');
}

    
    
    
}
