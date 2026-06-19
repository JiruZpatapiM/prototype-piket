<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelTemplateService
{
    public function generateTemplate($jenisPiket)
    {
        $templateData = \App\Models\Template::where('jenis_piket', $jenisPiket)->first();
        if (!$templateData) {
            throw new \Exception("Template not found for " . $jenisPiket);
        }

        $spreadsheet = new Spreadsheet();
        
        if ($jenisPiket == 'Daily') {
            return $this->generateDailyTemplate($spreadsheet, $templateData);
        }

        $sheet = $spreadsheet->getActiveSheet();

        // 1. Column Widths
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(5);   // Letter
        $sheet->getColumnDimension('C')->setWidth(45);  // Checklist item
        $sheet->getColumnDimension('D')->setWidth(10);  // Kondisi 1
        $sheet->getColumnDimension('E')->setWidth(10);  // Kondisi 2
        $sheet->getColumnDimension('F')->setWidth(12);  // Metode 1
        $sheet->getColumnDimension('G')->setWidth(12);  // Metode 2

        // Common Styles
        $styleHeaderSection = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']],
        ];
        $styleTitle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
        ];
        $styleSubHeader = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
        ];
        $styleBorderAll = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $styleCenter = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $styleGray = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
            'font' => ['bold' => true]
        ];

        // Header Top
        $sheet->setCellValue('A1', 'Branch: ............................................');
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('E1', 'Tanggal: ............................................');
        $sheet->mergeCells('E1:G1');

        // Main Title
        $titleText = $jenisPiket == 'Daily' ? 'MONITORING PIKET DIREKSI - WEEKEND TAHUN 2026' : 'MONITORING PIKET DIREKSI DALAM RANGKA ANGKUTAN LEBARAN 2026';
        $sheet->setCellValue('A2', $titleText);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2:G2')->applyFromArray($styleTitle);
        $sheet->getStyle('A2:G2')->applyFromArray($styleBorderAll);

        $row = 3;

        foreach ($templateData->content as $section) {
            $displayCategory = preg_replace('/^[A-Z][\.\s]+|^[A-Z]\.?\s*/i', '', $section['section_title']);
            $sheet->setCellValue("A{$row}", strtoupper($displayCategory));
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleHeaderSection);
            $row++;

            if ($section['is_resume']) {
                $startHeaderRow = $row;
                $sheet->setCellValue("A{$row}", "No"); 
                $sheet->setCellValue("B{$row}", "Item Pengecekan / Uraian"); $sheet->mergeCells("B{$row}:D{$row}");
                $sheet->setCellValue("E{$row}", "Keterangan / Temuan"); $sheet->mergeCells("E{$row}:G{$row}");
                $sheet->getStyle("A{$startHeaderRow}:G{$row}")->applyFromArray($styleSubHeader);
                $row++;

                if (!empty($section['subsections'][0]['items'])) {
                    foreach ($section['subsections'][0]['items'] as $idx => $item) {
                        $sheet->setCellValue("A{$row}", $idx + 1);
                        $sheet->setCellValue("B{$row}", $item); $sheet->mergeCells("B{$row}:D{$row}");
                        $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                        $sheet->mergeCells("E{$row}:G{$row}");
                        $sheet->getStyle("A{$row}")->applyFromArray($styleCenter);
                        $sheet->getRowDimension($row)->setRowHeight(40);
                        $row++;
                    }
                }
                $sheet->getStyle("A{$startHeaderRow}:G".($row-1))->applyFromArray($styleBorderAll);
                $row++;
            } else {
                $startHeaderRow = $row;
                $sheet->setCellValue("A{$row}", "No"); $sheet->mergeCells("A{$row}:A".($row+1));
                $sheet->setCellValue("B{$row}", "Item Pengecekan / Uraian"); $sheet->mergeCells("B{$row}:C".($row+1));
                $sheet->setCellValue("D{$row}", "Kondisi"); $sheet->mergeCells("D{$row}:E{$row}");
                $sheet->setCellValue("F{$row}", "Metode"); $sheet->mergeCells("F{$row}:G{$row}");
                $row++;

                $firstSub = $section['subsections'][0] ?? null;
                $k1 = $firstSub['kondisi_options'][0] ?? 'Baik';
                $k2 = $firstSub['kondisi_options'][1] ?? 'Kurang';
                $m1 = $firstSub['metode_options'][0] ?? 'Site Visit';
                $m2 = $firstSub['metode_options'][1] ?? 'Online';

                $sheet->setCellValue("D{$row}", $k1); $sheet->setCellValue("E{$row}", $k2);
                $sheet->setCellValue("F{$row}", $m1); $sheet->setCellValue("G{$row}", $m2);
                $sheet->getStyle("A{$startHeaderRow}:G{$row}")->applyFromArray($styleSubHeader);
                $row++;

                foreach ($section['subsections'] as $no => $sub) {
                    if (!empty($sub['name'])) {
                        $sheet->setCellValue("A{$row}", $no + 1);
                        $sheet->setCellValue("B{$row}", $sub['name']);
                        $sheet->mergeCells("B{$row}:G{$row}");
                        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleGray);
                        $row++;
                    }

                    $letters = range('a', 'z');
                    foreach ($sub['items'] as $idx => $item) {
                        if (!empty($sub['name'])) {
                            $sheet->setCellValue("B{$row}", $letters[$idx]);
                            $sheet->getStyle("B{$row}")->applyFromArray($styleCenter);
                            $sheet->setCellValue("C{$row}", $item);
                        } else {
                            $sheet->setCellValue("A{$row}", $idx + 1);
                            $sheet->getStyle("A{$row}")->applyFromArray($styleCenter);
                            $sheet->setCellValue("B{$row}", $item);
                            $sheet->mergeCells("B{$row}:C{$row}");
                        }
                        
                        $sheet->setCellValue("D{$row}", "☐");
                        $sheet->setCellValue("E{$row}", "☐");
                        $sheet->setCellValue("F{$row}", "☐");
                        $sheet->setCellValue("G{$row}", "☐");
                        $sheet->getStyle("D{$row}:G{$row}")->applyFromArray($styleCenter);
                        $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                        $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);
                        $row++;
                    }
                }
                $sheet->getStyle("A{$startHeaderRow}:G".($row-1))->applyFromArray($styleBorderAll);
                $row++;
            }
        }

        // Catatan box
        $sheet->setCellValue("A{$row}", "Catatan");
        $sheet->getStyle("A{$row}")->getFont()->setUnderline(true);
        $sheet->mergeCells("A{$row}:G".($row+4));
        $sheet->getStyle("A{$row}:G".($row+4))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP]
        ]);
        $row += 6;

        if ($jenisPiket == 'Daily') {
            // Signatures for Daily
            $sheet->setCellValue("A{$row}", "Mengetahui");
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray($styleCenter)->getFont()->setBold(true);
            $row += 2;

            // Left box (DBM)
            $sheet->setCellValue("A{$row}", "DBM Perencanaan & Pengendalian Operasional");
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->mergeCells("A".($row+1).":C".($row+4));
            $sheet->setCellValue("A".($row+5), "(......................................................)");
            $sheet->mergeCells("A".($row+5).":C".($row+5));
            $sheet->getStyle("A{$row}:C".($row+5))->applyFromArray($styleBorderAll);
            $sheet->getStyle("A{$row}:C".($row+5))->applyFromArray($styleCenter);

            // Right box (Branch Manager)
            $sheet->setCellValue("E{$row}", "Branch Manager");
            $sheet->mergeCells("E{$row}:G{$row}");
            $sheet->mergeCells("E".($row+1).":G".($row+4));
            $sheet->setCellValue("E".($row+5), "(......................................................)");
            $sheet->mergeCells("E".($row+5).":G".($row+5));
            $sheet->getStyle("E{$row}:G".($row+5))->applyFromArray($styleBorderAll);
            $sheet->getStyle("E{$row}:G".($row+5))->applyFromArray($styleCenter);

            $row += 8;

            // Center box (Direksi Jaga)
            $sheet->setCellValue("C{$row}", "Disetujui Oleh");
            $sheet->mergeCells("C{$row}:E{$row}");
            $sheet->getStyle("C{$row}")->applyFromArray($styleCenter)->getFont()->setBold(true);
            $row += 2;

            $sheet->setCellValue("C{$row}", "Direksi Jaga");
            $sheet->mergeCells("C{$row}:E{$row}");
            $sheet->mergeCells("C".($row+1).":E".($row+4));
            $sheet->setCellValue("C".($row+5), "(......................................................)");
            $sheet->mergeCells("C".($row+5).":E".($row+5));
            $sheet->getStyle("C{$row}:E".($row+5))->applyFromArray($styleBorderAll);
            $sheet->getStyle("C{$row}:E".($row+5))->applyFromArray($styleCenter);
        } else {
            // Signatures for Angkutan Lebaran
            $sheet->setCellValue("D{$row}", "Mengetahui & Menyetujui");
            $sheet->mergeCells("D{$row}:G{$row}");
            $sheet->getStyle("D{$row}")->applyFromArray($styleCenter)->getFont()->setBold(true);
        }

        return $spreadsheet;
    }

    public function generateFilledTemplate($input)
    {
        $jenisPiket = $input->jenis_piket;
        $templateData = \App\Models\Template::where('jenis_piket', $jenisPiket)->first();
        if (!$templateData) {
            throw new \Exception("Template not found for " . $jenisPiket);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Column Widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);

        // Common Styles
        $styleHeaderSection = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']],
        ];
        $styleTitle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
        ];
        $styleSubHeader = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
        ];
        $styleBorderAll = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $styleCenter = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $styleGray = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
            'font' => ['bold' => true]
        ];

        // Header Top
        $sheet->setCellValue('A1', 'Branch: ' . $input->lokasi);
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('E1', 'Tanggal: ' . $input->tanggal);
        $sheet->mergeCells('E1:G1');

        // Main Title
        $titleText = $jenisPiket == 'Daily' ? 'MONITORING PIKET DIREKSI - WEEKEND TAHUN 2026' : 'MONITORING PIKET DIREKSI DALAM RANGKA ANGKUTAN LEBARAN 2026';
        $sheet->setCellValue('A2', $titleText);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2:G2')->applyFromArray($styleTitle);
        $sheet->getStyle('A2:G2')->applyFromArray($styleBorderAll);

        $row = 3;

        foreach ($templateData->content as $section) {
            $catPrefix = explode('. ', $section['section_title'])[0] ?? 'Section';
            $catPrefix = explode(' ', $catPrefix)[0]; // A, B, C...

            if ($section['is_resume']) {
                $catPrefix = 'F. RESUME';
            }

            $displayCategory = preg_replace('/^[A-Z][\.\s]+|^[A-Z]\.?\s*/i', '', $section['section_title']);
            $sheet->setCellValue("A{$row}", strtoupper($displayCategory));
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleHeaderSection);
            $row++;

            if ($section['is_resume']) {
                $startHeaderRow = $row;
                $sheet->setCellValue("A{$row}", "No"); 
                $sheet->setCellValue("B{$row}", "Item Pengecekan / Uraian"); $sheet->mergeCells("B{$row}:D{$row}");
                $sheet->setCellValue("E{$row}", "Keterangan / Temuan"); $sheet->mergeCells("E{$row}:G{$row}");
                $sheet->getStyle("A{$startHeaderRow}:G{$row}")->applyFromArray($styleSubHeader);
                $row++;

                if (!empty($section['subsections'][0]['items'])) {
                    foreach ($section['subsections'][0]['items'] as $idx => $item) {
                        $sheet->setCellValue("A{$row}", $idx + 1);
                        $sheet->setCellValue("B{$row}", $item); $sheet->mergeCells("B{$row}:D{$row}");
                        $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                        
                        $detail = $input->details->where('category', $catPrefix)->where('subcategory', 'Resume')->where('item_name', $item)->first();
                        if ($detail) {
                            $sheet->setCellValue("E{$row}", $detail->kondisi);
                        }
                        
                        $sheet->mergeCells("E{$row}:G{$row}");
                        $sheet->getStyle("E{$row}")->getAlignment()->setWrapText(true);
                        $sheet->getStyle("A{$row}")->applyFromArray($styleCenter);
                        $sheet->getRowDimension($row)->setRowHeight(40);
                        $row++;
                    }
                }
                $sheet->getStyle("A{$startHeaderRow}:G".($row-1))->applyFromArray($styleBorderAll);
                $row++;
            } else {
                $startHeaderRow = $row;
                $sheet->setCellValue("A{$row}", "No"); $sheet->mergeCells("A{$row}:A".($row+1));
                $sheet->setCellValue("B{$row}", "Item Pengecekan / Uraian"); $sheet->mergeCells("B{$row}:C".($row+1));
                $sheet->setCellValue("D{$row}", "Kondisi"); $sheet->mergeCells("D{$row}:E{$row}");
                $sheet->setCellValue("F{$row}", "Metode"); $sheet->mergeCells("F{$row}:G{$row}");
                $row++;

                $firstSub = $section['subsections'][0] ?? null;
                $k1 = $firstSub['kondisi_options'][0] ?? 'Baik';
                $k2 = $firstSub['kondisi_options'][1] ?? 'Kurang';
                $m1 = $firstSub['metode_options'][0] ?? 'Site Visit';
                $m2 = $firstSub['metode_options'][1] ?? 'Online';

                $sheet->setCellValue("D{$row}", $k1); $sheet->setCellValue("E{$row}", $k2);
                $sheet->setCellValue("F{$row}", $m1); $sheet->setCellValue("G{$row}", $m2);
                $sheet->getStyle("A{$startHeaderRow}:G{$row}")->applyFromArray($styleSubHeader);
                $row++;

                foreach ($section['subsections'] as $no => $sub) {
                    if (!empty($sub['name'])) {
                        $sheet->setCellValue("A{$row}", $no + 1);
                        $sheet->setCellValue("B{$row}", $sub['name']);
                        $sheet->mergeCells("B{$row}:G{$row}");
                        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleGray);
                        $row++;
                    }

                    $letters = range('a', 'z');
                    
                    $subK1 = $sub['kondisi_options'][0] ?? $k1;
                    $subK2 = $sub['kondisi_options'][1] ?? $k2;
                    $subM1 = $sub['metode_options'][0] ?? $m1;
                    $subM2 = $sub['metode_options'][1] ?? $m2;

                    foreach ($sub['items'] as $idx => $item) {
                        if (!empty($sub['name'])) {
                            $sheet->setCellValue("B{$row}", $letters[$idx]);
                            $sheet->getStyle("B{$row}")->applyFromArray($styleCenter);
                            $sheet->setCellValue("C{$row}", $item);
                        } else {
                            $sheet->setCellValue("A{$row}", $idx + 1);
                            $sheet->getStyle("A{$row}")->applyFromArray($styleCenter);
                            $sheet->setCellValue("B{$row}", $item);
                            $sheet->mergeCells("B{$row}:C{$row}");
                        }
                        
                        $safeSubName = empty($sub['name']) ? 'None' : $sub['name'];
                        $detail = $input->details->where('category', $catPrefix)->where('subcategory', $safeSubName)->where('item_name', $item)->first();
                        $valD = "☐"; $valE = "☐"; $valF = "☐"; $valG = "☐";
                        if ($detail) {
                            if ($detail->kondisi == $subK1) $valD = "☑";
                            elseif ($detail->kondisi == $subK2) $valE = "☑";
                            
                            if ($detail->metode == $subM1) $valF = "☑";
                            elseif ($detail->metode == $subM2) $valG = "☑";
                        }
                        
                        $sheet->setCellValue("D{$row}", $valD);
                        $sheet->setCellValue("E{$row}", $valE);
                        $sheet->setCellValue("F{$row}", $valF);
                        $sheet->setCellValue("G{$row}", $valG);
                        $sheet->getStyle("D{$row}:G{$row}")->applyFromArray($styleCenter);
                        $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                        $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);
                        $row++;
                    }
                }
                $sheet->getStyle("A{$startHeaderRow}:G".($row-1))->applyFromArray($styleBorderAll);
                $row++;
            }
        }

        // Catatan box
        $sheet->setCellValue("A{$row}", "Catatan: \n" . ($input->catatan ?? '-'));
        $sheet->mergeCells("A{$row}:G".($row+4));
        $sheet->getStyle("A{$row}:G".($row+4))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true]
        ]);
        $row += 6;

        if ($jenisPiket == 'Daily') {
            // Signatures for Daily
            $sheet->setCellValue("A{$row}", "Mengetahui");
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray($styleCenter)->getFont()->setBold(true);
            $row += 2;

            $sheet->setCellValue("A{$row}", "DBM Perencanaan & Pengendalian Operasional");
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->mergeCells("A".($row+1).":C".($row+4));
            $sheet->setCellValue("A".($row+5), "(......................................................)");
            $sheet->mergeCells("A".($row+5).":C".($row+5));
            $sheet->getStyle("A{$row}:C".($row+5))->applyFromArray($styleBorderAll);
            $sheet->getStyle("A{$row}:C".($row+5))->applyFromArray($styleCenter);

            $sheet->setCellValue("E{$row}", "Branch Manager");
            $sheet->mergeCells("E{$row}:G{$row}");
            $sheet->mergeCells("E".($row+1).":G".($row+4));
            $sheet->setCellValue("E".($row+5), "(......................................................)");
            $sheet->mergeCells("E".($row+5).":G".($row+5));
            $sheet->getStyle("E{$row}:G".($row+5))->applyFromArray($styleBorderAll);
            $sheet->getStyle("E{$row}:G".($row+5))->applyFromArray($styleCenter);

            $row += 8;

            $sheet->setCellValue("C{$row}", "Disetujui Oleh");
            $sheet->mergeCells("C{$row}:E{$row}");
            $sheet->getStyle("C{$row}")->applyFromArray($styleCenter)->getFont()->setBold(true);
            $row += 2;

            $sheet->setCellValue("C{$row}", "Direksi Jaga");
            $sheet->mergeCells("C{$row}:E{$row}");
            $sheet->mergeCells("C".($row+1).":E".($row+4));
            $sheet->setCellValue("C".($row+5), "(......................................................)");
            $sheet->mergeCells("C".($row+5).":E".($row+5));
            $sheet->getStyle("C{$row}:E".($row+5))->applyFromArray($styleBorderAll);
            $sheet->getStyle("C{$row}:E".($row+5))->applyFromArray($styleCenter);
        } else {
            // Signatures for Angkutan Lebaran
            $sheet->setCellValue("D{$row}", "Mengetahui & Menyetujui");
            $sheet->mergeCells("D{$row}:G{$row}");
            $sheet->getStyle("D{$row}")->applyFromArray($styleCenter)->getFont()->setBold(true);
        }

        return $spreadsheet;
    }

    private function generateDailyTemplate(Spreadsheet $spreadsheet, $templateData)
    {
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);   // No
        $sheet->getColumnDimension('B')->setWidth(55);  // Poin Checklist
        $sheet->getColumnDimension('C')->setWidth(12);  // Baik
        $sheet->getColumnDimension('D')->setWidth(12);  // Kurang
        $sheet->getColumnDimension('E')->setWidth(15);  // Site Visit
        $sheet->getColumnDimension('F')->setWidth(15);  // Online

        // Common Styles
        $styleHeaderSection = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000'], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BFBFBF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $styleSubHeader = [
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $styleBorderAll = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $styleCenter = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Main Title
        $sheet->setCellValue('A1', "Form Checklist Piket Monitoring\nTahun 2026");
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(60);

        $row = 3;

        foreach ($templateData->content as $section) {
            // Remove 'A.', 'B.', etc.
            $displayCategory = preg_replace('/^[A-Z][\.\s]+|^[A-Z]\.?\s*/i', '', $section['section_title']);
            
            $sheet->setCellValue("A{$row}", $displayCategory);
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($styleHeaderSection);
            $row++;

            if ($section['is_resume']) {
                $sheet->setCellValue("A{$row}", "No"); 
                $sheet->setCellValue("B{$row}", "Poin Checklist"); 
                $sheet->setCellValue("C{$row}", "Uraian"); 
                $sheet->mergeCells("C{$row}:F{$row}");
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($styleSubHeader);
                $row++;

                if (!empty($section['subsections'][0]['items'])) {
                    $itemNo = 1;
                    foreach ($section['subsections'][0]['items'] as $item) {
                        if (strtolower(trim($item)) == 'catatan') {
                            $row++;
                            $sheet->setCellValue("A{$row}", "Catatan :");
                            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                            $sheet->mergeCells("A{$row}:F".($row+4));
                            $sheet->getStyle("A{$row}:F".($row+4))->applyFromArray([
                                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THICK]],
                                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true]
                            ]);
                            $row += 5;
                            continue;
                        }

                        $sheet->setCellValue("A{$row}", $itemNo);
                        $sheet->setCellValue("B{$row}", $item);
                        $sheet->mergeCells("C{$row}:F{$row}");
                        
                        $sheet->getStyle("A{$row}")->applyFromArray($styleCenter);
                        $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($styleBorderAll);
                        $sheet->getRowDimension($row)->setRowHeight(35);
                        $row++;
                        $itemNo++;
                    }
                }
                $row++;
            } else {
                // Column headers
                $sheet->setCellValue("A{$row}", "No"); 
                $sheet->setCellValue("B{$row}", "Poin Checklist"); 
                
                $firstSub = $section['subsections'][0] ?? null;
                $sheet->setCellValue("C{$row}", $firstSub['kondisi_options'][0] ?? 'Baik');
                $sheet->setCellValue("D{$row}", $firstSub['kondisi_options'][1] ?? 'Kurang');
                $sheet->setCellValue("E{$row}", $firstSub['metode_options'][0] ?? 'Site Visit');
                $sheet->setCellValue("F{$row}", $firstSub['metode_options'][1] ?? 'Online');
                
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($styleSubHeader);
                $row++;

                $itemNo = 1;
                foreach ($section['subsections'] as $sub) {
                    foreach ($sub['items'] as $item) {
                        $sheet->setCellValue("A{$row}", $itemNo);
                        $sheet->setCellValue("B{$row}", $item);
                        
                        // Checkboxes
                        $sheet->setCellValue("C{$row}", "☐");
                        $sheet->setCellValue("D{$row}", "☐");
                        $sheet->setCellValue("E{$row}", "☐");
                        $sheet->setCellValue("F{$row}", "☐");
                        
                        $sheet->getStyle("A{$row}")->applyFromArray($styleCenter);
                        $sheet->getStyle("C{$row}:F{$row}")->applyFromArray($styleCenter);
                        $sheet->getStyle("C{$row}:F{$row}")->getFont()->setSize(16);
                        $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                        
                        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($styleBorderAll);
                        $sheet->getRowDimension($row)->setRowHeight(30);
                        
                        $row++;
                        $itemNo++;
                    }
                }
                $row++;
            }
        }

        return $spreadsheet;
    }
}
