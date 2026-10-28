<?php

namespace App\Events;

use App\Domain\Questions\Models\QuestionReport;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewReportEvent
{
    use Dispatchable, SerializesModels;

    public QuestionReport $report;

    public function __construct(QuestionReport $report)
    {
        $this->report = $report;
    }
}