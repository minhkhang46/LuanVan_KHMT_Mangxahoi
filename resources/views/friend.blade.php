@extends('layouts.app')
@section('title', "Danh sách bạn bè $user->name")
@section('content')
<div class="mt-5 ml-4 text-left">
    @if(session('id') == $user->id)  <!-- assuming you have the $user variable -->
        <a href="{{ route('profile', ['id' => session('id')]) }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
            ← Quay lại trang cá nhân 
        </a>
    @else
        <a href="{{ route('profiles', ['id' => $user->id]) }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
            ← Quay lại trang cá nhân của {{$user->name}} 
        </a>
    @endif
</div>

<div class="container mx-auto mt-8 mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Danh sách bạn bè của {{ $user->name }}</h1>

    {{-- Hiển thị bạn bè chung nếu có --}}
    @if(session('id') != $user->id)
        @if($mutualFriends->isNotEmpty() )
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Bạn bè chung</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach ($mutualFriends as $mutualFriendId)
                        @php
                            $mutualFriend = $friendInfos->firstWhere('id', $mutualFriendId);
                        @endphp
                        @if ($mutualFriend)
                            <div class="bg-white shadow-lg rounded-lg p-6 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl duration-300 ease-in-out">
                                <a href="{{ route('profile', ['id' => $mutualFriend->id]) }}">
                                    <img src="{{ asset('storage/' . ($mutualFriend->avatar ?? 'default-avatar.png')) }}" 
                                        alt="{{ $mutualFriend->name }}" 
                                        class="w-32 h-32 rounded-full mb-4 object-cover border-4 border-green-500 transition duration-300 ease-in-out hover:scale-105">
                                </a>
                                <p class="text-xl font-semibold text-gray-800">{{ $mutualFriend->name }}</p>

                                {{-- Kiểm tra trạng thái bạn bè --}}
                                @if($mutualFriends->contains($mutualFriend->id))
                                    <p class="mt-2 inline-block px-4 py-2 text-base font-medium rounded-lg bg-green-100 text-green-700">
                                        Bạn bè
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <p class="bg-green-100 text-center text-green-800 p-4 rounded-lg shadow-">Không có bạn bè chung.</p>
        @endif
        <div class="border border-gray-300 my-4"></div> 
    @endif

    {{-- Hiển thị danh sách bạn bè --}}
    @if($friendInfos->isNotEmpty())
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Danh sách bạn bè</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($friendInfos as $friend)
                <div class="bg-white shadow-lg rounded-lg p-6 flex flex-col items-center transition transform hover:scale-105 hover:shadow-2xl duration-300 ease-in-out">
                    <a href="{{ session('id') == $friend->id ? route('profile',  ['id' => session('id')]) : route('profiles', ['id' => $friend->id]) }}">
                        <img src="{{ asset('storage/' . ($friend->avatar ?? 'default-avatar.png')) }}" 
                             alt="{{ $friend->name }}" 
                             class="w-32 h-32 rounded-full mb-4 object-cover border-4 border-gray-400">
                    </a>
                    <p class="text-xl font-semibold text-gray-800">{{ $friend->name }}</p>
                    @if($friend->id === session('id'))
                        <p class="mt-2 inline-block px-4 py-2 text-base font-medium rounded-lg bg-gray-100 text-gray-700">
                            Bạn
                        </p>
                    @else
                        {{-- Kiểm tra trạng thái bạn bè --}}
                        @if($mutualFriends->contains($friend->id))
                            <div class="mt-2 flex items-center space-x-4">
                                <p class="inline-block px-4 py-2 text-base font-medium rounded-lg bg-green-100 text-green-700">
                                    Bạn bè
                                </p>
                                {{-- Nút nhắn tin --}}
                                <a href="{{ route('chat', ['id' => $friend->id]) }}" 
                                   class="inline-block px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-lg hover:bg-blue-700">
                                    Nhắn tin
                                </a>
                            </div>
                        @elseif($isRequestSent[$friend->id] ?? false)
                            <div class="mt-2 flex items-center space-x-4">
                                <p class="inline-block px-4 py-2 text-base font-medium rounded-lg bg-yellow-100 text-yellow-700">
                                    Đã gửi yêu cầu
                                </p>
                                {{-- Nút nhắn tin --}}
                                <a href="{{ route('chat', ['id' => $friend->id]) }}" 
                                   class="inline-block px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-lg hover:bg-blue-700">
                                    Nhắn tin
                                </a>
                            </div>
                        @else
                            <form action="{{ route('sendFriendRequest', ['id' => $friend->id]) }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Thêm bạn bè
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="bg-green-100 text-center text-green-800 p-4 rounded-lg shadow-">Chưa có bạn bè.</p>
    @endif
</div>
@endsection
