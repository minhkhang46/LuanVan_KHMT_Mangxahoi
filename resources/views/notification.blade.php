@extends('layouts.app')

@section('title', 'Thông báo')

@section('content')
<div class="container mx-auto p-6 max-w-5xl">
    <h1 class="text-3xl font-semibold mb-6 text-gray-900">Thông báo</h1>

    <div class="flex space-x-4 mb-6">
        <a href="{{ route('notifications', ['id' => session('id'), 'status' => 'all']) }}" 
            class="px-6 py-2 text-white rounded-full transition duration-300 
                {{ $status == 'all' ? 'bg-blue-500 hover:bg-blue-600' : 'bg-blue-300 hover:bg-blue-400' }}">
            Tất cả
        </a>
        <a href="{{ route('notifications', ['id' => session('id'), 'status' => 'unread']) }}" 
            class="px-6 py-2 text-white rounded-full transition duration-300 
                {{ $status == 'unread' ? 'bg-gray-500 hover:bg-gray-600' : 'bg-gray-300 hover:bg-gray-400' }}">
            Chưa đọc
        </a>
    </div>

    @if($noUnreadNotifications)
        <div class="bg-yellow-100  text-yellow-800 text-center p-4 rounded-lg shadow-lg">
            Không có thông báo chưa đọc.
        </div>
    @elseif($notifications->isEmpty())
        <div class="bg-green-100 text-center text-green-800 p-4 rounded-lg shadow-lg">
            Không có thông báo nào.
        </div>
    @else
    <ul id="notification-list" class="space-y-4">
    @foreach($notifications as $notification)
        @php
            $data = json_decode($notification->data);
            $isRead = $notification->read_at ? true : false;
        @endphp
        
            <li class="flex items-start p-4 {{ $isRead ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded-lg shadow-md hover:bg-gray-50 transition duration-300">
                @if(isset($data->url) && $data->url)
                    <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="w-full">
                @else
                    <div class="w-full"> <!-- Nếu không có URL thì dùng <div> thay cho <a> -->
                    <a href="{{ route('notifications.markAsRead', $notification->id) }}" >
                @endif

                    <div class="flex w-full">
                        <!-- Avatar -->
                            @if(isset($data->avatar) && $data->avatar)
                                <img src="{{ asset('storage/' . $data->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full border-2 border-gray-200 mr-4  ">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-20 h-20 rounded-full border-2 border-gray-200 mr-4 ">
                            @endif

                        <!-- Nội dung thông báo -->
                        <div class="flex flex-col w-full">
                            <!-- Phần thông báo -->
                            <div class="text-gray-800 text-lg mb-1 w-full ">
                                {{ $data->message }}
                                @if(isset($data->reason))
                                    <br> <!-- Thêm thẻ <br> để xuống dòng -->
                                    {{ $data->reason }}
                                @endif
                            </div>
                            
                            <!-- Phần liên hệ với admin -->
                            @if(!empty($data->contact) && !empty($data->chat_url))
                                <div class="flex items-center text-lg text-gray-600 mb-1 w-full flex-grow">
                                    {{ $data->contact ?? 'Không có thông tin liên hệ.' }} : 
                                    <a href="{{ $data->chat_url }}" class="text-blue-500 hover:underline ml-2">
                                        Liên hệ với admin
                                    </a>
                                </div>
                            @endif

                            <!-- Thời gian và trạng thái đọc -->
                            <div class="flex items-center justify-between w-full ">
                                <span class="text-base text-gray-500">
                                @php
                                    $createdAt = $notification->created_at;
                                @endphp

                                @if ($createdAt->diffInDays(Carbon\Carbon::now()) >= 1)
                                    <!-- Hiển thị ngày theo định dạng YYYY-MM-DD nếu đã qua 1 ngày -->
                                    {{ $createdAt->locale('vi')->isoFormat('DD-MM-YYYY') }}
                                @else
                                    <!-- Hiển thị thời gian tương đối nếu chưa qua 1 ngày -->
                                    {{ $createdAt->locale('vi')->diffForHumans() }}
                                @endif
                                </span>
                                @if(!$isRead)
                                    <span class="ml-4 text-xs text-red-600 font-semibold bg-red-100 px-2 py-1 rounded-full">
                                        Chưa đọc
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(isset($data->url) && $data->url)
                </a> <!-- Đóng thẻ <a> nếu có URL -->
            @else
                </div> <!-- Đóng thẻ <div> nếu không có URL -->
            @endif
            </li>
    @endforeach
</ul>

    @endif
</div>

<script>
    function fetchNotifications() {
        fetch('/notifications/fetch')
            .then(response => response.json())
            .then(data => {
                const notificationList = document.getElementById('notification-list');
                const notificationCount = document.getElementById('notification-count');

                if (data.notifications.length > 0) {
                    notificationCount.textContent = data.newCount;
                    data.notifications.forEach(notification => {
                        const newNotification = `
                            <li class="flex items-center p-4 bg-white border border-gray-300 rounded-lg shadow-md hover:bg-gray-50 transition duration-300">
                                <img src="${notification.avatar}" alt="Avatar" class="w-14 h-14 rounded-full border-2 border-gray-200 mr-4">
                                <div class="text-gray-800 text-lg">${notification.message}</div>
                                <div class="text-gray-800 text-lg">${notification.contact}</div>
                                <span class="ml-4 text-xs text-red-600 font-semibold bg-red-100 px-2 py-1 rounded-full">Chưa đọc</span>
                            </li>
                        `;
                        notificationList.insertAdjacentHTML('afterbegin', newNotification);
                    });
                }
            });
    }

    // Polling every 10 seconds
    setInterval(fetchNotifications, 10000);
</script>

@endsection
