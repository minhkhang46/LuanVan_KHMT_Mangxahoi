@extends('layouts.app')

@section('title', 'Thông báo')

@section('content')
<div class="container mx-auto p-6 max-w-3xl">
    <h1 class="text-3xl font-semibold mb-6 text-gray-900">Thông báo</h1>

    <div class="flex space-x-4 mb-4">
        <a href="{{ route('notifications', ['id' =>  session('id'), 'status' => 'all']) }}" class="px-4 py-2 {{ $status == 'all' ? 'bg-blue-500' : 'bg-blue-300' }} text-white rounded hover:bg-blue-600">Tất cả</a>
        <a href="{{ route('notifications', ['id' =>  session('id'), 'status' => 'unread']) }}" class="px-4 py-2 {{ $status == 'unread' ? 'bg-gray-500' : 'bg-gray-300' }} text-white rounded hover:bg-gray-600">Chưa đọc</a>
    </div>
    @if($noUnreadNotifications)
        <div class="bg-yellow-100 text-yellow-800 text-center p-4 rounded-lg shadow-lg overflow-auto">
            Không có thông báo chưa đọc.
        </div>
    @elseif($notifications->isEmpty())
        <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow-lg">
            No pending friend requests.
        </div>
    @else
    <ul id="notification-list" class="space-y-6 overflow-hidden">
        @foreach($notifications as $notification)
            @php
                $data = json_decode($notification->data);
                $isRead = $notification->read_at ? true : false;
            @endphp
            <li class="flex items-center p-4 {{ $isRead ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded-lg shadow-md hover:bg-gray-50 transition duration-300">
                <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="flex items-center w-full">
                    @if(isset($data->avatar) && $data->avatar)
                        <img src="{{ asset('storage/' . $data->avatar) }}" alt="Avatar" class="w-14 h-14 rounded-full border-2 border-gray-200 mr-4">
                    @else
                        <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-14 h-14 rounded-full border-2 border-gray-200 mr-4">
                    @endif
                    <div class="flex-1">
                        <div class="text-gray-800 text-lg mb-1">
                            {{ $data->message }}
                        </div>
                        <div class="flex">
                            <span class="text-base text-gray-500 ">  {{ $notification->created_at->locale('vi')->diffForHumans() }}</span>
                            @if(!$isRead)
                                <span class="ml-4 text-xs text-red-600 font-semibold bg-red-100 px-2 py-1 rounded-full">Chưa đọc</span>
                            @endif
                        </div>
                    </div>
                </a>
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
