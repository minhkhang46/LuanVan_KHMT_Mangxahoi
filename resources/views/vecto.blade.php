@extends('layouts.app')
@section('title', 'Liên kết người dùng')
@section('content')

<style>
    /* Container chính */
    .graph-container {
        display: flex;
        flex-wrap: wrap; /* Đảm bảo hiển thị tốt trên màn hình nhỏ */
        justify-content: space-between;
        gap: 20px;
        margin-top: 40px;
        margin-left: 20px;
        margin-right: 20px;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    /* Đồ thị */
    .graph {
        flex: 1 1 60%; /* Ưu tiên chiếm 60% không gian */
        max-width: 100%;
        height: 80vh;
        background-color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    /* Gợi ý kết bạn */
    .legend {
        flex: 1 1 15%;
        background-color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 20px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .legend h3 {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .legend ul {
        list-style: none;
        padding: 0;
    }

    .legend li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        margin-bottom: 15px;
        border-radius: 12px;
        transition: background-color 0.3s ease;
        cursor: pointer;
        background-color:  rgb(239 246 255);
    }

    .legend li:hover {
        background-color: rgb(191 219 254);
    }

    .legend li img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-right: 15px;
        border-width: 2px;
    }

    .legend .user-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: 'black';
        flex-grow: 1;
    }
    .sigma-node {
    width: 40px; /* Đảm bảo kích thước nút vừa đủ */
    height: 40px;
    border-radius: 50%; /* Đảm bảo nút có dạng tròn */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden; /* Ẩn bất kỳ phần nào của hình ảnh ngoài vùng tròn */
}

.sigma-node img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Đảm bảo ảnh trong nút có thể thay đổi kích thước đúng */
}

.sigma-node {
    transition: all 0.3s ease; /* Thêm hiệu ứng chuyển động khi thao tác */
}

.sigma-node:hover {
    cursor: pointer; /* Thêm hiệu ứng khi hover */
    background-color: rgba(255, 0, 0, 0.2); /* Màu nền mờ khi hover */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Tăng độ bóng khi hover */
}
.sigma-node.selected {
    background-color: #FF0000; /* Đổi màu khi người dùng chọn */
    border: 2px solid #FFFFFF; /* Thêm viền sáng để làm nổi bật */
    transform: scale(1.2); /* Tăng kích thước để dễ nhận diện */
}


.sigma-node:hover {
    cursor: pointer; /* Thêm hiệu ứng khi hover */
    background-color: rgba(255, 0, 0, 0.2); /* Màu nền mờ khi hover */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Tăng độ bóng khi hover */
}

    /* .legend button {
        background-color: #4C6D84;
        color: #ffffff;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .legend button:hover {
        background-color: #2b4a62;
    } */
    #tooltip {
    position: absolute;
    display: none;
    background-color: #f5f5f5;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    color: #333;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    pointer-events: none; /* Đảm bảo tooltip không gây trở ngại đến thao tác chuột */
}

</style>

<div class="graph-container">
    <!-- Đồ thị -->
    <div id="sigma-container" class="graph"></div>

    <div class="legend">
    <!-- Phần 1: Danh sách bạn bè -->
    <div class="friends-section bg-gray-100 p-4 rounded-lg shadow-md mb-4">
        <h3 class="text-xl font-bold mb-3">Danh sách bạn bè</h3>
        <ul>
            @foreach ($suggestedUserMap as $id => $name)
                @if (isset($friendStatusMap[$id]) && $friendStatusMap[$id] == 1)
                    <li class="flex items-center mb-3">
                        <img src="{{ $suggestedAvatarMap[$id] }}" alt="Avatar" class="w-10 h-10 rounded-full mr-3">
                        <span class="user-name font-medium">{{ $name }}</span>
                        <button class="ml-auto bg-green-200 text-green-700 px-4 py-1 rounded-md" disabled>Bạn bè</button>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    <!-- Phần 2: Gợi ý kết bạn -->
    <div class="suggested-section bg-gray-100 p-4 rounded-lg shadow-md">
        <h3 class="text-xl font-bold mb-3">Gợi ý kết bạn</h3>
        <ul>
            @foreach ($suggestedUserMap as $id => $name)
                @if (!isset($friendStatusMap[$id]) || $friendStatusMap[$id] == 0)
                    <li class="flex items-center mb-3">
                        <img src="{{ $suggestedAvatarMap[$id] }}" alt="Avatar" class="w-10 h-10 rounded-full mr-3">
                        <span class="user-name font-medium">{{ $name }}</span>
                        @if (isset($friendStatusMap[$id]) && $friendStatusMap[$id] == 0)
                            <button class="ml-auto bg-yellow-200 text-yellow-700 px-4 py-1 rounded-md" disabled>Đã gửi yêu cầu</button>
                        @else
                            <form action="{{ route('sendFriendRequest', $id) }}" method="POST" class="ml-auto">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Thêm bạn bè
                                </button>
                            </form>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/1.0.3/sigma.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/1.0.3/plugins/sigma.layout.forceAtlas2.min.js"></script>

<script>
    const embeddings = @json($embeddings);
    const sessionId = "{{ session('id') }}";
    const userMap = @json($userMap);

    const graph = {
        nodes: [],
        edges: []
    };

    const userIds = new Set();
    const relatedUsers = new Set(); // Set để lưu các user liên quan đến người dùng đã chọn

    embeddings.forEach(row => {
        const userId = String(row[0]).trim();
        const nearestUserId = String(row[1]).trim();

        if (userId === 'user_id' || nearestUserId === 'nearest_user_id') return;

        // Tạo nút cho userId
        if (userId && !userIds.has(userId)) {
            graph.nodes.push({
                id: userId,
                label: userMap[userId] || `User ${userId}`,
                x: Math.random(),
                y: Math.random(),
                size: 10,
                color: (userId === sessionId) ? '#BB0000' : '#4C6D84',
                image: `path_to_images/${userId}.jpg`,  // Hình ảnh đại diện người dùng
                shadowColor: '#888888', // Bóng cho các nút
                shadowBlur: 5, // Độ mờ của bóng
            });
            userIds.add(userId);
        }

        // Tạo nút cho nearestUserId
        if (nearestUserId && !userIds.has(nearestUserId)) {
            graph.nodes.push({
                id: nearestUserId,
                label: userMap[nearestUserId] || `User ${nearestUserId}`,
                x: Math.random(),
                y: Math.random(),
                size: 10,
                color: (nearestUserId === sessionId) ? '#BB0000' : '#4C6D84',
                image: `path_to_images/${nearestUserId}.jpg`,  // Hình ảnh đại diện người dùng
                shadowColor: '#888888', // Bóng cho các nút
                shadowBlur: 5, // Độ mờ của bóng
            });
            userIds.add(nearestUserId);
        }

        // Tạo cạnh giữa các người dùng
        if (nearestUserId) {
            graph.edges.push({
                id: `edge_${userId}_${nearestUserId}`,
                source: userId,
                target: nearestUserId,
                color: (userId === sessionId || nearestUserId === sessionId) ? '#BB0000' : '#888888',
                size: 2,
                type: 'arrow'
            });
        }
    });

    const container = document.getElementById('sigma-container');
    const sigmaInstance = new sigma({
        graph: graph,
        container: container,
        settings: {
            defaultNodeColor: '#ec51448',
            edgeLabelSize: 'proportional',
            labelThreshold: 0,
            drawEdges: true,
            drawLabels: true,
        }
    });

    // Áp dụng ForceAtlas2
    sigmaInstance.startForceAtlas2({
        worker: true, // Sử dụng Web Worker nếu trình duyệt hỗ trợ
        barnesHutOptimize: true, // Tối ưu hóa cho các đồ thị lớn
        slowDown: 10, // Tăng giá trị để giản rộng hơn
        gravity: 0, // Tăng lực hấp dẫn để các node không phân tán quá xa
    });

    // Tự động dừng sau 10 giây
    setTimeout(() => {
        sigmaInstance.stopForceAtlas2();
    }, 10000);

    // Hiển thị thông tin chi tiết khi hover qua node
    sigmaInstance.bind('overNode', function(event) {
        const node = event.data.node;
        const tooltip = document.getElementById('tooltip');
        tooltip.innerHTML = `User: ${userMap[node.id] || node.id}`;
        tooltip.style.display = 'block';
        tooltip.style.left = `${event.data.captor.clientX + 10}px`;
        tooltip.style.top = `${event.data.captor.clientY + 10}px`;
    });

    sigmaInstance.bind('outNode', function() {
        const tooltip = document.getElementById('tooltip');
        tooltip.style.display = 'none';
    });

    // Lắng nghe sự kiện click và làm nổi bật các nút liên quan
    sigmaInstance.bind('clickNode', function(event) {
        const clickedNodeId = event.data.node.id;
        relatedUsers.clear(); // Xóa các user liên quan cũ

        // Tìm các nút có liên quan đến nút được chọn (tức là các nút có kết nối với nút đó)
        graph.edges.forEach(edge => {
            if (edge.source === clickedNodeId) {
                relatedUsers.add(edge.target);
            }
            if (edge.target === clickedNodeId) {
                relatedUsers.add(edge.source);
            }
        });

        // Cập nhật màu sắc các nút liên quan
        graph.nodes.forEach(node => {
            if (relatedUsers.has(node.id)) {
                node.color = '#FF0000';  // Màu nổi bật cho các nút liên quan
            } else {
                node.color = (node.id === sessionId) ? '#BB0000' : '#4C6D84';
            }
        });

        // Cập nhật lại đồ thị để áp dụng các thay đổi
        sigmaInstance.refresh();
    });
</script>

<!-- Tooltip để hiển thị thông tin chi tiết -->
<div id="tooltip" style="position: absolute; display: none; background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></div>


@endsection
