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
        
            
            // Kiểm tra avatarMap để xem kết quả
            
            
        }
        // dd($userMap, $avatarMap);
        // $avatarMap = [];
        // foreach ($users as $user) {
        //     $avatarMap[$user->id] = url('storage/' . $user->avatar);

        //     dd(  $user->avatar);
        // }
        // Trả về view và truyền dữ liệu đã đọc
        $currentUserId = session('id');              
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
                                 ->where('is_read', 0) // Đếm số tin nhắn chưa đọc
                                 ->count();
        $notificationCount = Notification::where('user_id', $currentUserId)
                                 ->where('read_at', 0)
                                 ->count();
        return view('vecto', [
            'embeddings' => $embeddings, // Truyền dữ liệu vào view
            'csvPath' => $csvPath,        // Truyền đường dẫn vào view
            'userMap' => $userMap  ,       // Truyền userMap vào view
            'avatarMap'=>$avatarMap, 'newMessagesCount' =>$newMessagesCount, 
            'notificationCount'=>$notificationCount
        ]);
    }
    
    
    
    
    
    

    
}


