@extends('layouts.app')

@section('content')
<div class=" mt-4 ml-4 text-left">
    <a href="{{ route('dashboard', ['id' => session('id')]) }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
        ← Quay lại 
    </a>
</div>
<div class="container mx-auto mt-10">
    @if (session('message'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow-md text-center">
                {{ session('message') }}
            </div>
        @endif


    <h1 class="text-3xl font-semibold mb-8 text-center text-gray-800">Quản Lý Chủ Đề</h1>

    <!-- Nút Mở Modal -->
    <div class="flex justify-center mb-6">
        <button id="openModal" 
                class="bg-indigo-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-600 transition duration-300">
            Tạo Chủ Đề Mới
        </button>
    </div>

    <div class="">
        <h2 class="text-2xl font-semibold text-black text-center mb-6 px-6 pt-6">Danh Sách Chủ Đề</h2>
        <div class="bg-white shadow-lg rounded-lg">
       
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                        <th class="py-3 px-4 border-b font-semibold text-center">Chủ đề</th>
                        <th class="py-3 px-4 border-b font-semibold text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($topic as $t)
                    <tr class="text-gray-700 hover:bg-gray-1000">
                      
                        <td class="py-3 px-4 border-b text-center">{{$t->topic}}</td>
                        <td  class="py-3 px-4 border-b text-center">
                            <form action="" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
      

        <button id="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
            &times;
        </button>
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Tạo Chủ Đề Mới</h2>
        <form action=" {{route('topic')}}" method="POST" class="space-y-6">
            @csrf
            <!-- Input Tiêu Đề -->
            <div>
                <label for="topic" class="block text-sm font-medium">Tiêu đề</label>
                <input 
                    type="text" 
                    id="topic" 
                    name="topic" 
                    class="w-full mt-2 px-4 py-2 border-gray-300 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                    placeholder="Nhập chủ đề">
            </div>
            <!-- Button Tạo -->
            <button type="submit" 
                    class="w-full bg-indigo-500 text-white font-bold py-3 rounded-lg hover:bg-indigo-600 transition duration-300">
                Tạo Chủ Đề
            </button>
        </form>
    </div>
</div>

<script>
    // JavaScript để điều khiển modal
    document.getElementById('openModal').addEventListener('click', function() {
        document.getElementById('modal').classList.remove('hidden');
    });

    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('modal').classList.add('hidden');
    });
</script>
@endsection
