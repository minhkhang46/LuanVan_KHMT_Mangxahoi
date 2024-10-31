@extends('layouts.app')
@section('title', 'Liên kết người dùng')
@section('content')

<!-- Đặt các lớp Tailwind cho phần thân trang -->

    <!-- Vùng chứa cho đồ thị -->
    <div class="flex flex-col items-center justify-center w-full h-5/6 p-10" style="height: 100%;">
    <!-- Vùng chứa bảng ký hiệu -->
    <!-- <div class="w-full max-w-xs mb-5 p-4 bg-white border border-gray-300 rounded-lg shadow-lg">
        <h2 class="text-center font-semibold text-lg mb-2">Bảng ký hiệu</h2>
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-700">
                    <th class="py-2 px-4">Màu</th>
                    <th class="py-2 px-4">Ý nghĩa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4"><span class="inline-block w-4 h-4 bg-red-500 rounded-full"></span></td>
                    <td class="py-2 px-4">Người dùng hiện tại</td>
                </tr>
                <tr>
                    <td class="py-2 px-4"><span class="inline-block w-4 h-4 bg-green-500 rounded-full"></span></td>
                    <td class="py-2 px-4">Người dùng gần nhất</td>
                </tr>
                <tr>
                    <td class="py-2 px-4"><span class="inline-block w-4 h-1 bg-gray-300"></span></td>
                    <td class="py-2 px-4">Liên kết giữa các người dùng</td>
                </tr>
            </tbody>
        </table>
    </div> -->

    <!-- Vùng chứa cho đồ thị -->
    <div id="sigma-container" class="w-full border bg-white p-10 border-gray-300 rounded-lg shadow-lg" style="height: 100%;"></div>
</div>




<script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/1.0.3/sigma.min.js"></script>
<script>
// Lấy dữ liệu từ PHP
const embeddings = @json($embeddings);
const sessionId = "{{ session('id') }}"; // Lấy session id từ PHP
const userMap = @json($userMap); // Truyền biến userMap vào JavaScript

// Tạo graph object cho Sigma.js
const graph = {
    nodes: [],
    edges: []
};

// Tạo Set để theo dõi user_ids
const userIds = new Set();

// Duyệt qua dữ liệu để tạo nodes và edges
embeddings.forEach(row => {
    const userId = String(row[0]).trim(); // Giả sử cột đầu tiên là user_id
    const nearestUserId = String(row[1]).trim(); // Giả sử cột thứ hai là nearest_user_id

    // Kiểm tra xem userId có trùng với sessionId không
    if (userId === sessionId) {
        // Thêm node cho user_id
        if (userId && !userIds.has(userId)) {
            graph.nodes.push({
                id: userId,
                label: userMap[userId] || `User ${userId}`, // Sử dụng tên người dùng nếu có
                x: Math.random() * 1, // Giảm kích thước random
                y: Math.random() * 1, // Giảm kích thước random
                size: 1000, // Tăng kích thước của node
                color: '#f00' // Màu cho node
            });
            userIds.add(userId);
        }

        // Thêm node cho nearest_user_id nếu chưa có
        if (nearestUserId && !userIds.has(nearestUserId)) {
            graph.nodes.push({
                id: nearestUserId,
                label: userMap[nearestUserId] || `User ${nearestUserId}`, // Sử dụng tên người dùng nếu có
                x: Math.random() * 1 + 0, // Giảm kích thước random
                y: Math.random() * 1 + 0, // Giảm kích thước random
                size: 1000, // Tăng kích thước của node
                color: '#0f0' // Màu cho node
            });
            userIds.add(nearestUserId);
        }

        // Thêm edge giữa user_id và nearest_user_id (đồ thị có hướng)
        if (nearestUserId) {
            graph.edges.push({
                id: `edge_${userId}_${nearestUserId}`,
                source: userId,
                target: nearestUserId,
                color: '#ccc',
                size: 5, // Tăng độ dày của edge
                type: 'arrow', // Đánh dấu edge là có hướng
            });
        }
    }
});

// Khởi tạo Sigma.js
const container = document.getElementById('sigma-container');
const sigmaInstance = new sigma({
    graph: graph,
    container: container,
    settings: {
        defaultNodeColor: '#ec51448',
        edgeLabelSize: 'proportional', // Kích thước nhãn cạnh
        labelThreshold: 0, // Hiển thị nhãn ở tất cả các cạnh
        edgesHoverColor: 'red', // Màu khi hover vào cạnh
        edgesColor: 'default', // Màu mặc định của cạnh
        drawEdges: true, // Vẽ cạnh
        drawLabels: true, // Vẽ nhãn
    }
});

// Hàm để vẽ node là hình tròn
sigma.canvas.nodes.image = function(node, context, settings) {
    const prefix = settings('prefix') || '';
    const size = node[prefix + 'size'];
    const x = node[prefix + 'x'];
    const y = node[prefix + 'y'];

    context.save();
    context.beginPath();
    context.arc(x, y, size, 0, 2 * Math.PI, false);
    context.fillStyle = node.color; // Sử dụng màu của node
    context.fill();
    context.restore();
};

</script>

@endsection
