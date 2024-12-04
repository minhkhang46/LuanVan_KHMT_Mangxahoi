<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="shortcut icon" type="image/png" href="/luanvan_tn/logo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" /> <!-- Thêm Toastr CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Thêm jQuery trước Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> <!-- Thêm Toastr JS -->
    <style>
        .background {
            background-image: url('{{ asset('image/hinhnnen.jpg') }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(6px);
        }

        .text-shadow {
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
        }

        .input-field {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid rgba(0, 0, 0, 0.1);
        }

        .input-field:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.3);
        }

        .button {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .button:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
        }

        .button:active {
            transform: translateY(1px);
        }

        @media (max-width: 768px) {
            .white-background {
                flex-direction: column;
                padding: 10px;
            }
        }
        input[type="password"] {
            padding-right: 2.5rem;
        }

        #togglePassword {
            top: 2.8rem;
        }

        /* Định dạng cho thông báo Toastr */
        .toast {
            width: auto !important; 
            font-size: 16px !important;
        }
    </style>
</head>

<body>
    <div class="background">
        <div class="flex shadow-xl ">
            <!-- Welcome Section -->
            <div class="flex flex-col w-full md:w-3/4 items-center justify-center text-center mr-12 bg-white p-8 rounded-l-lg bg-opacity-80" style="width: 84%;">
                <h1 class="text-4xl font-bold text-gray-900 text-shadow transition duration-300 hover:text-gray-700">Chào mừng đến với</h1>
                <img src="/luanvan_tn/public/image/logo2.png" alt="Welcome Image" class="w-80 h-auto transition duration-300 hover:scale-105 -mt-2">
                <p class="text-lg text-gray-900 max-w-7xl leading-relaxed mt-2 transition duration-300 hover:text-gray-500">
                    Nơi để khám phá những cơ hội mới và <br> kết nối với mọi người.
                </p>
            </div>

            <!-- Login Form Section -->
            <div class="flex flex-col w-full md:w-3/4 max-w-7xl items-center justify-center ">
                <div class="w-full p-10 bg-white rounded-r-lg" style="width: 130%;">
                    <h2 class="text-3xl font-bold text-center text-gray-900 mb-6">Đăng Nhập</h2>

                    <form method="POST" action="{{ route('logins') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 font-semibold mb-2" for="emailorphone">Email hoặc Số điện thoại</label>
                            <input type="text" name="emailorphone" id="emailorphone" class="w-full px-4 py-2 border rounded-lg focus:outline-none input-field" placeholder="Nhập Email hoặc Số điện thoại">
                        </div>
                        <div class="mb-4 relative">
                            <label class="block text-gray-700 font-semibold mb-2">Mật Khẩu</label>
                             <input type="password" name="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none pr-10" placeholder="Nhập mật khẩu">
                            <span id="togglePassword" class="absolute right-3 top-3 cursor-pointer">
                                <img src="/luanvan_tn/public/image/show.png" class="h-5 w-5">
                            </span>
                        </div>

                        <button type="submit" class="w-full button text-white font-bold py-2 rounded-lg">Đăng nhập</button>
                    </form>
                    <p class="text-center text-gray-600 mt-6">
                        Bạn chưa có tài khoản?
                        <a href="{{ route('registers') }}" class="text-blue-500 hover:underline">Đăng ký ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hiển thị Toastr thông báo nếu có thông báo lỗi từ session
        $(document).ready(function() {
            const errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                toastr.error(errorMessage, 'Lỗi', { // Thay đổi thông báo nếu cần
                    positionClass: 'toast-top-right',
                    timeOut: 5000, // Thời gian tự động ẩn
                    closeButton: true,
                    progressBar: true
                  
                });
            }
        });

        // Hiển thị hoặc ẩn mật khẩu
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
        });
    </script>
</body>

</html>
