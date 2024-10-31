<div class="mb-5">
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
                    {{-- Hiển thị tên người bình luận gốc cùng với nội dung bình luận của người hiện tại --}}
                    <span class="font-semibold mt-2">{{ $parentComment['user_name'] }}</span> {{ $comment['comment_content'] }}
                @else
                    {{-- Nếu không tìm thấy bình luận gốc --}}
                    {{ $comment['comment_content'] }}
                @endif
            </p>
        </div>
    </div>

    <div class="ml-16 mt-2 flex">
        <p class="text-gray-500 mr-3">{{ $comment['created_at']->locale('vi')->diffForHumans() }}</p>
        <button onclick="toggleReplyForm({{ $comment['id'] }})" class="text-blue-500">Trả lời</button>

        @php
            $hasReplies = collect($comments)->where('parent_id', $comment['id'])->isNotEmpty();
        @endphp

        @if ($hasReplies)
            <button onclick="toggleReplies({{ $comment['id'] }})" class="text-blue-500 ml-3">Xem phản hồi</button>
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
