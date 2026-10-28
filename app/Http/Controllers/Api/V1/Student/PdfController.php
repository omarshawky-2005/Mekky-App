<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Domain\Content\Models\Pdf;
use App\Domain\Content\Models\PdfView;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    /**
     * بث ملف PDF مع دعم Range Requests
     */
    public function showFile(Request $request, Pdf $pdf): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // 1. فحص الصلاحية (يمكن إضافة Logic هنا لاحقاً، مثلاً التأكد من أن الطالب مسجل في الكورس)
        // if (!$request->user()->hasAccessTo($pdf->lesson->course)) { abort(403); }

        $path = Storage::disk('public')->path($pdf->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        // Laravel's response() يدعم Range requests تلقائياً
        return Storage::disk('public')->response($pdf->file_path);
    }

    /**
     * تتبع فتح الطالب للملف (Upsert)
     */
    public function trackView(Request $request, Pdf $pdf): JsonResponse
    {
        PdfView::updateOrCreate(
            [
                'student_id' => $request->user()->id,
                'pdf_id' => $pdf->id,
            ],
            [
                'last_opened_at' => now(),
            ]
        );

        return ApiResponse::success(null, 'تم تسجيل المشاهدة');
    }
}