<?php

namespace App\Exports;

use App\Models\NewsletterSubscriber;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewsletterExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return NewsletterSubscriber::select('email', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['Email', 'Subscribed At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

