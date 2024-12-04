@extends('layouts.app')
@section('title', 'Quản lý nhóm')
@section('content')
<div class="container mx-auto mt-12">
    <div class="text-center">
        <h1 class="text-3xl font-semibold mb-8 text-center text-gray-800">Quản lý Nhóm</h1>
        <!-- <p class="text-gray-600 mb-12 text-xl">Dễ dàng quản lý thông tin nhóm và bài đăng của các nhóm bạn quản lý.</p> -->
    </div>
    <div class="flex justify-center gap-12">
        <!-- Nút Thông tin nhóm -->
        <a href="{{ route('requested.groups') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg shadow-lg flex items-center space-x-3 transition duration-300 transform hover:scale-105">
            <img src="/luanvan_tn/public/image/infor.png" alt="Icon Bảng tin" class="w-6 h-6">
            <span class="text-lg font-semibold">Thông tin nhóm</span>
        </a>

        <!-- Nút Quản lý bài đăng -->
        <a href="{{ route('post_group_admin') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg shadow-lg flex items-center space-x-3 transition duration-300 transform hover:scale-105">
            <img src="/luanvan_tn/public/image/post_group.png" alt="Icon Bảng tin" class="w-6 h-6">
            <span class="text-lg font-semibold">Quản lý bài đăng nhóm</span>
        </a>

      <!-- Nút kích hoạt modal -->
        <a href="#" 
        class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg shadow-lg flex items-center space-x-3 transition duration-300 transform hover:scale-105"
        onclick="toggleModal('createGroupModal')">
            <img src="/luanvan_tn/public/image/add.png" alt="Icon Tạo nhóm" class="w-6 h-6">
            <span class="text-lg font-semibold">Tạo nhóm</span>
        </a>

        <!-- Modal -->
        <div id="createGroupModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                <!-- Close button -->
                <button class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" onclick="toggleModal('createGroupModal')">
                    &times;
                </button>

                <!-- Modal content -->
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">Tạo Nhóm</h2>
                <form action="{{ route('groupcreate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <div class="mt-4">
                            <img id="image-preview" src="" alt="Xem trước ảnh" class="w-28 h-28 object-cover rounded-full hidden">
                        </div>
                        <label for="group-image" class="block text-lg font-medium text-gray-700">Ảnh nhóm</label>
                        <input type="file" name="image" id="group-image" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input" accept="image/*" onchange="previewImage(event)">
                        
                        <!-- Nơi hiển thị hình ảnh xem trước -->
                        
                    </div>   
                    <div class="mb-4">
                        <label for="group_name" class="block text-gray-700 font-semibold">Tên nhóm</label>
                        <input type="text" name="name" id="group_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input" required>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 font-semibold">Mô tả nhóm</label>
                        <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input" rows="4"></textarea>
                    </div>
                    <input type="text" name="is_approved" id="is_approved" hidden value="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                    <input type="text" name="status" id="status" hidden value="public" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                    <div class="flex justify-end">
                        <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 mr-2" onclick="toggleModal('createGroupModal')">
                            Hủy
                        </button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            Tạo
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function() {
            const output = document.getElementById('image-preview');
            output.src = reader.result;
            output.classList.remove('hidden'); // Hiển thị ảnh khi đã tải lên
        }
        
        if (file) {
            reader.readAsDataURL(file); // Đọc dữ liệu ảnh và chuyển thành URL
        }
    }
</script>
<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>

@endsection
