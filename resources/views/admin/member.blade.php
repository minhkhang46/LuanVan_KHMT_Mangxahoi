@extends('layouts.app')

@section('content')

<div class=" mt-5 ml-4 text-left">
    @if(session('possition') != 0)
        <a href="{{ route('requested.groups') }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
            ← Quay lại 
        </a>
    @else
        <a href="{{ route('groups.show', ['id' => $group->id]) }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
            ← Quay lại nhóm 
        </a>
    @endif
</div>
@if (session('success') && session('possition') != 0)
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow-md text-center">
            {{ session('success') }}
        </div>
    @endif
<div class="container mx-auto mt-10">
    <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Thành viên của nhóm: {{ $group->name }}</h1>

    @if($members->isEmpty()) 
        <div class="text-center p-6 bg-gray-100 border border-gray-300 rounded-lg">
            <p class="text-gray-600 font-semibold">Chưa có thành viên trong nhóm này.</p>
        </div>
    @else
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Quản trị viên</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8 mb-8">
            @foreach($members as $member)
                @if($member->possition == 1)
                    <div class="bg-white shadow-lg rounded-lg p-6 flex flex-col items-center text-center hover:shadow-xl transition duration-300 transform hover:scale-105">
                        <img src="{{ asset('storage/' . ($member->avatar ?? 'default-avatar.png')) }}" 
                             alt="{{ $member->name }}" 
                             class="w-32 h-32 rounded-full mb-4 object-cover">
                        
                        <h2 class="text-xl font-semibold text-gray-900">{{ $member->name }}</h2>
                        <span class="mt-2 inline-block px-4 py-2 text-sm font-medium rounded-lg bg-green-100 text-green-700">
                            Admin
                        </span>
                    </div>
                @endif
            @endforeach
        </div>

        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Thành viên</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8">
            @foreach($members as $member)
                        
                @if($member->possition != 1) 
                    <div class="bg-white shadow-lg rounded-lg p-4 flex flex-col items-center text-center hover:shadow-xl transition duration-300 transform hover:scale-105">
                        <a href="{{ $member->id === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $member->id]) }}">
                            <img src="{{ asset('storage/' . ($member->avatar ?? 'default-avatar.png')) }}" 
                                 alt="{{ $member->name }}" 
                                 class="w-32 h-32 rounded-full mb-4 object-cover">
                            @if($member->id === session('id'))
                                <h2 class="text-xl font-semibold text-gray-900">Bạn</h2>
                            @else
                                <h2 class="text-xl font-semibold text-gray-900">{{ $member->name }}</h2>
                            @endif
                            <span class="mt-2 inline-block px-4 py-2 text-sm font-medium rounded-lg bg-blue-100 text-blue-700">
                                Thành viên
                            </span>
                        </a>

         
                        @if(session('possition') != 0)
                            <div class="flex space-x-4 ">
                                <button 
                                    type="button" 
                                    onclick="openDeleteModal({{ $member->id }})" 
                                    class="bg-red-600 mt-2 inline-block px-4 py-2 text-base font-medium rounded-lg hover:bg-red-700 text-white">Xóa
                                </button>
                                <a href="{{ route('chat', ['receiverId' => $member->id]) }}" class="mt-2 inline-block px-4 py-2 text-base font-medium rounded-lg bg-blue-500 text-white">Nhắn tin</a>
                            </div>
                        @endif
                        <div class="flex space-x-4 ">
                            @if($member->id != session('id') && session('possition') == 0)
                                <a href="{{ route('chat', ['receiverId' => $member->id]) }}" class="mt-2 inline-block px-4 py-2 text-base font-medium rounded-lg bg-blue-500 text-white">Nhắn tin</a>
                            @endif
                            @if(session('possition') == 0)
                                @if($member->friend_status === 1)
                                
                                    <span class="mt-2 inline-block px-2 py-2 text-base font-medium rounded-lg bg-green-100 text-green-700">
                                        Bạn bè
                                    </span>
                                @elseif($member->friend_status === 0)

                                    <span class="mt-2 inline-block px-4 py-2 text-base font-medium rounded-lg bg-yellow-100 text-yellow-700">
                                        Yêu cầu đã gửi
                                    </span>
                                @else 
                                    @if($member->id != session('id'))
                                        <form action="{{ route('sendFriendRequest', $member->id) }}" method="POST" class="mt-4">
                                            @csrf
                                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                Thêm bạn bè
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
                <div id="deleteModal-{{ $member->id }}" class="fixed inset-0 bg-gray-500 bg-opacity-50 hidden flex items-center justify-center">
                    <div class="bg-white p-6 rounded shadow-lg w-1/3">
                        <h2 class="text-xl font-bold mb-4 text-gray-700">Xác nhận xóa nhóm</h2>
                                
                             <!-- Phần nhập lý do xóa -->
                            <form method="POST" action="{{ route('group.removeMember', ['groupId' => $group->id, 'userId' => $member->id ]) }}">
                                @csrf
                                @method('DELETE')
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Vui lòng nhập lý do xóa bài viết:</p>
                                    <textarea 
                                        name="reason" 
                                        id="reason-{{ $member->id }}" 
                                        placeholder="Nhập lý do xóa..." 
                                        required 
                                        class="w-full border rounded p-2 mb-4"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" onclick="closeDeleteModal({{ $member->id }})" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">Hủy</button>
                                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Xác nhận xóa</button>
                                    </div>
                            </form>


                              
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<script>
// Mở modal
function openDeleteModal(memberId) {
    document.getElementById('deleteModal-' + memberId).classList.remove('hidden');
    document.getElementById('confirmDelete-' + memberId).disabled = true; // Vô hiệu hóa nút xác nhận ban đầu
}

// Đóng modal
function closeDeleteModal(memberId) {
    document.getElementById('deleteModal-' + memberId).classList.add('hidden');
}

// Kiểm tra lý do nhập vào trước khi cho phép xác nhận xóa
document.querySelectorAll('textarea[name="reason"]').forEach((textarea) => {
    textarea.addEventListener('input', function () {
        const submitButton = document.querySelector(`#confirmDelete-${this.id.replace('reason-', '')}`);
        if (this.value.trim() !== '') {
            submitButton.disabled = false;  // Kích hoạt nút xác nhận nếu có lý do
        } else {
            submitButton.disabled = true;  // Vô hiệu hóa nếu không có lý do
        }
    });
});

</script>
@endsection
