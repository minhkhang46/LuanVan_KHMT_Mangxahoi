<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Friend;
use App\Models\UserNd;
use App\Models\Messager;
use App\Models\Notification;
use App\Notifications\NewPostNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class NotificationController extends Controller
{
  
    
    public function shownotification(Request $request, $id = null)
    {
        $userId = session('id');
    
        if (!$id) {
            $id = $userId;
        }
    
        // Xử lý trạng thái lọc thông báo
        $status = $request->query('status', 'all');
    
        $query = Notification::where('user_id', $userId);
    
        if ($status == 'unread') {
            $query->where('read_at', 0);
        }
    
        $notifications = $query->orderBy('created_at', 'desc')->get();
        $notificationCount = Notification::where('user_id', $userId)
        ->where('read_at', 0) 
        ->count();
    
        // Đếm số lượng tin nhắn chưa đọc
        $newMessagesCount = Messager::where('receiver_id', $userId)
                                     ->where('is_read', 0) 
                                     ->count();
                                     $noUnreadNotifications = $status == 'unread' && $notificationCount == 0;
        return view('notification', [
            'notifications' => $notifications,
            'newMessagesCount' => $newMessagesCount,
            'notificationCount' => $notificationCount,
            'status' => $status,
            'noUnreadNotifications'=>$noUnreadNotifications
        ]);
    }
    
    
    
    
    public function markAsRead($notificationId)
    {
        $userId = session('id');
        
        // Tìm và cập nhật trạng thái của thông báo
        $notification = Notification::where('id', $notificationId)
                                     ->where('user_id', $userId)
                                     ->first();
        
        if ($notification && $notification->read_at == 0) {
            $notification->read_at = 1;
            $notification->save();
        }
        
        // Kiểm tra xem thông báo có URL đính kèm không
        $data = json_decode($notification->data);
     
        // Nếu có URL, điều hướng người dùng đến URL đó
        if (isset($data->url)) {
            return redirect($data->url);
        }
        
        // Nếu không có URL, quay lại danh sách thông báo
        return redirect()->route('notifications', ['id' => $userId]);
    }
    
    public function fetchNotifications(Request $request)
    {
        $userId = session('id');
        $status = $request->query('status', 'all');
    
        $query = Notification::where('user_id', $userId);
    
        if ($status == 0) {
            $query->where('read_at', 0);
        }
    
        $notifications = $query->get();
        $newCount = $notifications->where('read_at', 0)->count();
    
        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                $data = json_decode($notification->data);
                return [
                    'id' => $notification->id,
                    'message' => $data->message,
                    'reason' => $data->reason,
                    'contact' => $data->contact,
                    'avatar' => asset('storage/' . ($data->avatar ?? 'images/default-avatar.png')),
                    'isRead' => $notification->read_at ? true : false
                ];
            }),
            'newCount' => $newCount
        ]);
    }
    

}