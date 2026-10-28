<?php

namespace App\Domain\Videos\Jobs;

use App\Domain\Videos\Models\ExplanationVideo;
use App\Domain\Videos\Services\HlsPackager;
use App\Domain\Videos\Services\R2VideoService;
use App\Enums\ProcessingStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ExplanationVideo $video;
    public int $tries = 3;
    public int $maxExceptions = 2;
    public int $timeout = 3600; // ساعة واحدة

    public function __construct(ExplanationVideo $video)
    {
        $this->video = $video;
    }

    public function handle(
        HlsPackager $packager,
        R2VideoService $r2Service
    ): void {
        try {
            // تحديث الحالة
            $this->video->update([
                'processing_status' => ProcessingStatus::PROCESSING,
            ]);

            // 1. تنزيل الفيديو من R2
            $localPath = storage_path('app/temp/' . $this->video->id . '.mp4');
            $this->downloadFromR2($r2Service, $localPath);

            // 2. معالجة الفيديو لـ HLS مع التشفير
            $outputDir = storage_path('app/hls/' . $this->video->id);
            $packager->package($localPath, $outputDir);

            // 3. رفع الملفات لـ R2
            $playlistKey = 'videos/' . $this->video->id . '/playlist.m3u8';
            $this->uploadHlsFiles($r2Service, $outputDir, $this->video->id);

            // 4. الحصول على مدة الفيديو
            $duration = $packager->getDuration($localPath);

            // 5. تحديث الفيديو
            $this->video->update([
                'processing_status' => ProcessingStatus::READY,
                'storage_path' => 'videos/' . $this->video->id,
                'duration_seconds' => $duration,
            ]);

            // 6. تنظيف الملفات المؤقتة
            $this->cleanup($localPath, $outputDir);

            Log::info("Video processed successfully: {$this->video->id}");

        } catch (\Exception $e) {
            Log::error("Video processing failed: {$e->getMessage()}");
            
            $this->video->update([
                'processing_status' => ProcessingStatus::FAILED,
            ]);

            throw $e;
        }
    }

    protected function downloadFromR2(R2VideoService $r2Service, string $localPath): void
    {
        $url = $r2Service->getPresignedGetUrl(
            $this->video->storage_path,
            3600
        );

        $contents = file_get_contents($url);
        Storage::put($localPath, $contents);
    }

    protected function uploadHlsFiles(
        R2VideoService $r2Service,
        string $localDir,
        int $videoId
    ): void {
        $files = glob($localDir . '/*');
        $baseKey = "videos/{$videoId}";

        foreach ($files as $file) {
            $filename = basename($file);
            $key = $baseKey . '/' . $filename;
            $mimeType = pathinfo($file, PATHINFO_EXTENSION) === 'm3u8' 
                ? 'application/vnd.apple.mpegurl' 
                : 'video/mp2t';

            $r2Service->uploadFile($file, $key, $mimeType);
        }
    }

    protected function cleanup(string $videoPath, string $hlsDir): void
    {
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }
        
        if (is_dir($hlsDir)) {
            array_map('unlink', glob($hlsDir . '/*'));
            rmdir($hlsDir);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessVideoJob failed for video {$this->video->id}: " . $exception->getMessage());
    }
}