@extends('layouts.app')

@section('content')
<style>
.modal {
    display: none; /* Modal bị ẩn mặc định */
}

.modal.show {
    display: flex; /* Modal hiển thị khi thêm lớp `show` */
}
</style>
<div class="container mx-auto max-w-3xl my-10 p-7 bg-white shadow-md rounded-lg">
    <div class="flex items-center mb-4">
            
                @if($post->group_id)
                   
                    <div class="relative">
                        <a href="{{ route('groups.show', $post->group_id) }}">
                            <img class="h-14 w-14  rounded-full" src="{{ asset('storage/' .  $groupImg) }}" alt="Group Image">
                        </a>

                        <!-- Hình người dùng ở góc của hình nhóm -->
                        <a href="{{ $post->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $post->id_nd]) }}" class="flex items-center">
                            <div class="absolute bottom-0 right-0 transform translate-x-1/4 translate-y-1/4">
                                <img class="h-10 w-10 rounded-full border-4 border-white " src="{{ asset('storage/' . $user->avatar) }}" alt="User Avatar">
                            </div>
                        </a>
                    </div>

                    <!-- Thông tin về bài đăng -->
                    <div class="ml-3 flex-1">
                        <a href="{{ route('groups.show', $post->group_id) }}">
                            <p class="text-xl font-bold text-black">Nhóm {{ $groupName }}</p>
                        </a>
                        <a href="{{ $post->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $post->id_nd]) }}" class="flex items-center">
                            <div class="flex mt-1">
                                <p class="text-lg font-medium text-black">{{ $user->name }}.</p>
                                <p class="text-lg text-gray-500">
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
                        <img class="h-14 w-14 rounded-full " src="{{ asset('storage/' . $user->avatar) }}" alt="User Avatar">
                        </div>
                        <div class="ml-3 flex-1">
                        
                            <p class="text-xl font-semibold text-black">{{ $user->name }}</p>
                            <div class="flex">
                                <p class="text-lg text-gray-500">
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

    @if($post->topic)
                <p class="text-black font-semibold text-xl mb-3">Chủ đề {{ $post->topic }}</p>
                @endif
    <p class="text-black text-xl">{{ $post->noidung }}</p>

    @if ($post->images && $post->images !== 'default/image.png')
    <div class="mb-6">
        <img src="{{ asset('storage/' . $post->images) }}" alt="Image"
            class="w-full h-auto rounded-lg shadow-lg">
    </div>
    @endif

    @if ($post->files)
    <div class="mb-6">
        <a href="{{ asset('storage/' . $post->files) }}" class="inline-block text-blue-500 hover:underline">
            Tải xuống file đính kèm
        </a>
    </div>
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

    <!-- Modal -->
    <div id="likes-modal-{{ $post->id }}" class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center z-50">
        <div class="modal-content bg-white p-6 rounded-lg shadow-lg max-w-lg w-full relative">
            <button class="close-btn absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl" onclick="closeModal({{ $post->id }})">
                &times;
            </button>
            <h2 class="text-xl font-semibold mb-4 text-center">Danh sách người đã thích bài viết</h2>
            <ul class="list-disc pl-5 space-y-4">
               
                    @foreach($likes as $like)
                        <li class="flex items-center mb-3">
                            @if($like['id'] == session('id'))
                                <a href="{{ route('profile', ['id' => session('id')]) }}" class="flex items-center">
                                    <img src="{{ asset('storage/' . $like['avatar']) }}" alt="Avatar" class="w-12 h-12 rounded-full mr-3">
                                    <span class="text-lg ml-2">Bạn</span>
                                </a>
                            @else
                                <a href="{{ route('profiles', ['id' => $like['id']]) }}" class="flex items-center">
                                    <img src="{{ asset('storage/' . $like['avatar']) }}" alt="Avatar" class="w-12 h-12 rounded-full mr-3">
                                    <span class="text-lg ml-2">{{ $like['name'] }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
               
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
                    <img id="imageIcon" src="/luanvan_tn/public/image/like.png" alt="Image Icon" class="w-8 h-8">
                    <span class="mt-1 ml-2 font-semibold">Thích</span>
                </button>
                @else
                <button type="submit"
                    class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                    <img id="imageIcon" src="/luanvan_tn/public/image/like_blue.png" alt="Image Icon" class="w-8 h-8">
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
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    $('form').on('submit', function(e) {
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