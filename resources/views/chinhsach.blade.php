@extends('layouts.app')

@section('title', 'Giới thiệu và chính sách')

@section('content')
<body class="bg-gray-100 text-gray-800">



    <!-- Nội dung chính -->
    <main class="container mx-auto mt-20 p-8 bg-white rounded-lg shadow-lg">
        <!-- Phần Giới Thiệu -->
        <section class="mb-10">
            <h2 class="text-3xl font-semibold text-black">Giới Thiệu</h2>
            <p class="mt-4 text-justify leading-7 text-lg">
    Chào mừng bạn đến với <strong>Connect AI</strong>! Đây là nền tảng kết nối các nhà khoa học, nơi chúng tôi tạo ra môi trường thuận lợi để phát triển các nghiên cứu và khám phá những sáng kiến mới trong lĩnh vực trí tuệ nhân tạo.
</p>
<p class="mt-4 text-justify leading-7 text-lg">
    Tại <strong>Connect AI</strong>, chúng tôi cam kết xây dựng một không gian hợp tác, nơi các nhà khoa học có thể chia sẻ kiến thức, trao đổi ý tưởng và cùng nhau thúc đẩy sự đổi mới trong nghiên cứu khoa học.
</p>


        </section>

        <!-- Phần Chính Sách -->
        <section>
            <h2 class="text-3xl font-semibold text-black mb-6">Chính Sách Chung</h2>
            <div class="mt-6">
                <h3 class="text-2xl font-semibold text-black">1. Chính Sách Bảo Mật</h3>
                <p class="mt-2 text-justify leading-7 text-lg">
                    Chúng tôi hiểu rằng quyền riêng tư là rất quan trọng đối với người dùng. Vì vậy, mọi thông tin cá nhân bạn cung cấp sẽ được bảo vệ cẩn thận và chỉ được sử dụng nhằm nâng cao chất lượng hệ thống. Chúng tôi cam kết không chia sẻ dữ liệu của bạn với bất kỳ bên thứ ba nào mà không có sự đồng ý từ bạn.
                </p>
            </div>

            <div class="mt-8">
                <h3 class="text-2xl font-semibold text-black">2. Điều Khoản Sử Dụng</h3>
                <ul class="list-disc list-inside mt-4 text-justify leading-7 text-lg">
                    <li>Nội dung đăng tải phải tuân thủ các quy định.</li>
                    <li>Người dùng có trách nhiệm đảm bảo thông tin mình chia sẻ là chính xác và không gây tổn hại đến người khác.</li>
                    <li>Chúng tôi có quyền loại bỏ các nội dung không phù hợp mà không cần thông báo trước.</li>
                </ul>
            </div>


            <div class="mt-8">
                <h3 class="text-2xl font-semibold text-black">3. Chính Sách Hỗ Trợ</h3>
                <p class="mt-2 text-justify leading-7 text-lg">
                    Để được hỗ trợ, vui lòng liên hệ với chúng tôi qua email: <a href="mailto:.com" class="text-black font-semibold">ConnectAI@gmail.com</a>.
                </p>
            </div>
        </section>
    </main>


 

</body>
@endsection
