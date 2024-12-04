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
                    'user_id' => $user ? $user->id: null ,
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
            $joined = GroupMember::where('user_id', $currentUserId)
                                  ->pluck('group_id') // Chỉ lấy ID của các nhóm đã tham gia
                                  ->toArray(); // Chuyển thành mảng để dễ kiểm tra
                              
        $joinedGroups = Group::whereIn('id', $joinedGroupIds)->get();
        // Truyền dữ liệu đến view
        return view('group', ['groups' => $groups ,'newMessagesCount' => $newMessagesCount, 'notificationCount'=>$notificationCount, 'allGroups'=>$allGroups, 'joinedGroups'=>$joinedGroups, 'posts' => $posts, 'likes'=>$likes,
                        'imageSize'=>$imageSize, 'u'=>$u, 'postsWithComments'=>$postsWithComments, 'joined'=>$joined]);
    }
// Method to display group details
public function show($id)
{
    $group = Group::find($id);
    $currentUserId = session('id');
    // dd(asset($group));  // In ra đường dẫn URL của ảnh ra terminal


    if (!$group) {
        abort(404, 'Group not found');
    }
    // $user = UserNd::where('id', $currentUserId)->where('possition', 1)->first();
    // if (!$user) {
    //     abort(404, 'User not found');
    // }
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

    // Kiểm tra xem người dùng có phải là thành viên nhóm này hay không
    $groupMember = GroupMember::where('group_id', $groupId)
                              ->where('user_id', $currentUserId)
                              ->first();

    // Nếu thành viên không tồn tại trong nhóm, trả về thông báo lỗi
    if (!$groupMember) {
        return back()->with('error', 'Bạn không phải là thành viên của nhóm này hoặc nhóm không tồn tại.');
    }

    // Tiến hành xóa thành viên khỏi nhóm
    $groupMember->delete();

    // Trả về thông báo thành công
    return back()->with('success', 'Bạn đã rời nhóm thành công.');
}


public function store(Request $request)
{
    // Validate dữ liệu form
   
    if (is_null($request->name) || $request->name === '' && is_null($request->description) || $request->description === '') {
        return back()->with('error', 'Điền đầy đủ các thông tin.');
    }

    // Kiểm tra nếu biến 'description' trong request là null hoặc rỗng
   

    // Xử lý việc tải ảnh lên nếu có
    $imagePath = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imagePath = $image->store('group_images', 'public'); // Lưu ảnh vào thư mục public
    }
// Kiểm tra nếu nhóm đã tồn tại
        $existingGroup = Group::where('name', $request->name)->first();

        if ($existingGroup) {
            // Nếu nhóm đã tồn tại
            return back()->with('warning', 'Nhóm đã tồn tại.');
        }

    // Tạo nhóm bằng phương thức create
    $group = Group::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePath,
        'status' =>$request->status,
        'is_approved' => $request->is_approved // Đặt trạng thái duyệt là 0 (chưa duyệt)
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
          // Kiểm tra nếu người dùng hiện tại có position = 1
            if ($user->possition != 1) {
                // Tìm người dùng có position = 1
                $userWithPosition = UserNd::where('possition', 1)->first();

                if ($userWithPosition) {
                    Notification::create([
                        'user_id' => $userWithPosition->id, // ID của người dùng có position = 1
                        'type' => 'group_creation',
                        'data' => json_encode([
                            'message' => "{$user->name} đã yêu cầu tạo nhóm {$group->name}.", // Nội dung thông báo kèm tên người tạo
                            'avatar' => $user->avatar ?? 'default-avatar.png', // Hình ảnh nhóm nếu có
                            'url' => route('requested.groups') // Link đến trang chi tiết nhóm
                        ]),
                        'read_at' => 0, // Đặt là null để đánh dấu thông báo chưa được đọc
                    ]);
                } else {
                    // Xử lý nếu không tìm thấy người dùng có position = 1
                    throw new Exception('Không tìm thấy người dùng có position = 1.');
                }
            }

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
        ->where('read_at', 0) // Sử dụng whereNull để kiểm tra cột read_at có giá trị null hay không
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

        // Kiểm tra xem người dùng hiện tại có phải là admin không
        $adminExists = GroupMember::where('group_id', $group->id)
        ->where('user_id', $currentUserId)
        ->exists();

        if (!$adminExists) {
            // Nếu người dùng chưa là thành viên trong nhóm, thêm người dùng vào nhóm với quyền admin
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $currentUserId, // Người dùng hiện tại
            
            ]);
        }
        foreach ($members as $member) {
            // Tìm kiếm thông tin người dùng từ bảng usernd
            $user = UserNd::find($member->user_id); // Lấy thông tin người dùng từ bảng usernd
            $gropUrl = route('groups.show', $group->id);
            if ($user->possition == 0) {
                // Gửi thông báo cho từng thành viên
                Notification::create([
                    'user_id' => $user->id, // ID của người dùng trong bảng usernd
                    'type' => 'new_request',
                    'data' => json_encode([
                        $avatar = $user->possition == 1 ? ($user->avatar ?? 'default-avatar.png') : 'default-avatar.png',
                        'message' => "Yêu cầu tạo nhóm '{$group->name}' đã được chấp nhận bởi admin.",
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

//Hiển thị nhóm cho admin
public function show_admin_group (){
    $currentUserId = session('id');
    $newMessagesCount = Messager::where('receiver_id', $currentUserId)
    ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
    ->count();

    $notificationCount = Notification::where('user_id', $currentUserId)
    ->where('read_at', 0) // Sử dụng whereNull để kiểm tra cột read_at có giá trị null hay không
    ->count();
    return view('admin.group_admin', compact( 'newMessagesCount', 'notificationCount'));
}


public function show_group_postadmin()
{
    // Lấy tất cả bài đăng có group_id không null
    $posts = Post::whereNotNull('group_id')
    ->orderBy('created_at', 'desc') // Sắp xếp theo thời gian mới nhất
    ->get();


    // Tìm kiếm thông tin nhóm và người dùng
    foreach ($posts as $post) {
        // Lấy tên nhóm từ bảng Group
        $group = Group::where('id', $post->group_id)->first();
        $post->group_name = $group ? $group->name : 'Không xác định';

        // Lấy tên người dùng từ bảng UserNd
        $user = UserNd::where('id', $post->id_nd)->first();
        $post->user_name = $user ? $user->name : 'Không xác định';
    }

    $currentUserId = session('id');
    $newMessagesCount = Messager::where('receiver_id', $currentUserId)
        ->where('is_read', 0)
        ->count();

    $notificationCount = Notification::where('user_id', $currentUserId)
        ->where('read_at', 0)
        ->count();

    return view('admin.post_group_admin', compact('posts', 'newMessagesCount', 'notificationCount'));
}

public function deletePost_group(Request $request, $id)
{
    
    $userId = session('id');
    $user = UserNd::find($userId);
    // Tìm bài đăng
    $post = Post::find($id);
    $group = Group::where('id', $post->group_id)->first();
    // Nếu không tìm thấy bài đăng, trả về thông báo lỗi
    if (!$post) {
        return redirect()->back()->with('error', 'Bài đăng không tồn tại.');
    }

    // Kiểm tra xem bài đăng có hình ảnh không và xóa nếu có
    if ($post->images && $post->images !== 'default/image.png') {
        // Kiểm tra nếu hình ảnh tồn tại trên hệ thống
        if (Storage::exists('public/' . $post->images)) {
            Storage::delete('public/' . $post->images);
        } else {
            // Nếu không tìm thấy hình ảnh, trả về thông báo lỗi
            return redirect()->back()->with('error', 'Không tìm thấy hình ảnh để xóa.');
        }
    }
    $reason = $request->input('reason');
    // dd($reason);
    if ($reason) {
        Notification::create([
            'user_id' => $post->id_nd,  // Gửi thông báo đến người dùng liên quan đến bài viết
            'type' => 'post_deleted',   // Loại thông báo
            'data' => json_encode([
                'message' => "Bài viết nhóm {$group->name} của bạn có nội dung \"{$post->noidung}\" đã bị xóa bởi quản trị viên..",
                'reason' => "Lý do: {$reason}",
                'contact' => "Vui lòng liên hệ với admin tại: ",  // Thêm phần liên hệ với admin
                'chat_url' => route('chat', ['receiverId' => session('id')]) ,  // Đường dẫn đến trang chat của admin
                'avatar' => $user->avatar ?? 'default-avatar.png',  // Avatar người gửi thông báo
            ]),
            'read_at' => 0,  // Đánh dấu thông báo chưa đọc
        ]);
        
    }
    // Xóa bài đăng
    $postDeleted = $post->delete();

    // Nếu bài đăng không được xóa, trả về thông báo lỗi
    if (!$postDeleted) {
        return redirect()->back()->with('error', 'Đã xảy ra lỗi khi xóa bài đăng.');
    }

    // Lưu lý do xóa vào thông báo nếu có lý do
  

    // Quay lại trang trước và thông báo thành công
    return redirect()->back()->with('success', 'Xóa bài đăng thành công.');
}

 //hàm hiểnt thị thành viên nhóm
 public function groupMembers($id)
 {
     $currentUserId = session('id');
 
 
     $group = Group::findOrFail($id);
 
     $memberIds = GroupMember::where('group_id', $id)->pluck('user_id');
 
     $members = UserNd::whereIn('id', $memberIds)->get();
 
     // Lấy danh sách bạn bè và trạng thái
     $friendships = Friend::where(function ($query) use ($currentUserId) {
         $query->where('user_id', $currentUserId)
               ->orWhere('friend_id', $currentUserId);
     })->get();
 
   
     foreach ($members as $member) {
         if ($member->id == $currentUserId) {
             $member->friend_status = null; // Không kiểm tra chính mình
         } else {
             $friendship = $friendships->first(function ($friend) use ($member, $currentUserId) {
                 return ($friend->user_id == $member->id || $friend->friend_id == $member->id);
             });
 
             $member->friend_status = $friendship ? $friendship->status : 0;
         }
     }
 
     $newMessagesCount = Messager::where('receiver_id', $currentUserId)
         ->where('is_read', 0)
         ->count();
 
 
     $notificationCount = Notification::where('user_id', $currentUserId)
         ->where('read_at', 0)
         ->count();
 
     // Trả về view
     return view('admin.member', compact('group', 'members', 'newMessagesCount', 'notificationCount'));
 }
 

 


 public function removeMemberGroup(Request $request, $groupId, $userId)
{
    // Tìm thành viên trong nhóm
    $member = GroupMember::where('group_id', $groupId)->where('user_id', $userId)->first();
    $adminId = session('id');
    $admin = UserNd::find($adminId);

    // Lấy tên nhóm sử dụng where
    $groupName = Group::where('id', $groupId)->value('name'); // Lấy tên nhóm theo ID

    // Kiểm tra nếu thành viên tồn tại trong nhóm
    if ($member) {
        // Lý do xóa (nếu có)
        $reason = $request->input('reason', 'Không có lý do cụ thể.');

        // Gửi thông báo đến thành viên bị xóa
        Notification::create([
            'user_id' => $member->user_id,
            'type' => 'member_deleted',   // Loại thông báo
            'data' => json_encode([
                'message' => "Bạn đã bị xóa khỏi nhóm '{$groupName}' bởi quản trị viên.",
                'reason' => "Lý do: {$reason}",
                'contact' => "Vui lòng liên hệ với admin tại:",
                'chat_url' => route('chat', ['receiverId' => $adminId]),
                'avatar' => $admin->avatar ?? 'default-avatar.png',
            ]),
            'read_at' => 0, // Đánh dấu thông báo chưa đọc
        ]);

        // Xóa thành viên
        $member->delete();

        // Thông báo thành công
        return redirect()->back()->with('success', 'Đã xóa thành viên khỏi nhóm và gửi thông báo.');
    }

    // Nếu không tìm thấy thành viên
    return redirect()->back()->with('error', 'Không tìm thấy thành viên trong nhóm.');
}

 

// Hàm xóa nhóm
public function deleteGroup(Request $request, $groupId)
{
    // Tìm nhóm theo ID
    $group = Group::find($groupId);
    $userId = session('id');
    $user = UserNd::find($userId);
    // Kiểm tra xem nhóm có tồn tại không
    if (!$group) {
        return redirect()->back()->with('error', 'Nhóm không tồn tại!');
    }

 
    // Lấy danh sách thành viên nhóm
    $members = GroupMember::where('group_id', $groupId)->pluck('user_id');
        // Gửi thông báo đến tất cả thành viên
        foreach ($members as $memberId) {
              $reason = $request->input('reason');
            Notification::create([
                'user_id' => $memberId,
                'type' => 'group_deleted',   // Loại thông báo
                'data' => json_encode([
                    'message' => "Nhóm '{$group->name}' đã bị xóa bởi quản trị viên.",
                    'reason' => "Lý do: {$reason}",
                    'contact' => "Vui lòng liên hệ với admin tại: ",  // Thêm phần liên hệ với admin
                    'chat_url' => route('chat', ['receiverId' => session('id')]) ,
                    'avatar' => $user->avatar ?? 'default-avatar.png',  // Avatar người gửi thông báo 
                ]),
                'read_at' => 0,  // Đánh dấu thông báo chưa đọc
            ]);
        }
           // Xóa nhóm
           $group->delete();
 
 
        // Xóa các thành viên
        GroupMember::where('group_id', $group->id)->delete();

    return redirect()->back()->with('success', 'Nhóm đã được xóa và thông báo đã được gửi đến các thành viên!');
}

}
