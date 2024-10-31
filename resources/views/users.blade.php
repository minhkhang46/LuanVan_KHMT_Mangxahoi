<!-- resources/views/profile.blade.php -->
@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<style>
  .modal {
    display: none; /* Modal bị ẩn mặc định */
}

.modal.show {
    display: flex; /* Modal hiển thị khi thêm lớp `show` */
}
</style>
  <div class="container mx-auto px-4 py-8 mt-20">
    <!-- User Info Section -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200  w-full h-full">
      <div class="flex flex-col md:flex-row items-center p-5 -ml-32">
        <!-- User Avatar Section -->
        <div class="relative mb-6 md:mb-0 md:w-1/3 text-center">
          <img
            src="{{ asset('storage/' . $user->avatar) }}"
            srcset="{{ asset('storage/' . $user->avatar) }} 1x, {{ asset('storage/' . $user->avatar) }} 2x"
            alt="User Avatar"
            class="h-44 w-44 rounded-full  border-4 border-blue-500 absolute -top-36 left-1/2 transform -translate-x-1/2"
          >
        </div>

        <!-- User Info Section -->
        <div class="md:w-2/3 -ml-32 mt-20 md:mt-0">
            <h2 class="text-4xl font-semibold text-left md:text-left text-gray-900 capitalize mb-2">{{ $user->name }}</h2>
          
            <!-- Buttons Section -->
            <div class="flex justify-between items-center mt-2">
              <p class="text-lg text-black">{{$totalFriends}} bạn bè</p>
            
              <div class="flex space-x-4 -mr-20"> <!-- Thêm thẻ div để bao quanh các nút -->
              <a href="{{ route('chat', ['receiverId' => $user->id]) }}" class="bg-blue-500  text-white font-semibold  px-4 py-2  text-lg rounded-lg hover:bg-blue-700 transition duration-300">Nhắn tin</a>
                  @if($receivedRequest)
                      @if($receivedRequest->status == 0)
                          <form action="{{ route('acceptFriendRequest', $user->id) }}" method="POST" class="inline-block">
                              @csrf
                              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                                  Chấp nhận
                              </button>
                          </form>
                          <form action="#" method="POST" class="inline-block">
                              @csrf
                              <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition duration-300">
                                  Từ chối
                              </button>
                          </form>
                            @elseif($receivedRequest->status == 1)
                            <div class="relative inline-block text-left">
                                <div>
                                    <button id="dropdownButton1" class="inline-flex justify-between w-full text-lg bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold">
                                        Bạn bè
                                    </button>
                                </div>
                                
                                <!-- Dropdown menu -->
                                <div id="dropdownMenu1" class="hidden  absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                    <div class="py-1 ">
                                    <form action="{{ route('removeFriend',  $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="flex px-4 py-2  hover:bg-gray-100">
                                                <img src="/luanvan_tn/public/image/un.png" alt="Email Icon" class="w-8 h-8 mr-2" />
                                                <button type="submit" class="block w-full text-left  font-semibold text-gray-800 text-lg hover:bg-gray-100">
                                                    Hủy Kết Bạn
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                      @endif
                    @elseif($requestStatus)
                      @if($requestStatus->status == 0)
                          <p class="text-lg bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold">Yêu cầu đã gửi</p>
                      @elseif($requestStatus->status == 1)
                      <div class="relative inline-block text-left">
                                <div>
                                    <button id="dropdownButton1" class="inline-flex justify-between w-full text-lg bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold">
                                        Bạn bè
                                    </button>
                                </div>
                                
                                <!-- Dropdown menu -->
                                <div id="dropdownMenu1" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                    <div class="py-1">
                                        <form action="{{ route('removeFriend', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="block w-full text-left px-4 py-2 font-semibold text-gray-800 text-lg hover:bg-gray-100">
                                                Hủy Kết Bạn
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                      @elseif($requestStatus->status == 2)
                          <p class="text-lg text-red-600">Yêu cầu đã bị từ chối</p>
                      @endif
                  @else
                      <form action="{{ route('sendFriendRequest', $user->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="bg-green-500 text-white px-4 py-2 text-lg rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                              Thêm bạn bè
                          </button>
                      </form>
                  @endif
                
              </div> <!-- Kết thúc thẻ div bao quanh -->
          </div>




        </div>
      </div>
    </div>

    <!-- User Details and Posts Section -->
    <div class="flex flex-col md:flex-row gap-8 mt-5">
      <!-- User Details and Friends List -->
      <div class="w-full md:w-1/3">
        <!-- Giới thiệu -->
        <div class="w-full bg-white p-5 rounded-lg shadow-lg border border-gray-200">
            <div class="mb-4">
              <h3 class="text-2xl font-semibold text-gray-900 mb-3">Giới thiệu</h3>
              @if($user->description)
              <p class="text-gray-700  text-center text-lg mb-4">{{ $user->description }}</p>
              @else 
              <p class="text-gray-700  text-center text-lg mb-4">Chưa có mô tả</p>
              @endif
              <div class="border border-gray-300 mb-4"></div>
                <div class="flex items-center mb-2 ml-2">
                    <img src="/luanvan_tn/public/image/study.png" alt="Email Icon" class="w-8 h-8 mr-2" />
                    @if($user->chuyende )
                        <p class="text-gray-700 text-lg">Chuyên đề {{ $user->chuyende }}</p>
                    @else
                        <p class="text-gray-700 text-lg">Chưa có chuyên đề</p>
                    @endif
                </div>
                <div class="flex items-center mb-2 ml-2">
                    <img src="/luanvan_tn/public/image/cv.png" alt="CV Icon" class="w-8 h-8 mr-2" />
                    @if($user->cv)
                        <a href="{{ asset('storage/cv/' . $user->cv) }}" download class="text-blue-600 text-lg">
                                {{ $user->cv }}
                        </a>
                    @else 
                        <p class="text-gray-700 text-lg">Chưa có CV</p>
                    @endif
                </div>
              <div class="flex items-center mb-2 ml-3">
                <img src="/luanvan_tn/public/image/email.png" alt="Email Icon" class="w-6 h-6 mr-2" />
                <p class="text-gray-700 text-lg">{{ $user->email }}</p>
              </div>
              <div class="flex items-center mb-2 ml-3">
                <img src="/luanvan_tn/public/image/phone.png" alt="Phone Icon" class="w-6 h-6 mr-2" />
                <p class="text-gray-700 text-lg">{{ $user->phone }}</p>
              </div>
              <div class="flex items-center mb-2 ml-3">
                <img src="/luanvan_tn/public/image/day.png" alt="Day Icon" class="w-6 h-6 mr-2" />
                <p class="text-gray-700 text-lg">
                  @php
                    use Carbon\Carbon;
                    $carbonDate = Carbon::parse($user->date);
                    $formattedDate = $carbonDate->locale('vi')->translatedFormat('d F Y');
                  @endphp
                  {{ $formattedDate }}
                </p>
              </div>
            </div>
        </div>
        <!-- Danh sách bạn bè -->
        <div class="mb-4 w-full bg-white p-5 rounded-lg shadow-lg border border-gray-200 mt-5" >
          <h3 class="text-2xl font-semibold text-gray-900 mb-3">Danh sách bạn bè</h3>
          <div class="border border-gray-300 mb-4"></div>
            @if(empty($friendInfos))
                  <p class="text-gray-700 ml-3 text-lg text-center">Chưa có bạn bè.</p>
              @else
              @php
                  $count = 0;
              @endphp

                <ul class="flex flex-wrap">
                    @foreach($friendInfos as $friendInfo)
                        @php
                            $friendName = $friendInfo->name ?? 'Không xác định';
                            $friendAvatar = $friendInfo->avatar ?? 'default-avatar.png';
                            $profileRoute = route('profiles', ['id' => $friendInfo->id]);
                            $isCurrentUser = ($friendInfo->id === $sessionUserId);
                            $count++;
                        @endphp

                        <li class="mb-4 flex flex-col w-1/3">
                            <a href="{{ $isCurrentUser ? route('profile', ['id' => $friendInfo->id]) : $profileRoute }}" class="flex flex-col items-center">
                                <img src="{{ asset('storage/' . $friendAvatar) }}" alt="Friend Avatar" class="h-32 w-32 rounded-lg mb-2">
                                <p class="text-lg text-gray-700 text-center">{{ $friendName }}</p>
                            </a>
                        </li>

                        @if($count % 6 == 0)
                            
                            <ul class="flex flex-wrap"></ul>
                        @endif
                    @endforeach
                </ul>

              @endif
          </div>
        </div>
      
      <!-- User Posts Section -->
        <div class="w-full md:w-2/3">
          <div class="bg-white p-5 rounded-lg mb-4">
            <h3 class="text-2xl font-semibold text-gray-900 text-left">Bài viết</h3>
          </div>
          @if($posts->isEmpty())
            <!-- Nếu không có bài viết -->
            <div class="bg-white p-6 rounded-lg mb-4">
              <p class="text-gray-700 mb-2 text-xl text-center">Hiện tại không có bài viết.</p>
            </div>
          @else
            @foreach ($posts as $p)
              <!-- Hiển thị từng bài viết -->
              <div class="bg-white p-10 rounded-lg mb-6 border border-gray-300 shadow-sm">
                <div class="flex items-center mb-4">
                  <div class="flex-shrink-0">
                    <img class="h-14 w-14 rounded-full" src="{{ asset('storage/' . $p->user_avatar) }}" alt="User Avatar">
                  </div>
                  <div class="ml-3 flex-1">
                    <p class="text-2xl font-semibold text-black-700">{{ $p->user_name }}</p>
                    <div class="flex">
                        <p class="text-lg text-gray-500">{{ $p->created_at->locale('vi')->diffForHumans() }}</p>
                        @if( $p->regime === 1)
                            <img id="imageIcon" src="/luanvan_tn/public/image/friend.png" alt="Image Icon"
                                class="w-5 h-5 ml-3 mt-1">
                        @else
                            <img id="imageIcon" src="/luanvan_tn/public/image/publlic1.png" alt="Image Icon"
                            class="w-5 h-5 ml-3 mt-1">
                        @endif
                    </div>
                  </div>
                </div>
                @if($p->topic)
                <p class="text-black font-semibold text-xl mb-2">Chủ đề {{ $p->topic }}</p>
                @endif
                <p class="text-black  text-xl">{{ $p->noidung }}</p>
                @if ($p->images && $p->images !== 'default/image.png')
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
                  <a href="{{ asset('storage/' . $p->files) }}" download class="text-blue-500 mt-4 inline-block hover:underline">
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
  </div>
  <script>


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

document.getElementById('dropdownButton1').addEventListener('click', function() {
    var dropdownMenu = document.getElementById('dropdownMenu1');
    
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
