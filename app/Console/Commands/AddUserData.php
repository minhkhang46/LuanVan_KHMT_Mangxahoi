<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserNd;

class AddUserData extends Command
{
    // Tên của lệnh
    protected $signature = 'add:users';

    // Mô tả lệnh
    protected $description = 'Thêm nhiều người dùng vào cơ sở dữ liệu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Danh sách người dùng cần thêm
        $users = [
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Thanh Thủy', 'email' => 'nguyenthanhthuy@gmail.com', 'phone' => '0982341222', 'description' => 'Giáo sư ngành công nghệ thông tin', 'chuyende' => 'Khoa học máy tính', 'avatar' => 'public\image\images.jpg' ],
            ['name' => 'Nguyễn Văn C', 'email' => 'vanc@example.com', 'password' => 'password123'],
        ];

        // Duyệt qua từng người dùng và thêm vào cơ sở dữ liệu
        foreach ($users as $userData) {
            // Xử lý avatar
            $avatarPath = $this->uploadAvatar($userData['avatar']);

            // Tạo người dùng
            UserNd::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt($userData['password']),
                'avatar' => $avatarPath,
            ]);

            $this->info("Đã thêm người dùng: {$userData['name']} với avatar {$avatarPath}");
        }

        $this->info('Hoàn tất thêm tất cả người dùng.');
    }
}
