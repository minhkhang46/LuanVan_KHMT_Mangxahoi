@extends('layouts.app')

@section('title', "{$keyword} - Kết quả tìm kiếm")

@section('content')
<style>
    .modal {
    display: none; /* Modal bị ẩn mặc định */
}

.modal.show {
    display: flex; /* Modal hiển thị khi thêm lớp `show` */
}
</style>
<div class="bg-gray-100 ">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 ">
            <!-- Bộ lọc -->
            <div class="md:col-span-1 bg-white rounded-lg  shadow-md p-6" style="  height: 21rem ">
                <h1 class="text-3xl font-semibold text-gray-900 mb-3">Kết quả tìm kiếm</h1>
                <div class="border border-gray-300 mb-4"></div>
                <h2 class="text-2xl font-medium text-gray-900 mb-6">Bộ lọc</h2>
                <nav>
                    <ul class="space-y-4">
                        <li>
                            <a href="#" 
                            class="text-blue-600 hover:text-blue-800 text-2xl font-medium transition duration-300" 
                            onclick="toggleSections('all', {{ $users->isNotEmpty() ? 'true' : 'false' }}, {{ $groups->isNotEmpty() ? 'true' : 'false' }},  {{ $posts->isNotEmpty() ? 'true' : 'false' }});">
                            Tất cả
                            </a>
                        </li>


                        @if($users->isNotEmpty())
                            <li>
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-2xl font-medium transition duration-300" onclick="toggleSections('users')">Mọi người</a>
                            </li> 
                            <li>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-2xl font-medium transition duration-300" onclick="toggleSections('user-posts')">Bài viết</a>
                            </li>  
                        @else
                            <li>
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-2xl font-medium transition duration-300" onclick="toggleSections('user_group')">Mọi người</a>
                            </li>
                            <li>
                        
                        </li>
                        @endif
                        @if($posts->isNotEmpty() && !$users->isNotEmpty())
                            
                            <li>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-2xl font-medium transition duration-300" onclick="toggleSections('user-posts')">Bài viết</a>
                            </li>  
                        @endif
                        @if($groups->isNotEmpty()  && !$users->isNotEmpty() && !$posts->isNotEmpty())   
                            <li>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-2xl font-medium transition duration-300" onclick="toggleSections('post')">Bài viết</a>
                        </li>
                        @endif
                    
                        <li>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-2xl font-medium transition duration-300" onclick="toggleSections('groups')">Nhóm</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Kết quả Tìm kiếm -->
            <div class="md:col-span-2 p-6 " id="all"   >
           
                <div id="users" class="{{ ($posts->isNotEmpty() || $groups->isNotEmpty()) ? '' : 'mb-12' }}">
                
                @if($users->count())
                        @php
                            $currentUserId = session('id');
                            $currentUser = $users->firstWhere('id', $currentUserId);
                            $otherUsers = $users->where('id', '!=', $currentUserId);
                        @endphp

                        <!-- hiển thị người dùng hiện tại -->
                        @if($currentUser)
                            <div class="bg-white h-auto py-2 rounded-lg">
                                <ul>
                                    <a href="{{ route('profile', $currentUser->id) }}">
                                        <li class="flex items-center px-4 py-3 mt-4 mb-4">
                                            <img src="{{ asset('storage/' . $currentUser->avatar) }}" alt="{{ $currentUser->name }}" class="w-20 h-20 rounded-full mr-4 border-2 border-gray-200">
                                            <div class="flex-grow">
                                                <p class="text-xl font-semibold text-blue-600 hover:text-blue-800 transition duration-300">{{ $currentUser->name }}</p>
                                                <p class="text-gray-500 text-lg">{{ $currentUser->email }}</p>

                                                @php
                                                    $userGroupMembers = $users->where('id', $currentUser->id);
                                                @endphp

                                                @if($userGroupMembers->count())
                                                    <p class="text-gray-500 text-lg">Thành viên nhóm: 
                                                        @php
                                                            $groupNames = $userGroupMembers->pluck('group_names')->flatten()->toArray();
                                                            $firstPart = array_slice($groupNames, 0, 2);
                                                            $remainingPart = array_slice($groupNames, 2);
                                                        @endphp
                                                        {{ implode(', ', $firstPart) }}@if (count($remainingPart) > 0),@endif
                                                        @if (count($remainingPart) > 0)
                                                            <br>{{ implode(', ', $remainingPart) }}
                                                        @endif
                                                        {{ empty($groupNames) ? 'Chưa tham gia nhóm' : '' }}
                                                    </p>
                                                @else
                                                    <p class="text-gray-500 text-lg">Không tìm thấy nhóm nào.</p>
                                                @endif
                                            </div>
                                        </li>
                                        <div class="-mt-2 mb-3 px-4">
                                            <button onclick="window.location.href='{{ route('profile', $currentUser->id) }}'" class="bg-blue-200 -mt-2 w-full text-xl text-blue-900 font-bold py-2 px-3 rounded-lg hover:text-blue-800">
                                                Xem trang cá nhân
                                            </button>
                                        </div>
                                    </a>
                                </ul>
                            </div>
                        @endif

                        <!-- hiển thị người khác -->
                        <div class="bg-white mt-4 rounded-lg py-2 mb-4">
                            <h2 class="text-2xl font-bold mt-4 ml-4">{{ $currentUser ? 'Người khác' : 'Mọi người' }}</h2>
                            @foreach($otherUsers as $user)
                                <ul>
                                    <a href="{{ route('profiles', $user->id) }}">
                                        <li class="flex items-center px-4 py-3 mt-4 mb-4">
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full mr-4 border-2 border-gray-200">
                                            <div class="flex-grow">
                                                <p class="text-xl font-semibold text-blue-600 hover:text-blue-800 transition duration-300">{{ $user->name }}</p>
                                                <p class="text-gray-500 text-lg">{{ $user->email }}</p>

                                                @php
                                                    $userGroupMembers = $users->where('id', $user->id);
                                                @endphp

                                                @if($userGroupMembers->count())
                                                    <p class="text-gray-500 text-lg">Thành viên nhóm: 
                                                        @php
                                                            $groupNames = $userGroupMembers->pluck('group_names')->flatten()->toArray();
                                                            $firstPart = array_slice($groupNames, 0, 2);
                                                            $remainingPart = array_slice($groupNames, 2);
                                                        @endphp
                                                        {{ implode(', ', $firstPart) }}@if (count($remainingPart) > 0),@endif
                                                        @if (count($remainingPart) > 0)
                                                            <br>{{ implode(', ', $remainingPart) }}
                                                        @endif
                                                        {{ empty($groupNames) ? 'Chưa tham gia nhóm' : '' }}
                                                    </p>
                                                @else
                                                    <p class="text-gray-500 text-lg">Không tìm thấy nhóm nào.</p>
                                                @endif

                                                <div class="-mt-10 mr-4 flex justify-end items-start">
                                                    @php
                                                        $friendStatus = $friends[$user->id] ?? null;
                                                    @endphp

                                                    @if($friendStatus !== null)
                                                        @if($friendStatus == 1)
                                                            <button class="bg-green-500 text-white text-lg font-semibold py-1 px-3 rounded-lg">Bạn bè</button>
                                                        @elseif($friendStatus == 0)
                                                            <button class="bg-yellow-500 text-white text-lg font-semibold py-1 px-3 rounded-lg">Yêu cầu đã gửi</button>
                                                        @endif
                                                    @else
                                                        <form action="{{ route('sendFriendRequest', $user->id) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="bg-blue-500 text-lg text-white font-semibold py-1 px-3 rounded-lg">Thêm bạn bè</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    </a>
                                </ul>
                            @endforeach
                        </div>

                        <!-- hiển thị thành viên nhóm tìm kiếm -->
                        
                    @endif

                </div>
               
              
                <div id="user_group" class="mb-12" style="display: none;">
               
                    @if($nameMembers->count())
                        <div class="bg-white rounded-lg py-2">
                            <ul>
                                @foreach($membersWithGroups as $m)
                                    <li class="flex items-center px-4 py-3 mt-4 mb-4">
                                        <img src="{{ asset('storage/' . $m->avatar) }}" alt="{{ $m->name }}" class="w-20 h-20 rounded-full mr-4 border-2 border-gray-200">
                                        <div class="flex-grow">
                                            <p class="text-xl font-semibold text-blue-600 hover:text-blue-800 transition duration-300">{{ $m->name }}</p>
                                            <p class="text-gray-500 text-lg">{{ $m->email }}</p>
                                            @if($m->group_names)
                                    <p class="text-gray-500 text-lg">Nhóm: {{ implode(', ', $m->group_names) }}</p>
                                @else
                                    <p class="text-gray-500 text-lg">Chưa tham gia nhóm</p>
                                @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-gray-500">Không tìm thấy thành viên nào.</p>
                    @endif
              

                </div>

               
               
                <div id="user-posts"   >
                    @if($posts->count())
                        <ul class="">
                        @foreach ($posts as $p)
                            <!-- Hiển thị từng bài viết -->
                            <div class="bg-white p-10 rounded-lg mb-6 border border-gray-300 shadow-sm w-full ">
                                <div class="flex items-center mb-4">
                               
                                    @if($p->group_id )
                    
                                        <div class="relative">
                                            <a href="{{ route('groups.show', $p->group_id) }}">
                                                <img class="h-14 w-14  rounded-full" src="{{ asset('storage/' . $p->group_image) }}" alt="Group Image">
                                            </a>

                                            <!-- Hình người dùng ở góc của hình nhóm -->
                                            <a href="{{ $p->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $p->id_nd]) }}" class="flex items-center">
                                                <div class="absolute bottom-0 right-0 transform translate-x-1/4 translate-y-1/4">
                                                    <img class="h-10 w-10 rounded-full border-4 border-white " src="{{ asset('storage/' . $p->user_avatar) }}" alt="User Avatar">
                                                </div>
                                            </a>
                                        </div>

                                        <!-- Thông tin về bài đăng -->
                                        <div class="ml-3 flex-1">
                                            <a href="{{ route('groups.show', $p->group_id) }}">
                                                <p class="text-xl font-bold text-gray-700">Nhóm {{ $p->group_name }}</p>
                                            </a>
                                            <a href="{{ $p->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $p->id_nd]) }}" class="flex items-center">
                                                <div class="flex mt-1">
                                                    <p class="text-lg font-medium text-gray-700">{{ $p->user_name }}.</p>
                                                    <p class="text-lg text-gray-500 ml-4">{{ $p->created_at->locale('vi')->diffForHumans() }}</p>

                                                </div>
                                            </a>
                                        </div>

                                        
                                    @else

                                        <a href="{{ $p->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $p->id_nd]) }}" class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <img class="h-14 w-14 rounded-full " src="{{ asset('storage/' . $p->user_avatar) }}" alt="User Avatar">
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-xl font-semibold text-black">{{ $p->user_name }}</p>
                    
                                                <div class="flex">
                                                    <p class="text-lg text-gray-500">{{ $p->created_at->locale('vi')->diffForHumans() }}</p>
                                                    @if( $p->regime === 1)
                                                        <img id="imageIcon" src="/luanvan_tn/public/image/friend.png" alt="Image Icon" class="w-5 h-5 ml-3 mt-1">
                                                    @else
                                                        <img id="imageIcon" src="/luanvan_tn/public/image/publlic1.png" alt="Image Icon"class="w-5 h-5 ml-3 mt-1">
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @endif                
                                </div>
                                @if($p->topic)
                                    <p class="text-black font-semibold text-xl mb-2">Chủ đề {{ $p->topic }}</p>
                                @endif
                                    <p class="text-black  text-xl">{{ $p->noidung }}</p>
                                @if ($p->images && $p->images !== 'default/image.png')
                                    <img src="{{ asset('storage/' . $p->images) }}" alt="Post Image" class="w-full h-auto mt-4 border border-gray-300 rounded-md">
                                @endif
                                @if($p->files)
                                    @php
                                        $fileName = basename($p->files);
                                    @endphp
                                    <a href="{{ asset('storage/' . $p->files) }}" download class="text-blue-500 mt-4 inline-block hover:underline"> Download {{ $fileName }}</a>
                                @endif
                                <div class="post">

                                    <div class="text-base mt-2 flex justify-between">
                                        <span id="like-count-{{ $p->id }}" class="cursor-pointer" onclick="openModal({{ $p->id }})">
                                            @if($p->is_liked)
                                                @if($p->likes_count > 1)
                                                    <span class="like-count">Bạn và {{ $p->likes_count - 1 }} người khác</span>
                                                @else
                                                    <span class="like-count">Bạn</span>
                                                @endif
                                            @else
                                                <span class="like-count">
                                                    @if($p->likes_count > 0)
                                                        {{ $p->likes_count }}
                                                    @endif
                                                </span>
                                            @endif
                                        </span>
                                        @if($postsWithComments[$p->id]['comment_count'] > 0) 
                                            <span  onclick="toggleComments({{ $p->id }})">{{ $postsWithComments[$p->id]['comment_count'] }} bình luận</span> 
                                        @endif
                                    </div>

                                    <div id="likes-modal-{{ $p->id }}" class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center z-50">
                                        <div class="modal-content bg-white p-6 rounded-lg shadow-lg max-w-lg w-full relative">
                                            <button class="close-btn absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl"
                                                onclick="closeModal({{ $p->id }})">
                                                &times;
                                            </button>
                                            <h2 class="text-xl font-semibold mb-4 text-center">Danh sách người đã thích bài viết</h2>
                                            <ul class="list-disc pl-5 space-y-4">

                                                @if(isset($likes[$p->id]))
                                                @foreach($likes[$p->id] as $like)
                                                <li class="flex items-center">
                                                    @if($like->id == session('id'))
                                                    <a href="{{ route('profile', ['id' => session('id')]) }}" class="flex items-center">
                                                        <img src="{{ asset('storage/' . $like->avatar) }}" alt="Avatar"
                                                            class="w-12 h-12 rounded-full mr-3">
                                                        <span class="text-lg font-medium text-gray-800">Bạn</span>
                                                    </a>
                                                    @else
                                                    <a href="{{ route('profiles', ['id' => $like->id]) }}" class="flex items-center">
                                                        <img src="{{ asset('storage/' . $like->avatar) }}" alt="Avatar"
                                                            class="w-12 h-12 rounded-full mr-3">
                                                        <span class="text-lg font-medium text-gray-800">{{ $like->name }}</span>
                                                    </a>
                                                    @endif
                                                </li>
                                                @endforeach
                                                @endif

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="border border-gray-300 mt-2 mb-2"></div>
                                    <div class="flex justify-around items-center space-x-4 -mb-5">
                                        <form action="{{ route('like.toggle') }}" method="POST"
                                            class="like-form flex items-center justify-center w-full space-x-2">
                                            @csrf
                                            <input type="hidden" name="post_id" value="{{ $p->id }}">
                                            @if(!$p->is_liked)
                                                <button type="submit"
                                                    class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                                    <img id="imageIcon" src="/luanvan_tn/public/image/like.png" alt="Image Icon"
                                                        class="w-8 h-8">
                                                    <span class="mt-1 ml-2 font-semibold">Thích</span>
                                                </button>
                                            @else
                                                <button type="submit"
                                                    class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                                    <img id="imageIcon" src="/luanvan_tn/public/image/like_blue.png" alt="Image Icon"
                                                        class="w-8 h-8">
                                                    <span class="mt-1 ml-2 font-semibold text-blue-700">Thích</span>
                                                </button>
                                            @endif
                                        </form>
                                        <button id="commentButton-{{$p->id}}" class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                            <img id="imageIcon" src="/luanvan_tn/public/image/comment.png" alt="Image Icon" class="w-8 h-8">
                                            <span class="ml-2 font-semibold text-lg">Bình luận</span>
                                        </button>    
                                    </div>
                                    <div class="post mb-8 mt-8">
                                        <div id="comments-{{ $p->id }}" class="comments hidden overflow-y-auto max-h-96"> <!-- Thay đổi max-h-60 thành chiều cao bạn mong muốn -->
                                            @if(isset($postsWithComments[$p->id]['comments']) && count($postsWithComments[$p->id]['comments']) > 0)
                                                @foreach ($postsWithComments[$p->id]['comments'] as $comment)
                                                    @if (!$comment['parent_id']) <!-- Hiển thị bình luận gốc -->
                                                        @include('partials.comment', ['comment' => $comment, 'post' => $p, 'comments' => $postsWithComments[$p->id]['comments']])
                                                    @endif
                                                @endforeach
                                            @else
                                                <p>Chưa có bình luận nào.</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div id="commentForm-{{ $p->id }}" class="hidden mt-6">
                                        <form action="{{ route('comments.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="post_id" value="{{ $p->id }}">
                                            <input type="hidden" name="user_id" value="{{ session('id') }}">
                                            <div class="flex">
                                                <textarea name="content" rows="1" class="w-full p-2 mr-2 border border-gray-300 rounded-md" placeholder="Viết bình luận..." required></textarea>
                                            <button type="submit" ><img src="/luanvan_tn/public/image/send.png" alt="Icon Bảng tin" class="w-8 h-8 "> </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </ul>
    
                    @endif
                </div>

                <div id="groups" class="mb-4" >
                @if($g->count())
                        <div class="bg-white h-auto py-2 rounded-lg">
                            
                            @foreach($g as $g)
                                <div class="mt-4 px-4">
                                    <a href="{{ route('groups.show', $g->id) }}">
                                        <li class="flex items-center px-4 py-4 mt-4 mb-4">
                                            <img src="{{ asset('storage/' . $g->image) }}" alt="{{ $g->name }}" class="w-20 h-20 rounded-full mr-4 border-2 border-gray-200">
                                            <div class="flex-grow">
                                                <p class="text-xl font-semibold text-blue-600 hover:text-blue-800 transition duration-300"> Nhóm {{ $g->name }}</p>
                                                <p class="text-gray-500">    {{ $g->member_count }}  thành viên</p>
                                              
                                            </div>
                                        </li>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                   
                    @endif

                    @if($groups->count())
                        <div class="bg-white h-auto py-2 rounded-lg">
                            
                            @foreach($groups as $group)
                                <div class="mt-4 px-4">
                                    <a href="{{ route('groups.show', $group->id) }}">
                                        <li class="flex items-center px-4 py-4 mt-4 mb-4">
                                            <img src="{{ asset('storage/' . $group->image) }}" alt="{{ $group->name }}" class="w-20 h-20 rounded-full mr-4 border-2 border-gray-200">
                                            <div class="flex-grow">
                                                <p class="text-xl font-semibold text-blue-600 hover:text-blue-800 transition duration-300"> Nhóm {{ $group->name }}</p>
                                                <p class="text-gray-500">    {{ $group->member_count }}  thành viên</p>
                                              
                                            </div>
                                        </li>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Không tìm thấy nhóm nào.</p>
                    @endif

                
                </div>
                <div id="post"  @if($users->isNotEmpty() || $posts->isNotEmpty() ) style="display: none;" @endif>
              
                    @if($post->count())
                        <ul class="">
                        @foreach ($post as $p)
                            <!-- Hiển thị từng bài viết -->
                            <div class="bg-white p-10 rounded-lg mb-6 border border-gray-300 shadow-sm w-full ">
                                <div class="flex items-center mb-4">
                               
                                @if($p->group_id)
                   
                                    <div class="relative">
                                        <a href="{{ route('groups.show', $p->group_id) }}">
                                            <img class="h-14 w-14   rounded-full" src="{{ asset('storage/' .  $p->group['image'] ) }}" alt="Group Image">
                                        </a>

                                        <!-- Hình người dùng ở góc của hình nhóm -->
                                        <a href="{{ $p->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $p->id_nd]) }}" class="flex items-center">
                                            <div class="absolute bottom-0 right-0 transform translate-x-1/4 translate-y-1/4">
                                                <img class="h-10 w-10 rounded-full border-4 border-white " src="{{ asset('storage/' . $p->user_img) }}" alt="User Avatar">
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Thông tin về bài đăng -->
                                    <div class="ml-3 flex-1">
                                        <a href="{{ route('groups.show', $p->group_id) }}">
                                            <p class="text-xl font-bold text-gray-700">Nhóm {{ $p->group['name'] }}</p>
                                        </a>
                                        <a href="{{ $p->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $p->id_nd]) }}" class="flex items-center">
                                            <div class="flex mt-1">
                                                <p class="text-lg font-medium text-gray-700">{{ $p->user }}.</p>
                                                <p class="text-lg text-gray-500 ml-4">{{ $p->created_at->locale('vi')->diffForHumans() }}</p>

                                            </div>
                                        </a>
                                    </div>

                                    
                                @else

                                <a href="{{ $p->id_nd === session('id') ? route('profile', ['id' => session('id')]) : route('profiles', ['id' => $p->id_nd]) }}" class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <img class="h-14 w-14 rounded-full " src="{{ asset('storage/' . $p->user_avatar) }}" alt="User Avatar">
                                        </div>
                                        <div class="ml-3 flex-1">
                                        
                                        <p class="text-xl font-semibold text-black">{{ $p->user_name }}</p>
                                       
                                        <div class="flex">
                                            <p class="text-lg text-gray-500">{{ $p->created_at->locale('vi')->diffForHumans() }}</p>
                                            @if( $p->regime === 1)
                                            <img id="imageIcon" src="/luanvan_tn/public/image/friend.png" alt="Image Icon"
                                            class="w-5 h-5 ml-3 mt-1">
                                            @else
                                            <img id="imageIcon" src="/luanvan_tn/public/image/publlic1.png" alt="Image Icon"
                                            class="w-5 h-5 ml-3 mt-1">
                                            @endif
                                        </div>
                                    </a>
                                @endif                
                                </div>
                                @if($p->topic)
                                <p class="text-black font-semibold text-xl mb-2">Chủ đề {{ $p->topic }}</p>
                                @endif
                                <p class="text-black  text-xl">{{ $p->noidung }}</p>
                                @if ($p->images && $p->images !== 'default/image.png')
                                <img src="{{ asset('storage/' . $p->images) }}" alt="Post Image" class="w-full h-auto mt-4 border border-gray-300 rounded-md">
                                @endif
                                @if($p->files)
                                @php
                                    $fileName = basename($p->files);
                                @endphp
                                <a href="{{ asset('storage/' . $p->files) }}" download class="text-blue-500 mt-4 inline-block hover:underline">
                                    Download {{ $fileName }}
                                </a>
                                @endif
                                <div class="post">

                                <div class="text-base mt-2 flex justify-between">
                                    <span id="like-count-{{ $p->id }}" class="cursor-pointer" onclick="openModal({{ $p->id }})">
                                        @if($p->is_liked)
                                            @if($p->likes_count > 1)
                                                <span class="like-count">Bạn và {{ $p->likes_count - 1 }} người khác</span>
                                            @else
                                                <span class="like-count">Bạn</span>
                                            @endif
                                        @else
                                            <span class="like-count">
                                                @if($p->likes_count > 0)
                                                    {{ $p->likes_count }}
                                                @endif
                                            </span>
                                        @endif
                                    </span>
                                    @if($postsWithComments[$p->id]['comment_count'] > 0) <!-- Kiểm tra số bình luận -->
                                        <span  onclick="toggleComments({{ $p->id }})">{{ $postsWithComments[$p->id]['comment_count'] }} bình luận</span> <!-- Hiển thị bình luận nếu có -->
                                    @endif
                                </div>

                                    <div id="likes-modal-{{ $p->id }}"
                                    class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center z-50">
                                    <div class="modal-content bg-white p-6 rounded-lg shadow-lg max-w-lg w-full relative">
                                        <button class="close-btn absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl"
                                            onclick="closeModal({{ $p->id }})">
                                            &times;
                                        </button>
                                        <h2 class="text-xl font-semibold mb-4 text-center">Danh sách người đã thích bài viết</h2>
                                        <ul class="list-disc pl-5 space-y-4">

                                            @if(isset($likes[$p->id]))
                                            @foreach($likes[$p->id] as $like)
                                            <li class="flex items-center">
                                                @if($like->id == session('id'))
                                                <a href="{{ route('profile', ['id' => session('id')]) }}" class="flex items-center">
                                                    <img src="{{ asset('storage/' . $like->avatar) }}" alt="Avatar"
                                                        class="w-12 h-12 rounded-full mr-3">
                                                    <span class="text-lg font-medium text-gray-800">Bạn</span>
                                                </a>
                                                @else
                                                <a href="{{ route('profiles', ['id' => $like->id]) }}" class="flex items-center">
                                                    <img src="{{ asset('storage/' . $like->avatar) }}" alt="Avatar"
                                                        class="w-12 h-12 rounded-full mr-3">
                                                    <span class="text-lg font-medium text-gray-800">{{ $like->name }}</span>
                                                </a>
                                                @endif
                                            </li>
                                            @endforeach
                                            @endif

                                        </ul>
                                    </div>
                                </div>


                                    <div class="border border-gray-300 mt-2 mb-2"></div>
                                    <div class="flex justify-around items-center space-x-4 -mb-5">
                                        <form action="{{ route('like.toggle') }}" method="POST"
                                            class="like-form flex items-center justify-center w-full space-x-2">
                                            @csrf
                                            <input type="hidden" name="post_id" value="{{ $p->id }}">
                                            @if(!$p->is_liked)
                                            <button type="submit"
                                                class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                                <img id="imageIcon" src="/luanvan_tn/public/image/like.png" alt="Image Icon"
                                                    class="w-8 h-8">
                                                <span class="mt-1 ml-2 font-semibold">Thích</span>
                                            </button>
                                            @else
                                            <button type="submit"
                                                class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                                <img id="imageIcon" src="/luanvan_tn/public/image/like_blue.png" alt="Image Icon"
                                                    class="w-8 h-8">
                                                <span class="mt-1 ml-2 font-semibold text-blue-700">Thích</span>
                                            </button>
                                            @endif
                                        </form>
                                        <button id="commentButton-{{ $p->id }}" class="flex items-center justify-center space-x-2 text-lg text-black w-full px-4 py-2 rounded hover:bg-gray-300">
                                            <img id="imageIcon" src="/luanvan_tn/public/image/comment.png" alt="Image Icon" class="w-8 h-8">
                                            <span class="ml-2 font-semibold text-lg">Bình luận</span>
                                        </button>    
                                    </div>
                                    <div class="post mb-8 mt-8">
                                        <div id="comments-{{ $p->id }}" class="comments hidden overflow-y-auto max-h-96">
                                            @if(isset($postsWithComments[$p->id]['comments']) && count($postsWithComments[$p->id]['comments']) > 0)
                                                @foreach ($postsWithComments[$p->id]['comments'] as $comment)
                                                    @if (!$comment['parent_id']) 
                                                        @include('partials.comment', ['comment' => $comment, 'post' => $p, 'comments' => $postsWithComments[$p->id]['comments']])
                                                    @endif
                                                @endforeach
                                            @else
                                                <p>Chưa có bình luận nào.</p>
                                            @endif
                                        </div>
                                    </div>


                                    <div id="commentForm-{{ $p->id }}" class="hidden mt-6">
                                                <form action="{{ route('comments.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="post_id" value="{{$p->id }}">
                                                    <input type="hidden" name="user_id" value="{{ session('id') }}">
                                                    <div class="flex">
                                                        <textarea name="content" rows="1" class="w-full p-2 mr-2 border border-gray-300 rounded-md" placeholder="Viết bình luận..." required></textarea>
                                                
                                                        <button type="submit" ><img src="/luanvan_tn/public/image/send.png" alt="Icon Bảng tin" class="w-8 h-8 "> </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </ul>
                    @else
                    <p class="text-gray-500">Không tìm thấy bài viết.</p>
                    @endif
              
                </div>
                <!-- <div id="output"></div> -->
            </div>
        </div>
    </div>
</div>


<script>
function toggleSections(section, hasUsers = false, hasGroups = false, hasPosts = false) {
    const usersSection = document.getElementById('users'); // Phần hiển thị người dùng
    const postsSection = document.getElementById('user-posts'); // Phần hiển thị bài viết
    const groupsSection = document.getElementById('groups'); // Phần hiển thị nhóm
    const usergroupsSection = document.getElementById('user_group'); // Phần nhóm người dùng
    const ggroupsSection = document.getElementById('post'); // Phần bài viết nhóm
    
    // Log biến hasGroups, hasUsers và hasPosts để kiểm tra
    // output.innerHTML = `hasUsers: ${hasUsers}, hasGroups: ${hasGroups}, hasPosts: ${hasPosts}`;

    // Mặc định ẩn hết tất cả các phần
    usersSection.style.display = 'none';
    postsSection.style.display = 'none';
    groupsSection.style.display = 'none';
    usergroupsSection.style.display = 'none';
    ggroupsSection.style.display = 'none';
    
    // Hiển thị phần tương ứng dựa trên section
    if (section === 'user-posts') {
        postsSection.style.display = 'block'; // Hiển thị bài viết
        groupsSection.style.display = 'none'; // Ẩn nhóm
        ggroupsSection.style.display = 'none'; // Ẩn bài viết nhóm
    } else if (section === 'users') {
        usersSection.style.display = 'block'; // Hiển thị người dùng
    } else if (section === 'groups') {
        groupsSection.style.display = 'block'; // Hiển thị nhóm
        postsSection.style.display = 'none'; // Ẩn bài viết
        ggroupsSection.style.display = 'none'; // Ẩn bài viết nhóm
    } else if (section === 'user_group') {
        usergroupsSection.style.display = 'block'; // Hiển thị nhóm người dùng
    } else if (section === 'post') {
        ggroupsSection.style.display = 'block'; // Hiển thị bài viết nhóm
        groupsSection.style.display = 'none'; // Ẩn nhóm
        postsSection.style.display = 'none'; // Ẩn bài viết
    } else {
        // Hiển thị người dùng, bài viết nếu có users và posts
        if (hasUsers && !hasGroups && hasPosts) {
            // Nếu chỉ có người dùng và bài viết mà không có nhóm
            usersSection.style.display = 'block'; // Hiển thị phần người dùng
            postsSection.style.display = 'block'; // Hiển thị bài viết người dùng
            groupsSection.style.display = 'none'; // Ẩn nhóm
            ggroupsSection.style.display = 'none'; // Ẩn bài viết nhóm
        } else if (!hasUsers && hasGroups && !hasPosts) {
            // Nếu chỉ có nhóm mà không có người dùng và không có bài viết
            usersSection.style.display = 'none'; // Ẩn phần người dùng
            postsSection.style.display = 'none'; // Ẩn bài viết người dùng
            groupsSection.style.display = 'block'; // Hiển thị nhóm
            ggroupsSection.style.display = 'block'; // Ẩn bài viết nhóm
            usergroupsSection.style.display = 'block'; // Hiển thị nhóm người dùng
        } else if (hasUsers && hasGroups && hasPosts) {
            // Nếu có cả người dùng, nhóm và bài viết
            usersSection.style.display = 'block'; // Hiển thị người dùng
            postsSection.style.display = 'block'; // Hiển thị bài viết người dùng
            groupsSection.style.display = 'none'; // Ẩn nhóm
            ggroupsSection.style.display = 'none'; // Ẩn bài viết nhóm
            usergroupsSection.style.display = 'block'; // Hiển thị nhóm người dùng
        }
        else if (!hasUsers && !hasGroups && !hasPosts) {
            // Nếu có cả người dùng, nhóm và bài viết
            usersSection.style.display = 'block'; // Hiển thị người dùng
            postsSection.style.display = 'block'; // Hiển thị bài viết người dùng
            groupsSection.style.display = 'none'; // Ẩn nhóm
            ggroupsSection.style.display = 'none'; // Ẩn bài viết nhóm
            usergroupsSection.style.display = 'block'; // Hiển thị nhóm người dùng
        } else {
            // Nếu không có cả người dùng, nhóm và bài viết
            usersSection.style.display = 'none'; // Ẩn người dùng
            postsSection.style.display = hasPosts ? 'block' : 'none'; // Hiển thị bài viết nếu có
            groupsSection.style.display = hasGroups ? 'block'  : 'none'; // Ẩn nhóm
            ggroupsSection.style.display = 'none'; // Ẩn bài viết nhóm
        }
    }
}








</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('form.like-form').on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn hành vi gửi form mặc định

            var form = $(this);
            var postId = form.find('input[name="post_id"]').val();
            var button = form.find('button');
            var icon = button.find('img');
            var text = button.find('span');
            var likeCountSpan = form.closest('.post').find('.like-count');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Cập nhật trạng thái like
                    if (response.is_liked) {
                        icon.attr('src', '/luanvan_tn/public/image/like_blue.png');
                        text.text('Thích').addClass('text-blue-700');
                    } else {
                        icon.attr('src', '/luanvan_tn/public/image/like.png');
                        text.text('Thích').removeClass('text-blue-700');
                    }
                    
                    // Cập nhật số lượt thích
                    if (response.likes_count > 0) {
                        if (response.likes_count > 1 && response.is_liked) {
                            likeCountSpan.text('Bạn và ' + (response.likes_count - 1) + ' người khác');
                        } else if (response.likes_count === 1 && response.is_liked) {
                            likeCountSpan.text('Bạn');
                        } else {
                            likeCountSpan.text(response.likes_count);
                        }
                    } else {
                        likeCountSpan.text(''); // Không hiển thị gì nếu số lượt thích là 0
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });
    });
</script>


<script>
function openModal(postId) {
    var modal = document.getElementById('likes-modal-' + postId);
    modal.classList.remove('hidden'); // Loại bỏ lớp hidden để hiển thị modal
    modal.classList.add('show'); // Thêm lớp show để modal hiển thị
}

function closeModal(postId) {
    var modal = document.getElementById('likes-modal-' + postId);
    modal.classList.add('hidden'); // Thêm lớp hidden để ẩn modal
    modal.classList.remove('show'); // Loại bỏ lớp show
}

// Close modals when clicking outside of the modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        event.target.classList.remove('show');
    }
}

// Close modals when clicking outside of the modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        event.target.classList.remove('show');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Lắng nghe sự kiện nhấp vào nút ba chấm
    document.querySelectorAll('[id^="dropdown-btn-"]').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.id.split('-').pop();
            const dropdownMenu = document.getElementById('dropdown-menu-' + postId);
            dropdownMenu.classList.toggle('hidden');
        });
    });

    // Đóng dropdown khi nhấp ra ngoài
    document.addEventListener('click', function(event) {
        const isClickInside = event.target.closest('.dropdown-menu') || event.target.closest('[id^="dropdown-btn-"]');
        if (!isClickInside) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });
});

document.getElementById('dropdownButton1').addEventListener('click', function() {
    var dropdownMenu = document.getElementById('dropdownMenu1');
    
    // Toggle the 'hidden' class to show/hide the dropdown
    dropdownMenu.classList.toggle('hidden');
});


</script>
<script>
    document.querySelectorAll('[id^="commentButton-"]').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.id.split('-')[1]; // Lấy post_id từ ID của nút
            const commentForm = document.getElementById('commentForm-' + postId); // Tìm form tương ứng với post_id
            commentForm.classList.toggle('hidden'); // Ẩn/hiện form bình luận
        });
    });
</script>
<script>
   function toggleComments(postId) {
    const commentsDiv = document.getElementById(`comments-${postId}`);
    // Kiểm tra nếu phần bình luận đang ẩn hay hiển thị
    if (commentsDiv.classList.contains('hidden')) {
        commentsDiv.classList.remove('hidden'); // Hiển thị phần bình luận
    } else {
        commentsDiv.classList.add('hidden'); // Ẩn phần bình luận
    }
}
function toggleReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    replyForm.classList.toggle('hidden');
}

</script>
@endsection
