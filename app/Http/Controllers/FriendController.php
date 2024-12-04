<?php

namespace App\Http\Controllers;
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
use Illuminate\Http\Request;

class FriendController extends Controller
{
    //Hàm gửi lời mời
    public function sendRequest($id)
    {
        $userId = session('id');
        $friendId = $id;
    
        // Kiểm tra nếu yêu cầu kết bạn đã tồn tại
        $existingRequest = Friend::where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->where('status', 0)
            ->exists();
    
        if ($existingRequest) {
            return back()->with('info', 'Bạn đã gửi yêu cầu kết bạn tới người dùng này.');
        }
    
        // Thêm yêu cầu kết bạn vào bảng friends
        Friend::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => 0 // 0: pending
        ]);
    
        // Tạo thông báo cho người nhận yêu cầu kết bạn
        $sender = UserNd::find($userId);
        $receiver = UserNd::find($friendId);
    
        if ($sender && $receiver) {
            Notification::create([
                'user_id' => $friendId,
                'type' => 'friend_request',
                'data' => json_encode([
                    'message' => "{$sender->name} đã gửi lời mời kết bạn cho bạn.",
                    'avatar' => $sender->avatar ?? 'default-avatar.png',
                    'url' => route('profiles', $sender->id),
                     // Thêm URL dẫn tới trang cá nhân của người gửi
                ]),
                'read_at' => 0, // Đánh dấu thông báo là chưa đọc
            ]);
            
        }
    
        return back()->with('success', 'Yêu cầu kết bạn đã được gửi!');
    }
    

    //hàm chấp nhận
    public function acceptRequest($id)
    {
        $currentUserId = session('id');
        $friendId = $id;
    
        // Tìm yêu cầu kết bạn mà người dùng hiện tại đã nhận
        $request = Friend::where('user_id', $friendId)
            ->where('friend_id', $currentUserId)
            ->where('status', 0) // 0: pending
            ->first();
    
        // Kiểm tra nếu yêu cầu kết bạn không tồn tại hoặc đã được chấp nhận
        if (!$request) {
            return back()->with('error', 'Yêu cầu kết bạn không tồn tại hoặc đã được xử lý.');
        }
    
        // Cập nhật trạng thái yêu cầu kết bạn thành accepted
        $request->status = 1; // 1: accepted
        $request->save();
    
        // Lấy thông tin người dùng
        $receiver = UserNd::find($friendId); // Người đã gửi yêu cầu kết bạn
        $currentUser = UserNd::find($currentUserId); // Người đang chấp nhận yêu cầu
    
        // Tạo thông báo cho người gửi yêu cầu kết bạn
        if ($receiver && $currentUser) {
            Notification::create([
                'user_id' => $friendId,
                'type' => 'friend_request_accepted',
                'data' => json_encode([
                    'message' => "{$currentUser->name} đã chấp nhận yêu cầu kết bạn của bạn.",
                    'avatar' => $currentUser->avatar ?? 'default-avatar.png',
                    'url' => route('profiles', $currentUser->id),
                ]),
                'read_at' => 0, // Đánh dấu thông báo là chưa đọc
            ]);
        }
    
        return back()->with('success', 'Yêu cầu kết bạn đã được chấp nhận!');
    }

    // hàm xóa kết bạn
    public function removeFriend($id)
    {
        $currentUserId = session('id'); // Lấy ID người dùng hiện tại từ session
    
        // Tìm tất cả các mối quan hệ bạn bè (tìm cả theo chiều ngược lại)
        $friendships = Friend::where(function($query) use ($currentUserId, $id) {
            $query->where('user_id', $currentUserId)
                  ->where('friend_id', $id);
        })
        ->orWhere(function($query) use ($currentUserId, $id) {
            $query->where('user_id', $id)
                  ->where('friend_id', $currentUserId);
        })
        ->get();
    
        // Kiểm tra nếu có mối quan hệ bạn bè
        if ($friendships->isNotEmpty()) {
            // Xóa tất cả các bản ghi liên quan đến mối quan hệ bạn bè
            $friendships->each(function($friendship) {
                $friendship->delete();
            });
    
            // Trả về thông báo thành công và quay lại trang trước
            return back()->with('success', 'Đã xóa bạn bè.');
        }
    
        // Nếu không tìm thấy bạn bè
        return back()->with('error', 'Không tìm thấy bạn bè này.');
    }
    
// hàm từ chối bạn bè
public function rejectFriendRequest($friendId)
{
    $userId = session('id'); // Lấy ID người dùng hiện tại từ session

    // Tìm mối quan hệ bạn bè trong bảng friends
    $friendship = Friend::where(function ($query) use ($userId, $friendId) {
        $query->where('user_id', $userId)->where('friend_id', $friendId)
              ->orWhere('user_id', $friendId)->where('friend_id', $userId);
    })
    ->where('status', 0) // Chỉ xử lý các yêu cầu kết bạn đang chờ
    ->first();

    // Kiểm tra nếu tìm thấy mối quan hệ bạn bè và trạng thái là 'pending'
    if ($friendship) {
        // Cập nhật trạng thái thành 'rejected'
        $friendship->status = 2;
        $friendship->save();

        // Chuyển hướng về trang trước đó và gửi thông báo
        return redirect()->back()->with('success', 'Đã từ chối yêu cầu kết bạn.');
    }

    // Nếu không tìm thấy yêu cầu kết bạn hoặc yêu cầu không còn trong trạng thái 'pending'
    return redirect()->back()->with('error', 'Không tìm thấy yêu cầu kết bạn hoặc yêu cầu đã bị thay đổi.');
}

public function friendList($id)
{
    $currentUserId = session('id'); // Lấy ID người dùng hiện tại từ session

    // Lấy danh sách bạn bè của người dùng khác
    $friendIds = Friend::where(function ($query) use ($id) {
        $query->where('user_id', $id)
              ->orWhere('friend_id', $id);
    })
    ->where('status', 1) // Chỉ lấy bạn bè có status = 1
    ->get(['user_id', 'friend_id']);
// dd($friendIds);
    // Lấy các ID bạn bè của người dùng khác
    $friendIds = $friendIds->map(function ($friend) use ($id) {
        return $friend->user_id == $id ? $friend->friend_id : $friend->user_id;
    });

    // Khởi tạo mảng trống nếu không có bạn bè
    $friendIds = $friendIds->isEmpty() ? collect() : $friendIds;

    // Lấy danh sách bạn bè của người dùng hiện tại
    $currentUserFriendIds = Friend::where(function ($query) use ($currentUserId) {
        $query->where('user_id', $currentUserId)
              ->orWhere('friend_id', $currentUserId);
    })
    ->where('status', 1)
    ->get(['user_id', 'friend_id']);

    // Lấy các ID bạn bè của người dùng hiện tại
    $currentUserFriendIds = $currentUserFriendIds->map(function ($friend) use ($currentUserId) {
        return $friend->user_id == $currentUserId ? $friend->friend_id : $friend->user_id;
    });

    // Khởi tạo mảng trống nếu không có bạn bè
    $currentUserFriendIds = $currentUserFriendIds->isEmpty() ? collect() : $currentUserFriendIds;

    // Kiểm tra xem người dùng hiện tại đã là bạn bè với ai trong danh sách
    $mutualFriends = $friendIds->intersect($currentUserFriendIds); // Danh sách bạn bè chung

    // Kiểm tra nếu người dùng hiện tại đã gửi yêu cầu kết bạn
    $sentFriendRequest = Friend::where(function ($query) use ($currentUserId, $id) {
        $query->where('friend_id', $currentUserId)->where('user_id', $id)
              ->orWhere('friend_id', $id)->where('user_id', $currentUserId);
    })
    ->where('status', 0) // Yêu cầu kết bạn đang chờ
    ->exists(); // Kiểm tra xem có yêu cầu kết bạn nào hay không
// dd( $currentUserFriendIds);
    // Lấy thông tin chi tiết của danh sách bạn bè
    $friendInfos = UserNd::whereIn('id', $friendIds)->get();

    // Dữ liệu của người dùng mà chúng ta đang xem danh sách bạn bè
    $user = UserNd::findOrFail($id);

    // Đếm số tin nhắn chưa đọc của người dùng hiện tại
    $newMessagesCount = Messager::where('receiver_id', $currentUserId)
        ->where('is_read', 0)
        ->count();

    // Đếm số thông báo chưa đọc của người dùng hiện tại
    $notificationCount = Notification::where('user_id', $currentUserId)
        ->where('read_at', 0)
        ->count();
        $isRequestSent = [];
        foreach ($friendInfos as $friend) {
            $isRequestSent[$friend->id] = Friend::where('user_id', $currentUserId)
                ->where('friend_id', $friend->id)
                ->where('status', 0)
                ->exists();
        }
    
    // Trả về view với dữ liệu danh sách bạn bè và thông tin người dùng
    return view('friend', compact('friendInfos', 'user', 'newMessagesCount', 'notificationCount', 'mutualFriends', 'sentFriendRequest','isRequestSent'));
}


}