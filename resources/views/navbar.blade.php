<nav class="bg-white text-white p-4 shadow-lg">
  <div class="container mx-auto flex justify-between items-center">
    <!-- Tiêu đề -->
    <h1 class="text-3xl text-black font-bold whitespace-nowrap">
</h1>


<img  src="/luanvan_tn/public/image/logo.png" alt="Logo" class="w-56 h-14 -ml-24  mr-5 inline-block">
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
      <li><a href="{{ route('homes', ['id' => session('id')]) }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/home.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"></a></li>
      @if(session('possition') != 0)
        <li><a href="{{ route('user') }}" class="hover:underline text-lg"> <img src="/luanvan_tn/public/image/user2.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"></a></li>
      @endif
        <li><a href="{{ route('group') }}" class="hover:underline text-lg"><img src="/luanvan_tn/public/image/group.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2"></a></li>
      <li class="relative">
        <a href="{{ route('notifications', ['id' => session('id')]) }}" class="hover:underline text-lg  flex items-center">
          <img src="/luanvan_tn/public/image/notification.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2 relative">
          @if($notificationCount > 0)
                <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                    {{ $notificationCount }}
                </span>
            @endif
      </a></li>
      <li class="relative">
        <a href="{{ route('chat', ['receiverId' => session('id')]) }}" class="hover:underline text-lg flex items-center">
            <img src="/luanvan_tn/public/image/message.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2 relative">
            @if($newMessagesCount > 0)
                <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                    {{ $newMessagesCount }}
                </span>
            @endif
        </a>
    </li>
  

    <li class="relative">
      <img src="{{ asset('storage/' . session('avatar')) }}" alt="Avatar" class="w-12 h-12 mx-2 rounded-full  bg-white cursor-pointer" id="userAvatar">

      <div id="dropdownMenu" class="absolute right-0 mt-2 w-80 h-auto bg-gray-100 border border-gray-200 rounded-lg shadow-lg z-10 hidden ">
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
         
            <a href="{{ route('vecto') }}" class="hover:underline text-lg mb-2 flex items-center">
            <div class="flex items-center px-2 py-3 ml-2 rounded-lg hover:bg-white focus:outline-none" style="  width: 18.8rem ">
              <img src="/luanvan_tn/public/image/connect.png" alt="Icon Bảng tin" class="w-10 h-10 mx-2 relative"> 
              <h2 class="text-black text-xl mt-2 font-semibold flex items-center ml-4">
              
                Liên kết người dùng
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
</nav>
