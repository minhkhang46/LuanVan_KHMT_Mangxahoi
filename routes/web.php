<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MessagerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\EmbeddingController;
use App\Http\Controllers\CommentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Homecontroller
Route::get('/home/{id}', 'App\Http\Controllers\HomeController@index')->name('homes');
Route::post('/login', 'App\Http\Controllers\HomeController@login')->name('logins'); //đăng nhập
Route::get('/user/{id}', 'App\Http\Controllers\HomeController@profile')->name('profile');// hiển thị trang profile
Route::post('/logout', 'App\Http\Controllers\HomeController@logout')->name('logout'); //đăng xuất
Route::post('/register', 'App\Http\Controllers\HomeController@register')->name('register'); //đăng nhập
Route::get('/register', 'App\Http\Controllers\HomeController@register_index')->name('registers'); //đăng nhập
Route::post('/search', [HomeController::class, 'timKiem'])->name('searchs'); //tìm kiếm người dùng
Route::get('/search', [HomeController::class, 'timKiem'])->name('searchs'); //tìm kiếm người dùng
Route::get('/profile/{id}', [HomeController::class, 'show'])->name('profiles'); //hiển thị trang cá nhân của người khác
Route::post('/profile/{id}',  [HomeController::class, 'update'])->name('user.profile');  
Route::get('/user', 'App\Http\Controllers\HomeController@usermanger')->name('user'); //hàm hiển thị tài khỏan người  dùng
Route::post('/users/{id}/toggle-status/{status}', [HomeController::class, 'toggleStatus'])->name('users.toggleStatus'); // khóa tài khoản người dùng

//Postcontroller
Route::post('/post_content', 'App\Http\Controllers\PostController@posts')->name('post_content');// post bài đăng
Route::get('/file-content/{filename}', [PostController::class, 'showFileContent'])->name('file_content');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');// hiển thị trang bài đăng 
Route::post('/like-toggle', [PostController::class, 'toggleLike'])->name('like.toggle');
Route::get('/getLikes', [PostController::class, 'getLikes']);
Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy'); // xóa bài đăng


//Friendcontroller
Route::post('/send-friend-request/{id}', [FriendController::class, 'sendRequest'])->name('sendFriendRequest'); // gửi lời mời
Route::post('/friend/accept/{id}', [FriendController::class, 'acceptRequest'])->name('acceptFriendRequest'); // chấp nhận
Route::delete('/friends/remove/{id}', [FriendController::class, 'removeFriend'])->name('removeFriend'); // xóa kết bạn

///MessagerController
Route::post('/messages/send', [MessagerController::class, 'sendMessage'])->name('messages.send'); //gửi tin nhắn
Route::get('/chat/{receiverId?}/{groupId?}', [MessagerController::class, 'getMessages'])->name('chat');

Route::get('/chat', [MessagerController::class, 'showLatestMessages'])->name('messages');
Route::get('/chat/{groupId}', [ChatController::class, 'getMessagesgroup'])->name('group.chat');



//notificationController
Route::get('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');// lấy thông tin
Route::get('/notifications/{id}', [NotificationController::class, 'showFriendRequests'])->name('notifications'); //hiển thị thông báo
Route::get('/notifications/fetch', [NotificationController::class, 'fetchNotifications'])->name('notifications.fetch');// cập nhật dữ liệu

//GroupController
Route::get('/groups', [GroupController::class, 'index'])->name('group'); // hiển thị nhóm
Route::get('/groups/{id}', [GroupController::class, 'show'])->name('groups.show'); // hiển thị chi tiết nhóm
Route::post('/groups/{id}/join', [GroupController::class, 'joinGroup'])->name('group.join');// tham gia nhóm
Route::delete('/groups/remove/{groupId}', [GroupController::class, 'removeMember'])->name('removeMember'); // xóa kết bạn
Route::post('group', [GroupController::class, 'store'])->name('groupcreate'); // tạo group
Route::get('/requested-groups', [GroupController::class, 'showRequestedGroups'])->name('requested.groups'); // xem yêu cầu group
Route::post('/group/accept/{id}', [GroupController::class, 'acceptRequest'])->name('group.accept'); // chấp nhận yêu cầu tạo nhóm
Route::post('/group/decline/{id}', [GroupController::class, 'declineRequest'])->name('group.decline');// từ chối yêu cầu tạo nhóm

//EmbeddingController
Route::get('/vecto', [EmbeddingController::class, 'getEmbeddings'])->name('vecto'); // hiển thị đồ thị
Route::get('/generate-embeddings', [EmbeddingController::class, 'generateEmbeddings']); //gọi file python

//CommentController
Route::post('/comments', [CommentController::class, 'Comments'])->name('comments.store'); //bình luận 
Route::post('/posts/{post_id}/reply', [CommentController::class, 'storeReply'])->name('comments.reply');



Route::get('/group-info', function () {
    return view('group-info');
})->name('group.info');
Route::get('/', function () {
    return view('login');
})->name('login');
// Route::get('/no', function () {
//     return view('notication');
// })->name('no');