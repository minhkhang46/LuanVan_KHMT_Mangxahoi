@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<style>
/* 
.container {
  display: flex;
  justify-content: center;
} */
.status.online {
    color: #10b981;
    /* Màu xanh lá cho trạng thái online */
}

.feed {
    flex: 2;
    margin-left: 26%;

    max-width: 45%;
    /* Điều chỉnh giới hạn chiều rộng của phần feed */
    min-height: 50vh;
}

.activity {
    flex: 1;
    margin-left: auto;
    /* Đẩy phần người liên hệ sang phải */
    max-width: 20%;
    /* Giới hạn chiều rộng của phần người liên hệ */
    min-height: 50vh;
}

.activity img {
    width: 48px;
    height: 48px;
}

#toast-pro {
    max-width: 600px; /* Đặt chiều dài tối đa */
    width: auto; /* Tự động điều chỉnh theo nội dung */
}
.feed-content,
.activity-content {
    min-height: 1px;
    /* Đảm bảo các phần tử bên trong không thay đổi kích thước */
}
.modal {
    display: none; /* Modal bị ẩn mặc định */
}

.modal.show {
    display: flex; /* Modal hiển thị khi thêm lớp `show` */
}
/* .status-dot {
    position: absolute;
    width: 12px;
    height: 12px;
    background-color: green;
    border-radius: 50%;
    border: 2px solid white;
    bottom: 0;
    right: 0;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
  }
  
  .profile-container {
    position: relative;
    display: inline-block;
  } */
  #suggestionsContainer {
    overflow-x: auto; /* Cho phép cuộn ngang */
    overflow-y: hidden; /* Ẩn cuộn dọc */
    white-space: nowrap; /* Ngăn ngừa xuống dòng */
}

#suggestionsContainer::-webkit-scrollbar {
    display: none; /* Ẩn thanh cuộn trên trình duyệt WebKit */
}


#toast-container>div {
    width: 300px; /* Thay đổi giá trị này để tăng/giảm chiều rộng */
}


.hidden { display: none; }
#dropdown {
    position: absolute; /* Định vị tuyệt đối để dropdown xuất hiện ngay dưới ô nhập */
    z-index: 1000; /* Đảm bảo dropdown nổi lên trên các phần tử khác */
    background-color: white; /* Nền trắng để nổi bật */
    border: 1px solid #ccc; /* Viền dropdown */
    border-radius: 8px; /* Bo tròn góc */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Đổ bóng để đẹp hơn */
    max-height: 200px; /* Giới hạn chiều cao dropdown */
    overflow-y: auto; /* Thêm cuộn khi danh sách quá dài */
}

</style>


<div class="py-10 -mt-2">
    
    <div class="flex justify-center">
        <section id="feed" class="feed w-full justify-center">
        <!-- @if($friendSuggestions->isNotEmpty())
            <h2 class="text-3xl font-semibold mb-6 text-gray-800">Gợi ý kết bạn</h2>
        
                
            <div class="relative flex items-center">
                <button id="prevButton" class="absolute  left-1 z-10 px-2 py-4 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none" aria-label="Previous">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <div class="flex flex-nowrap gap-6 mb-8  overflow-x-auto scroll-smooth" id="suggestionsContainer">
                
                        @foreach($friendSuggestions as $user)
                            <div class="flex-none bg-white hover:bg-gray-200  transition duration-300 rounded-lg shadow-sm w-52">
                                <a href="{{ route('profiles', $user->id) }}" class="flex flex-col items-center ">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-52 h-52  rounded-t-lg mb-2">
                                    <h3 class="text-xl font-semibold text-center text-gray-900 mt-2">{{ $user->name }} </h3>
                                </a>
            
                                <form action="{{ route('sendFriendRequest', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white my-3 mx-2 px-4 py-2 text-lg rounded-lg w-11/12 font-semibold hover:bg-green-700 transition duration-300">
                                        Thêm bạn bè
                                    </button>
                                </form>
                            </div>
                        @endforeach
                
                
                </div>

                <button id="nextButton" class="absolute  right-2 z-10 px-2 py-4 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none" aria-label="Next">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            @endif -->




       
            <!-- Form to post new content -->
            <div  class="bg-white p-5 mb-8 rounded-lg shadow-md overflow-y-auto">
                <form action="{{ route('post_content') }}" id="postForm2" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input name="id_nd" type="hidden" value="{{ session('id') }}" />
                    <div class="mb-4  items-center">
                        <img class="h-16 w-16 rounded-full mb-4 ml-2" src="{{ asset('storage/' . session('avatar')) }}"
                            alt="User Avatar">
                   
                            <div class="flex">
                          
                            <div class="relative w-2/3 ml-2 mb-3">
                                <input type="text" id="topic-input" name="topic"   autocomplete="off" 
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg " 
                                    placeholder="Nhập hoặc chọn chủ đề..."
                                    onfocus="showDropdown()" 
                                    oninput="filterDropdown()">
                                <div id="dropdown" class="absolute hidden bg-white text-lg  border  rounded-lg  w-full">
                                    @foreach ($topics as $topic)
                                        <div class="p-2 cursor-pointer hover:bg-gray-200" 
                                            onclick="selectDropdown('{{ $topic->topic }}')">
                                            {{ $topic->topic }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>


                                <!-- Thẻ select chỉ cho phép người dùng chọn chủ đề từ danh sách -->
                                <select id="regime" name="regime" class="w-1/3 p-2 ml-2 mb-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                                    <option value="">Tùy chỉnh chế độ</option> <!-- Tùy chọn mặc định (trống) -->
                                    <option value="Bạn bè">Bạn bè</option>
                                    <option value="Công khai">Công khai</option>
                                </select>

                            </div>
                            <div class="mr-2">
                            <textarea id="content" name="noidung" rows="1"
                                class="w-full p-2 ml-2 border  border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                                placeholder="Write something..." ></textarea>
                            </div>
                    </div>
                    <div class="border border-gray-300 -mb-3"></div>
                    <div class="flex flex-col mb-4 space-y-4 items-end mt-3">
                        <div id="imagePreviewContainer" class="hidden">
                            <img id="imagePreview" class="w-full h-auto border border-gray-300 p-1"
                                alt="Selected Image">
                            <div id="imageFileName" class="text-xs text-gray-500 truncate"></div>
                        </div>
                        <div id="filePreviewContainer" class="hidden">
                            <div id="filePreview" class="w-full h-auto border border-gray-300 p-1"></div>
                            <div id="fileFileName" class="text-xs text-gray-500 truncate"></div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Image Icon -->
                            <label for="image"
                                class="cursor-pointer text-blue-500 hover:text-blue-600 flex flex-col items-center">
                                <img id="imageIcon" src="/luanvan_tn/public/image/image.png" alt="Image Icon"
                                    class="w-8 h-8">
                                <input id="image" name="images" type="file" class="sr-only" 
                                    onchange="previewSelectedFile(event, 'imagePreview', 'imageFileName')" />
                            </label>
                            <!-- File Icon -->
                            <label for="file"
                                class="cursor-pointer text-blue-500 hover:text-blue-600 flex flex-col items-center">
                                <img id="fileIcon" src="/luanvan_tn/public/image/folder.png" alt="File Icon"
                                    class="w-8 h-8">
                                <input id="file" name="files" type="file" class="sr-only"
                                    onchange="previewSelectedFile(event, 'filePreview', 'fileFileName')" />
                            </label>
                        </div>
                    </div>
                    <button  type="submit"
                        class="bg-blue-500 text-white w-full px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition duration-300 text-lg "
                        >Đăng</button>
                </form>
            </div>
          
            <!-- Display posts -->
            @if($posts->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                <p class="text-gray-500 text-center">Chưa có bài viết.</p>
            </div>
            @else
            @foreach($posts as $post)
            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
              <div class="flex items-center mb-4">
            
                @if($post->group_id)
                   
                    <div class="relative">
                        <a href="{{ route('groups.show', $post->group_id) }}">
                            <img class="h-14 w-14  rounded-full" src="{{ asset('storage/' . $post->group_image) }}" alt="Group Image">
                        </a>

                        <!-- Hình người dùng ở góc của hình nhóm -->
                        <a href="{{ $post->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $post->id_nd]) }}" class="flex items-center">
                            <div class="absolute bottom-0 right-0 transform translate-x-1/4 translate-y-1/4">
                                <img class="h-10 w-10 rounded-full border-4 border-white " src="{{ asset('storage/' . $post->user_avatar) }}" alt="User Avatar">
                            </div>
                        </a>
                    </div>

                    <!-- Thông tin về bài đăng -->
                    <div class="ml-3 flex-1">
                        <a href="{{ route('groups.show', $post->group_id) }}">
                            <p class="text-xl font-bold text-black">Nhóm {{ $post->group_name }}</p>
                        </a>
                        <a href="{{ $post->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $post->id_nd]) }}" class="flex items-center">
                            <div class="flex mt-1">
                                <p class="text-lg font-medium text-black">{{ $post->user_name }}.</p>
                                <p class="text-lg text-black ml-2">
                                    @if (now()->diffInHours($post->created_at) >= 24)
                                        {{ $post->created_at->addDay()->format('d-m-Y') }}
                                    @else
                                        {{ $post->created_at->locale('vi')->diffForHumans() }}
                                    @endif
                                </p>

                            </div>
                        </a>
                    </div>

                   
                @else

                <a href="{{ $post->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $post->id_nd]) }}" class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-14 w-14 rounded-full " src="{{ asset('storage/' . $post->user_avatar) }}" alt="User Avatar">
                        </div>
                        <div class="ml-3 flex-1">
                        
                            <p class="text-xl font-semibold text-black">{{ $post->user_name }}</p>
                            <div class="flex">
                                <p class="text-lg text-gray-700">
                                    @if (now()->diffInHours($post->created_at) >= 24)
                                        {{ $post->created_at->addDay()->format('d-m-Y') }}
                                    @else
                                        {{ $post->created_at->locale('vi')->diffForHumans() }}
                                    @endif
                                </p>

                                @if( $post->regime === 1)
                                <img id="imageIcon" src="/luanvan_tn/public/image/friend.png" alt="Image Icon"
                                class="w-5 h-5 ml-3 mt-1">
                                @else
                                <img id="imageIcon" src="/luanvan_tn/public/image/publlic1.png" alt="Image Icon"
                                class="w-5 h-5 ml-3 mt-1">
                                @endif
                            </div>
                        </div>
                    </a>
                    @endif                
                    @if($post->id_nd === session('id'))
                    <div class="relative ml-auto">
                        <button id="dropdown-btn-{{ $post->id }}" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <img src="/luanvan_tn/public/image/dots.png" alt="Options" class="w-6 h-6">
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdown-menu-{{ $post->id }}" class="dropdown-menu hidden absolute right-0 mt-1 w-40 bg-gray-100 rounded-md shadow-lg z-50">
                            <form action="{{ route('posts.destroy', ['id' => $post->id]) }}" method="POST" id="delete-post-form-{{ $post->id }}" class="delete-post-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100" onclick="deletePost({{ $post->id }})">
                                    <div class="flex items-center">
                                        <img id="imageIcon" src="/luanvan_tn/public/image/trash.png" alt="Image Icon" class="w-5 h-5">
                                        <span class="ml-2">Xóa bài đăng</span>
                                    </div>
                                </button>
                            </form>
                        </div>

                    </div>
                  @endif
                </div>
                @if($post->topic)
                <p class="text-black font-semibold text-xl mb-2">Chủ đề {{ $post->topic }}</p>
                @endif
                <p class="text-black text-xl">{{ $post->noidung }}</p>
                @if ($post->images && $post->images !== 'default/image.png')
                <div class="flex justify-center mb-4">
                    @php
                        $imageSize = $imageSizes[$post->id] ?? null;
                        $width = $imageSize[0] ?? null; // Chiều rộng
                        $height = $imageSize[1] ?? null; // Chiều cao
                    @endphp

                    <img src="{{ asset('storage/' . $post->images) }}" alt="Post Image" 
                        class="mt-4 border border-gray-300 rounded-md custom-image"
                        style="max-height: 700px; 
                                @if($height && $width && $height > $width) width: auto; height: auto; object-fit: contain; 
                                @else width: 100%; height: auto; object-fit: cover; @endif">
                </div>
                @endif
                @if($post->files)
                @php
                $fileName = basename($post->files);
                @endphp
                <a href="{{ asset('storage/' . $post->files) }}" download
                    class="text-blue-500 text-xl mt-2 inline-block">
                    Download {{ $fileName }}
                </a>
                @endif

                <div class="post">

                <div class="text-base mt-2 flex justify-between">
                    <span id="like-count-{{ $post->id }}" class="cursor-pointer" onclick="openModal({{ $post->id }})">
                        @if($post->is_liked)
                            @if($post->likes_count > 1)
                                <span class="like-count">Bạn và {{ $post->likes_count - 1 }} người khác</span>
                            @else
                                <span class="like-count">Bạn</span>
                            @endif
                        @else
                            <span class="like-count">
                                @if($post->likes_count > 0)
                                    {{ $post->likes_count }}
                                @endif
                            </span>
                        @endif
                    </span>
                    @if($postsWithComments[$post->id]['comment_count'] > 0) <!-- Kiểm tra số bình luận -->
                        <span  onclick="toggleComments({{ $post->id }})">{{ $postsWithComments[$post->id]['comment_count'] }} bình luận</span> <!-- Hiển thị bình luận nếu có -->
                    @endif
                </div>

                    
                    <div id="likes-modal-{{ $post->id }}"
                      class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center z-50">
                      <div class="modal-content bg-white p-6 rounded-lg shadow-lg max-w-lg w-full relative">
                          <button class="close-btn absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl"
                              onclick="closeModal({{ $post->id }})">
                              &times;
                          </button>
                          <h2 class="text-xl font-semibold mb-4 text-center">Danh sách người đã thích bài viết</h2>
                          <ul class="list-disc pl-5 space-y-4">

                              @if(isset($likes[$post->id]))
                              @foreach($likes[$post->id] as $like)
                              <li class="flex items-center">
                                  @if($like->id == session('id'))
                                  <a href="{{ route('profile', ['id' => session('id')]) }}" class="flex items-center">
                                      <img src="{{ asset('storage/' . $like->avatar) }}" alt="Avatar"
                                          class="w-12 h-12 rounded-full mr-3">
                                      <span class="text-lg font-medium text-gray-800">Bạn</span>
                                  </a>
                                  @else
                                  <a href="{{ route('profiles', ['id' => $like->id]) }}" class="flex items-center">
                                      <img src="{{ asset('storage/' . $like->avatar) }}" alt="Avatar"
                                          class="w-12 h-12 rounded-full mr-3">
                                      <span class="text-lg font-medium text-gray-800">{{ $like->name }}</span>
                                  </a>
                                  @endif
                              </li>
                              @endforeach
                              @endif

                          </ul>
                      </div>
                  </div>





                    <div class="border border-gray-300 mt-2 mb-2"></div>
                    <div class="flex justify-around items-center space-x-4 -mb-5">
                        <form action="{{ route('like.toggle') }}" method="POST"
                            class="like-form flex items-center justify-center w-full space-x-2">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            @if(!$post->is_liked)
                            <button type="submit"
                                class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                <img id="imageIcon" src="/luanvan_tn/public/image/like.png" alt="Image Icon"
                                    class="w-8 h-8">
                                <span class="mt-1 ml-2 font-semibold">Thích</span>
                            </button>
                            @else
                            <button type="submit"
                                class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                <img id="imageIcon" src="/luanvan_tn/public/image/like_blue.png" alt="Image Icon"
                                    class="w-8 h-8">
                                <span class="mt-1 ml-2 font-semibold text-blue-700">Thích</span>
                            </button>
                            @endif
                        </form>
                  
                      
                            <!-- Nút Bình luận cho bài viết -->
                            <button id="commentButton-{{ $post->id }}" class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                <img id="imageIcon" src="/luanvan_tn/public/image/comment.png" alt="Image Icon" class="w-8 h-8">
                                <span class="ml-2 font-semibold text-lg">Bình luận</span>
                            </button>    
                    </div>
                
    
               
                    <div class="post mb-8 mt-8">
                        <div id="comments-{{ $post->id }}" class="comments hidden overflow-y-auto max-h-96"> <!-- Thay đổi max-h-60 thành chiều cao bạn mong muốn -->
                            @if(isset($postsWithComments[$post->id]['comments']) && count($postsWithComments[$post->id]['comments']) > 0)
                                @foreach ($postsWithComments[$post->id]['comments'] as $comment)
                                    @if (!$comment['parent_id']) <!-- Hiển thị bình luận gốc -->
                                        @include('partials.comment', ['comment' => $comment, 'post' => $post, 'comments' => $postsWithComments[$post->id]['comments']])
                                    @endif
                                @endforeach
                            @else
                                <p>Chưa có bình luận nào.</p>
                            @endif
                        </div>
                    </div>


                    <div id="commentForm-{{ $post->id }}" class="hidden mt-6">
                                <form action="{{ route('comments.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <input type="hidden" name="user_id" value="{{ session('id') }}">
                                    <div class="flex">
                                        <textarea name="content" rows="1" class="w-full p-2 mr-2 border border-gray-300 rounded-md" placeholder="Viết bình luận..." required></textarea>
                                
                                        <button type="submit" ><img src="/luanvan_tn/public/image/send.png" alt="Icon Bảng tin" class="w-8 h-8 "> </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            
            @endforeach

            @endif
         
        </section>

        <!-- Activity Section -->
        <aside class="activity p-2">

            <h3 class="text-2xl font-semibold mb-4">Người liên hệ</h3>
            <div class="border border-gray-300 mb-4"></div>
            @foreach ($friends as $friend)
            @if($friend->id !== session('id'))
            @php
            // Kiểm tra trạng thái trực tuyến của bạn bè
            $isOnline = isset($onlineUsers[$friend->id]) && $onlineUsers[$friend->id]['is_online'];

            // Lấy thời gian hoạt động cuối cùng nếu không trực tuyến
            $lastSeen = !$isOnline ? ($onlineUsers[$friend->id]['last_seen'] ?? null) : null;
            @endphp

            <a href="{{ route('chat', ['receiverId' => $friend->id]) }}">
                <div class="mb-5 flex items-center">
                    <div class="relative profile-container">
                        <img class="h-14 w-14 rounded-full" src="{{ asset('storage/' . $friend->avatar) }}"
                            alt="{{ $friend->name }}'s Avatar">
                        <!-- Có thể thêm dấu chấm trạng thái nếu cần -->
                        <!-- <div class="status-dot {{ $isOnline ? 'bg-green-500' : 'bg-gray-400' }}"></div> -->
                    </div>
                    <div class="ml-3">
                        <p class="text-xl font-semibold">{{ $friend->name }}</p>
                        <p>
                            @if ($isOnline)
                            <span class="status online text-lg text-green-500">Đang hoạt động</span>
                            @else
                              @if ($lastSeen)
                                @php
                                // Chuyển đổi last_seen thành đối tượng Carbon
                                $lastSeenTime = \Carbon\Carbon::parse($lastSeen);
                                // Kiểm tra nếu thời gian hoạt động cuối cùng nằm trong vòng 10 giờ
                                $isRecent = $lastSeenTime->diffInHours(now()) <= 10; 
                                @endphp 
                                @if ($isRecent) <span
                                    class="status offline text-gray-500">Hoạt động
                                    {{ $lastSeenTime->locale('vi')->diffForHumans() }}</span>

                                @endif

                              @endif
                           @endif
                        </p>
                    </div>
                </div>
            </a>
            @endif
            @endforeach


        </aside>
    </div>
</div>
  <footer class="bg-white text-black text-center py-8 mt-8">
  <div class="flex justify-center space-x-6">
    <!-- Nút dẫn đến Trang Chủ -->
    <a href="{{ route('homes', ['id' => session('id')]) }}" class="text-lg font-semibold hover:text-indigo-300 transition duration-300">Trang Chủ</a>
    <!-- Nút dẫn đến Trang Giới Thiệu -->
    <a href="{{ route('lists') }}" class="text-lg font-semibold hover:text-indigo-300 transition duration-300">Giới Thiệu</a>
  </div>
  <p class="text-sm mt-4">&copy; 2024 ConnectAI. Kết nối cộng đồng, thúc đẩy sự phát triển lĩnh vực trí tuệ nhân tạo</p>
</footer>
<script>
function previewSelectedFile(event, previewId, fileNameId) {
    const input = event.target;
    const previewContainerId = input.id === 'image' ? 'imagePreviewContainer' : 'filePreviewContainer';
    const previewElement = document.getElementById(previewId);
    const fileNameElement = document.getElementById(fileNameId);
    const previewContainer = document.getElementById(previewContainerId);
    const reader = new FileReader();

    reader.onload = function() {
        if (input.id === 'image') {
            previewElement.src = reader.result;
            previewContainer.classList.remove('hidden');
        } else {
            previewElement.innerHTML = `<a href="${reader.result}" download>${input.files[0].name}</a>`;
            previewContainer.classList.remove('hidden');
        }
        fileNameElement.textContent = input.files[0].name;
    };

    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.classList.add('hidden');
    }
}

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('form.like-form').on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn hành vi gửi form mặc định

            var form = $(this);
            var postId = form.find('input[name="post_id"]').val();
            var button = form.find('button');
            var icon = button.find('img');
            var text = button.find('span');
            var likeCountSpan = form.closest('.post').find('.like-count');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Cập nhật trạng thái like
                    if (response.is_liked) {
                        icon.attr('src', '/luanvan_tn/public/image/like_blue.png');
                        text.text('Thích').addClass('text-blue-700');
                    } else {
                        icon.attr('src', '/luanvan_tn/public/image/like.png');
                        text.text('Thích').removeClass('text-blue-700');
                    }
                    
                    // Cập nhật số lượt thích
                    if (response.likes_count > 0) {
                        if (response.likes_count > 1 && response.is_liked) {
                            likeCountSpan.text('Bạn và ' + (response.likes_count - 1) + ' người khác');
                        } else if (response.likes_count === 1 && response.is_liked) {
                            likeCountSpan.text('Bạn');
                        } else {
                            likeCountSpan.text(response.likes_count);
                        }
                    } else {
                        likeCountSpan.text(''); // Không hiển thị gì nếu số lượt thích là 0
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
function openModal(postId) {
    var modal = document.getElementById('likes-modal-' + postId);
    modal.classList.remove('hidden'); // Loại bỏ lớp hidden để hiển thị modal
    modal.classList.add('show'); // Thêm lớp show để modal hiển thị
}

function closeModal(postId) {
    var modal = document.getElementById('likes-modal-' + postId);
    modal.classList.add('hidden'); // Thêm lớp hidden để ẩn modal
    modal.classList.remove('show'); // Loại bỏ lớp show
}

// Close modals when clicking outside of the modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        event.target.classList.remove('show');
    }
}

// Close modals when clicking outside of the modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        event.target.classList.remove('show');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Lắng nghe sự kiện nhấp vào nút ba chấm
    document.querySelectorAll('[id^="dropdown-btn-"]').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.id.split('-').pop();
            const dropdownMenu = document.getElementById('dropdown-menu-' + postId);
            dropdownMenu.classList.toggle('hidden');
        });
    });

    // Đóng dropdown khi nhấp ra ngoài
    document.addEventListener('click', function(event) {
        const isClickInside = event.target.closest('.dropdown-menu') || event.target.closest('[id^="dropdown-btn-"]');
        if (!isClickInside) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });
});

function deletePost(postId) {
    swal({
            title: "Xác nhận xóa?",
            text: "Bạn có chắc chắn muốn xóa bài đăng này?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                submitPostDeletion(postId); // Gọi hàm gửi request xóa bài đăng
            } else {
                swal("Đã hủy", "Việc xóa đã bị hủy", "error");
            }
        });
}

// Hàm để gửi request xóa bài đăng
function submitPostDeletion(postId) {
    const form = document.querySelector(`#delete-post-form-${postId}`);
    form.submit(); // Submit form xóa bài đăng
}

document.getElementById('nextButton').addEventListener('click', function() {
    // Tìm phần tử có id 'suggestionsContainer' và cuộn nó sang phải
    document.getElementById('suggestionsContainer').scrollBy({ left: 300, behavior: 'smooth' });
});

document.getElementById('prevButton').addEventListener('click', function() {
    // Tìm phần tử có id 'suggestionsContainer' và cuộn nó sang trái
    document.getElementById('suggestionsContainer').scrollBy({ left: -300, behavior: 'smooth' });
});

</script>
<script>
    document.querySelectorAll('[id^="commentButton-"]').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.id.split('-')[1]; // Lấy post_id từ ID của nút
            const commentForm = document.getElementById('commentForm-' + postId); // Tìm form tương ứng với post_id
            commentForm.classList.toggle('hidden'); // Ẩn/hiện form bình luận
        });
    });
</script>
<script>
   function toggleComments(postId) {
    const commentsDiv = document.getElementById(`comments-${postId}`);
    // Kiểm tra nếu phần bình luận đang ẩn hay hiển thị
    if (commentsDiv.classList.contains('hidden')) {
        commentsDiv.classList.remove('hidden'); // Hiển thị phần bình luận
    } else {
        commentsDiv.classList.add('hidden'); // Ẩn phần bình luận
    }
}
function toggleReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    replyForm.classList.toggle('hidden');
}
function showSuggestions(value) {
    const suggestions = document.getElementById('suggestions');
    const items = suggestions.getElementsByTagName('li');

    // Hiển thị danh sách nếu có nội dung
    if (value) {
        suggestions.classList.remove('hidden');

        // Ẩn các gợi ý không khớp
        Array.from(items).forEach((item) => {
            if (item.innerText.toLowerCase().includes(value.toLowerCase())) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    } else {
        suggestions.classList.add('hidden'); // Ẩn danh sách nếu không có nội dung
    }
}

</script>

<script>
function showDropdown() {
    document.getElementById('dropdown').classList.remove('hidden');
}

function filterDropdown() {
    const input = document.getElementById('topic-input').value.toLowerCase();
    const dropdown = document.getElementById('dropdown');
    const items = dropdown.children;

    for (let i = 0; i < items.length; i++) {
        const text = items[i].innerText.toLowerCase();
        if (text.includes(input)) {
            items[i].style.display = 'block';
        } else {
            items[i].style.display = 'none';
        }
    }
}

function selectDropdown(topic) {
    document.getElementById('topic-input').value = topic;
    document.getElementById('dropdown').classList.add('hidden');
}

$(document).ready(function() {
            const errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                toastr.error(errorMessage, 'Lỗi', { // Thay đổi thông báo nếu cần
                    positionClass: 'toast-top-right',
                    timeOut: 5000, // Thời gian tự động ẩn
                    closeButton: true,
                    progressBar: true
                  
                });
                // $('.toast').css('max-width', '600px'); // Đặt chiều dài tối đa
            }
        });
$(document).ready(function() {
        const successMessage = "{{ session('success') }}";
            if (successMessage) {
                toastr.success(successMessage, 'Thành công', { // Thay đổi thông báo nếu cần
                    positionClass: 'toast-top-right',
                    timeOut: 5000, // Thời gian tự động ẩn
                    closeButton: true,
                    progressBar: true
                });
            }
        });
</script>

@endsection