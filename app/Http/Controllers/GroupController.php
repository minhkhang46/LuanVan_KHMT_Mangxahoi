<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserNd;
use App\Models\Post;
use App\Models\Friend;
use App\Models\Messager;
use App\Models\Notification;
use App\Models\Like;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Comment;
class GroupController extends Controller
{
    

    public function index()
    {
        $currentUserId = session('id');
        // Dữ liệu tĩnh của các nhóm
       
        $groups = Group::all();
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
        ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
        ->count();
        $notificationCount = Notification::where('user_id', $currentUserId)
        ->where('read_at', 0)
        ->count();
        $posts = Post::orderBy('created_at', 'desc')->get();
        $imageSize = [];

        foreach ($posts as $post) {
            $imagePath = storage_path('app/public/' . $post->images);
            
            // Kiểm tra nếu ảnh tồn tại
            if (file_exists($imagePath)) {
                $imageSize[$post->id] = getimagesize($imagePath); // Lưu kích thước ảnh theo post ID
            } else {
                $imageSize[$post->id] = null; // Nếu không có ảnh
            }
        }
        // Lấy danh sách id người dùng liên quan
        $userIds = $posts->pluck('id_nd')->unique();
        // Lấy thông tin người dùng
        $users = UserNd::whereIn('id', $userIds)->get()->keyBy('id');
        $u = UserNd::where('id', $currentUserId)->where('possition', 1)->first();

        // lấy danh sách id nhóm trong bảng post
        $groupIds = $posts->pluck('group_id')->unique();
        //lấy danh sách nhóm
        $groups = Group::whereIn('id',$groupIds)->get()->keyBy('id');
        $likes = [];
        foreach ($posts as $post) {
            $likedUserIds = Like::where('post_id', $post->id)->pluck('user_id');
            $likes[$post->id] = UserNd::whereIn('id', $likedUserIds)->get(['name', 'avatar', 'id']);
        }
        $postsWithComments = []; 

        foreach ($posts as $post) {
            // Đếm số bình luận cho bài viết
            $commentCount = Comment::where('post_id', $post->id)->count();
            
            // Lấy các bình luận và thông tin người dùng cho bài viết
            $comments = Comment::where('post_id', $post->id)->get();
            $commentsWithUser = [];
            
            foreach ($comments as $comment) {
                $user = UserNd::where('id', $comment->user_id)->first(['name', 'avatar']);
                $commentsWithUser[] = [
                    'id' => $comment->id,
                    'comment_content' => $comment->content,
                    'user_name' => $user ? $user->name : 'Người dùng không xác định',
                    'user_avatar' => $user ? $user->avatar : null,
                    'created_at' => $comment->created_at,
                    'parent_id'=>$comment->parent_id
                ];
            }
        
            // Lưu thông tin bài viết, số lượng bình luận và bình luận vào mảng
            $postsWithComments[$post->id] = [
                'post' => $post, // Thêm thông tin bài viết
                'comment_count' => $commentCount,
                'comments' => $commentsWithUser,
            ];
        }
        
        foreach ($posts as $post) {
            if (isset($users[$post->id_nd])) {
                $post->user_name = $users[$post->id_nd]->name;
                $post->user_avatar = $users[$post->id_nd]->avatar;
              
            } else {
                $post->user_name = 'Unknown'; // Hoặc giá trị khác nếu người dùng không tồn tại
                $post->user_avatar = 'default/avatar.png'; // Đặt giá trị mặc định nếu cần
            }
    
            if (isset($groups[$post->group_id])) {
                $post->group_name = $groups[$post->group_id]->name;
                $post->group_image = $groups[$post->group_id]->image;
            
            } else {
                $post->group_name = 'không có nhóm'; // Hoặc giá trị khác nếu người dùng không tồn tại
            
            }
            $post->is_liked = Like::where('user_id', $currentUserId)
            ->where('post_id', $post->id)
            ->exists();
            $post->likes_count = Like::where('post_id', $post->id)->count();
           
        }
        $allGroups = Group::all(); // Danh sách tất cả các nhóm
        $joinedGroupIds = GroupMember::where('user_id', $currentUserId)
                                  ->pluck('group_id');
        $joinedGroups = Group::whereIn('id', $joinedGroupIds)->get();
        // Truyền dữ liệu đến view
        return view('group', ['groups' => $groups ,'newMessagesCount' => $newMessagesCount, 'notificationCount'=>$notificationCount, 'allGroups'=>$allGroups, 'joinedGroups'=>$joinedGroups, 'posts' => $posts, 'likes'=>$likes,
                        'imageSize'=>$imageSize, 'u'=>$u, 'postsWithComments'=>$postsWithComments]);
    }
// Method to display group details
public function show($id)
{
    $group = Group::find($id);
    $currentUserId = session('id');
    //  dd(asset($group->image));  // In ra đường dẫn URL của ảnh ra terminal


    if (!$group) {
        abort(404, 'Group not found');
    }
    $user = UserNd::find($currentUserId);

    if (!$user) {
        abort(404, 'User not found');
    }
    // Kiểm tra thành viên nhóm
    $isMember = GroupMember::where('group_id', $id)
        ->where('user_id', $currentUserId)
        ->exists();

    $newMessagesCount = Messager::where('receiver_id', $currentUserId)
        ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
        ->count();
    $notificationCount = Notification::where('user_id', $currentUserId)
        ->where('read_at', 0)
        ->count();

    // Lấy danh sách user từ bảng groupmember
    $groupMemberIds = GroupMember::where('group_id', $id)
        ->pluck('user_id');
    // Lấy thông tin từ bảng usernd 
    $members = UserNd::whereIn('id', $groupMemberIds)->get();
    $memberCount = GroupMember::where('group_id', $id)->count();

    // Lấy thông tin bài đăng từ group
    $posts = Post::where('group_id', $id)->orderBy('created_at', 'desc')->get();
    $imageSize = [];

        foreach ($posts as $post) {
            $imagePath = storage_path('app/public/' . $post->images);
            
            // Kiểm tra nếu ảnh tồn tại
            if (file_exists($imagePath)) {
                $imageSize[$post->id] = getimagesize($imagePath); // Lưu kích thước ảnh theo post ID
            } else {
                $imageSize[$post->id] = null; // Nếu không có ảnh
            }
        }
    $userIds = $posts->pluck('id_nd')->unique(); // Lấy tất cả user_id từ danh sách bài đăng
    $users = UserNd::whereIn('id', $userIds)->get()->keyBy('id'); // Lấy thông tin tất cả user liên quan
    //cho người dùng like bài viết
    $likes = [];
    foreach ($posts as $post) {
        $likedUserIds = Like::where('post_id', $post->id)->pluck('user_id');
        $likes[$post->id] = UserNd::whereIn('id', $likedUserIds)->get(['name', 'avatar', 'id']);
    }

    $postsWithComments = []; 

    foreach ($posts as $post) {
        // Đếm số bình luận cho bài viết
        $commentCount = Comment::where('post_id', $post->id)->count();
        
        // Lấy các bình luận và thông tin người dùng cho bài viết
        $comments = Comment::where('post_id', $post->id)->get();
        $commentsWithUser = [];
        
        foreach ($comments as $comment) {
            $user = UserNd::where('id', $comment->user_id)->first(['name', 'avatar']);
            $commentsWithUser[] = [
                'id' => $comment->id,
                'comment_content' => $comment->content,
                'user_name' => $user ? $user->name : 'Người dùng không xác định',
                'user_avatar' => $user ? $user->avatar : null,
                'created_at' => $comment->created_at,
                'parent_id'=>$comment->parent_id
            ];
        }
    
        // Lưu thông tin bài viết, số lượng bình luận và bình luận vào mảng
        $postsWithComments[$post->id] = [
            'post' => $post, // Thêm thông tin bài viết
            'comment_count' => $commentCount,
            'comments' => $commentsWithUser,
        ];
    }
    
    // Gán thông tin người dùng cho từng bài đăng
    foreach ($posts as $post) {
        if (isset($users[$post->id_nd])) {
            $post->user_name = $users[$post->id_nd]->name;
            $post->user_avatar = $users[$post->id_nd]->avatar;
        } else {
            $post->user_name = 'Unknown'; // Hoặc giá trị khác nếu người dùng không tồn tại
            $post->user_avatar = 'default/avatar.png'; // Đặt giá trị mặc định nếu cần
        }
        $post->is_liked = Like::where('user_id', $currentUserId)
        ->where('post_id', $post->id)
        ->exists();
        $post->likes_count = Like::where('post_id', $post->id)->count();
    }

    return view('groupshow', [
        'group' => $group,
        'newMessagesCount' => $newMessagesCount,
        'notificationCount' => $notificationCount,
        'user' => $user,
        'isMember' => $isMember,
        'members' => $members, 
        'posts' => $posts,
        'likes' => $likes, 'memberCount'=>$memberCount, 'imageSize'=>$imageSize ,'postsWithComments'=>$postsWithComments
    ]);
}



public function joinGroup(Request $request, $id)
{
    
    $currentUserId = session('id');
    
  
    $isMember = GroupMember::where('group_id', $id)
        ->where('user_id', $currentUserId)
        ->exists();

    if (!$isMember) {
        GroupMember::create([
            'group_id' => $id,
            'user_id' => $currentUserId
        ]);
    }

    return redirect()->back()->with('success', 'You have joined the group.');
}

public function showImage($filename)
{
    $path = storage_path('app/public/images/' . $filename);
    
    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return response($file, 200)->header("Content-Type", $type);
}


// xóa nhóm
public function removeMember($groupId)
{
    // Lấy ID của người dùng hiện tại
    $currentUserId = session('id');

    // Tìm và xóa thành viên trong nhóm dựa trên user_id (người dùng hiện tại) và group_id
    $groupMember = GroupMember::where('group_id', $groupId)
                              ->where('user_id', $currentUserId)
                              ->first();

    // Kiểm tra xem thành viên có tồn tại không
    if ($groupMember) {
        $groupMember->delete();
        return back()->with('success', 'Bạn đã rời nhóm thành công.');
    } else {
        return back()->with('error', 'Bạn không phải là thành viên của nhóm này.');
    }
}

public function store(Request $request)
{
    // Validate dữ liệu form
   

    // Xử lý việc tải ảnh lên nếu có
    $imagePath = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imagePath = $image->store('group_images', 'public'); // Lưu ảnh vào thư mục public
    }

    // Tạo nhóm bằng phương thức create
    $group = Group::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePath,
        'status' =>$request->status,
        'is_approved' => 0, // Đặt trạng thái duyệt là 0 (chưa duyệt)
    ]);
    
    // Thêm người tạo vào bảng group_members bằng create
    GroupMember::create([
        'group_id' => $group->id,
        'user_id' => session('id'), // Lấy ID người dùng từ session
    ]);
    // Lấy danh sách thành viên trong nhóm
    $members = GroupMember::where('group_id', $group->id)->get(); // Lấy tất cả thành viên của nhóm

    foreach ($members as $member) {
        // Tìm kiếm thông tin người dùng từ bảng UserNd
        $user = UserNd::find($member->user_id); // Lấy thông tin người dùng từ bảng UserNd

        if ($user) { // Kiểm tra xem người dùng có tồn tại không
            // Gửi thông báo đến từng thành viên
            Notification::create([
                'user_id' => 12, // ID của người dùng nhận thông báo
                'type' => 'group_creation',
                'data' => json_encode([
                    'message' => " {$user->name} đã yêu cầu tạo nhóm {$group->name}.", // Nội dung thông báo kèm tên người tạo
                    'avatar' => $user->avatar ?? 'default-avatar.png', // Hình ảnh nhóm nếu có
                    'url' => route('requested.groups') // Link đến trang chi tiết nhóm
                ]),
                'read_at' => 0, // Đặt là null để đánh dấu thông báo chưa được đọc
            ]);
        }
    }
    return redirect()->back()->with('message', 'Yêu cầu của bạn đang chờ admin duyệt');

    }



public function showRequestedGroups()
{
    // Lấy ID người dùng từ session
    $currentUserId = session('id');

    // Lấy tất cả các nhóm
    $allGroups = Group::all();

    // Lấy danh sách nhóm mà người dùng đã gửi yêu cầu
    $requestedGroupIds = GroupMember::where('user_id', $currentUserId)->pluck('group_id')->toArray();

    // Thêm thông tin yêu cầu vào nhóm và lấy danh sách thành viên
    foreach ($allGroups as $group) {
        $group->is_requested = in_array($group->id, $requestedGroupIds); // Kiểm tra xem người dùng đã gửi yêu cầu tham gia nhóm này hay chưa
        
        // Lấy danh sách thành viên trong nhóm
        $members = GroupMember::where('group_id', $group->id)->pluck('user_id');
        $group->members = UserNd::whereIn('id', $members)->get(); // Lấy tất cả thông tin thành viên
    }

    $newMessagesCount = Messager::where('receiver_id', $currentUserId)
        ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
        ->count();

    $notificationCount = Notification::where('user_id', $currentUserId)
        ->whereNull('read_at') // Sử dụng whereNull để kiểm tra cột read_at có giá trị null hay không
        ->count();

    return view('request_group', compact('allGroups', 'newMessagesCount', 'notificationCount'));
}

public function acceptRequest($id)
{
    $currentUserId = session('id');

    // Tìm nhóm theo ID
    $group = Group::find($id);

    if ($group) {
        $group->is_approved = true; // Đánh dấu là đã được chấp thuận
        $group->save();

        // Lấy thông tin người dùng từ bảng group_member
       // Lấy danh sách tất cả các thành viên của nhóm
        $members = GroupMember::where('group_id', $group->id)->get();

        foreach ($members as $member) {
            // Tìm kiếm thông tin người dùng từ bảng usernd
            $user = UserNd::find($member->user_id); // Lấy thông tin người dùng từ bảng usernd
            $gropUrl = route('groups.show', $group->id);
            if ($user) {
                // Gửi thông báo cho từng thành viên
                Notification::create([
                    'user_id' => $user->id, // ID của người dùng trong bảng usernd
                    'type' => 'new_request',
                    'data' => json_encode([
                        'message' => "Yêu cầu tạo nhóm '{$group->name}' đã được chấp nhận.",
                        'image' => $group->image ?? 'default-avatar.png', // Hình ảnh nhóm
                        'url' => $gropUrl // Link đến trang nhóm
                    ]),
                    'read_at' => 0, // Để null để đánh dấu là chưa đọc
                ]);
            }
        }

        return redirect()->back()->with('success', 'Đã chấp nhận yêu cầu tham gia nhóm cho tất cả thành viên.');


    return redirect()->back()->with('error', 'Không tìm thấy nhóm.');
    }
}
public function declineRequest($id)
{
    $currentUserId = session('id');

    // Tìm nhóm theo ID
    $group = Group::find($id);

    if ($group) {
        $group->is_approved = 2; // Đặt trạng thái thành 2 (từ chối)
        $group->save();

        // Lấy thông tin người dùng từ bảng group_member
        $members = GroupMember::where('group_id', $group->id)->get();

        foreach ($members as $member) {
            // Tìm kiếm thông tin người dùng từ bảng usernd
            $user = UserNd::find($member->user_id); // Lấy thông tin người dùng từ bảng usernd
            $gropUrl = route('groups.show', $group->id);
            if ($user) {
                // Gửi thông báo cho từng thành viên
                Notification::create([
                    'user_id' => $user->id, // ID của người dùng trong bảng usernd
                    'type' => 'new_request',
                    'data' => json_encode([
                        'message' => "Yêu cầu tạo nhóm '{$group->name}' đã bị từ chối.",
                        'avatar' => $group->image ?? 'default-avatar.png', // Hình ảnh nhóm
                        'url' => $gropUrl // Link đến trang nhóm
                    ]),
                    'read_at' => 0, // Để null để đánh dấu là chưa đọc
                ]);
            }
        }

        return redirect()->back()->with('success', 'Đã từ chối yêu cầu tham gia nhóm.');
    }

    return redirect()->back()->with('error', 'Không tìm thấy nhóm.');
}



}
