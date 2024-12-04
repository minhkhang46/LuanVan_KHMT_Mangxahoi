@extends('layouts.app')
@section('title', 'Quản lý bài viết')
@section('content')
<div class="mx-20 py-8">
    <h2 class="text-3xl font-semibold mb-8 text-center text-gray-800">Quản Lý Bài Viết Người Dùng</h2>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow-md text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg shadow-lg">
        <table class="min-w-full w-full bg-white border border-gray-300">
            <thead class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                <tr>
                    <th class="py-3 px-4 border-b font-semibold text-center">ID</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Người đăng</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Tiêu đề</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Nội dung</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Hình ảnh</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Tệp</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Ngày đăng</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr class="text-gray-700 hover:bg-gray-100">
                        <td class="py-3 px-4 border-b text-center">{{ $post->id }}</td>
                        <td class="py-3 px-4 border-b text-center">
                            {{ $userNames[$post->id_nd] ?? 'Ẩn danh' }}
                        </td>

                        <td class="py-3 px-4 border-b text-center">{{ $post->topic ?? 'Không có chủ đề' }}</td>
                        <td class="py-3 px-4 border-b w-96 text-center">{{ $post->noidung}}</td>
                        <td class="py-3 px-4 border-b text-center">
                            @if ($post->images && $post->images !== 'default/image.png') 

                                <button 
                                    class="focus:outline-none"
                                    onclick="document.getElementById('modal-{{ $post->id }}').classList.remove('hidden')">
                                    <!-- <img src="{{ asset('storage/' . $post->images) }}" 
                                        alt="Image" 
                                        class="w-16 h-16 object-cover rounded shadow hover:opacity-75 transition"> -->
                                        <span class="text-blue-500 hover:underline">Xem hình ảnh</span>
                                </button>

                                <!-- Modal hiển thị ảnh -->
                                <div id="modal-{{ $post->id }}" 
                                    class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
                                    <div class="relative">
                                        <!-- Nút đóng modal -->
                                        <button 
                                            class="absolute top-2 right-2 text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded"
                                            onclick="document.getElementById('modal-{{ $post->id }}').classList.add('hidden')">
                                            Đóng
                                        </button>
                                        <!-- Hình ảnh lớn -->
                                        <img src="{{ asset('storage/' . $post->images) }}" 
                                            alt="Image" 
                                            class="max-w-7xl max-h-96 rounded shadow-lg">
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-500 italic">Không có ảnh</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 border-b text-center">
                            @if($post->files)
                                <a href="{{ asset('storage/file/' . $post->file) }}" download class="text-blue-500 hover:underline">
                                    Tải xuống file
                                </a>
                            @else
                                <span class="text-gray-500">Không có file</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b text-center">
                        <p>
                               
                        {{ $post->created_at->format('j/n/Y') }}

                               
                            </p>
                        </td>
                       
                      <!-- Nút Xóa -->
<td class="py-3 px-4 border-b text-center">
    <button 
        type="button" 
        onclick="openDeleteModal({{ $post->id }})" 
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
        Xóa
    </button>
</td>

<!-- Modal Xóa -->
<div id="deleteModal-{{ $post->id }}" class="fixed inset-0 bg-gray-500 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-1/3">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Xác nhận xóa bài đăng</h2>
        
        <!-- Phần nhập lý do xóa -->
        <form method="POST" action="{{ route('admin.posts.delete', $post->id) }}">
            @csrf
            @method('DELETE')
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Vui lòng nhập lý do xóa bài viết:</p>
                <textarea 
                    name="reason" 
                    id="reason-{{ $post->id }}" 
                    placeholder="Nhập lý do xóa..." 
                    required 
                    class="w-full border rounded p-2 mb-4"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeDeleteModal({{ $post->id }})" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">Hủy</button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Xác nhận xóa</button>
            </div>
        </form>


        <!-- Phần xác nhận xóa -->
        <!-- <div class="flex justify-end">
            <button 
                type="button" 
                onclick="closeDeleteModal({{ $post->id }})" 
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">
                Hủy
            </button>
            <form id="deleteForm-{{ $post->id }}" method="POST" action="{{ route('admin.posts.delete', $post->id) }}">
                @csrf
                @method('DELETE')
                <button 
                    type="submit" 
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                    id="confirmDelete-{{ $post->id }}"
                    disabled>
                    Xác nhận xóa
                </button>
            </form>
        </div> -->
    </div>
</div>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
// Mở modal
function openDeleteModal(postId) {
    document.getElementById('deleteModal-' + postId).classList.remove('hidden');
    document.getElementById('confirmDelete-' + postId).disabled = true; // Vô hiệu hóa nút xác nhận ban đầu
}

// Đóng modal
function closeDeleteModal(postId) {
    document.getElementById('deleteModal-' + postId).classList.add('hidden');
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
