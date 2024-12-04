<nav class="bg-white text-white p-4 shadow-lg">
  <div class="container mx-auto flex justify-between items-center">
    <!-- Tiêu đề -->
    <h1 class="text-3xl text-black font-bold whitespace-nowrap">
</h1>


<img  src="/luanvan_tn/public/image/logo2.png" alt="Logo" class="w-72 h-20 -mt-10 -ml-20  mr-5 inline-block">
    <!-- Form tìm kiếm -->
    <div class="flex items-center flex-grow mx-6">
      <form action="{{ route('searchs') }}" method="POST" class="flex w-full max-w-md">
        @csrf
        <input type="text" name="keyword" value="{{ old('keyword', $keyword ?? '') }}" placeholder="Tìm kiếm..." class="w-full p-2 bg-gray-50 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-400 text-black"/>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-lg font-semibold hover:bg-blue-700 transition duration-300">Tìm</button>
      </form>
    </div>

    <!-- Các liên kết và hình ảnh người dùng -->
    <ul class="flex items-center space-x-10 whitespace-nowrap">
    @if(session('possition') != 0)
    <li><a href="{{ route('dashboard', ['id' => session('id')]) }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/home.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"><span class="{{ Route::currentRouteName() === 'dashboard' ? 'underline-active' : '' }}"></span></a></li>
      @else
      <li><a href="{{ route('homes', ['id' => session('id')]) }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/home.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2 "><span class="{{ Route::currentRouteName() === 'homes' ? 'underline-active' : '' }}"></span></a>
      
    </li>
      @endif
      <!-- <li><a href="{{ route('homes', ['id' => session('id')]) }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/home.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"></a></li> -->
      @if(session('possition') != 0)
        <li><a href="{{ route('post_admin') }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/post.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"><span class="{{ Route::currentRouteName() === 'post_admin' ? 'underline-active' : '' }}"></span></a></li>
      @endif
      @if(session('possition') != 0)
        <li><a href="{{ route('user') }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/user2.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"><span class="{{ Route::currentRouteName() === 'user' ? 'underline-active' : '' }}"></span></a></li>
      @endif
      @if(session('possition') != 0)
        <li><a href="{{ route('group.admin') }}" class="hover:underline text-lg"><img src="/luanvan_tn/public/image/group.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"><span class="{{ Route::currentRouteName() === 'group' ? 'underline-active' : '' }}"></span></a></li>
      @else
        <li><a href="{{ route('group') }}" class="hover:underline text-lg"><img src="/luanvan_tn/public/image/group.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"><span class="{{ Route::currentRouteName() === 'group' ? 'underline-active' : '' }}"></span></a></li>
      @endif
      <li class="relative">
          <a href="{{ route('notifications', ['id' => session('id')]) }}" class="text-lg flex flex-col items-center relative">
              <div class="relative">
                  <img src="/luanvan_tn/public/image/notification.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2">
                  @if($notificationCount > 0)
                      <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white px-2 py-1 rounded-full text-xs z-10">
                          {{ $notificationCount }}
                      </span>
                  @endif
              </div>
              <span class="{{ Route::currentRouteName() === 'notifications' ? 'underline-active' : '' }}"></span>
          </a>
      </li>
      <li class="relative">
        <a href="{{ route('chat', ['receiverId' => session('id')]) }}" class="hover:underline text-lg flex flex-col items-center relative">
              <div class="relative">
              <img src="/luanvan_tn/public/image/message.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2 ">
                  @if($newMessagesCount > 0)
                      <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                          {{ $newMessagesCount }}
                      </span>
                  @endif

              </div>
              <span class="{{ Route::currentRouteName() === 'chat' ? 'underline-active' : '' }}"></span>
          </a>
      </li>
      @if(session('possition') == 0)
        <li><a href="{{ route('vecto') }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/connect1.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"><span class="{{ Route::currentRouteName() === 'vecto' ? 'underline-active' : '' }}"></span></a></li>
      @endif
     
    <li class="relative">
      <img src="{{ asset('storage/' . session('avatar')) }}" alt="Avatar" class="w-12 h-12 mx-2 rounded-full  bg-white cursor-pointer" id="userAvatar">

      <div id="dropdownMenu" class="absolute right-0  mt-5  w-80 h-auto bg-gray-100 border border-gray-200 rounded-lg shadow-lg z-10 hidden ">
          <a href="{{ route('profile', ['id' => session('id')]) }}" class="block px-2 py-3  ">
              <div class="bg-white hover:bg-gray-200 text-white px-2 py-4 rounded-md w-full">
              <div class="flex items-center mx-2">
                  <img src="{{ asset('storage/' . session('avatar')) }}" alt="Avatar" class="w-12 h-12 rounded-full  bg-white">
                  <h2 class="text-xl font-semibold  text-gray-900 ml-2">{{session('name')}} </h2>
              </div>

                <div class="border border-gray-300 mt-2" ></div>
                <h2 class="text-blue-400 text-xl mt-2 font-semibold flex items-center">
                  <img src="/luanvan_tn/public/image/user.png" alt="Icon Bảng tin" class="w-11 h-11 mx-2"> 
                  Xem trang cá nhân
              </h2>

              </div>
          </a>
         
            <a href="{{ route('lists') }}" class="hover:underline text-lg mb-2 flex items-center">
            <div class="flex items-center px-2 py-3 ml-2 rounded-lg hover:bg-white focus:outline-none" style="  width: 18.8rem ">
              <img src="/luanvan_tn/public/image/policy.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2 relative"> 
              <h2 class="text-black text-xl mt-2 font-semibold flex items-center ml-4">
              
               Giới thiệu và chính sách
              </h2>
              </div>
             
            </a>
      
          <!-- Đăng Xuất -->
          <form action="{{ route('logout') }}" method="POST" class="block">
              @csrf
              <div class="flex items-center px-2 py-3 ml-2 mb-4 rounded-lg hover:bg-white focus:outline-none" style="  width: 18.8rem ">
                <img src="/luanvan_tn/public/image/logout.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"> 
                <button type="submit" class="w-full text-left px-4 py-2 text-xl text-black font-semibold ">Đăng xuất</button>
              </div>
          </form>
      </div>
  </li>

    </ul>
  </div>
  <!-- Script để xử lý hiển thị dropdown -->
<script>
    document.getElementById('userAvatar').addEventListener('click', function () {
        var dropdownMenu = document.getElementById('dropdownMenu');
        dropdownMenu.classList.toggle('hidden');
    });

    // Đóng dropdown nếu click bên ngoài
    document.addEventListener('click', function (event) {
        var avatar = document.getElementById('userAvatar');
        var dropdownMenu = document.getElementById('dropdownMenu');
        if (!avatar.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });
</script>
<style>
/* Gạch chân khi được chọn (luôn hiển thị khi đã chọn) */
.underline-active {
    display: block;
    width: 55px; /* Chiều rộng của gạch chân khi đã chọn */
    height: 5px; /* Độ dày của gạch chân */
    background-color: #3490dc; /* Màu xanh dương */
    margin-top: 5px; /* Khoảng cách giữa icon và gạch chân */
    border-radius: 10px; /* Bo góc để gạch chân mềm mại */
    z-index: 1; /* Đặt gạch chân dưới số thông báo */
    position: relative; /* Đảm bảo z-index hoạt động đúng */
    transition: all 0.3s ease; /* Chuyển động mượt mà */
}

/* Hiển thị gạch chân khi hover */
a:hover .underline-active {
    width: 55px; /* Giữ chiều rộng khi hover */
    background-color: #1d72b8; /* Màu xanh dương đậm khi hover */
}

/* Đảm bảo số thông báo luôn trên cùng */
.relative .absolute {
    z-index: 10; /* Số thông báo luôn nằm trên gạch chân và icon */
}


</style>
</nav>
