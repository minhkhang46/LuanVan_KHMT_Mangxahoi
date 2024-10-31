<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" /> <!-- Thêm Toastr CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Thêm jQuery trước Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> <!-- Thêm Toastr JS -->
    <style>
        /* Custom styles */
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
        .hidden-input {
            display: none;
        }
        .hidden-step {
            display: none;
        }
        .form-button {
            padding: 12px 0;
        }
        .step {
            width: 360px; /* Thay đổi giá trị này tùy theo chiều cao bạn muốn */
            overflow: hidden; /* Ẩn các phần bị tràn */
            height: 560px; 
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

        .button1 {
            /* background:#6a6a6a; */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .button1:hover {
            background: #8c94a4;
            transform: translateY(-2px);
        }

        .button1:active {
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
            top: 2.8rem; /* Điều chỉnh vị trí theo yêu cầu */
        }
        .toast {
            width: auto !important; /* Đặt chiều rộng cho thông báo */
            font-size: 16px !important; /* Đặt kích thước chữ cho thông báo */
        }
    </style>
</head>
<body>
<div class="background ">
<div class="flex shadow-xl ">
    <div class="flex flex-col w-full md:w-3/4 items-center justify-center text-center bg-white p-8 rounded-l-lg bg-opacity-80">
        <h1 class="text-4xl font-bold text-gray-900 text-shadow transition duration-300 hover:text-gray-700">Chào mừng đến với</h1>
        <img src="/luanvan_tn/public/image/logo.png" alt="Welcome Image" class="w-72 h-auto transition duration-300 hover:scale-105 mt-4">
        <p class="text-lg text-gray-900 max-w-7xl leading-relaxed mt-2 transition duration-300 hover:text-gray-500">
        Vui lòng điền thông tin của bạn để tạo tài khoản <br> và bắt đầu kết nối với mọi người.
        </p>
    </div>

    <!-- Phần chào mừng với nền riêng -->
    
    <div class="w-full md:w-3/4 items-center justify-center  ">
        <div class="w-full max-w-2xl p-10 bg-white rounded-r-lg  ">
        <h2 class="text-4xl font-bold text-center text-gray-900 mb-6">Đăng Ký</h2>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <!-- Step 1: Personal Details -->
            <div class="step" id="step-1">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 form-label">Avatar</label>
                    <input type="file" name="avatar" id="avatar" class="hidden-input">
                    <div class="flex items-center space-x-4">
                        <img id="avatarPreview" src="#" alt="Avatar Preview" class="hidden w-20 h-20 rounded-full border border-gray-300">
                        <button type="button" onclick="document.getElementById('avatar').click()" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Chọn Avatar</button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 ">Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none input-field" placeholder="Nhập tên của bạn" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 ">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none input-field" placeholder="Nhập email của bạn" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 ">Số Điện Thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none input-field" placeholder="Nhập số điện thoại của bạn" required>
                </div>
                <div class="mb-4 relative">
                        <label class="block text-gray-700 font-semibold mb-2">Mật Khẩu <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none input-field" placeholder="Nhập mật khẩu">
                    
                            <span id="togglePassword" class="absolute right-3 top-3 cursor-pointer">
                                <img src="/luanvan_tn/public/image/show.png" class="h-5 w-5">
                            </span>
                </div>
                <button type="button" class="w-full button bg-blue-500 text-white font-bold py-3 rounded-lg mt-auto" onclick="nextStep()">Tiếp Theo</button>
            </div>

            <!-- Step 2: Contact Information -->
            <div class="step hidden-step" id="step-2" >
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 " for="description">Mô Tả <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" class= "w-full px-4 py-2 border rounded-lg focus:outline-none input-field" placeholder="Nhập mô tả của bạn" rows="1" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 " for="chuyende">Chuyên Ngành <span class="text-red-500">*</span></label>
                    <input type="text" name="chuyende" id="chuyende" class="w-full px-4 py-2 border rounded-lg focus:outline-none input-field" placeholder="Nhập chuyên ngành của bạn" required>
                </div>

                <!-- CV Upload Field -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 " for="cv">Tải CV <span class="text-red-500">*</span></label>
                    <input type="file" name="cv" id="cv" class="w-full px-4 py-2 border rounded-lg focus:outline-none input-field" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Ngày Sinh</label>
                    <div class="flex gap-3">
                        <!-- Day -->
                        <select id="day" class="w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none">
                            <option value="" disabled selected>Ngày</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <!-- Month -->
                        <select id="month" class="w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none">
                            <option value="" disabled selected>Tháng</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                        <!-- Year -->
                        <select id="year" class="w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none">
                            <option value="" disabled selected>Năm</option>
                            @for ($i = date('Y'); $i >= 1900; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <input type="hidden" name="date" id="date">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2 form-label">Giới Tính</label>
                    <div class="flex gap-3">
                        <label class="flex items-center w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 cursor-pointer hover:bg-gray-200 form-input">
                            <input type="radio" name="gender" value="male" class="form-radio text-blue-500">
                            <span class="ml-2">Nam</span>
                        </label>
                        <label class="flex items-center w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 cursor-pointer hover:bg-gray-200 form-input">
                            <input type="radio" name="gender" value="female" class="form-radio text-blue-500">
                            <span class="ml-2">Nữ</span>
                        </label>
                        <label class="flex items-center w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-50 cursor-pointer hover:bg-gray-200 form-input">
                            <input type="radio" name="gender" value="other" class="form-radio text-blue-500">
                            <span class="ml-2">Khác</span>
                        </label>
                    </div>
                </div>
                <div class="flex  ">
                    <button type="button" class="w-1/2 button1 bg-gray-500 text-white font-bold py-3 rounded-lg mr-2" onclick="prevStep()">Quay Lại</button>
                    <button type="submit" class="w-1/2 button bg-blue-500 text-white font-bold py-3 rounded-lg ml-2">Đăng Ký</button>
                </div>
            </div>
        </form>
        <p class="text-center text-gray-600 -mt-2">
    Bạn đã có tài khoản?
    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Đăng nhập ngay</a>
</p>

        </div>
    </div>
    </div>
</div>

    <script>
        function nextStep() {
            document.getElementById('step-1').classList.add('hidden-step');
            document.getElementById('step-2').classList.remove('hidden-step');
        }

        function prevStep() {
            document.getElementById('step-2').classList.add('hidden-step');
            document.getElementById('step-1').classList.remove('hidden-step');
        }

        // Avatar preview
        document.getElementById('avatar').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                    document.getElementById('avatarPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });


        document.querySelectorAll('#day, #month, #year').forEach(function(select) {
            select.addEventListener('change', function() {
                const day = document.getElementById('day').value;
                const month = document.getElementById('month').value;
                const year = document.getElementById('year').value;

                // Kiểm tra nếu tất cả các trường đều có giá trị
                if (day && month && year) {
                    // Đẩy dữ liệu vào thẻ input ẩn
                    document.getElementById('date').value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                } else {
                    document.getElementById('date').value = ''; // Nếu không đủ dữ liệu thì xóa giá trị
                }
            });
        });
        // hiển thị thông báo lỗi
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

        // hiển thị thông báo thành công
        $(document).ready(function() {
            const successMessage = "{{ session('success') }}";
            if (successMessage) {
                toastr.success(successMessage, 'Thành công', { // Thay đổi thông báo nếu cần
                    positionClass: 'toast-top-right',
                    timeOut: 5000, // Thời gian tự động ẩn
                    closeButton: true,
                    progressBar: true
                });
            }
        });

document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;

    // Thay đổi biểu tượng tùy theo trạng thái
  
});

    </script>
</body>
</html>
