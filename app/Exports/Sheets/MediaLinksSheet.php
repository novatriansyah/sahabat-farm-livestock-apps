<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use App\Schemas\AnimalTemplateSchema;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class MediaLinksSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function title(): string
    {
        return 'MEDIA_LINKS';
    }

    public function query()
    {
        return Animal::query()->whereNotNull('google_drive_link');
    }

    public function headings(): array
    {
        return [
            'animal_id',
            'tag_id',
            AnimalTemplateSchema::CANONICAL_GDRIVE_FIELD,
            'created_at',
        ];
    }

    public function map($animal): array
    {
        return [
            (string) $animal->id,
            (string) $animal->tag_id,
            (string) AnimalTemplateSchema::extractGDriveUrl($animal),
            $animal->created_at ? date('Y-m-d H:i:s', strtotime($animal->created_at)) : '',
        ];
    }

    public function columnFormats(): array
    {
        return ['B' => NumberFormat::FORMAT_TEXT];
    }
}
