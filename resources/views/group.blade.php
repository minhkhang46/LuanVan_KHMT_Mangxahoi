@extends('layouts.app')
@section('title', 'Nhóm')
@section('content')
<style>
    .modal {
        display: none; /* Modal bị ẩn mặc định */
    }

    .modal.show {
        display: flex; /* Modal hiển thị khi thêm lớp `show` */
    }
    .toast {
            width: auto !important; 
            font-size: 16px !important;
        }
</style>
<!-- Thêm Alert 2 CSS -->


<div class="container mx-auto mt-5 flex">
<!-- @if(session('message'))
            <div id="messageModal" class="fixed inset-0 flex items-center justify-center bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg p-5 max-w-sm w-full">
                    <h2 class="text-xl text-center font-bold mb-4">Thông báo</h2>
                    <p id="modalMessage" class="text-lg text-center">{{ session('message') }}</p>
                    <div class="flex justify-end mt-4">
                        <button id="closeModal" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Đóng</button>
                    </div>
                </div>
            </div>
        @endif -->

    <!-- Bên Trái: Nhóm đã tham gia và tất cả nhóm -->
    <div class="flex flex-col -ml-10 mt-2  h-auto overflow-y-auto space-y-8 mb-9" style="overflow: hidden; width: 33%;">
        <!-- Khu vực nhóm -->
        <div class="bg-white  w-full  mt-2 p-5 rounded-lg shadow-md border border-gray-200">
            <!-- Tiêu đề để hiện thị tất cả nhóm -->
            <h2 id="showAllGroupsTitle" class="text-2xl font-semibold mb-4 mt-2 text-gray-800 cursor-pointer">Tất cả nhóm</h2>
            
            <!-- Tất cả Nhóm (ẩn mặc định) -->
            <div id="allGroupsSection" class="hidden">
                <div class="space-y-4">
                    @foreach ($allGroups as $group)
                        @php
                            // Kiểm tra xem nhóm hiện tại có trong danh sách đã tham gia không
                            $isJoined = in_array($group->id, $joined);
                        @endphp
                        @if($group->is_approved === 1)
                            <div class="bg-gray-50 hover:bg-gray-100 transition duration-300 rounded-lg shadow-sm p-4 flex items-center">
                                <a href="{{ route('groups.show', $group->id) }}" class="flex items-center flex-1">
                                    <img src="{{ asset('storage/' . $group->image) }}" alt="{{ $group->name }}" class="w-16 h-16 rounded-full mr-4">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ $group->name }}</h3>
                                </a>
                                <div>
                                    @if ($isJoined)
                                        <button class="px-4 py-2 bg-green-500 text-white rounded-lg cursor-not-allowed" disabled>Đã tham gia</button>
                                    @else
                                        <form action="{{ route('group.join', $group->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Tham gia nhóm</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endif

                    @endforeach
                </div>
            </div>
            <div class="border border-gray-300 mb-4 mt-4"></div>
            <div class="flex justify-end mb-4">
                <button id="openFormBtn" class="bg-blue-500 text-xl w-full text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 focus:outline-none">
                    Tạo nhóm mới
                </button>
            </div>
            <div class="border border-gray-300 mb-4 mt-4"></div>
          
            <!-- Nhóm đã tham gia -->
            <div id="joinedGroupsSection">
                <h2 class="text-2xl font-semibold mb-4  text-gray-800">Nhóm bạn đã tham gia</h2>
                <div class="space-y-4">
                    @foreach ($joinedGroups as $group)
                        @if($group->is_approved === 1)
                            <div class="bg-gray-50 hover:bg-gray-100 transition duration-300 rounded-lg shadow-sm">
                                <a href="{{ route('groups.show', $group->id) }}" class="flex items-center p-4">
                                    <img src="{{ asset('storage/' . $group->image) }}" alt="{{ $group->name }}" class="w-16 h-16  rounded-full mr-4">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ $group->name }}</h3>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
   

<!-- Form tạo nhóm mới (ẩn mặc định) -->
    <div id="createGroupForm" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-md shadow-lg w-full max-w-md relative">
     

            <!-- Nút đóng form -->
            <button id="closeFormBtn" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                X
            </button>

            <!-- Form tạo nhóm -->
            <h2 class="text-3xl font-semibold mb-4 text-center">Tạo nhóm mới</h2>
            <form action="{{ route('groupcreate') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="mb-4">
                    <div class="mt-4">
                        <img id="image-preview" src="" alt="Xem trước ảnh" class="w-28 h-28 object-cover rounded-full hidden">
                    </div>
                    <label for="group-image" class="block text-lg font-medium text-gray-700">Ảnh nhóm</label>
                    <input type="file" name="image" id="group-image" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input" accept="image/*" onchange="previewImage(event)">
                    
                    <!-- Nơi hiển thị hình ảnh xem trước -->
                    
                </div>   
                <div class="mb-4">
                    <label for="group-name" class="block text-lg font-medium text-gray-700">Tên nhóm</label>
                    <input type="text" name="name" id="group-name"  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-lg font-medium text-gray-700">Mô tả</label>
                    <textarea name="description" id="description" rows="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input"></textarea>
                </div>
        
                <input type="text" name="is_approved" id="is_approved" hidden value="0"  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">

                <input type="text" name="status" id="status" hidden value="public"  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                <!-- <div class="mb-4">
                <label for="description" class="block text-lg font-medium text-gray-700">Chọn quyền riêng tư</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                                    <option value="">Chọn quyền riêng tư</option> 
                                    <option value="private">Riêng tư</option>
                                    <option value="public">Công khai</option>
                                </select>

                </div> -->
                <!-- Trường tải ảnh và hiển thị xem trước -->
               

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg w-full shadow hover:bg-blue-600 focus:outline-none">
                        Tạo nhóm
                    </button>
                </div>
            </form>

        </div>
    </div>


    <!-- Bên Phải: Bài đăng của nhóm -->
    <div class="flex-1 h-auto max-w-4xl ml-24 p-6">
        <h2 class="text-2xl font-semibold text-gray-500 mb-4">Bài viết gần đây</h2>
            @foreach($posts as $post)
                @if($post->group_id) 
                    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                        <div class="flex items-center mb-4">
                    
                            <div class="relative">
                                <a href="{{ route('groups.show', $post->group_id) }}">
                                    <img class="h-14 w-14   rounded-full" src="{{ asset('storage/' . $post->group_image) }}" alt="Group Image">
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
                                    <p class="text-xl  font-bold text-black">Nhóm {{ $post->group_name }}</p>
                                </a>
                                <a href="{{ $post->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $post->id_nd]) }}" class="flex items-center">
                                    <div class="flex mt-1">
                                        <p class="text-lg font-medium text-black">{{ $post->user_name }}.</p>
                                        <p class="text-lg text-black ml-4">
                                            @if (now()->diffInHours($post->created_at) >= 24)
                                                {{ $post->created_at->addDay()->format('d-m-Y') }}
                                            @else
                                                {{ $post->created_at->locale('vi')->diffForHumans() }}
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            </div>
                    
                                    
                            @if($post->id_nd === session('id'))
                                <div class="relative ml-auto">
                                    <button id="dropdown-btn-{{ $post->id }}" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <img src="/luanvan_tn/public/image/dots.png" alt="Options" class="w-6 h-6">
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div id="dropdown-menu-{{ $post->id }}" class="dropdown-menu hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                        <form action="{{ route('posts.destroy', ['id' => $post->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <img src="/luanvan_tn/public/image/trash.png" alt="Delete" class="w-5 h-5">
                                                <span class="ml-2">Xóa bài đăng</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="text-gray-700 text-xl">{{ $post->noidung }}</p>
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
                            <a href="{{ asset('storage/' . $post->files) }}" download class="text-blue-500 text-xl mt-2 inline-block">Download {{ $fileName }}</a>
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
                @endif
                
            @endforeach
      
    </div>

   
</div>


<script>
    document.getElementById('showAllGroupsTitle').addEventListener('click', function() {
        var allGroupsSection = document.getElementById('allGroupsSection');
        var isHidden = allGroupsSection.classList.contains('hidden');
        
        // Hiển thị hoặc ẩn phần tất cả nhóm
        allGroupsSection.classList.toggle('hidden');
        this.textContent = isHidden ? 'Tất cả nhóm' : 'Tất cả nhóm';
    });
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

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-post-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Ngăn việc submit mặc định để xử lý bằng JS

            if (confirm('Bạn có chắc chắn muốn xóa bài đăng này không?')) {
                // Gửi request xóa bài đăng
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Gửi CSRF token
                    }
                }).then(response => {
                    if (response.ok) {
                        alert('Bài đăng đã được xóa thành công.'); // Thông báo xóa thành công
                        location.reload(); // Làm mới trang sau khi xóa thành công
                    } else {
                        alert('Không thể xóa bài đăng.'); // Thông báo lỗi nếu xóa không thành công
                        console.error('Error: Failed to delete post');
                    }
                }).catch(error => {
                    alert('Đã xảy ra lỗi khi xóa bài đăng.'); // Thông báo lỗi khi gặp sự cố
                    console.error('Error:', error);
                });
            }
        });
    });
});

// Lấy các phần tử trong DOM
const openFormBtn = document.getElementById('openFormBtn');
const closeFormBtn = document.getElementById('closeFormBtn');
const createGroupForm = document.getElementById('createGroupForm');

// Hiển thị form khi nhấn nút "Tạo nhóm mới"
openFormBtn.addEventListener('click', function() {
    createGroupForm.classList.remove('hidden');
});

// Ẩn form khi nhấn nút "X"
closeFormBtn.addEventListener('click', function() {
    createGroupForm.classList.add('hidden');
});

// Ẩn form khi nhấn ra ngoài modal
window.addEventListener('click', function(event) {
    if (event.target === createGroupForm) {
        createGroupForm.classList.add('hidden');
    }
});

</script>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function() {
            const output = document.getElementById('image-preview');
            output.src = reader.result;
            output.classList.remove('hidden'); // Hiển thị ảnh khi đã tải lên
        }
        
        if (file) {
            reader.readAsDataURL(file); // Đọc dữ liệu ảnh và chuyển thành URL
        }
    }
</script>
<!-- <script>
        window.onload = function() {
            @if(session('message'))
                document.getElementById('messageModal').classList.remove('hidden');
                document.getElementById('modalMessage').innerText = "{{ session('message') }}";
            @endif

            // Đóng modal
            document.getElementById('closeModal').onclick = function() {
                document.getElementById('messageModal').classList.add('hidden');
            }
        }
    </script> -->

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
    $(document).ready(function() {
        const message = "{{ session('message') }}";
        if (message) {
            toastr.info(message, 'Thông báo', { // Thông báo dạng thông tin
                positionClass: 'toast-top-right',
                timeOut: 5000, // Thời gian tự động ẩn
                closeButton: true,
                progressBar: true
            });
        }
    });
    $(document).ready(function() {
            const errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                toastr.error(errorMessage, 'Lỗi', { // Thay đổi thông báo nếu cần
                    positionClass: 'toast-top-right',
                    timeOut: 5000, // Thời gian tự động ẩn
                    closeButton: true,
                    progressBar: true
                  
                });
            }
        });
        $(document).ready(function() {
            const warningMessage = "{{ session('warning') }}";
            if (warningMessage) {
                toastr.warning(warningMessage, 'Cảnh báo', { // Thông báo cảnh báo
                    positionClass: 'toast-top-right',
                    timeOut: 5000, // Thời gian tự động ẩn
                    closeButton: true,
                    progressBar: true
                });
            }
        });

</script>
@endsection
