<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNd;
use App\Models\Post;
use App\Models\Friend;
use App\Models\Messager;
use App\Models\Notification;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Comment;
class HomeController extends Controller
{
   

    public function index()
    {
        $currentUserId = session('id'); 
        $id = Auth::id();
        
        // Cập nhật trạng thái trực tuyến cho người dùng hiện tại
        if ($currentUserId) {
            $currentUser = UserNd::find($currentUserId);
            if ($currentUser) {
                $currentUser->last_seen = now(); // Cập nhật thời gian hiện tại
                $currentUser->save();
            }
        }
    
        // Lấy tất cả các ID bạn bè (bao gồm cả user_id và friend_id)
        $friendIds = Friend::where(function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId)
                  ->orWhere('friend_id', $currentUserId);
        })
        ->where('status', 1)
        ->pluck('user_id')
        ->merge(Friend::where(function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId)
                  ->orWhere('friend_id', $currentUserId);
        })
        ->where('status', 1)
        ->pluck('friend_id'))
        ->unique();
    
        // Lấy tất cả người dùng với ID đã lấy
        $allUsers = UserNd::whereIn('id', $friendIds)->get();
    
        $onlineUsers = [];
        foreach ($allUsers as $user) {
            $lastSeen = $user->last_seen ? Carbon::parse($user->last_seen) : null;
            $userIsOnline = $lastSeen && $lastSeen->diffInMinutes(now()) < 5;
            $onlineUsers[$user->id] = [
                'is_online' => $userIsOnline,
                'last_seen' => $lastSeen
            ];
        }
    
        // Lấy thông tin bạn bè liên quan đến các ID
        $friends = UserNd::whereIn('id', $friendIds)->get();
    
        // Lấy tất cả bài đăng và kiểm tra chế độ "Bạn bè" hoặc "Công khai"
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
        $filteredPosts = $posts->filter(function ($post) use ($currentUserId, $friendIds) {
            // Kiểm tra nếu bài đăng có chế độ bạn bè
            if ($post->regime == 1) {
                // Nếu chế độ bạn bè, kiểm tra người dùng hiện tại có là bạn bè với người đăng bài không
                return $friendIds->contains($post->id_nd);
            }
            // Nếu bài đăng công khai (regime == 0), hiển thị cho tất cả mọi người
            return true;
        });
    
        // Lấy danh sách id người dùng liên quan từ các bài đăng đã lọc
        $userIds = $filteredPosts->pluck('id_nd')->unique();
    
        // Lấy thông tin người dùng liên quan đến bài đăng
        $users = UserNd::whereIn('id', $userIds)->get()->keyBy('id');
    
        // Lấy danh sách id nhóm trong bảng post
        $groupIds = $filteredPosts->pluck('group_id')->unique();
    
        // Lấy danh sách nhóm
        $groups = Group::whereIn('id', $groupIds)->get()->keyBy('id');
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
        
        // Thêm thông tin người dùng và nhóm vào các bài đăng đã lọc
        foreach ($filteredPosts as $post) {
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
                $post->group_name = 'Không có nhóm'; // Hoặc giá trị khác nếu nhóm không tồn tại
            }
    
            // Kiểm tra người dùng hiện tại đã thích bài đăng chưa
            $post->is_liked = Like::where('user_id', $currentUserId)
                ->where('post_id', $post->id)
                ->exists();
            $post->likes_count = Like::where('post_id', $post->id)->count();
        }
    
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
            ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
            ->count();
    
        $notificationCount = Notification::where('user_id', $currentUserId)
            ->where('read_at', 0)
            ->count();
    
        // Lấy gợi ý kết bạn
        $friendStatus = Friend::where(function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId)
                  ->orWhere('friend_id', $currentUserId);
        })
        ->whereIn('status', [0, 1])
        ->pluck('user_id')
        ->merge(Friend::where(function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId)
                  ->orWhere('friend_id', $currentUserId);
        })
        ->whereIn('status', [0, 1])
        ->pluck('friend_id'))
        ->unique();
    
        $friendSuggestions = UserNd::where('id', '!=', $currentUserId)
            ->whereNotIn('id', $friendStatus)
            ->get();
  
        return view('welcome', [
            'id' => $id,
            'posts' => $filteredPosts, // Chỉ những bài đăng đã lọc được hiển thị
            'newMessagesCount' => $newMessagesCount,
            'friends' => $friends,
            'notificationCount' => $notificationCount,
            'onlineUsers' => $onlineUsers,
            'likes' => $likes,
            'friendSuggestions' => $friendSuggestions,'imageSize'=>$imageSize ,'postsWithComments'=>$postsWithComments
        ]);
    }
    
    
    
    
    //Hàm hiển thị thông tin cá nhân
    public function profile($id)
    {
        // Lấy ID của người dùng hiện tại từ session
        $currentUserId = session('id');
        // Lấy thông tin người dùng theo ID
        $user = UserNd::findOrFail($id);
    
        // Lấy tất cả bài viết của người dùng theo ID
        $posts = Post::where('id_nd', $id)->orderBy('created_at', 'desc')->get();
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
        // Lấy danh sách id_nd duy nhất từ bài viết
        $userIds = $posts->pluck('id_nd')->unique(); 
    
        // Lấy thông tin người dùng theo danh sách ID
        $users = UserNd::whereIn('id', $userIds)->get()->keyBy('id'); 
        // cho người dùng like bài viết
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
            $postsWithComments[$post->id] = [
                'post' => $post, // Thêm thông tin bài viết
                'comment_count' => $commentCount,
                'comments' => $commentsWithUser,
            ];
        }
        
        // Kết hợp thông tin người dùng vào bài viết
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
    
        
        if (!$currentUserId) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem trang này.');
        }
    
        // Đếm số lượng bạn bè của người dùng hiện tại
        $totalFriends = Friend::where(function ($query) use ($id) {
            $query->where('user_id', $id)
                  ->orWhere('friend_id', $id);
        })->where('status', 1) // Trạng thái 1: Đã là bạn bè
          ->count();
          $friends = Friend::where(function ($query) use ($id) {
            $query->where('user_id', $id)
                  ->orWhere('friend_id', $id);
        })->where('status', 1)->get();

    $friendInfos = [];
    $sessionUserId = session('id');

    foreach ($friends as $friend) {
        $friendIds = collect([$friend->user_id, $friend->friend_id])
            ->filter(fn($friendId) => $friendId !== (int)$id);

        $friendDetails = UserNd::whereIn('id', $friendIds)->get()->keyBy('id');

        $friendInfoUserId = $friendDetails->get($friend->user_id);
        $friendInfoFriendId = $friendDetails->get($friend->friend_id);

        if ($friendInfoUserId && $friendInfoUserId->id !== (int)$id) {
            $friendInfos[] = $friendInfoUserId;
        } elseif ($friendInfoFriendId && $friendInfoFriendId->id !== (int)$id) {
            $friendInfos[] = $friendInfoFriendId;
        }
    }
    
        // Hiển thị trạng thái yêu cầu kết bạn
        $requestStatus = Friend::where('user_id', $currentUserId)
                               ->where('friend_id', $id)
                               ->first();
    
        // Kiểm tra yêu cầu kết bạn đã nhận từ người dùng hiện tại
        $receivedRequest = Friend::where('user_id', $id)
                                 ->where('friend_id', $currentUserId)
                                 ->first();
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
                                 ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
                                 ->count();
         $notificationCount = Notification::where('user_id', $currentUserId)
                                 ->where('read_at', 0)
                                 ->count();                     
        // Truyền tất cả dữ liệu cần thiết đến view
        return view('profile', compact('user', 'imageSize','posts', 'requestStatus', 'receivedRequest', 'totalFriends', 'newMessagesCount', 'id','friendInfos', 'sessionUserId', 'notificationCount', 'likes', 'postsWithComments'));
    
    }
    
    

    //Ham xưr lý đăng nhập
    public function login(Request $request)
{
    $emailorphone = $request->input('emailorphone');
    $password = md5($request->input('password'));

    // Tìm người dùng bằng email hoặc số điện thoại
    $user = UserNd::where('email', $emailorphone)
                  ->orWhere('phone', $emailorphone)
                  ->first();

    if (!$user) {
        // Nếu không tìm thấy người dùng, hiển thị thông báo không có tài khoản
        return redirect()->back()->with('error', 'Bạn chưa có tài khoản. Vui lòng đăng ký.');
    }

    if ($password == $user->password) {
        if ($user->status == 0) {
            // Lưu thông tin vào session nếu tài khoản đang mở
            session()->put('id', $user->id);
            session()->put('name', $user->name);
            session()->put('email', $user->email);
            session()->put('phone', $user->phone);
            session()->put('date', $user->date);
            session()->put('avatar', $user->avatar);
            session()->put('possition', $user->possition);

            return redirect()->route('homes', ['id' => $user->id])->with('success', 'Đăng nhập thành công.');
        } else {
            // Tài khoản bị khóa, trả về thông báo lỗi
            return redirect()->back()->with('error', 'Tài khoản của bạn hiện đang bị khóa.');
        }
    } 

    // Xử lý nếu đăng nhập thất bại
    return redirect()->back()->with('error', 'Thông tin đăng nhập không chính xác. Vui lòng thử lại.');
}


    // hàm xử lý đăng xuất
     public function logout()
    {
        Auth::logout(); // Đăng xuất người dùng
        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất thành công.');
    }
    //Hiển thị trang đăng ký
    public function register_index(){
        return view('register');
    }

    //Hàm đăng ký tài khoản
    public function register(Request $request)
    {
        // Kiểm tra xem số điện thoại hoặc email đã tồn tại chưa
        $existingUser = UserNd::where('email', $request->email)
                               ->orWhere('phone', $request->phone)
                               ->first();
    
        if ($existingUser) {
            // Nếu đã tồn tại, hiển thị thông báo lỗi
            return redirect()->route('register')->with('error', 'Số điện thoại hoặc email đã tồn tại. Vui lòng thử lại');
        }
    
        // Xử lý tải lên avatar nếu có
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        } else {
            $avatarPath = 'default/avatar.png'; // Đường dẫn tới ảnh mặc định nếu người dùng không tải lên avatar
        }
    
        // Kiểm tra và lưu CV
        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $cvName = time() . '_' . $cv->getClientOriginalName();
            $cv->storeAs('cv', $cvName, 'public'); // Lưu vào thư mục cv trong storage
            $cvPath = $cvName; // Lưu đường dẫn file CV
        } else {
            $cvPath = null; // Nếu không có file CV, gán là null hoặc xử lý tùy ý
        }
    
        // Tạo người dùng mới
        $user_nd = UserNd::create([
            'name' => $request->name,
            'password' => md5($request->password), // Băm mật khẩu bằng MD5
            'email' => $request->email,
            'phone' => $request->phone,
            'date' => $request->date,
            'gender' => $request->gender,
            'avatar' => $avatarPath, // Lưu đường dẫn avatar vào cơ sở dữ liệu
            'description' => $request->description,
            'chuyende' => $request->chuyende,
            'cv' => $cvPath, // Lưu đường dẫn file CV vào cơ sở dữ liệu
            'possiton' => $request->possition
        ]);
    
        // Kiểm tra xem người dùng đã được tạo thành công hay không
        if ($user_nd) {
            // Nếu thành công, chuyển hướng hoặc thực hiện các thao tác khác ở đây
            return redirect()->route('register')->with('success', 'Tạo tài khoản thành công!');
        } else {
            // Nếu không thành công, chuyển hướng hoặc hiển thị thông báo lỗi tương ứng
            return redirect()->route('register')->with('error', 'Đã xảy ra lỗi khi tạo tài khoản.');
        }
    }
    

    public function timKiem(Request $request)
    {
        $keyword = $request->input('keyword');
        $currentUserId = session('id');
      
        // Tìm kiếm người dùng
        $users = UserNd::where('name', 'LIKE', "%{$keyword}%")
        ->orWhere('email', 'LIKE', "%{$keyword}%")
        ->paginate(10);
        
        $uIds = $users->pluck('id');
     
        // Lấy group_id từ bảng groupmember cho người dùng đã tìm kiếm
        $grIds = GroupMember::whereIn('user_id', $uIds)->pluck('group_id');
    
        // Lấy thông tin nhóm từ bảng group theo group_id
        $g = Group::whereIn('id', $grIds)->get();
        
        // Tìm kiếm bài đăng của người dùng
        $posts = Post::where('noidung', 'LIKE', "%{$keyword}%")
        ->orWhereIn('id_nd', $users->pluck('id')) // Lấy các bài viết của người dùng đã tìm
        ->orWhere('topic', 'LIKE', "%{$keyword}%") // Tìm kiếm theo chủ đề
        ->select('id', 'noidung', 'id_nd', 'group_id', 'created_at', 'files', 'images',  'topic', 'regime')
        ->orderBy('created_at', 'desc')
        ->get();

// dd($posts);
        // Lấy danh sách người dùng và nhóm
        $userpost = $posts->pluck('id_nd')->unique();
        $grouppost = $posts->pluck('group_id')->unique();

        $userps = UserNd::whereIn('id', $userpost)->get()->keyBy('id');
        $groupps = Group::whereIn('id', $grouppost)->get()->keyBy('id');
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
            $postsWithComments[$post->id] = [
                'post' => $post, // Thêm thông tin bài viết
                'comment_count' => $commentCount,
                'comments' => $commentsWithUser,
            ];
        }
        // Gán tên người dùng và tên nhóm cho mỗi bài viết
        foreach ($posts as $post) {
            $post->user_name = $userps->get($post->id_nd)->name ?? 'Unknown';
            $post->user_avatar = $userps->get($post->id_nd)->avatar ?? 'Unknown';
            $post->group_name = $groupps->get($post->group_id)->name ?? 'Unknown';
            $post->group_image =  $groupps->get($post->group_id)->image ?? 'Unknown';
            $post->is_liked = Like::where('user_id', $currentUserId)
            ->where('post_id', $post->id)
            ->exists();
            $post->likes_count = Like::where('post_id', $post->id)->count();
            
        }
        // Tìm kiếm nhóm
       
        $groups = Group::where('name', 'LIKE', "%{$keyword}%")->get();
        //  dd($groups, $posts);
        $searchGroups = Group::where('name', 'LIKE', "%{$keyword}%")->get();


        // Nếu không tìm thấy nhóm nào theo từ khóa, lấy tất cả nhóm
        // if ($groups->isEmpty()) {
        //     $groups = Group::all();
        // }
        foreach ($groups as $group) {
            $group->member_count = GroupMember::where('group_id', $group->id)->count();
        }
     
        // Lấy tất cả dữ liệu thành viên nhóm
       // Lấy tất cả thành viên của nhóm
        $groupMembersData = GroupMember::all();

        // Lọc user_id từ $groupMembersData
        $userIds = GroupMember::whereIn('group_id', $groups->pluck('id'))->pluck('user_id')->toArray();

        // Tìm kiếm thành viên trong bảng UserNd dựa trên từ khóa
        $groupMembers = UserNd::whereIn('id', $userIds)
            ->where('name', 'LIKE', "%{$keyword}%")
            ->get();
      
        // Lấy group_ids để tìm tên nhóm
        $groupIds = GroupMember::whereIn('group_id', $groups->pluck('id'))->pluck('group_id')->toArray();
        // Lấy tất cả group_id từ các nhóm tìm kiếm
       
        $post = Post::whereIn('group_id', $groupIds)
            ->select('id', 'noidung', 'id_nd', 'group_id', 'created_at', 'files', 'images', 'topic', 'regime')
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy tất cả người dùng và nhóm một lần
        $u = UserNd::whereIn('id', $post->pluck('id_nd')->toArray())->get()->keyBy('id');
        $gr = Group::whereIn('id', $post->pluck('group_id')->toArray())->get()->keyBy('id');

        // Duyệt qua các bài viết và gán thông tin người dùng và nhóm
        foreach ($post as $p) {
            $p->user = $u->get($p->id_nd)->name ?? 'Unknown';
            $p->user_img = $u->get($p->id_nd)->avatar ?? 'Unknown';
            // Kiểm tra xem nhóm có tồn tại không
            $group = $gr->get($p->group_id);
            if ($group) {
                $p->group = [
                    'name' => $group->name,
                    'image' => $group->image // Giả sử bạn có thuộc tính `image` trong nhóm
                ];
            } else {
                $p->group = null; // Gán null nếu nhóm không tồn tại
            }
        }

        // Lấy user_id từ bảng GroupMember dựa trên group_id
        $groupMembersList = GroupMember::whereIn('group_id', $groupIds)->get();

        // Lấy tất cả user_id từ các bản ghi groupMembersList
        $userIdsList = $groupMembersList->pluck('user_id')->toArray();
        
        // Lấy thông tin người dùng từ bảng UserNd dựa trên user_id
        $nameMembers = UserNd::whereIn('id', $userIdsList)->get();
        
        // Khởi tạo mảng để lưu tên nhóm cho từng thành viên
        $membersWithGroups = [];
        
        // Kết hợp thông tin nhóm với thành viên
        foreach ($nameMembers as $m) {
            // Lấy tất cả group_id cho từng thành viên từ groupMembersList
            $groupIdsForMember = $groupMembersList->where('user_id', $m->id)->pluck('group_id');
        
            // Lấy thông tin nhóm cho tất cả group_id
            $groupDetails = Group::whereIn('id', $groupIdsForMember)->get();
        
            // Gán tên nhóm vào thuộc tính group_names của thành viên
            $m->group_names = $groupDetails->pluck('name')->toArray();
        
            // Thêm vào mảng thành viên với nhóm
            $membersWithGroups[] = $m;
        }
        


        // Lấy tên nhóm từ bảng Group
        $groupNames = Group::whereIn('id', $grIds)->pluck('name', 'id')->toArray();

        // Gán tên nhóm cho từng thành viên
      // Giả sử $users chứa danh sách người dùng đã tìm kiếm
    foreach ($users as $member) {
        // Khởi tạo mảng để lưu group_names
        $groupNamesList = [];
        
        // Lấy tất cả group_id của thành viên trong bảng GroupMember
        $memberGroupIds = GroupMember::where('user_id', $member->id)
            ->pluck('group_id'); // Lấy tất cả group_id mà thành viên này tham gia

        // Kiểm tra nếu có group_id
        if ($memberGroupIds->isNotEmpty()) {
            // Lấy tên nhóm từ bảng Group
            $groupNames = Group::whereIn('id', $memberGroupIds)->pluck('name', 'id')->toArray();

            // Duyệt qua các group_id để lấy tên nhóm
            foreach ($memberGroupIds as $groupId) {
                $groupNamesList[] = $groupNames[$groupId] ?? 'Chưa tham gia nhóm'; // Lưu tên nhóm vào mảng
            }
        }

    // Gán tên nhóm cho thành viên
    $member->group_names = $groupNamesList; // Gán danh sách tên nhóm
}

        // Lấy danh sách bạn bè từ cả hai phía
        $friends = Friend::where(function ($query) use ($currentUserId) {
                            $query->where('user_id', $currentUserId)
                                  ->orWhere('friend_id', $currentUserId);
                        })
                        ->get()
                        ->mapWithKeys(function ($item) use ($currentUserId) {
                            $key = ($item->user_id == $currentUserId) ? $item->friend_id : $item->user_id;
                            return [$key => $item->status, 'user_id' => $item->user_id];
                        });
    
        // Đếm số tin nhắn chưa đọc
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
                            ->where('is_read', 0)
                            ->count();
    
        // Đếm số thông báo chưa đọc
        $notificationCount = Notification::where('user_id', $currentUserId)
                            ->where('read_at', 0)
                            ->count();
        $hasResults = $users->isNotEmpty() || $posts->isNotEmpty() || $groups->isNotEmpty();  
   
      
        // Trả về view với cả người dùng, bài đăng, và nhóm
        return view('search', [
            'users' => $users,
            'posts' => $posts,
            'post' => $post,
            'groups' => $groups,
            'g' => $g,
            'keyword' => $keyword,
            'friends' => $friends,
            'currentUserId' => $currentUserId,
            'newMessagesCount' => $newMessagesCount,
            'notificationCount' => $notificationCount,
            'searchGroups' => $searchGroups,
            'hasResults' => $hasResults,
            'postsWithComments'=>$postsWithComments,
            'groupMembers' => $groupMembers, 'likes'=>$likes ,'membersWithGroups'=>$membersWithGroups,'nameMembers'=>$nameMembers
        ]);
    
    
    }
    


    //hiển thị trang cá nhân tìm kiếm
    public function show($id)
    {
        // Lấy thông tin người dùng theo ID
        $user = UserNd::findOrFail($id);
        $currentUserId = session('id');
           // Lấy tất cả các ID bạn bè (bao gồm cả user_id và friend_id)
        $friendIds = Friend::where(function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId)
                  ->orWhere('friend_id', $currentUserId);
        })
        ->where('status', 1)
        ->pluck('user_id')
        ->merge(Friend::where(function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId)
                  ->orWhere('friend_id', $currentUserId);
        })
        ->where('status', 1)
        ->pluck('friend_id'))
        ->unique();
        // Lấy tất cả bài viết của người dùng theo ID
        $posts = Post::where('id_nd', $id)->orderBy('created_at', 'desc')->get();
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
  $filteredPosts = $posts->filter(function ($post) use ($currentUserId, $friendIds) {
            // Kiểm tra nếu bài đăng có chế độ bạn bè
            if ($post->regime == 1) {
                // Nếu chế độ bạn bè, kiểm tra người dùng hiện tại có là bạn bè với người đăng bài không
                return $friendIds->contains($post->id_nd);
            }
            // Nếu bài đăng công khai (regime == 0), hiển thị cho tất cả mọi người
            return true;
        });
    
        // Lấy danh sách id người dùng liên quan từ các bài đăng đã lọc
        $userIds = $filteredPosts->pluck('id_nd')->unique();
    
        // Lấy thông tin người dùng liên quan đến bài đăng
        $users = UserNd::whereIn('id', $userIds)->get()->keyBy('id');
    
        // Lấy danh sách id nhóm trong bảng post
        $groupIds = $filteredPosts->pluck('group_id')->unique();
    
        // Lấy danh sách nhóm
        $groups = Group::whereIn('id', $groupIds)->get()->keyBy('id');
        $likes = [];
        foreach ($posts as $post) {
            $likedUserIds = Like::where('post_id', $post->id)->pluck('user_id');
            $likes[$post->id] = UserNd::whereIn('id', $likedUserIds)->get(['name', 'avatar', 'id']);
        }
        // Thêm thông tin người dùng và nhóm vào các bài đăng đã lọc
        foreach ($filteredPosts as $post) {
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
                $post->group_name = 'Không có nhóm'; // Hoặc giá trị khác nếu nhóm không tồn tại
            }
    
            // Kiểm tra người dùng hiện tại đã thích bài đăng chưa
            $post->is_liked = Like::where('user_id', $currentUserId)
                ->where('post_id', $post->id)
                ->exists();
            $post->likes_count = Like::where('post_id', $post->id)->count();
        }
    
        // Lấy ID của người dùng hiện tại từ session
     
        if (!$currentUserId) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem trang này.');
        }
    
        // Đếm số lượng bạn bè của người dùng hiện tại
        $totalFriends = Friend::where(function ($query) use ($id) {
            $query->where('user_id', $id)
                  ->orWhere('friend_id', $id);
        })->where('status', 1) // Trạng thái 1: Đã là bạn bè
          ->count();
          
          $friends = Friend::where(function ($query) use ($id) {
            $query->where('user_id', $id)
                  ->orWhere('friend_id', $id);
        })->where('status', 1)->get();

        $friendInfos = [];
        $sessionUserId = session('id');

        foreach ($friends as $friend) {
            $friendIds = collect([$friend->user_id, $friend->friend_id])
                ->filter(fn($friendId) => $friendId !== (int)$id);

            $friendDetails = UserNd::whereIn('id', $friendIds)->get()->keyBy('id');

            $friendInfoUserId = $friendDetails->get($friend->user_id);
            $friendInfoFriendId = $friendDetails->get($friend->friend_id);

            if ($friendInfoUserId && $friendInfoUserId->id !== (int)$id) {
                $friendInfos[] = $friendInfoUserId;
            } elseif ($friendInfoFriendId && $friendInfoFriendId->id !== (int)$id) {
                $friendInfos[] = $friendInfoFriendId;
            }
        }
            
        $postsWithComments = []; 

        foreach ($posts as $post) {
            // Đếm số bình luận cho bài viết
            $commentCount = Comment::where('post_id', $post->id)->count();
            
            // Lấy các bình luận và thông tin người dùng cho bài viết
            $comments = Comment::where('post_id', $post->id)->get();
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
            $postsWithComments[$post->id] = [
                'post' => $post, // Thêm thông tin bài viết
                'comment_count' => $commentCount,
                'comments' => $commentsWithUser,
            ];
        }
          
        // Hiển thị trạng thái yêu cầu kết bạn
        $requestStatus = Friend::where('user_id', $currentUserId)
                               ->where('friend_id', $id)
                               ->first();
    
        // Kiểm tra yêu cầu kết bạn đã nhận từ người dùng hiện tại
        $receivedRequest = Friend::where('user_id', $id)
                                 ->where('friend_id', $currentUserId)
                                 ->first();
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
                                 ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
                                 ->count();
        $notificationCount = Notification::where('user_id', $currentUserId)
                                 ->where('read_at', 0)
                                 ->count();
        // Truyền tất cả dữ liệu cần thiết đến view
      
        return view('users', [
            'id'=>$id,
            'user' => $user,
            'posts' => $filteredPosts, 
            'requestStatus'=>$requestStatus,
            'receivedRequest'=>$receivedRequest,
            'newMessagesCount' => $newMessagesCount,
            'totalFriends'=>$totalFriends,
            'friends' => $friends,
            'notificationCount' => $notificationCount,
            'friendInfos' => $friendInfos,
            'likes' => $likes,
            'sessionUserId' => $sessionUserId,
            'imageSize' => $imageSize,'postsWithComments' =>$postsWithComments
        ]);
    }
    
    // hàm cập nhật thông tin người dùng
    public function update(Request $request, $id)
    {
        // Tìm người dùng với id được truyền vào
        $user = UserNd::find($id);
    
        // Kiểm tra nếu không tìm thấy người dùng
        if (!$user) {
            return redirect()->back()->with('error', 'Người dùng không tồn tại.');
        }
    
        // Kiểm tra đầu vào có khớp với ID người dùng hay không
        if ($user->id == $id) {
            // Validate dữ liệu đầu vào, các trường không bắt buộc (nullable)
          
    
            // Cập nhật tên, số điện thoại, ngày sinh, mô tả (nếu có)
            if ($request->input('name')) {
                $user->name = $request->input('name');
            }
            if ($request->input('phone')) {
                $user->phone = $request->input('phone');
            }
            if ($request->input('date')) {
                $user->date = $request->input('date');
            }
            if ($request->input('description')) {
                $user->description = $request->input('description');
            }
            if ($request->input('chuyende')) {
                $user->chuyende = $request->input('chuyende'); // Cập nhật chuyên đề
            }
    
            // Kiểm tra và cập nhật file CV nếu có upload
            if ($request->hasFile('cv')) {
                $cv = $request->file('cv');
                $cvName = time() . '_' . $cv->getClientOriginalName();
                $cv->storeAs('cv', $cvName, 'public'); // Lưu vào thư mục cv trong storage
                $user->cv = $cvName;
            }
    
            // Lưu lại thông tin người dùng
            $user->save();
    
            // Chuyển hướng về trang trước với thông báo thành công
            return redirect()->back()->with('success', 'Cập nhật thông tin thành công.');
        } else {
            // Nếu ID không khớp, trả về lỗi
            return redirect()->back()->with('error', 'Không thể cập nhật thông tin.');
        }
    }

    // hàm hiển thị nguời dùng để quản lý tài khoản 
    public function usermanger(){
        $currentUserId = session('id');
        $user_ma = UserNd::all();
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
            ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
            ->count();
        $notificationCount = Notification::where('user_id', $currentUserId)
            ->where('read_at', 0)
            ->count();
        return view('admin.user', ['user_ma'=>$user_ma,  'newMessagesCount' => $newMessagesCount,'notificationCount' => $notificationCount,]);
    }
    
    // hàm khóa tài khoản người dùng
    public function toggleStatus($id)
    {
        $user = UserNd::findOrFail($id); // Sử dụng UserNd thay vì User
        $user->status = !$user->status; // Đảo ngược trạng thái hiện tại
        $user->save();
        $message = $user->status ? 'Tài khoản đã bị khóa' : 'Tài khoản đã mở khóa';
        return redirect()->route('user')->with('success', $message);
    }

}
