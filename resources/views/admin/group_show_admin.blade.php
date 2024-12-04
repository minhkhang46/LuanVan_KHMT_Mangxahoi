@extends('layouts.app')
@section('title', "Nhóm  $group->name ")
@section('content')
<style>
  .modal {
    display: none; /* Modal bị ẩn mặc định */
}

.modal.show {
    display: flex; /* Modal hiển thị khi thêm lớp `show` */
}
</style>
<div class=" mt-4 ml-4 text-left">
    @if(session('possition') != 0)
        <a href="{{ route('requested.groups') }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
            ← Quay lại 
        </a>
    @else
        <a href="{{ route('group') }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
            ← Quay lại danh sách nhóm 
        </a>
    @endif
</div>


<div class="container mx-auto px-4 py-10 mt-24">
    <!-- Tên nhóm -->
    @if ($group->is_approved !=0)
    <div class=" text-center mb-8 bg-white rounded-lg shadow-lg border border-gray-200 w-full h-full">
        <div class="flex flex-col md:flex-row items-center p-5 -ml-32">
            <!-- Avatar của nhóm -->
            <div class="relative mb-6 md:mb-0 md:w-1/3 text-center">
           
             

                <img src="{{ asset('storage/' . $group->image) }}"
                     srcset="{{ asset('storage/' . $group->image) }} 1x, {{ asset('storage/' . $group->image) }} 2x"
                     alt="Group Avatar"
                     class="h-44 w-44 rounded-full  border-4 border-blue-500 absolute -top-36 left-1/2 transform -translate-x-1/2">
            </div>

            <!-- Thông tin nhóm -->
            <div class="md:w-2/3 -ml-36 mt-20 md:mt-0">
                <h2 class="text-4xl font-semibold text-left md:text-left text-gray-900 capitalize mb-2">{{ $group->name }}</h2>
              
                <!-- Phần mô tả -->
                <div class="flex justify-between items-center mt-2">
                    <p class="text-lg text-black">{{$memberCount}} thành viên</p>
                    <!-- <p class="text-lg text-black">{{ $group->description }}</p> -->
                </div>
            </div>
            <div class="mt-4 -ml-10 flex space-x-4">
                               
                <!-- Nút Tham Gia Hoặc Thông Báo Đã Tham Gia -->
                @if (!$isMember)
                    <!-- Hiển thị nút Tham gia nhóm nếu người dùng chưa là thành viên -->
                    <form action="{{ route('group.join', $group->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-500 text-white font-semibold  px-6 py-2 text-lg rounded-lg hover:bg-green-600 transition duration-300">
                            Tham gia nhóm
                        </button>
                    </form>
                @else
                    <div class="relative inline-block text-left">
                                <div>
                                    <button id="dropdownButtongroup" class="bg-gray-500 text-white font-semibold px-6 py-2 text-lg rounded-lg">
                                    Đã tham gia
                                    </button>
                                </div>
                                
                                <!-- Dropdown menu -->
                                <div id="dropdownMenugroup" class="hidden  absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                    <div class="py-1 ">
                                        <form action="{{ route('removeMember', $group->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex px-4 py-2  hover:bg-gray-100">
                                                <img src="/luanvan_tn/public/image/logout.png" alt="Email Icon" class="w-8 h-8 mr-2" />
                                                <button type="submit" class="block w-full text-left  font-semibold text-gray-800 text-lg hover:bg-gray-100">
                                                   Rời nhóm
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                  
                   
                @endif
            </div>

        </div>
    </div>

    <!-- Grid chia cột cho Thành viên và Bài đăng -->
    <div class="flex flex-col md:flex-row gap-8 mt-5">
        <div class="w-full md:w-1/3"> 
        <div class="w-full bg-white p-5 rounded-lg shadow-lg border border-gray-200">
            <div class="mb-4">
              <h3 class="text-2xl font-semibold text-gray-900 mb-3">Giới thiệu</h3>
              <div class="border border-gray-300 mb-4"></div>
              <div class="flex items-center mb-2 ml-3">
               
                <p class="text-gray-900 text-xl text-center">{{ $group->description }}</p>
              </div>
              <!-- <div class="flex items-center mb-2 ml-3">
               @if( $group->status == 'public')
               <img src="/luanvan_tn/public/image/publlic.png" alt="Email Icon" class="w-6 h-6 mr-2" />
                <p class="text-gray-900 font-semibold text-xl">Nhóm công khai</p>
                @endif
              </div> -->
              
            </div>
        </div>
            <!-- Danh sách thành viên -->
            <div class="w-full mb-4 mt-5 bg-white p-5 rounded-lg shadow-lg border border-gray-200">
                <div class= "flex">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Thành viên</h2>
                    <a href="#" class="  text-lg ml-36 mt-1 text-blue-700  rounded-md hover:text-blue-800 ">
                    Xem tất cả thành viên
                    </a>
                </div>
                <div class="border border-gray-300 mb-4"></div>
                <ul class="flex flex-wrap">
                    @if($members->isEmpty())
                        <p class="text-gray-700 ml-3 text-lg text-center">Chưa có thành viên.</p>
                    @else
                        @php
                            // Giới hạn số thành viên hiển thị là 6
                            $limitedMembers = $members->take(6);
                        @endphp
                        
                        @foreach ($limitedMembers as $member)
                            @php
                                $memberName = $member->name ?? 'Không xác định';
                                $memberAvatar = $member->avatar ?? 'default-avatar.png';
                                $isCurrentUser = ($member->id === session('id'));
                                $profileRoute = route('profiles', ['id' => $member->id]);
                            @endphp
                            <li class="mb-4 flex flex-col w-1/3">
                                <a href="{{ $isCurrentUser ? route('profile', ['id' => $member->id]) : $profileRoute }}" class="flex flex-col items-center">
                                    <img src="{{ asset('storage/' . $memberAvatar) }}" alt="Member Avatar" class="h-32 w-32 rounded-lg mb-2">
                                    <p class="text-lg text-gray-700">{{ $memberName }}</p>
                                </a>
                            </li>
                        @endforeach

                    
                    @endif
                </ul>

            </div>
        </div>
        <div class="w-full md:w-2/3">
            <div id="postForm" class="bg-white p-5 mb-8 rounded-lg shadow-md overflow-y-auto">
                <form action="{{ route('post_content') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input name="id_nd" type="hidden" value="{{ session('id') }}" />
                    <input name="group_id" type="hidden" value="{{ $group->id }}" />
                    <div class="mb-4 flex items-center">
                        <img class="h-11 w-11 rounded-full" src="{{ asset('storage/' . session('avatar')) }}" alt="User Avatar">
                        <textarea id="content" name="noidung" rows="1"
                                  class="w-full p-2  ml-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                                  placeholder="Write something..." oninput="toggleSubmitButton()"></textarea>
                    </div>
                    <div class="border border-gray-300 -mb-3"></div>
                    <div class="flex flex-col mb-4 space-y-4 items-end mt-3">
                        <div id="imagePreviewContainer" class="hidden">
                            <img id="imagePreview" class="w-full h-auto border border-gray-300 p-1" alt="Selected Image">
                            <div id="imageFileName" class="text-xs text-gray-500 truncate"></div>
                        </div>
                        <div id="filePreviewContainer" class="hidden">
                            <div id="filePreview" class="w-full h-auto border border-gray-300 p-1"></div>
                            <div id="fileFileName" class="text-xs text-gray-500 truncate"></div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Image Icon -->
                            <label for="image" class="cursor-pointer text-blue-500 hover:text-blue-600 flex flex-col items-center">
                                <img id="imageIcon" src="/luanvan_tn/public/image/image.png" alt="Image Icon" class="w-8 h-8">
                                <input id="image" name="images" type="file" class="sr-only"
                                       onchange="previewSelectedFile(event, 'imagePreview', 'imageFileName')" />
                            </label>
                            <!-- File Icon -->
                            <label for="file" class="cursor-pointer text-blue-500 hover:text-blue-600 flex flex-col items-center">
                                <img id="fileIcon" src="/luanvan_tn/public/image/folder.png" alt="File Icon" class="w-8 h-8">
                                <input id="file" name="files" type="file" class="sr-only"
                                       onchange="previewSelectedFile(event, 'filePreview', 'fileFileName')" />
                            </label>
                        </div>
                    </div>
                    <button id="postButton" type="submit"
                            class="bg-blue-500 text-white w-full px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition duration-300 text-lg opacity-50 cursor-not-allowed"
                            disabled>Post</button>
                </form>
            </div>

            <div class="bg-white p-5 rounded-lg mb-4">
                <h3 class="text-2xl font-semibold text-gray-900 text-left">Bài viết</h3>
            </div>
            <!-- Danh sách bài đăng -->
            @if($posts->isEmpty())
                <!-- Nếu không có bài viết -->
                <div class="bg-white p-6 rounded-lg mb-4">
                    <p class="text-gray-700 mb-2 text-xl text-center">Hiện tại không có bài viết.</p>
                </div>
            @else
                @foreach ($posts as $p)
                    <!-- Hiển thị từng bài viết -->
                    <div class= " bg-white p-10 rounded-lg mb-6 border border-gray-300 shadow-sm">
                        <div class="flex items-center mb-4">
                        @if($p->id_nd === session('id'))
                            <a href="{{ route('profile', ['id' => session('id')]) }}" class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-14 w-14 rounded-full" src="{{ asset('storage/' . $p->user_avatar) }}" alt="User Avatar">
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-xl font-semibold text-black">{{ $p->user_name }}</p>
                                    <p class="text-lg text-gray-700">
                                        @if (now()->diffInHours($p->created_at) >= 24)
                                            {{ $p->created_at->addDay()->format('d-m-Y') }}
                                        @else
                                            {{ $p->created_at->locale('vi')->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                                </a>
                        @else
                            <a href="{{ route('profiles', ['id' => $p->id_nd]) }}" class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-14 w-14 rounded-full" src="{{ asset('storage/' . $p->user_avatar) }}" alt="User Avatar">
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-xl font-semibold text-black">{{ $p->user_name }}</p>
                                    <p class="text-lg text-gray-700">
                                        @if (now()->diffInHours($p->created_at) >= 24)
                                            {{ $p->created_at->addDay()->format('d-m-Y') }}
                                        @else
                                            {{ $p->created_at->locale('vi')->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @endif
                            

                            <!-- Dropdown Trigger -->
                            @if($p->id_nd === session('id'))
                            <div class="relative ml-auto">
                                <button id="dropdown-btn-{{ $p->id }}" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <!-- Icon ba chấm -->
                                    <img id="imageIcon" src="/luanvan_tn/public/image/dots.png" alt="Image Icon" class="w-4 h-4">
                                </button>

                                <!-- Dropdown Menu -->
                                <div id="dropdown-menu-{{ $p->id }}" class="dropdown-menu hidden absolute right-0 mt-1 w-40 bg-gray-100 rounded-md shadow-lg z-50">
                                    <form action="{{ route('posts.destroy', ['id' => $p->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
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

                        <p class="text-gray-700 mb-4 mt-5 text-xl">{{ $p->noidung }}</p>
                        @if($p->images && $p->images !== 'default/image.png')
                        <div class="flex justify-center mb-4">
                    @php
                        $imageSize = $imageSizes[$p->id] ?? null;
                        $width = $imageSize[0] ?? null; // Chiều rộng
                        $height = $imageSize[1] ?? null; // Chiều cao
                    @endphp

                    <img src="{{ asset('storage/' . $p->images) }}" alt="Post Image" 
                        class="mt-4 border border-gray-300 rounded-md custom-image"
                        style="max-height: 700px; 
                                @if($height && $width && $height > $width) width: auto; height: auto; object-fit: contain; 
                                @else width: 100%; height: auto; object-fit: cover; @endif">
                </div>
                        @endif
                        @if($p->files)
                            @php
                                $fileName = basename($p->files);
                            @endphp
                            <a href="{{ asset('storage/' . $p->files) }}" download
                               class="text-blue-500 mt-4 inline-block hover:underline">
                                Download {{ $fileName }}
                            </a>
                        @endif
                        <div class="post">

                        <div class="text-base mt-2 flex justify-between">
                            <span id="like-count-{{ $p->id }}" class="cursor-pointer" onclick="openModal({{ $p->id }})">
                                @if($p->is_liked)
                                    @if($p->likes_count > 1)
                                        <span class="like-count">Bạn và {{ $p->likes_count - 1 }} người khác</span>
                                    @else
                                        <span class="like-count">Bạn</span>
                                    @endif
                                @else
                                    <span class="like-count">
                                        @if($p->likes_count > 0)
                                            {{ $p->likes_count }}
                                        @endif
                                    </span>
                                @endif
                            </span>
                            @if($postsWithComments[$p->id]['comment_count'] > 0) <!-- Kiểm tra số bình luận -->
                                <span  onclick="toggleComments({{ $p->id }})">{{ $postsWithComments[$p->id]['comment_count'] }} bình luận</span> <!-- Hiển thị bình luận nếu có -->
                            @endif
                        </div>

                            <div id="likes-modal-{{ $p->id }}"
                            class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center z-50">
                            <div class="modal-content bg-white p-6 rounded-lg shadow-lg max-w-lg w-full relative">
                                <button class="close-btn absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl"
                                    onclick="closeModal({{ $p->id }})">
                                    &times;
                                </button>
                                <h2 class="text-xl font-semibold mb-4 text-center">Danh sách người đã thích bài viết</h2>
                                <ul class="list-disc pl-5 space-y-4">

                                    @if(isset($likes[$p->id]))
                                    @foreach($likes[$p->id] as $like)
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
                                    <input type="hidden" name="post_id" value="{{ $p->id }}">
                                    @if(!$p->is_liked)
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
                                <button id="commentButton-{{ $p->id }}" class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                    <img id="imageIcon" src="/luanvan_tn/public/image/comment.png" alt="Image Icon" class="w-8 h-8">
                                    <span class="ml-2 font-semibold text-lg">Bình luận</span>
                                </button>  
                                </div>
                                <div class="post mb-8 mt-8">
                                    <div id="comments-{{ $p->id }}" class="comments hidden overflow-y-auto max-h-96"> <!-- Thay đổi max-h-60 thành chiều cao bạn mong muốn -->
                                        @if(isset($postsWithComments[$p->id]['comments']) && count($postsWithComments[$p->id]['comments']) > 0)
                                            @foreach ($postsWithComments[$p->id]['comments'] as $comment)
                                                @if (!$comment['parent_id']) <!-- Hiển thị bình luận gốc -->
                                                    @include('partials.comment', ['comment' => $comment, 'post' => $p, 'comments' => $postsWithComments[$p->id]['comments']])
                                                @endif
                                            @endforeach
                                        @else
                                            <p>Chưa có bình luận nào.</p>
                                        @endif
                                    </div>
                                </div>
                                <div id="commentForm-{{ $p->id }}" class="hidden mt-6">
                                    <form action="{{ route('comments.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="post_id" value="{{ $p->id }}">
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
        </div>
    </div>
    @else 
    <div class="bg-green-100 text-center text-xl text-green-800 p-4 rounded-lg shadow-lg">
            Nhóm đang đợi duyệt
        </div>
    @endif
</div>

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

function toggleSubmitButton() {
    const textarea = document.getElementById('content');
    const submitButton = document.getElementById('postButton');

    if (textarea.value.trim() !== '') {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        submitButton.classList.add('opacity-100', 'cursor-pointer');
    } else {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        submitButton.classList.remove('opacity-100', 'cursor-pointer');
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

document.getElementById('dropdownButtongroup').addEventListener('click', function() {
    var dropdownMenu = document.getElementById('dropdownMenugroup');
    
    // Toggle the 'hidden' class to show/hide the dropdown
    dropdownMenu.classList.toggle('hidden');
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

</script>
@endsection
