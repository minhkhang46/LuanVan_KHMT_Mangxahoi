@extends('layouts.app')

@section('content')
<div class=" mt-4 ml-4 text-left">
    <a href="{{ route('group') }}" class="inline-block text-blue-600 hover:underline text-lg font-medium">
        ← Quay lại danh sách nhóm
    </a>
</div>
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
                                <button class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 show-members" data-group-id="{{ $group->id }}">
                                    Hiển thị thành viên
                                </button>
                                <div class="members-list hidden mt-2">
                                    <h3 class="text-sm font-semibold">Thành viên:</h3>
                                    <ul class="list-disc list-inside text-gray-600">
                                        @foreach($group->members as $member)
                                            <li class="flex items-center mb-2">
                                                <img src="{{ asset('storage/' .$member->avatar) }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-full mr-2">
                                                <span class="text-lg">{{ $member->name }}</span> <!-- Hiển thị tên thành viên -->
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <div class="p-4 bg-gray-100">
                            @if($group->is_approved)
                                <button class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Xóa nhóm
                                </button>
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
   
@endsection
