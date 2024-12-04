<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\UserNd;
use App\Models\Friend;
use App\Models\Notification;
use App\Models\Messager;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Topic;

class TopicController extends Controller
{
    public function topic()
    {
        $topic= Topic::all();
        $currentUserId = session('id');
        $newMessagesCount = Messager::where('receiver_id', $currentUserId)
        ->where('is_read', 0)
        ->count();
    
        $notificationCount = Notification::where('user_id', $currentUserId)
        ->where('read_at', 0)
        ->count();
        return view('admin.topic', compact('topic', 'newMessagesCount', 'notificationCount'));
    }

    //hàm tạo chủ đề
    public function run(Request $request){
        Topic::create([
            'topic' => $request->topic,
        ]);
        return redirect()->back()->with('message', 'Tạo chủ đề thành công');
    }
        
}
