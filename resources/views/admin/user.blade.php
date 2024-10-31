<!-- resources/views/users/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class=" mx-20 py-8">
    <h2 class="text-3xl font-semibold mb-8 text-center text-gray-800">Quản Lý Tài Khoản Người Dùng</h2>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6 shadow-md text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg shadow-lg">
        <table class="min-w-full w-full bg-white border border-gray-300">
            <thead class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                <tr>
                    <th class="py-3 px-4 border-b font-semibold text-left">ID</th>
                    <th class="py-3 px-4 border-b font-semibold text-left">Avatar</th>
                    <th class="py-3 px-4 border-b font-semibold text-left">Tên</th>
                    <th class="py-3 px-4 border-b font-semibold text-left">Email</th>
                    <th class="py-3 px-4 border-b font-semibold text-left">Số điện thoại</th>
                    <th class="py-3 px-4 border-b font-semibold text-left">Mô tả</th>
                    <th class="py-3 px-4 border-b font-semibold text-left">CV</th>
                    <th class="py-3 px-4 border-b font-semibold text-left">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_ma as $user)
                    <tr class="text-gray-700 hover:bg-gray-100">
                        <td class="py-3 px-4 border-b">{{ $user->id }}</td>
                        <td class="py-3 px-4 border-b">  <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-12 h-12 mx-2 rounded-full  bg-white cursor-pointer" id="userAvatar"></td>
                        <td class="py-3 px-4 border-b">{{ $user->name }}</td>
                        <td class="py-3 px-4 border-b">{{ $user->email }}</td>
                        <td class="py-3 px-4 border-b">{{ $user->phone }}</td>
                        <td class="py-3 px-4 border-b w-96 ">{{ $user->description }}</td>
                        <td class="py-3 px-4 border-b">
                            @if($user->cv)
                                <a href="{{ asset('storage/cv/' . $user->cv) }}" download class="text-blue-500 hover:underline">
                                    Tải xuống CV
                                </a>
                            @else
                                <span class="text-gray-500">Không có CV</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 border-b">
                            @if ($user->status == 0)
                                <!-- Hiển thị nút khóa tài khoản nếu tài khoản đang mở -->
                                <form action="{{ route('users.toggleStatus', ['id' => $user->id, 'status' => 'lock']) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:opacity-80 transition duration-200">Khóa</button>
                                </form>
                            @else
                                <!-- Hiển thị nút mở khóa tài khoản nếu tài khoản đang khóa -->
                                <form action="{{ route('users.toggleStatus', ['id' => $user->id, 'status' => 'unlock']) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:opacity-80 transition duration-200">Mở khóa</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
