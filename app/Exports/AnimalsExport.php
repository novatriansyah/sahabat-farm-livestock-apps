<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\MasterBreed;
use App\Models\MasterCategory;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;

class AnimalsExport implements WithHeadings, WithTitle, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            'tag_id',           // A
            'gender',           // B
            'breed_name',       // C
            'birth_date',       // D
            'initial_weight_kg',// E
            'physical_status',  // F
            'acquisition_type', // G
            'purchase_price',   // H
            'location_name',    // I
            'partner_name',     // J
            'generation',       // K
            'necklace_color',   // L
        ];
    }

    public function title(): string
    {
        return 'Template Import Ternak';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rowCount = 1000; // Validation up to 1000 rows

                // --- 1. POPULATE REFERENCE DATA (Hidden Columns) ---
                // Z: Gender
                // AA: Acquisition
                // AB: Generation
                // AC: Breeds
                // AD: Categories
                // AE: Locations
                // AF: Partners
                // AG: Phys Status

                // Static Lists
                $genders = ['JANTAN', 'BETINA'];
                $acquisitions = ['BELI', 'HASIL_TERNAK'];
                $generations = ['F1', 'F2', 'F3', 'PURE', 'CROSS'];
                
                // Dynamic DB Lists
                $breeds = MasterBreed::pluck('name')->toArray();
                $categories = MasterCategory::pluck('name')->toArray();
                $locations = MasterLocation::pluck('name')->toArray();
                $partners = MasterPartner::pluck('name')->toArray();
                $statuses = MasterPhysStatus::pluck('name')->toArray();

                $this->populateColumn($sheet, 'Z', $genders);
                $this->populateColumn($sheet, 'AA', $acquisitions);
                $this->populateColumn($sheet, 'AB', $generations);
                $this->populateColumn($sheet, 'AC', $breeds);
                $this->populateColumn($sheet, 'AD', $categories);
                $this->populateColumn($sheet, 'AE', $locations);
                $this->populateColumn($sheet, 'AF', $partners);
                $this->populateColumn($sheet, 'AG', $statuses);

                // --- 2. APPLY VALIDATION ---
                
                // Col B: Gender (Z)
                $this->setDropdown($sheet, 'B2:B'.$rowCount, '$Z$1:$Z$' . count($genders));

                // Col C: Breed (AC)
                if (count($breeds) > 0)
                    $this->setDropdown($sheet, 'C2:C'.$rowCount, '$AC$1:$AC$' . count($breeds));

                // Col F: Phys Status (AG)
                if (count($statuses) > 0)
                    $this->setDropdown($sheet, 'F2:F'.$rowCount, '$AG$1:$AG$' . count($statuses));

                // Col G: Acquisition (AA)
                $this->setDropdown($sheet, 'G2:G'.$rowCount, '$AA$1:$AA$' . count($acquisitions));

                // Col I: Location (AE)
                if (count($locations) > 0)
                    $this->setDropdown($sheet, 'I2:I'.$rowCount, '$AE$1:$AE$' . count($locations));
                
                // Col J: Partner (AF)
                if (count($partners) > 0)
                    $this->setDropdown($sheet, 'J2:J'.$rowCount, '$AF$1:$AF$' . count($partners));

                // Col K: Generation (AB)
                $this->setDropdown($sheet, 'K2:K'.$rowCount, '$AB$1:$AB$' . count($generations));

                 // --- 3. HIDE REFERENCE COLUMNS (Triple Check) ---
                 $hiddenCols = ['Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG'];
                 foreach ($hiddenCols as $col) {
                     $sheet->getColumnDimension($col)->setVisible(false);
                     $sheet->getColumnDimension($col)->setCollapsed(true);
                     $sheet->getColumnDimension($col)->setWidth(0);
                 }
            },
        ];
    }

    private function populateColumn($sheet, $colLetter, $data)
    {
        foreach ($data as $index => $value) {
            $row = $index + 1;
            $sheet->setCellValue($colLetter . $row, $value);
        }
    }

    private function setDropdown($sheet, $cellRange, $formula)
    {
        $validation = $sheet->getCell(explode(':', $cellRange)[0])->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1($formula);

        // Apply to all rows
        for ($i = 2; $i <= 1000; $i++) {
            $sheet->getCell(substr($cellRange, 0, 1) . $i)->setDataValidation(clone $validation);
        }
    }
}

