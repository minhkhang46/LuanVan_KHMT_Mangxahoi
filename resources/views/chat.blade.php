@extends('layouts.app')
@section('title', 'Tin nhắn')
@section('content')
<style>
    /* html, body {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        height: 100%;
    } */

    .custom-status {
        display: block; /* Để áp dụng căn chỉnh cho một phần tử khối */
        text-align: right; /* Căn chỉnh văn bản về bên phải */
        padding-top: 0.5rem; /* Tương đương với pt-2 trong Tailwind CSS */
        margin-left: 2.5rem; /* Tương đương với ml-10 trong Tailwind CSS */
        font-size: 0.875rem; /* Tương đương với text-sm trong Tailwind CSS */
    }

    #chat-box1 {
        height: 2rem; /* Chiều cao cố định */
        display: flex; /* Sử dụng flexbox để căn chỉnh nội dung */
        background-color: white;
    }

    .image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .image-modal img {
        max-width: 90%;
        max-height: 90%;
    }

    .flex-container {
        display: flex;
        height: calc(100vh - 4rem); /* Chiều cao của viewport - chiều cao của navbar cố định (nếu có) */
    }

    .user-list {
        width: 450px; /* Bạn có thể điều chỉnh chiều rộng của danh sách người dùng */
        overflow-y: auto;
    }

    .chat-area {
        flex: 1; /* Chiếm toàn bộ không gian còn lại */
        display: flex;
        flex-direction: column;
    }

    #chat-box {
        flex: 1; /* Chiếm toàn bộ không gian còn lại trong khu vực trò chuyện */
        overflow-y: auto;
        display: flex;
        flex-direction: column-reverse;
    }

    #chat-box1 {
        display: flex;
        align-items: center;
        padding: 0.5rem;
    }

    .message {
        margin: 0.5rem 0;
    }
</style>

<div class="flex-container mx-auto pt-8">
    <!-- Phần danh sách người dùng -->
    <div class="user-list bg-white rounded-lg overflow-auto">
        <div class="px-2 py-3">
            <h4 class="text-2xl font-semibold text-gray-900 text-center">Tin nhắn</h4>
        </div>
        <div class="border border-gray-300 mb-4"></div>
        <div class="px-1 py-2" id="user-list">
            @if($users->isEmpty())
                <p class="text-gray-500 text-center">Không có người dùng nào.</p>
            @else
                <div class="space-y-4">
                    @foreach($users->sortByDesc(function ($user) use ($messagesWithUsers) {
                        $latestMessage = $messagesWithUsers->firstWhere(function ($message) use ($user) {
                            return ($message->receiver_id == $user->id || $message->sender_id == $user->id);
                        });
                        return $latestMessage ? $latestMessage->created_at : now()->subYears(10);
                    }) as $user)
                        @php
                            $latestMessage = $messagesWithUsers->firstWhere(function ($message) use ($user) {
                                return ($message->receiver_id == $user->id || $message->sender_id == $user->id);
                            });
                            $isSender = $latestMessage->sender_id !== session('id');
                            $isRead = $latestMessage->is_read == 1;
                        @endphp
                        @if ($latestMessage)
                            <a href="{{ route('chat', ['receiverId' => $user->id]) }}" class="block p-4 hover:bg-gray-200 transition duration-200">
                                <div class="flex items-center">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="h-14 w-14 rounded-full  mr-4 ml-5">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                                        <div class="flex items-center justify-between space-x-2">
                                            <p class="{{ $isSender && !$isRead ? 'font-bold text-gray-700' : 'text-gray-700' }} text-lg truncate max-w-[calc(100% - 150px)]">
                                                @if ($latestMessage->image)
                                                    <span class="flex items-center space-x-1 text-lg">
                                                        {{ !$isSender ? 'Bạn: ' : '' }}
                                                        <img id="imageIcon1" src="/luanvan_tn/public/image/images.png" alt="Image Icon" class="w-8 h-8 cursor-pointer ml-2 mr-1">
                                                        <span class="text-lg">Hình ảnh</span>
                                                    </span>
                                                @elseif ($latestMessage->file)
                                                    <span class="text-blue-500 truncate block w-full" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ !$isSender ? 'Bạn: ' : '' }}{{ $latestMessage->file_name }}
                                                    </span>
                                                @else
                                                    {{ !$isSender ? 'Bạn: ' : '' }}{{ $latestMessage->content }}
                                                @endif
                                            </p>
                                            <p class="text-gray-500 text-lg ml-auto whitespace-nowrap">
                                                {{ $latestMessage->created_at->locale('vi')->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Phần trò chuyện -->
    <div class="chat-area border-l-2 border-gray-300">
        <div class="bg-white shadow-md rounded-r-lg h-full flex flex-col">
            @if($receiverId != session('id'))
                @foreach ($activeUsers as $user)
                    @if ($user['id'] == $receiverId)
                        <a href="{{ route('profiles', ['id' => $receiverId]) }}">
                            <div class="px-4 py-2 border-b flex h-20">
                                <img src="{{ asset('storage/' . $user['avatar']) }}" alt="Receiver Avatar" class="h-14 w-14 mt-2 rounded-full  mr-2">
                                <div class="ml-2 mt-2">
                                    <h4 class="text-2xl font-semibold">{{ $user['name'] }}</h4>
                                    @if ($user['is_active'])
                                        <span class="status online text-green-500 block mt-1 text-lg">Đang hoạt động</span>
                                    @elseif (!empty($user['last_seen']))
                                        @php
                                            // Convert last_seen to Carbon instance
                                            $lastSeen = \Carbon\Carbon::parse($user['last_seen']);
                                            // Check if last seen is within the last 10 hours
                                            $isRecent = $lastSeen->diffInHours(now()) <= 10;
                                        @endphp
                                        @if ($isRecent)
                                            <span class="status offline text-gray-500 block mt-1">Hoạt động {{ $lastSeen->locale('vi')->diffForHumans() }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach

                <div class="border border-gray-300"></div>
                <div class="flex-1 bg-gray-100 p-4" id="chat-box">
                    @php
                        // Xác định tin nhắn gần nhất
                        $latestMessage = $messages->sortByDesc('created_at')->first();
                    @endphp

                    @foreach($messages as $message)
                        @php
                            $sender = $messageSenders->get($message->sender_id);
                            $receivers = $messageReceivers->get($message->receiver_id);
                            $isLatestMessage = $message->id === $latestMessage->id;
                        @endphp

                        <div class="flex items-start my-2 {{ $message->sender_id == session('id') ? 'justify-end' : 'justify-start' }}">
                            @if($message->sender_id != session('id') && $sender)
                                <img src="{{ asset('storage/' . $sender->avatar) }}" alt="Sender Avatar" class="h-10 w-10 rounded-full  mr-2">
                            @endif
                            <div class="flex-row">
                                <div class="message flex flex-col px-4 py-2 rounded-md border bg-white {{ $message->sender_id == session('id') ? 'border-green-300' : 'border-gray-300' }}">
                                    <p class="text-gray-800 text-lg">{{ $message->content }}</p>
                                    @if($message->image)
                                        <img src="{{ asset('storage/' . $message->image) }}" alt="Image" class="mt-2 rounded-md max-w-3xl zoomable-image" onclick="openImageModal(this.src)">
                                    @endif

                                    <!-- Hiển thị file đính kèm nếu có -->
                                    @if($message->file)
                                        <a href="{{ asset('storage/' . $message->file) }}" class="text-blue-500 mt-2 text-lg" download>
                                            Tải file đính kèm: {{ $message->file_name }}
                                        </a>
                                    @endif
                                    <small class="text-gray-500 text-base mt-1">{{ $message->created_at->locale('vi')->diffForHumans() }}</small>
                                </div>
                                @if($isLatestMessage)
                                    @if(!$message->is_read && $message->receiver_id != session('id'))
                                        <span class="custom-status">Đã gửi</span>
                                    @elseif($message->is_read && $message->receiver_id != session('id'))
                                        <span class="custom-status">Đã xem</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-4 bg-gray-100" id="chat-box1">
                    <div class="flex flex-row bg-white">
                        <p id="fileName2" class="text-gray-500 text-sm mt-2"></p>
                    </div>
                </div>
                <div class="border border-gray-300"></div>
                <div class="py-2 sticky bottom-0 bg-white">    
                    <form action="{{ route('messages.send') }}" method="POST" class="flex items-center p-3" enctype="multipart/form-data">
                        @csrf
                        <!-- Form tin nhắn nằm dưới -->
                        <div class="bg-white flex items-center w-full">
                            <div class="relative">
                                <img id="imageIcon1" src="/luanvan_tn/public/image/images.png" alt="Image Icon" class="w-8 h-8 mx-2 cursor-pointer" onclick="document.getElementById('fileInput1').click();">
                            </div>
                            <div class="relative mr-2">
                                <img id="imageIcon2" src="/luanvan_tn/public/image/file.png" alt="File Icon" class="w-8 h-8 cursor-pointer" onclick="document.getElementById('fileInput2').click();">
                            </div>
                            <input type="file" id="fileInput1" style="display: none;" name="image" accept="image/*" onchange="updateFileName('fileInput1')"/>
                            <input type="file" id="fileInput2" style="display: none;" name="file" onchange="updateFileName('fileInput2')"/>
                            <input type="hidden" name="receiver_id" value="{{ $receiverId }}">
                            <textarea name="content" rows="1" class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nhập tin nhắn"></textarea>
                            <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Gửi</button>
                        </div>
                    </form>
                </div>
            @else 
                <div class="bg-white shadow-md rounded-r-lg h-16 text-center">
                    <h4 class="text-xl font-semibold p-3">Chưa có tin nhắn</h4>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Khi nhấp vào biểu tượng hình ảnh, mở hộp thoại chọn ảnh
document.getElementById('imageIcon1').addEventListener('click', function() {
    document.getElementById('fileInput1').click();
});

// Khi nhấp vào biểu tượng file, mở hộp thoại chọn file
document.getElementById('imageIcon2').addEventListener('click', function() {
    document.getElementById('fileInput2').click();
});

function updateFileName(inputId) {
    var input = document.getElementById(inputId);
    var fileName = document.getElementById('fileName2'); // Đây là nơi hiển thị tên file
    if (input.files.length > 0) {
        var file = input.files[0];
        fileName.textContent = file.name;
    } else {
        fileName.textContent = '';
    }
}

// Mở modal phóng to
function openImageModal(src) {
    const modal = document.createElement('div');
    modal.classList.add('image-modal');
    modal.innerHTML = `<img src="${src}" alt="Zoomed Image">`;

    // Đóng modal khi nhấp vào
    modal.onclick = function() {
        document.body.removeChild(modal);
    };

    document.body.appendChild(modal);
}
</script>

@endsection
