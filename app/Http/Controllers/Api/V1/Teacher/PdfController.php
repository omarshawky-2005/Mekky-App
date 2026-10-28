<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Pdf;
use App\Domain\Content\Services\PdfService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StorePdfRequest;
use App\Http\Resources\Content\PdfResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class PdfController extends Controller
{
    protected PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function store(StorePdfRequest $request, Lesson $lesson): JsonResponse
    {
        // نتخطى Gate مؤقتاً
        // Gate::authorize('update', $lesson->course);

        $pdf = $this->pdfService->uploadPdf(
            $lesson,
            $request->file('file'),
            $request->title,
            $request->existing_pdf_id
        );

        return ApiResponse::success(new PdfResource($pdf), 'تم رفع الـ PDF بنجاح', 201);
    }

    public function destroy(Pdf $pdf): JsonResponse
    {
        // Gate::authorize('delete', $pdf->lesson->course);

        $this->pdfService->deletePdf($pdf);

        return ApiResponse::success(null, 'تم حذف الـ PDF بنجاح');
    }
}