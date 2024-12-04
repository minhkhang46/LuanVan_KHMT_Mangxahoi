<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use App\Models\ExecutionLog;
class CheckForNewData extends Command
{
    protected $signature = 'data:check-new'; // Đăng ký lệnh
    protected $description = 'Kiểm tra dữ liệu mới và chạy script Python nếu cần';

    public function handle()
    {
        // Bắt đầu đo thời gian
        $startTime = microtime(true);

        // Kiểm tra nếu có dữ liệu mới
        $latestUser = DB::table('user_nds')->latest('id')->first();

        if (!$latestUser) {
            $this->info('Không tìm thấy dữ liệu trong bảng user_nds.');
            return;
        }

        // Đường dẫn đến file last_run.txt
        $lastRunFile = 'C:\\xampp\\htdocs\\luanvan_tn\\last_run.txt';

        // Đọc ID mới nhất từ file
        $lastRunId = file_exists($lastRunFile) ? file_get_contents($lastRunFile) : null;

        // In ra giá trị để kiểm tra
        $this->info('ID mới nhất đã lưu: ' . ($lastRunId ?? 'Không có'));
        $this->info('ID của dữ liệu mới nhất: ' . $latestUser->id);

        // Nếu có ID mới hơn ID đã lưu, chạy script
        if (!$lastRunId || $latestUser->id > $lastRunId) {
            $this->info('Dữ liệu mới đã được phát hiện. Chạy script Python...');

            // Chạy script Python
            $process = new Process(['python', 'vecto.py']);
            $process->run();

            // Kiểm tra kết quả chạy script
            if ($process->isSuccessful()) {
                $this->info('Script đã chạy thành công.');
                // Cập nhật ID mới nhất vào last_run.txt
                file_put_contents($lastRunFile, $latestUser->id);
                $this->info('ID mới nhất đã được cập nhật.');
            } else {
                $this->error('Đã xảy ra lỗi khi chạy script Python: ' . $process->getErrorOutput());
            }
        } else {
            $this->info('Không có dữ liệu mới. Không cần chạy lại script.');
        }

        // Kết thúc đo thời gian
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
    
    
        ExecutionLog::create([
            'function_name' => 'data:check-new',
            'execution_time' => $executionTime,
        ]);
    
      
        // Hiển thị thời gian thực thi
        $this->info('Thời gian thực thi lệnh: ' . round($executionTime, 2) . ' giây.');
        \Log::info('Lệnh data:check-new thực thi trong ' . round($executionTime, 2) . ' giây.');
    }
}
