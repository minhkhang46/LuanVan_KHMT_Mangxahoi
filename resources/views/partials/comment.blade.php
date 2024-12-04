

    <div class="mb-5" id="comment-{{ $comment['id'] }}">
    <div class="flex">
        @if($comment['user_avatar'])
            <img src="{{ asset('storage/' . $comment['user_avatar']) }}" alt="{{ $comment['user_name'] }}" class="w-11 h-11 mr-5 rounded-full">
        @endif
        <div class="bg-gray-100 rounded-lg p-3">
            <p class="text-base font-semibold">{{ $comment['user_name'] }}</p>
            
            <p class="text-base">
                @php
                    // Tìm người bình luận gốc dựa trên parent_id
                    $parentComment = collect($comments)->firstWhere('id', $comment['parent_id']);
                @endphp

                @if($parentComment)
                    <span class="font-semibold mt-2">{{ $parentComment['user_name'] }}</span> {{ $comment['comment_content'] }}
                @else
                    {{ $comment['comment_content'] }}
                @endif
            </p>
        </div>

    
    </div>


    <div class="ml-16 mt-2 flex items-center space-x-3">
        <p class="text-gray-500">{{ $comment['created_at']->locale('vi')->diffForHumans() }}</p>
        <button onclick="toggleReplyForm({{ $comment['id'] }})" class="text-blue-500">Trả lời</button>

        @php
            $hasReplies = collect($comments)->where('parent_id', $comment['id'])->isNotEmpty();
        @endphp

        @if ($hasReplies)
            <button onclick="toggleReplies({{ $comment['id'] }})" class="text-blue-500">Xem phản hồi</button>
        @endif

        @if( $post->id_nd === session('id'))
            <!-- Form xóa bình luận -->
            <form id="delete-comment-form-{{ $comment['id'] }}" action="{{ route('comments.destroy', ['id' => $comment['id']]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="delete-comment text-red-600 text-base" data-id="{{ $comment['id'] }}">
                    Xóa bình luận
                </button>
            </form>
        @endif
    </div>


    <!-- Form trả lời bình luận -->
    <div id="reply-form-{{ $comment['id'] }}" class="hidden mt-2 ml-16 mr-5 bg-gray-200 p-4 rounded-lg">
        <div class="flex justify-end mb-2">
            <button onclick="toggleReplyForm({{ $comment['id'] }})" class="text-red-500 font-semibold">X</button>
        </div>
        <div class="flex">
            <form action="{{ route('comments.reply', ['post_id' => $post->id]) }}" method="POST" class="w-full">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment['id'] }}">
                <input type="hidden" name="user_id" value="{{ session('id') }}">
                <div class="flex">
                    <textarea id="reply-input-{{ $comment['id'] }}" name="content" rows="1" class="border border-gray-300 w-full mr-2 p-3 mb-2" placeholder="Trả lời bình luận của {{ $comment['user_name'] }}"></textarea>
                    <button type="submit">
                        <img src="/luanvan_tn/public/image/send.png" alt="Gửi" class="w-8 h-8">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hiển thị các câu trả lời cho bình luận gốc -->
    <div id="replies-{{ $comment['id'] }}" class="ml-8 mt-4 mb-4 hidden"> <!-- Ẩn mặc định -->
        @foreach ($comments as $reply)
            @if ($reply['parent_id'] === $comment['id'])
                <div class="relative flex">
                 
                    <div class="w-full">
                        @include('partials.comment', ['comment' => $reply, 'post' => $post, 'comments' => $comments])
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
<div id="csrf-token" style="display: none;">{{ csrf_token() }}</div>

<script>
function toggleReplies(commentId) {
    const repliesDiv = document.getElementById(`replies-${commentId}`);
    if (repliesDiv.classList.contains('hidden')) {
        repliesDiv.classList.remove('hidden');
        repliesDiv.classList.add('block'); // Hoặc bạn có thể sử dụng 'flex' nếu cần
    } else {
        repliesDiv.classList.add('hidden');
        repliesDiv.classList.remove('block'); // Hoặc 'flex'
    }
}
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.delete-comment', function() {
    var commentId = $(this).data('id');  // Lấy ID bình luận
    var token = $('meta[name="csrf-token"]').attr('content');  // Lấy CSRF Token
    var form = $('#delete-comment-form-' + commentId);  // Lấy form chứa ID bình luận

    // Gửi yêu cầu Ajax
    $.ajax({
        url: form.attr('action'),  // Đường dẫn đến route xóa bình luận
        method: 'POST',
        data: {
            _token: token,
            _method: 'DELETE'  // Xác nhận phương thức DELETE
        },
        success: function(response) {
            if (response.success) {
                // Nếu thành công, xóa bình luận khỏi giao diện
                $('#comment-' + commentId).remove();
            } else {
                // Nếu không có quyền xóa, hiển thị thông báo lỗi
                alert(response.message);
            }
        },
        // error: function() {
        //     alert('Có lỗi xảy ra khi xóa bình luận!');
        // }
    });
});
</script>



</script>



