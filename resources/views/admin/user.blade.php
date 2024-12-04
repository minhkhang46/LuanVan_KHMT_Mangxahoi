<!-- resources/views/users/index.blade.php -->
@extends('layouts.app')
@section('title', 'Quản lý người dùng')
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
                    <th class="py-3 px-4 border-b font-semibold text-center">ID</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Avatar</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Tên</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Email</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Số điện thoại</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Mô tả</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">CV</th>
                    <th class="py-3 px-4 border-b font-semibold text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_ma as $user)
                    @if($user->possition != 1)
                    <tr class="text-gray-700 hover:bg-gray-100">
                        <td class="py-3 px-4 border-b text-center">{{ $user->id }}</td>
                        <td class="py-3 px-4 border-b text-center">  <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-12 h-12 ml-8 rounded-full  bg-white cursor-pointer" id="userAvatar"></td>
                        <td class="py-3 px-4 border-b text-center">{{ $user->name }}</td>
                        <td class="py-3 px-4 border-b text-center">{{ $user->email }}</td>
                        <td class="py-3 px-4 border-b text-center">{{ $user->phone }}</td>
                        <td class="py-3 px-4 border-b w-96 text-center ">{{ $user->description }}</td>
                        <td class="py-3 px-4 border-b text-center">
                            @if($user->cv)
                                <a href="{{ asset('storage/cv/' . $user->cv) }}" download class="text-blue-500 hover:underline">
                                    Tải xuống CV
                                </a>
                            @else
                                <span class="text-gray-500">Không có CV</span>
                            @endif
                        </td>

                        <td class="py-3 px-4 border-b text-center">
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
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
