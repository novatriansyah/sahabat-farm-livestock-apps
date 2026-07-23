<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

trait BuildsPartnerCharts
{
    /**
     * Attach 4 mandatory charts to the designated worksheet and return chart instances.
     *
     * @param Worksheet $sheet Target worksheet (e.g. DASHBOARD_MITRA)
     * @param array $trendData Data arrays prepared for charting
     * @return Chart[]
     */
    public function buildPartnerCharts(Worksheet $sheet, array $trendData = []): array
    {
        // Populate chart source data cells on the worksheet starting at row 15
        $startRow = 15;
        
        // 1. Write Header for Monthly Trends
        $sheet->setCellValue("A{$startRow}", "Bulan");
        $sheet->setCellValue("B{$startRow}", "Populasi");
        $sheet->setCellValue("C{$startRow}", "Rata ADG (g/hari)");
        $sheet->setCellValue("D{$startRow}", "Kelahiran");

        $months = $trendData['months'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $population = $trendData['population'] ?? [10, 12, 14, 15, 18, 20, 22, 25, 28, 30, 32, 35];
        $adg = $trendData['adg'] ?? [150, 160, 155, 170, 165, 180, 175, 190, 185, 200, 195, 210];
        $births = $trendData['births'] ?? [1, 2, 0, 3, 1, 2, 4, 1, 2, 3, 1, 2];

        $rowCount = count($months);
        for ($i = 0; $i < $rowCount; $i++) {
            $r = $startRow + 1 + $i;
            $sheet->setCellValue("A{$r}", $months[$i]);
            $sheet->setCellValue("B{$r}", $population[$i]);
            $sheet->setCellValue("C{$r}", $adg[$i]);
            $sheet->setCellValue("D{$r}", $births[$i]);
        }

        $endRow = $startRow + $rowCount;

        // 2. Write Generation Composition Data
        $genStartRow = $endRow + 2;
        $sheet->setCellValue("A{$genStartRow}", "Generasi");
        $sheet->setCellValue("B{$genStartRow}", "Jumlah");

        $generations = $trendData['generations'] ?? ['PUREBRED' => 10, 'F1' => 15, 'F2' => 8, 'F3' => 4];
        $genIdx = 0;
        foreach ($generations as $genName => $genCount) {
            $r = $genStartRow + 1 + $genIdx;
            $sheet->setCellValue("A{$r}", $genName);
            $sheet->setCellValue("B{$r}", $genCount);
            $genIdx++;
        }
        $genEndRow = $genStartRow + count($generations);

        // --- CHART 1: Tren Populasi Ternak (12 Bulan) - Line Chart ---
        $categories1 = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$A\$" . ($startRow + 1) . ":\$A\${$endRow}", null, $rowCount)];
        $values1     = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheet->getTitle()}'!\$B\$" . ($startRow + 1) . ":\$B\${$endRow}", null, $rowCount)];
        $series1 = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values1) - 1),
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$B\${$startRow}", null, 1)],
            $categories1,
            $values1
        );
        $plotArea1 = new PlotArea(null, [$series1]);
        $legend1 = new Legend(Legend::POSITION_TOPRIGHT, null, false);
        $chart1 = new Chart('chart_populasi', new Title('1. Tren Populasi Ternak (12 Bulan)'), $legend1, $plotArea1);
        $chart1->setTopLeftPosition('E2');
        $chart1->setBottomRightPosition('M14');

        // --- CHART 2: Tren Bobot Rata-rata / ADG - Line Chart ---
        $categories2 = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$A\$" . ($startRow + 1) . ":\$A\${$endRow}", null, $rowCount)];
        $values2     = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheet->getTitle()}'!\$C\$" . ($startRow + 1) . ":\$C\${$endRow}", null, $rowCount)];
        $series2 = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values2) - 1),
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$C\${$startRow}", null, 1)],
            $categories2,
            $values2
        );
        $plotArea2 = new PlotArea(null, [$series2]);
        $legend2 = new Legend(Legend::POSITION_TOPRIGHT, null, false);
        $chart2 = new Chart('chart_adg', new Title('2. Tren ADG Rata-rata (g/hari)'), $legend2, $plotArea2);
        $chart2->setTopLeftPosition('E16');
        $chart2->setBottomRightPosition('M28');

        // --- CHART 3: Kelahiran per Bulan - Bar Chart ---
        $categories3 = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$A\$" . ($startRow + 1) . ":\$A\${$endRow}", null, $rowCount)];
        $values3     = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheet->getTitle()}'!\$D\$" . ($startRow + 1) . ":\$D\${$endRow}", null, $rowCount)];
        $series3 = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values3) - 1),
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$D\${$startRow}", null, 1)],
            $categories3,
            $values3
        );
        $plotArea3 = new PlotArea(null, [$series3]);
        $legend3 = new Legend(Legend::POSITION_TOPRIGHT, null, false);
        $chart3 = new Chart('chart_kelahiran', new Title('3. Jumlah Kelahiran per Bulan'), $legend3, $plotArea3);
        $chart3->setTopLeftPosition('E30');
        $chart3->setBottomRightPosition('M42');

        // --- CHART 4: Komposisi Generasi - Pie Chart ---
        $categories4 = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$A\$" . ($genStartRow + 1) . ":\$A\${$genEndRow}", null, count($generations))];
        $values4     = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'{$sheet->getTitle()}'!\$B\$" . ($genStartRow + 1) . ":\$B\${$genEndRow}", null, count($generations))];
        $series4 = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($values4) - 1),
            [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'{$sheet->getTitle()}'!\$B\${$genStartRow}", null, 1)],
            $categories4,
            $values4
        );
        $plotArea4 = new PlotArea(null, [$series4]);
        $legend4 = new Legend(Legend::POSITION_RIGHT, null, false);
        $chart4 = new Chart('chart_generasi', new Title('4. Komposisi Generasi Ternak'), $legend4, $plotArea4);
        $chart4->setTopLeftPosition('E44');
        $chart4->setBottomRightPosition('M56');

        $sheet->addChart($chart1);
        $sheet->addChart($chart2);
        $sheet->addChart($chart3);
        $sheet->addChart($chart4);

        return [$chart1, $chart2, $chart3, $chart4];
    }
}
