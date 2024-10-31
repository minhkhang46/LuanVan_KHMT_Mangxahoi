@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <style>
        #messages {
            position: relative;
            height: calc(100vh - 100px); /* Adjust as needed */
        }
        #message-content {
            overflow-y: auto;
            height: calc(100% - 50px); /* Adjust based on your design */
        }
    </style>

    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row">
            <!-- Danh sách người nhận và tin nhắn mới nhất -->
            <div class="w-full md:w-1/3 h-auto bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-semibold mb-4">Tin nhắn mới nhất</h2>
                
                <!-- Danh sách tin nhắn mới nhất từ mỗi người nhận -->
                @if($messagesWithUsers->isEmpty())
                    <p class="text-gray-500">Không có tin nhắn nào.</p>
                @else
                    <div class="space-y-4">
                        @foreach($messagesWithUsers as $message)
                            @php
                                $receiver = $message->receiver;
                                $sender = $message->sender;
                                $displayUser = $message->is_from_current_user ? $receiver : $sender;
                            @endphp
                            @if ($displayUser)
                            <div class="bg-gray-100 p-4 rounded-lg shadow-md cursor-pointer hover:bg-gray-200" onclick="loadMessages({{ $displayUser->id }})">
                                <div class="flex items-center">
                                    <img src="{{ asset('storage/' . $displayUser->avatar) }}" alt="Avatar" class="h-12 w-12 rounded-full  mr-3">
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ $displayUser->name }}</h3>
                                        <p class="text-gray-500 text-sm">{{ $message->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <p class="mt-2 text-gray-800">{{ $message->content }}</p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Khối hiển thị tin nhắn -->
            <div id="messages" class="w-full md:w-2/3">
                <div id="message-content" class="transition-none animate-none">
                    @if(session('currentReceiverId'))
                        <!-- Hiển thị tin nhắn từ người nhận hiện tại -->
                        @foreach($messagesWithUsers as $message)
                            @if($message->receiver_id == session('currentReceiverId') || $message->sender_id == session('currentReceiverId'))
                                <div class="{{ $message->is_from_current_user ? 'bg-blue-100' : 'bg-gray-100' }} p-4 rounded-lg mb-2">
                                    <p class="text-gray-800">{{ $message->content }}</p>
                                    <span class="text-gray-500 text-sm">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-gray-500">Chọn một người nhận để xem tin nhắn.</p>
                    @endif
                </div>

                <div class="px-4 py-2 border-t bg-white">
                    <form id="message-form" class="flex">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ session('currentReceiverId') }}" id="receiver-id">
                        <textarea id="message-input" name="content" rows="3" class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nhập tin nhắn"></textarea>
                        <button id="send-message" type="button" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Gửi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadMessages(receiverId) {
            $.ajax({
                url: '{{ route("chat", ["receiverId" => "__receiverId__"]) }}'.replace('__receiverId__', receiverId),
                method: 'GET',
                success: function(response) {
                    $('#message-content').data('receiver-id', receiverId);
                    $('#message-content').html(response);
                    $('#message-content').scrollTop($('#message-content')[0].scrollHeight);
                    localStorage.setItem('currentReceiverId', receiverId);
                    $('#receiver-id').val(receiverId);
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
        }

        $('#send-message').click(function(event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của nút gửi
            
            var message = $('#message-input').val();
            var receiverId = $('#receiver-id').val();
            
            if (message.trim() === '' || !receiverId) {
                return;
            }
            
            $.ajax({
                url: '{{ route("messages.send") }}',
                method: 'POST',
                data: {
                    message: message,
                    receiver_id: receiverId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    var messageHtml = `<div class="bg-blue-100 p-4 rounded-lg mb-2">
                                            <p class="text-gray-800">${response.message}</p>
                                            <span class="text-gray-500 text-sm">${response.created_at}</span>
                                        </div>`;
                    $('#message-content').append(messageHtml);
                    $('#message-input').val('');
                    $('#message-content').scrollTop($('#message-content')[0].scrollHeight);
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
        });

        function fetchLatestMessages() {
            var receiverId = $('#message-content').data('receiver-id');
            if (receiverId) {
                $.ajax({
                    url: '{{ route("chat", ["receiverId" => "__receiverId__"]) }}'.replace('__receiverId__', receiverId),
                    method: 'GET',
                    success: function(response) {
                        var newMessages = $(response).find('#message-content').html().trim();
                        var oldMessages = $('#message-content').html().trim();
                        if (oldMessages !== newMessages) {
                            $('#message-content').html(newMessages);
                            $('#message-content').scrollTop($('#message-content')[0].scrollHeight);
                        }
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr.responseText);
                    }
                });
            }
        }

        $(document).ready(function() {
            setInterval(fetchLatestMessages, 1000);

            var savedReceiverId = localStorage.getItem('currentReceiverId');
            if (savedReceiverId) {
                loadMessages(savedReceiverId);
            } else {
                $('#message-content').html('<p class="text-gray-500">Chọn một người nhận để xem tin nhắn.</p>');
            }
        });
    </script>

@endsection
