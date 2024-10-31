<!-- resources/views/profile.blade.php -->
@extends('layouts.app')

@section('title', 'Trang cá nhân')

@section('content')
<style>
.modal {
    display: none; /* Modal bị ẩn mặc định */
}

.modal.show {
    display: flex; /* Modal hiển thị khi thêm lớp `show` */
}
/* Ẩn thanh cuộn nhưng vẫn cho phép cuộn */
.modal-scroll {
    max-height: 80vh; /* Chiều cao tối đa */
    overflow-y: auto; /* Cho phép cuộn dọc */
}

/* Ẩn thanh cuộn */
.modal-scroll::-webkit-scrollbar {
    width: 0; /* Ẩn chiều rộng của thanh cuộn */
    background: transparent; /* Nền trong suốt */
}
.custom-image {
    max-height: 600px; /* Giới hạn chiều cao tối đa */
    width: auto;       /* Chiều rộng tự động để giữ tỷ lệ */
    height: auto;      /* Chiều cao tự động để giữ tỷ lệ */
    object-fit: contain; /* Giữ hình ảnh nguyên vẹn trong không gian mà không bị cắt */
}



</style>
<div class="container mx-auto px-4 py-8 mt-20">
    <!-- User Info Section -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200  w-full h-full">
        <div class="flex flex-col md:flex-row items-center p-5 -ml-32">
            <!-- User Avatar Section -->
            <div class="relative mb-6 md:mb-0 md:w-1/3 text-center">
                <img src="{{ asset('storage/' . $user->avatar) }}"
                    srcset="{{ asset('storage/' . $user->avatar) }} 1x, {{ asset('storage/' . $user->avatar) }} 2x"
                    alt="User Avatar"
                    class="h-44 w-44 rounded-full  border-4 border-blue-500 absolute -top-36 left-1/2 transform -translate-x-1/2">
            </div>

            <!-- User Info Section -->
            <div class="md:w-2/3 -ml-32 mt-20 md:mt-0">
                <h2 class="text-4xl font-semibold text-left md:text-left text-gray-900 capitalize mb-2">
                    {{ $user->name }}</h2>

                <!-- Buttons Section -->
                <div class="flex justify-between items-center mt-2">
                    <p class="text-lg text-black">{{$totalFriends}} bạn bè</p>

                </div>
            </div>
        </div>
    </div>

    <!-- User Details and Posts Section -->
    <div class="flex flex-col md:flex-row gap-8 mt-5">
        <!-- User Details and Friends List -->
        <div class="w-full md:w-1/3">
            <!-- Giới thiệu -->
            <div class="w-full bg-white p-5 rounded-lg shadow-lg border border-gray-200">
                <div class="mb-4">
                   
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Giới thiệu</h3>
                    
                    <!-- <img src="/luanvan_tn/public/image/study.png" alt="Email Icon" class="w-6 h-6 mr-2" /> -->
                    <p class="text-gray-700  text-center text-lg mb-4">{{ $user->description }}</p>
                   
                 
                    <div class="border border-gray-300 mb-4"></div>
                    <div class="flex items-center mb-2 ml-2">
                        <img src="/luanvan_tn/public/image/study.png" alt="Email Icon" class="w-8 h-8 mr-2" />
                        <p class="text-gray-700 text-lg">Chuyên đề {{ $user->chuyende }}</p>
                    </div>
                    <div class="flex items-center mb-2 ml-2">
                        <img src="/luanvan_tn/public/image/cv.png" alt="CV Icon" class="w-8 h-8 mr-2" />
                        <a href="{{ asset('storage/cv/' . $user->cv) }}" download class="text-blue-600 text-lg">
                            {{ $user->cv }}
                        </a>
                    </div>

                    <div class="flex items-center mb-2 ml-3">
                        <img src="/luanvan_tn/public/image/email.png" alt="Email Icon" class="w-6 h-6 mr-2" />
                        <p class="text-gray-700 text-lg">{{ $user->email }}</p>
                    </div>
                    <div class="flex items-center mb-2 ml-3">
                        <img src="/luanvan_tn/public/image/phone.png" alt="Phone Icon" class="w-6 h-6 mr-2" />
                        <p class="text-gray-700 text-lg">{{ $user->phone }}</p>
                    </div>
                    <div class="flex items-center mb-2 ml-3">
                        <img src="/luanvan_tn/public/image/day.png" alt="Day Icon" class="w-6 h-6 mr-2" />
                        <p class="text-gray-700 text-lg">
                            @php
                            use Carbon\Carbon;
                            $carbonDate = Carbon::parse($user->date);
                            $formattedDate = $carbonDate->locale('vi')->translatedFormat('d F Y');
                            @endphp
                            {{ $formattedDate }}
                        </p>
                    </div>
                   <!-- Nút để mở modal -->
                   <!-- Nút để mở modal -->
                    <button id="editButton" class="bg-gray-200 w-full text-lg px-2 py-2 rounded-lg mt-3">Chỉnh sửa thông tin</button>

                    <!-- Modal (ban đầu ẩn) -->
                   <!-- Modal (ban đầu ẩn) -->
                    <div id="editModal" class="fixed hidden inset-0 bg-gray-800 bg-opacity-70 flex justify-center items-center mt-20 z-50">
                        <div class="bg-white w-full max-w-xl  rounded-lg relative z-10  modal-scroll">
                        <!-- Nút đóng modal -->
                        <!-- Phần cố định -->
                        <div class="sticky top-0 bg-white z-20 border w-full border-gray-300 rounded-t-lg p-6">
                        <!-- Nút đóng modal -->
                        <button id="closeModal" class="absolute top-4 right-4 text-gray-500 text-2xl">X</button>
                        <h2 class="text-3xl text-center font-semibold">Chỉnh sửa thông tin</h2>
                    </div>

                        <!-- Form trong modal -->
                        <form action="{{ route('user.profile' , session('id')) }}" method="POST" enctype="multipart/form-data" class="max-w-xl p-6">
                            @csrf
                            <!-- Các trường thông tin -->
                            <div class="mb-4">
                                <label for="description" class="block text-lg text-gray-700 font-semibold mb-2">Mô tả</label>
                                <textarea id="description" name="description" class="w-full p-2 border-gray-300 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">{{ old('description', $user->description) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 text-lg font-semibold mb-2">Tên người dùng</label>
                                <input type="text" value="{{ $user->name }}" id="name" name="name" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-gray-700 text-lg font-semibold mb-2">Email</label>
                                <input type="text" value="{{ $user->email }}" id="email" name="email" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold text-lg mb-2">Số điện thoại</label>
                                <input type="text" name="phone" id="phone" value="{{$user->phone}}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input" placeholder="Nhập số điện thoại của bạn">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Ngày sinh</label>
                                <div class="flex gap-3">
                                    <!-- Day -->
                                    <div class="w-1/3">
                                        <select id="day" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                                            <option value="" disabled selected>Ngày</option>
                                            @for ($i = 1; $i <= 31; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <!-- Month -->
                                    <div class="w-1/3">
                                        <select id="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                                            <option value="" disabled selected>Tháng</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <!-- Year -->
                                    <div class="w-1/3">
                                        <select id="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input">
                                            <option value="" disabled selected>Năm</option>
                                            @for ($i = date('Y'); $i >= 1900; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden input to hold the selected date -->
                            <input type="hidden" name="date" id="date"  value="{{$user->date}}">

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold text-lg mb-2">Chuyên đề</label>
                                <input type="text" name="chuyende" id="chuyende" value="{{$user->chuyende}}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 form-input" placeholder="Nhập chuyên đề">
                            </div>
                            <div class="mb-4">
                                <label for="cv" class="block text-gray-700 mb-2">Tải lên CV</label>
                                <input type="file" id="cv" name="cv" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                            </div>

                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg w-full">Lưu</button>
                        </form>
                    </div>

                    </div>



                </div>
            </div>
            <!-- Danh sách bạn bè -->
            <div class="mb-4 w-full bg-white p-5 rounded-lg shadow-lg border border-gray-200 mt-5">
                <h3 class="text-2xl font-semibold text-gray-900 mb-3">Danh sách bạn bè</h3>
                <div class="border border-gray-300 mb-4"></div>
                @if(empty($friendInfos))
                <p class="text-gray-700 text-lg text-center">Chưa có bạn bè.</p>
                @else
                @php
                $count = 0;
                @endphp

                <ul class="flex flex-wrap">
                    @foreach($friendInfos as $friendInfo)
                    @php
                    $friendName = $friendInfo->name ?? 'Không xác định';
                    $friendAvatar = $friendInfo->avatar ?? 'default-avatar.png';
                    $profileRoute = route('profiles', ['id' => $friendInfo->id]);
                    $isCurrentUser = ($friendInfo->id === $sessionUserId);
                    $count++;
                    @endphp

                    <li class="mb-4 flex flex-col w-1/3">
                        <a href="{{ $isCurrentUser ? route('profile', ['id' => $friendInfo->id]) : $profileRoute }}"
                            class="flex flex-col items-center">
                            <img src="{{ asset('storage/' . $friendAvatar) }}" alt="Friend Avatar"
                                class="h-32 w-32 rounded-lg mb-2">
                            <p class="text-lg text-gray-700 text-center">{{ $friendName }}</p>
                        </a>
                    </li>

                    @if($count % 6 == 0)
                </ul>
                <ul class="flex flex-wrap">
                    @endif
                    @endforeach
                </ul>

                @endif
            </div>
        </div>

        <!-- User Posts Section -->
        <div class="w-full md:w-2/3">
            <div id="postForm" class="bg-white p-5 mb-8 rounded-lg shadow-md  overflow-y-auto">
                <form action="{{ route('post_content') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input name="id_nd" type="hidden" value="{{ session('id') }}" />
                    <div class="mb-4  items-center">
                        <img class="h-16 w-16 rounded-full mb-4 ml-2" src="{{ asset('storage/' . session('avatar')) }}"
                            alt="User Avatar">
                   
                            <div class="flex">
                                <input list="topics" id="topic" name="topic" class="w-full p-2 ml-2 mb-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg" placeholder="Chọn hoặc nhập chủ đề...">

                                <!-- Danh sách các chủ đề có sẵn để chọn -->
                                <datalist id="topics" class="w-full">
                                    <option value="Thị Giác Máy Tính">
                                    <option value="Xử Lý Ngôn Ngữ Tự Nhiên">
                                    <option value="Máy Học Ứng Dụng">
                                    <option value="Khoa Học Máy Tính">
                                    <option value="Khai Phá Dữ Liệu">   
                                </datalist>
                                <!-- Thẻ select chỉ cho phép người dùng chọn chủ đề từ danh sách -->
                                <select id="regime" name="regime" class="w-1/3 p-2 ml-2 mb-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                                    <option value="">Tùy chỉnh chế độ</option> <!-- Tùy chọn mặc định (trống) -->
                                    <option value="Bạn bè">Bạn bè</option>
                                    <option value="Công khai">Công khai</option>
                                </select>

                            </div>
                            <div class="mr-2">
                            <textarea id="content" name="noidung" rows="1"
                                class="w-full p-2 ml-2 border  border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                                placeholder="Write something..." oninput="toggleSubmitButton()"></textarea>
                            </div>
                    </div>
                    <div class="border border-gray-300 -mb-3"></div>
                    <div class="flex flex-col mb-4 space-y-4 items-end mt-3">
                        <div id="imagePreviewContainer" class="hidden">
                            <img id="imagePreview" class="w-full h-auto border border-gray-300 p-1"
                                alt="Selected Image">
                            <div id="imageFileName" class="text-xs text-gray-500 truncate"></div>
                        </div>
                        <div id="filePreviewContainer" class="hidden">
                            <div id="filePreview" class="w-full h-auto border border-gray-300 p-1"></div>
                            <div id="fileFileName" class="text-xs text-gray-500 truncate"></div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Image Icon -->
                            <label for="image"
                                class="cursor-pointer text-blue-500 hover:text-blue-600 flex flex-col items-center">
                                <img id="imageIcon" src="/luanvan_tn/public/image/image.png" alt="Image Icon"
                                    class="w-8 h-8">
                                <input id="image" name="images" type="file" class="sr-only"
                                    onchange="previewSelectedFile(event, 'imagePreview', 'imageFileName')" />
                            </label>
                            <!-- File Icon -->
                            <label for="file"
                                class="cursor-pointer text-blue-500 hover:text-blue-600 flex flex-col items-center">
                                <img id="fileIcon" src="/luanvan_tn/public/image/folder.png" alt="File Icon"
                                    class="w-8 h-8">
                                <input id="file" name="files" type="file" class="sr-only"
                                    onchange="previewSelectedFile(event, 'filePreview', 'fileFileName')" />
                            </label>
                        </div>
                    </div>
                    <button id="postButton" type="submit"
                        class="bg-blue-500 text-white w-full px-4 py-2 rounded-lg font-semibold hover:bg-blue-600 transition duration-300 text-lg opacity-50 cursor-not-allowed"
                        disabled>Post</button>
                </form>
            </div>
            <div class="bg-white p-5 rounded-lg mb-4">
                <h3 class="text-2xl font-semibold text-gray-900 text-left">Bài viết</h3>
            </div>
            @if($posts->isEmpty())
            <!-- Nếu không có bài viết -->
            <div class="bg-white p-6 rounded-lg mb-4">
                <p class="text-gray-700 mb-2 text-xl text-center">Hiện tại không có bài viết.</p>
            </div>
            @else
            @foreach ($posts as $p)
            <!-- Hiển thị từng bài viết -->
            <div class="bg-white p-10 rounded-lg mb-6 border border-gray-300 shadow-sm">
                  <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <img class="h-14 w-14 rounded-full" src="{{ asset('storage/' . $p->user_avatar) }}" alt="User Avatar">
                    </div>
                    <div class="ml-3 flex-1">
                    <p class="text-xl font-semibold text-black">{{ $p->user_name }}</p>
                            <div class="flex">
                                <p class="text-lg text-black">{{ $p->created_at->locale('vi')->diffForHumans() }}</p>
                                @if( $p->regime === 1)
                                <img id="imageIcon" src="/luanvan_tn/public/image/friend.png" alt="Image Icon"
                                class="w-5 h-5 ml-3 mt-1">
                                @else
                                <img id="imageIcon" src="/luanvan_tn/public/image/publlic1.png" alt="Image Icon"
                                class="w-5 h-5 ml-3 mt-1">
                                @endif
                            </div>
                    </div>
                    
                    <!-- Dropdown Trigger -->
                    <div class="relative">
                        <button id="dropdown-btn-{{ $p->id }}" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <!-- Icon ba chấm -->
                            <img id="imageIcon" src="/luanvan_tn/public/image/dots.png" alt="Image Icon"
                            class="w-4 h-4">
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdown-menu-{{ $p->id }}" class="dropdown-menu hidden absolute right-0 mt-1 w-40 bg-gray-100 rounded-md shadow-lg z-50">
                          <form action="{{ route('posts.destroy', ['id' => $p->id]) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                  <div class="flex items-center">
                                      <img id="imageIcon" src="/luanvan_tn/public/image/trash.png" alt="Image Icon" class="w-5 h-5">
                                      <span class="ml-2">Xóa bài đăng</span>
                                  </div>
                              </button>
                          </form>
                      </div>

                    </div>
                </div>
                  
                @if($p->topic)
                    <p class="text-black font-semibold text-xl mb-2">Chủ đề {{ $p->topic }}</p>
                    @endif
                                <p class="text-black  text-xl">{{ $p->noidung }}</p>
                @if($p->images && $p->images !== 'default/image.png')
                <div class="flex justify-center mb-4">
                    @php
                        $imageSize = $imageSizes[$p->id] ?? null;
                        $width = $imageSize[0] ?? null; // Chiều rộng
                        $height = $imageSize[1] ?? null; // Chiều cao
                    @endphp

                    <img src="{{ asset('storage/' . $p->images) }}" alt="Post Image" 
                        class="mt-4 border border-gray-300 rounded-md custom-image"
                        style="max-height: 700px; 
                                @if($height && $width && $height > $width) width: auto; height: auto; object-fit: contain; 
                                @else width: 100%; height: auto; object-fit: cover; @endif">
                </div>

                @endif
                @if($p->files)
                @php
                $fileName = basename($p->files);
                @endphp
                <a href="{{ asset('storage/' . $p->files) }}" download
                    class="text-blue-500 mt-4 inline-block hover:underline">
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

                  <!-- Modal -->
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
                  <div class="flex justify-around items-center space-x-4 -mb-16">
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
                  <div class="post mb-8 mt-16">
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
                    <div id="commentForm-{{ $p->id }}" class="hidden mt-16">
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
        @endif
    </div>
</div>
</div>
<script>
function previewSelectedFile(event, previewId, fileNameId) {
    const input = event.target;
    const previewContainerId = input.id === 'image' ? 'imagePreviewContainer' : 'filePreviewContainer';
    const previewElement = document.getElementById(previewId);
    const fileNameElement = document.getElementById(fileNameId);
    const previewContainer = document.getElementById(previewContainerId);
    const reader = new FileReader();

    reader.onload = function() {
        if (input.id === 'image') {
            previewElement.src = reader.result;
            previewContainer.classList.remove('hidden');
        } else {
            previewElement.innerHTML = `<a href="${reader.result}" download>${input.files[0].name}</a>`;
            previewContainer.classList.remove('hidden');
        }
        fileNameElement.textContent = input.files[0].name;
    };

    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.classList.add('hidden');
    }
}

function toggleSubmitButton() {
    const textarea = document.getElementById('content');
    const submitButton = document.getElementById('postButton');

    if (textarea.value.trim() !== '') {
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        submitButton.classList.add('opacity-100', 'cursor-pointer');
    } else {
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        submitButton.classList.remove('opacity-100', 'cursor-pointer');
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
// Add event listener to handle form submission
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-post-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Ngăn việc submit mặc định để xử lý bằng JS

            if (confirm('Bạn có chắc chắn muốn xóa bài đăng này không?')) {
                // Gửi request xóa bài đăng
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Gửi CSRF token
                    }
                }).then(response => {
                    if (response.ok) {
                        alert('Bài đăng đã được xóa thành công.'); // Thông báo xóa thành công
                        location.reload(); // Làm mới trang sau khi xóa thành công
                    } else {
                        alert('Không thể xóa bài đăng.'); // Thông báo lỗi nếu xóa không thành công
                        console.error('Error: Failed to delete post');
                    }
                }).catch(error => {
                    alert('Đã xảy ra lỗi khi xóa bài đăng.'); // Thông báo lỗi khi gặp sự cố
                    console.error('Error:', error);
                });
            }
        });
    });
});



</script>
<script>
    // Mở modal khi nhấn vào nút "Chỉnh sửa thông tin"
    document.getElementById('editButton').addEventListener('click', function() {
        document.getElementById('editModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Ngăn cuộn trang
    });

    // Đóng modal khi nhấn vào nút "X"
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden'); // Bật lại cuộn trang
    });

    // Đóng modal khi nhấn bên ngoài nội dung modal
    window.addEventListener('click', function(event) {
        var modal = document.getElementById('editModal');
        if (event.target === modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden'); // Bật lại cuộn trang
        }
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