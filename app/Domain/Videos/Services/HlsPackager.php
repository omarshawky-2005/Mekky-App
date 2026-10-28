<?php

namespace App\Domain\Videos\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class HlsPackager
{
    // المسار الكامل لـ ffmpeg و ffprobe على جهازك
    protected string $ffmpegPath = 'E:\ffmpeg-9.0.2-essentials_build\bin\ffmpeg.exe';
    protected string $ffprobePath = 'E:\ffmpeg-9.0.2-essentials_build\bin\ffprobe.exe';
    
    protected string $keyDir;
    protected string $keyName;
    protected string $keyInfoFile;

    public function __construct()
    {
        $this->keyDir = storage_path('app/hls-keys');
        $this->keyName = Str::random(32);
        $this->keyInfoFile = $this->keyDir . '/keyinfo.txt';
        
        if (!is_dir($this->keyDir)) {
            mkdir($this->keyDir, 0755, true);
        }
    }

    /**
     * تحويل الفيديو لـ HLS مع تشفير AES-128
     */
    public function package(string $inputPath, string $outputDir): void
    {
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // توليد مفتاح التشفير
        $keyFile = $this->keyDir . '/' . $this->keyName . '.key';
        $this->generateKey($keyFile);

        // إنشاء key info file
        $this->createKeyInfoFile($keyFile);

        // ffmpeg command لـ HLS مع التشفير
        // ملاحظة: تم إضافة المسار الكامل لـ ffmpeg واستخدام علامات التنصيص لتجنب مشاكل المسافات
        $command = sprintf(
            '"%s" -i "%s" ' .
            '-c:v libx264 -c:a aac ' .
            '-hls_time 10 ' .
            '-hls_playlist_type vod ' .
            '-hls_key_info_file "%s" ' .
            '-hls_segment_filename "%s/segment_%%03d.ts" ' .
            '"%s/playlist.m3u8" 2>&1',
            $this->ffmpegPath,
            $inputPath,
            $this->keyInfoFile,
            $outputDir,
            $outputDir
        );

        $result = Process::timeout(3600)->run($command);

        if (!$result->successful()) {
            throw new \RuntimeException("FFmpeg failed: " . $result->errorOutput());
        }
    }

    protected function generateKey(string $keyFile): void
    {
        $bytes = openssl_random_pseudo_bytes(16, $strong);
        if (!$strong) {
            throw new \RuntimeException('Could not generate strong key');
        }
        
        file_put_contents($keyFile, $bytes);
    }

    protected function createKeyInfoFile(string $keyFile): void
    {
        // Key info file format:
        // key-uri
        // key-file-path
        // iv (optional)
        
        // تم إصلاح الخطأ: استخدام keyName بدلاً من video->id
        $keyUri = '/api/v1/videos/keys/' . $this->keyName;
        $content = $keyUri . "\n" . $keyFile . "\n";
        
        file_put_contents($this->keyInfoFile, $content);
    }

    /**
     * الحصول على مفتاح التشفير
     */
    public function getKey(string $keyName): string
    {
        $keyFile = $this->keyDir . '/' . $keyName . '.key';
        if (!file_exists($keyFile)) {
            throw new \RuntimeException("Key file not found: {$keyFile}");
        }
        return file_get_contents($keyFile);
    }

    /**
     * الحصول على مدة الفيديو
     */
    public function getDuration(string $videoPath): int
    {
        $command = sprintf(
            '"%s" -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "%s"',
            $this->ffprobePath,
            $videoPath
        );

        $result = Process::run($command);
        return (int) round(floatval(trim($result->output())));
    }
}