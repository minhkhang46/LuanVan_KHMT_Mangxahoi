<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="/luanvan_tn/logo.png"/>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" /> <!-- Thêm Toastr CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Thêm jQuery trước Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> <!-- Thêm Toastr JS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>



  <style>
    html, body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      height: 100%;  overflow-y: hidden; /* Đảm bảo chiều cao 100% cho html và body */
    }
    
    /* Thanh điều hướng cố định */
    .navbar {
      position: fixed; /* Cố định thanh điều hướng */
      top: 0;
      left: 0;
      right: 0;
      background: #fff; /* Màu nền của thanh điều hướng */
      z-index: 1000; /* Đảm bảo thanh điều hướng nằm trên các phần tử khác */
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Thêm bóng cho thanh điều hướng */
      height: 4rem; /* Đặt chiều cao cho thanh điều hướng */
    }
    
    /* Phần tử chứa nội dung chính */
    .content-wrapper {
      margin-top: 5rem; /* Cung cấp không gian cho thanh điều hướng cố định */
      display: flex;
      flex-direction: column;
      height: calc(100% - 4rem); /* Chiều cao còn lại sau khi trừ chiều cao thanh điều hướng */
      overflow-y: auto; /* Thêm cuộn nếu nội dung vượt quá chiều cao */
    }
    
    .main-content {
      flex: 1; 
      
    }
  </style>
  <title>@yield('title', 'Social Media')</title>
</head>
<body class="bg-gray-100">
  <!-- Include Navigation -->
  <nav class="navbar">
    @include('navbar')
  </nav>

  <!-- Main Content -->
  <div class="content-wrapper">
    <div class="main-content">
      @yield('content')
    </div>
  </div>

  <!-- Footer -->
  <!-- <footer class="bg-gray-800 text-white text-center p-4 mt-4">
    <p>&copy; 2024 Simple Social Media</p>
  </footer> -->
  


</body>
</html>
