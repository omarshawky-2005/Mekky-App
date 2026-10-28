<?php

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Pdf;
use App\Domain\Content\Models\Lesson;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfService
{
    /**
     * رفع ملف PDF جديد أو استبدال ملف موجود
     */
    public function uploadPdf(Lesson $lesson, UploadedFile $file, string $title, ?int $existingPdfId = null): Pdf
    {
        // التحقق من نوع الملف الحقيقي (MIME Type)
        $mimeType = $file->getMimeType();
        if ($mimeType !== 'application/pdf') {
            throw new \InvalidArgumentException('الملف يجب أن يكون PDF فقط.');
        }

        // التحقق من الحجم (مثلاً 20 ميجابايت كحد أقصى)
        if ($file->getSize() > 20 * 1024 * 1024) {
            throw new \InvalidArgumentException('حجم الملف يجب أن لا يتجاوز 20 ميجابايت.');
        }

        // إذا كان هناك PDF موجود مسبقاً، نقوم بحذفه
        if ($existingPdfId) {
            $existingPdf = Pdf::findOrFail($existingPdfId);
            $this->deleteFile($existingPdf->file_path);
            $existingPdf->delete();
        }

        // توليد اسم فريد للملف
        $fileName = Str::uuid() . '.pdf';
        
        // تخزين الملف في disk مخصص للـ PDFs
        $path = $file->storeAs('pdfs/' . $lesson->id, $fileName, 'public');

        // إنشاء سجل الـ PDF في قاعدة البيانات
        return Pdf::create([
            'lesson_id' => $lesson->id,
            'title' => $title,
            'file_path' => $path,
        ]);
    }

    /**
     * حذف ملف PDF
     */
    public function deletePdf(Pdf $pdf): void
    {
        $this->deleteFile($pdf->file_path);
        $pdf->delete();
    }

    /**
     * حذف الملف الفعلي من التخزين
     */
    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}