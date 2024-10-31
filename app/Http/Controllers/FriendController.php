<?php

namespace App\Http\Controllers;
use App\Models\UserNd;
use App\Models\Friend;
use App\Models\Notification;
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
    $currentUserId = session('id');

    // Tìm yêu cầu kết bạn giữa hai người dùng
    $friendship = Friend::where(function($query) use ($currentUserId, $id) {
        $query->where('user_id', $currentUserId)
              ->where('friend_id', $id);
    })->orWhere(function($query) use ($currentUserId, $id) {
        $query->where('user_id', $id)
              ->where('friend_id', $currentUserId);
    })->first();

    if ($friendship) {
        // Xóa mối quan hệ bạn bè
        $friendship->delete();
        return back()->with('success', 'Đã xóa bạn bè.');
    }

    return back()->with('error', 'Không tìm thấy bạn bè này.');
}

}
