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
        return Animal::query()->whereNotNull('gdrive_folder_url')->orWhereNotNull('photo_url');
    }

    public function headings(): array
    {
        return [
            'animal_id',
            'tag_id',
            'gdrive_folder_url',
            'photo_url',
            'created_at',
        ];
    }

    public function map($animal): array
    {
        return [
            (string) $animal->id,
            '="' . (string) $animal->tag_id . '"',
            $animal->gdrive_folder_url,
            $animal->photo_url,
            $animal->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
