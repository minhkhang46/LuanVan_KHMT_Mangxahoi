<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ExecutionLog;

class ExecutionTimeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true); // Lấy thời gian bắt đầu

        // Xử lý request
        $response = $next($request);

        $endTime = microtime(true); // Lấy thời gian kết thúc

        // Tính thời gian thực thi
        $executionTime = $endTime - $startTime;

        // Lưu thông tin vào cơ sở dữ liệu
        ExecutionLog::create([
            'function_name' => $request->route()->getName() ?? 'Unnamed Route', // Lấy tên route hoặc route chưa đặt tên
            'execution_time' => $executionTime,
        ]);

        return $response;
    }
}

