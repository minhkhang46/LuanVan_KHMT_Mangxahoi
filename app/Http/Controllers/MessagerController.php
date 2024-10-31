<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messager;
use App\Models\UserNd;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class MessagerController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Xử lý upload file hình ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public'); // Lưu hình ảnh vào thư mục public/images
        }
    
        // Xử lý upload file đính kèm
        $filePath = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('files', 'public'); // Lưu file đính kèm vào thư mục public/files
            $fileName = $file->getClientOriginalName(); // Lưu tên gốc của file
        }
        // Tạo tin nhắn mới
        Messager::create([
            'sender_id' => session('id'),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'is_read' => 0,
            'image' => $imagePath, // Lưu đường dẫn hình ảnh
            'file' => $filePath,   // Lưu đường dẫn file
            'file_name' => $fileName // Lưu tên gốc của file
        ]);
    
        return redirect()->back()->with('success', 'Tin nhắn đã được gửi');
    }
    
// hàm hiển thị tin nhắn
public function getMessages($receiverId)
{
    $userId = session('id');

    // Lấy danh sách tin nhắn giữa người dùng hiện tại và người nhận
    $messages = Messager::where(function ($query) use ($userId, $receiverId) {
                        $query->where('sender_id', $userId)
                              ->where('receiver_id', $receiverId);
                    })
                    ->orWhere(function ($query) use ($userId, $receiverId) {
                        $query->where('sender_id', $receiverId)
                              ->where('receiver_id', $userId);
                    })
                    ->orderByRaw("CASE WHEN is_read = 0 AND receiver_id = {$userId} THEN 1 ELSE 0 END DESC") // Đưa tin nhắn chưa đọc lên đầu
                    ->orderBy('created_at', 'desc')
                    ->get();
    
    // Đánh dấu tin nhắn là đã đọc
    Messager::where('receiver_id', $userId)
    ->where('sender_id', $receiverId)
    ->where('is_read', 0)
    ->update(['is_read' => 1]);
    
    if ($userId) {
        $currentUser = UserNd::find($userId);
        if ($currentUser) {
            $currentUser->last_seen = now(); // Cập nhật thời gian hiện tại
            $currentUser->save();
        }
    }

  
    // Lấy thông tin người gửi và người nhận của mỗi tin nhắn
    $senderIds = $messages->pluck('sender_id')->unique();
    $receiverIds = $messages->pluck('receiver_id')->unique();
    
    $messageSenders = UserNd::whereIn('id', $senderIds)->get()->keyBy('id');
    $messageReceivers = UserNd::whereIn('id', $receiverIds)->get()->keyBy('id');
    
    // Lấy danh sách người dùng đã nhắn tin với người dùng hiện tại
    $messageThreads = Messager::where(function ($query) use ($userId) {
                        $query->where('sender_id', $userId)
                              ->orWhere('receiver_id', $userId);
                    })
                    ->distinct()
                    ->get(['sender_id', 'receiver_id']);
    
    $userIds = $messageThreads->pluck('sender_id')
                              ->merge($messageThreads->pluck('receiver_id'))
                              ->unique()
                              ->filter(fn($id) => $id != $userId) // Loại bỏ ID của người dùng hiện tại
                              ->toArray();
    
    $users = UserNd::whereIn('id', $userIds)->get();
    $allUsers = UserNd::all();

    // Lấy các recent_id từ bảng Messager
    $recentIds = Messager::pluck('receiver_id')->toArray();
    
    // Tạo một mảng để lưu thông tin trạng thái hoạt động và thời gian hoạt động
    $activeUsers = [];
    
    foreach ($allUsers as $user) {
      
            // Đảm bảo rằng last_seen có giá trị hợp lệ
            $lastSeen = $user->last_seen ? Carbon::parse($user->last_seen) : null;
            $userIsActive = $lastSeen && $lastSeen->diffInMinutes(now()) < 5;
    
            $activeUsers[] = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'last_seen' => $lastSeen,
                'is_active' => $userIsActive,
            ];
        
    }
    
    // Debug thông tin sau khi vòng lặp hoàn tất

   
    // Tìm người dùng có tin nhắn chưa đọc
    $usersWithNewMessages = UserNd::whereIn('id', Messager::where('receiver_id', $userId)
                                                        ->where('is_read', 0)
                                                        ->pluck('sender_id'))
                                  ->get();

    $receiver = UserNd::find($receiverId);

    // Đếm số tin nhắn chưa đọc
    $newMessagesCount = Messager::where('receiver_id', $userId)
                                ->where('is_read', 0)
                                ->count();
      // Lấy tin nhắn mới nhất từ người khác gửi đến người dùng hiện tại
      $latestMessagesReceived = Messager::select('messagers.*')
      ->where('receiver_id', $userId)
      ->whereIn('id', function($query) use ($userId) {
          $query->selectRaw('MAX(id)')
              ->from('messagers')
              ->where('receiver_id', $userId)
              ->groupBy('sender_id');
      })
      ->get();

  // Lấy tin nhắn gửi đi mới nhất từ người dùng hiện tại đến từng người nhận
  $latestMessagesSent = Messager::select('messagers.*')
      ->where('sender_id', $userId)
      ->whereIn('receiver_id', function($query) use ($userId) {
          $query->select('receiver_id')
              ->from('messagers')
              ->where('sender_id', $userId)
              ->groupBy('receiver_id');
      })
      ->whereIn('id', function($query) use ($userId) {
          $query->selectRaw('MAX(id)')
              ->from('messagers')
              ->where('sender_id', $userId)
              ->groupBy('receiver_id');
      })
      ->get();

  // Kết hợp tin nhắn gửi đi và tin nhắn nhận được
  $allMessages = $latestMessagesReceived->merge($latestMessagesSent)
      ->sortByDesc('created_at'); // Sắp xếp tất cả tin nhắn theo thời gian gửi

  // Lấy danh sách ID của người gửi và người nhận
  $userIds = $allMessages->pluck('sender_id')->merge($allMessages->pluck('receiver_id'))->unique();
  $user = UserNd::whereIn('id', $userIds)->get()->keyBy('id');

  // Tính số lượng tin nhắn chưa đọc
  $newMessagesCount = Messager::where('receiver_id', $userId)
      ->where('is_read', 0)
      ->count();

  // Gán người gửi và người nhận cho các tin nhắn
  $messagesWithUsers = $allMessages->map(function ($message) use ($users, $userId) {
      $message->sender = $users->get($message->sender_id);
      $message->receiver = $users->get($message->receiver_id);
      $message->is_from_current_user = $message->sender_id === $userId;
      return $message;
  });

  $notificationCount = Notification::where('user_id', $userId)
  ->where('read_at', 0)
  ->count();
  // Nhóm tin nhắn theo người và lấy tin nhắn mới nhất cho mỗi người
  $messagesGrouped = $messagesWithUsers->groupBy(function ($message) use ($userId) {
      return $message->is_from_current_user ? $message->receiver_id : $message->sender_id;
  })->map(function ($messages) {
      return $messages->sortByDesc('created_at')->first(); // Lấy tin nhắn mới nhất từ mỗi người
  });
    return view('chat', compact('messages', 'receiverId', 'users', 'messageSenders', 'messageReceivers', 'usersWithNewMessages', 'receiver', 'newMessagesCount',  'user',
    'messagesWithUsers','activeUsers', 'notificationCount'
));

}











    

}
