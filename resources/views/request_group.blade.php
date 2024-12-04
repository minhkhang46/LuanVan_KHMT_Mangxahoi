@extends('layouts.app')
@section('title', 'Danh sách nhóm')
@section('content')
<div class=" mt-4 ml-4 text-left">
    <a href="{{ route('group.admin') }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
        ← Quay lại 
    </a>
</div>
@if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow-md text-center">
            {{ session('success') }}
        </div>
    @endif
    <div class="container mx-auto mt-10 mb-20">
        <h1 class="text-3xl font-semibold text-center mb-6">Tất cả các nhóm</h1>

        @if($allGroups->isEmpty())
            <p class="text-gray-500 text-center">Không có nhóm nào.</p>
        @else
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($allGroups as $group)
              
                    <li class="border border-gray-200 rounded-lg shadow-lg overflow-hidden mb-2">
                        <a href="{{ route('groups.show', $group->id) }}">
                            <div class="bg-blue-500 text-white p-4">
                                <h2 class="text-lg font-semibold">{{ $group->name }}</h2>
                            </div>
                        </a>
                        <div class="p-4">
                            <p class="text-gray-700 mb-2">{{ $group->description }}</p>
                            <p class="text-lg text-gray-500">
                                Trạng thái yêu cầu: 
                                @if($group->is_approved == 1)
                                    <span class="text-blue-500 font-medium">Yêu cầu đã được chấp nhận</span>
                                @elseif($group->is_approved == 2)
                                    <span class="text-green-500 font-medium">Yêu cầu đã không được chấp nhận</span>
                                @else
                                    <span class="text-red-500 font-medium">Yêu cầu đang chờ xử lý</span>
                                @endif
                            </p>
                            <div class="mt-2">
                                <!-- <button class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 show-members" data-group-id="{{ $group->id }}">
                                    Hiển thị thành viên
                                </button>
                                <div class="members-list hidden mt-2">
                                    <h3 class="text-sm font-semibold">Thành viên:</h3>
                                    <ul class="list-disc list-inside text-gray-600">
                                        @foreach($group->members as $member)
                                            <li class="flex items-center mb-2">
                                                <img src="{{ asset('storage/' .$member->avatar) }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-full mr-2">
                                                <span class="text-lg">{{ $member->name }}</span> ->
                                            </li>
                                        @endforeach
                                    </ul>
                                </div> -->
                                <a href="{{ route('group.members', $group->id) }}"
                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        Hiển thị thành viên
                                </a>

                            </div>

                        </div>
                        <div class="p-4 bg-gray-100">
                            @if($group->is_approved)
                            <button 
                                type="button" 
                                onclick="openDeleteModal({{ $group->id }})" 
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                Xóa
                            </button>
                            <div id="deleteModal-{{ $group->id }}" class="fixed inset-0 bg-gray-500 bg-opacity-50 hidden flex items-center justify-center">
                            <div class="bg-white p-6 rounded shadow-lg w-1/3">
                                <h2 class="text-xl font-bold mb-4 text-gray-700">Xác nhận xóa nhóm</h2>
                                
                                <!-- Phần nhập lý do xóa -->
                                <form method="POST" action="{{ route('group.deleteGroup', $group->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Vui lòng nhập lý do xóa bài viết:</p>
                                        <textarea 
                                            name="reason" 
                                            id="reason-{{ $group->id }}" 
                                            placeholder="Nhập lý do xóa..." 
                                            required 
                                            class="w-full border rounded p-2 mb-4"></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="button" onclick="closeDeleteModal({{ $group->id }})" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">Hủy</button>
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Xác nhận xóa</button>
                                    </div>
                                </form>


                              
                            </div>
                            @else
                            <div class="flex space-x-2">
                                <form action="{{ route('group.accept', $group->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        Chấp nhận
                                    </button>
                                </form>
                                <form action="{{ route('group.decline', $group->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        Từ chối
                                    </button>
                                </form>
                            </div>

                            @endif
                        </div>
                    </li>
                
                @endforeach
                
            </ul>
        @endif
    </div>


        <script>
            document.querySelectorAll('.show-members').forEach(button => {
                button.addEventListener('click', function() {
                    const membersList = this.nextElementSibling;
                    membersList.classList.toggle('hidden');
                });
            });
        </script>
   <script>
// Mở modal
function openDeleteModal(groupId) {
    document.getElementById('deleteModal-' + groupId).classList.remove('hidden');
    document.getElementById('confirmDelete-' + groupId).disabled = true; // Vô hiệu hóa nút xác nhận ban đầu
}

// Đóng modal
function closeDeleteModal(groupId) {
    document.getElementById('deleteModal-' + groupId).classList.add('hidden');
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
