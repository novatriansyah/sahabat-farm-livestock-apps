<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MediaLinksSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
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
            'google_drive_link',
            'created_at',
        ];
    }

    public function map($animal): array
    {
        return [
            (string) $animal->id,
            '="' . (string) $animal->tag_id . '"',
            $animal->google_drive_link,
            $animal->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
