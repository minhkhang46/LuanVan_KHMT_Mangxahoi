@extends('layouts.app')
@section('title', 'Trang chủ')
@section('content')
<div class="container mx-auto mt-10">
    <!-- Tiêu đề -->
    <h1 class="text-3xl font-semibold mb-8 text-center text-gray-800">Trang Chủ Quản Trị Viên</h1>
    
    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Tổng bài viết -->
        <div class="bg-gradient-to-r from-blue-400 to-blue-600 text-white p-4 rounded-lg shadow-md text-center">
            <h2 class="text-xl font-semibold">Tổng bài viết</h2>
            <p class="text-4xl font-bold mt-2">{{ $postCount }}</p>
            <div class="mt-4">
                <p class="text-base font-medium">Người dùng: <span class="font-bold">{{ $userPostCount }}</span></p>
                <p class="text-base font-medium">Nhóm: <span class="font-bold">{{ $groupPostCount }}</span></p>
            </div>
        </div>

        <!-- Tổng số người dùng -->
        <div class="bg-gradient-to-r from-green-400 to-green-600 text-white p-4 rounded-lg shadow-md text-center">
            <h2 class="text-xl font-semibold">Người dùng</h2>
            <p class="text-4xl font-bold mt-2">{{ $userCount }}</p>
        </div>

        <!-- Tổng số nhóm -->
        <div class="bg-gradient-to-r from-purple-400 to-purple-600 text-white p-4 rounded-lg shadow-md text-center">
            <h2 class="text-xl font-semibold">Nhóm</h2>
            <p class="text-4xl font-bold mt-2">{{ $groupCount }}</p>
        </div>
    </div>

    <!-- Liên kết quản lý -->
    <div class="mt-12">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Quản lý</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Quản lý bài viết -->
            <a href="{{ route('post_admin') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white text-center py-4 rounded-lg shadow-md text-lg font-semibold">
                Quản lý bài viết người dùng
            </a>
            <!-- Quản lý người dùng -->
            <a href="{{ route('user') }}" 
               class="bg-green-500 hover:bg-green-600 text-white text-center py-4 rounded-lg shadow-md text-lg font-semibold">
                Quản lý người dùng
            </a>
            <!-- Quản lý nhóm -->
            <a href="{{ route('group.admin') }}" 
               class="bg-purple-500 hover:bg-purple-600 text-white text-center py-4 rounded-lg shadow-md text-lg font-semibold">
                Quản lý nhóm
            </a>
            <!-- Tạo Chủ Đề -->
            <a href="{{ route('topic_admin') }}" 
               class="bg-indigo-500 hover:bg-indigo-600 text-white text-center py-4 rounded-lg shadow-md text-lg font-semibold">
                Quản lý chủ đề
            </a>
        </div>
    </div>
</div>
@endsection
