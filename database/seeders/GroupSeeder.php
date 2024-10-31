<?php


namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     public function run()
     {
        Group::insert([
            ['name' => 'Xử lý ngôn ngữ tự nhiên', 'status' => 'public', 'image' => '/image/NLP.jpg'],
          
        ]);
         // Cập nhật dữ liệu cho các nhóm đã có
         Group::where('name', 'Xử lý ngôn ngữ tự nhiên')
             ->update(['status' => 'public', 'image' => '/image/NLP.jpg']);
 
         Group::where('name', 'Thị giác máy tính')
             ->update(['status' => 'public', 'image' => '/image/cv.jpg']);
 
         Group::where('name', 'Học máy')
             ->update(['status' => 'public', 'image' => '/image/mc.jpg']);
 
         Group::where('name', 'Học sâu')
             ->update(['status' => 'public', 'image' => '/image/DL.png']);
 
         Group::where('name', 'Khai phá dữ liệu')
             ->update(['status' => 'public', 'image' => '/image/dm2.jpg']);
     }

}

