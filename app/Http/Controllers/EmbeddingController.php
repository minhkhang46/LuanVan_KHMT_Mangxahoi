<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNd;
use App\Models\Post;
use App\Models\Friend;
use App\Models\Messager;
use App\Models\Notification;
class EmbeddingController extends Controller
{
    public function generateEmbeddings()
    {
        // Chạy script Python
        $output = shell_exec('python3 /luanvan_tn/vecto.py');

        // Kiểm tra nếu script chạy thành công
        if ($output) {
            return response()->json([
                'message' => 'Embeddings generated successfully!',
                'output' => $output
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to generate embeddings.'
            ], 500);
        }
    }
    public function getEmbeddings()
    {
        // Đường dẫn đến file CSV
        $csvPath = base_path('nguoi_dung_gan_nhat_combined1.csv'); // Đảm bảo đường dẫn đúng
    
        // Kiểm tra xem file có tồn tại không
        if (file_exists($csvPath)) {
            // Mở file CSV
            $file = fopen($csvPath, 'r');
            $embeddings = []; // Mảng để chứa dữ liệu
    
            // Đọc toàn bộ nội dung của file CSV
            while (($row = fgetcsv($file)) !== false) {
                // Thêm từng dòng vào mảng embeddings mà không chỉ định cụ thể các cột
                $embeddings[] = $row; // Lưu trữ toàn bộ dòng
            }
    
            // Đóng file sau khi đọc xong
            fclose($file);
        } else {
            // Xử lý lỗi nếu file không tồn tại
            return response()->json(['error' => 'File not found'], 404);
        }
    
        // Lấy danh sách người dùng từ cơ sở dữ liệu
        $users = UserNd::all(); // Hoặc sử dụng phương thức nào bạn đã thiết lập
        $userMap = [];
        $avatarMap = [];
        foreach ($users as $user) {
            $userMap[$user->id] = $user->name;
    
            // Nếu avatar không rỗng, thêm vào map
            if (!empty($user->avatar)) {
                $avatarMap[$user->id] = url('storage/' . $user->avatar); // Tạo đường dẫn đầy đủ
            } else {
                // Thêm avatar mặc định nếu không có
                $avatarMap[$user->id] = url('default-avatar.png');
            }
        }
    // dd($avatarMap[2]);
        // ID người dùng hiện tại
        $currentUserId = session('id');
    
        // Lấy danh sách bạn bè hiện tại của người dùng
        $friendList = Friend::where(function ($query) use ($currentUserId) {
            $query->where('user_id', $currentUserId)
                  ->orWhere('friend_id', $currentUserId);
        })
        ->where('status', 1) // Chỉ lấy bạn bè đã xác nhận
        ->pluck('user_id', 'friend_id') // Lấy cả user_id và friend_id
        ->flatten()
        ->unique()
        ->toArray();
    
        // Mảng chứa gợi ý kết bạn
        $suggestedFriends = [];
    
        // Duyệt qua mảng embeddings
        foreach ($embeddings as $row) {
            if ($row[0] == $currentUserId) { // Nếu user_id trùng với người dùng hiện tại
                $nearestUserId = $row[1]; // Lấy nearest_user_id
                if (!in_array($nearestUserId, $friendList)) { // Nếu chưa là bạn bè
                    $suggestedFriends[] = $nearestUserId;
                }
            }
        }
    
        // Lấy danh sách chi tiết người dùng được gợi ý
        $suggestedDetails = UserNd::whereIn('id', $suggestedFriends)->get();
        $suggestedUserMap = [];
        $suggestedAvatarMap = [];
        foreach ($suggestedDetails as $user) {
            $suggestedUserMap[$user->id] = $user->name;
            $suggestedAvatarMap[$user->id] = !empty($user->avatar)
                ? url('storage/' . $user->avatar)
                : url('default-avatar.png');
        }
        $friendStatusMap = Friend::where('user_id', $currentUserId)
        ->pluck('status', 'friend_id');
        // Đếm số tin nhắn và thông báo
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
            ->where('is_read', 0)
            ->count();
        $notificationCount = Notification::where('user_id', $currentUserId)
            ->where('read_at', 0)
            ->count();
    
        // Trả về view
        return view('vecto', [
            'embeddings' => $embeddings,              // Dữ liệu từ CSV
            'csvPath' => $csvPath,                   // Đường dẫn file CSV
            'userMap' => $userMap,                   // Map tên người dùng
            'avatarMap' => $avatarMap,               // Map avatar người dùng
            'suggestedUserMap' => $suggestedUserMap, // Map tên gợi ý kết bạn
            'suggestedAvatarMap' => $suggestedAvatarMap, // Map avatar gợi ý kết bạn
            'newMessagesCount' => $newMessagesCount, 
            'notificationCount' => $notificationCount,
            'user'=>$user, 'friendStatusMap' => $friendStatusMap 
        ]);
    }
    
    
    
    
    
    

    
}


