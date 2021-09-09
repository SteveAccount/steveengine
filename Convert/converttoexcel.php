<?php

namespace SteveEngine\Convert;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ConvertToExcel{
    private $columns = ["", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T"];
    private $table;
    private $excel;
    private $sheet;
    private $actualRowIndex = 1;

    public function toExcel(){
        $this->table = $this->setTable();
        $data = $this->getData();
        $this->makeExcelFile($data);
        $writer = new Xlsx($this->excel);
        $writer->save("a.xlsx");
        $this->directDownload("a.xlsx");
    }

    private function directDownload(string $file){
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=$file");
        header("Location: $file");
        exit;
    }

    private function getData(){
        $query = $this->table->getQuery(false, false);
        return db()
            ->query($query)
            ->answer("stdClass")
            ->select();
    }

    private function makeExcelFile(array $data){
        $this->excel = new Spreadsheet();
        $this->sheet = $this->excel->getActiveSheet();

        //Header
        foreach ($this->table->fields() as $index => $field){
            $this->sheet->setCellValue($this->cell($index + 1, $this->actualRowIndex), $field->header);
        }

        //Data
        $excelRowIndex = 2;
        foreach ($data as $row){
            foreach ($this->table->fields() as $colIndex => $field){
                $fieldName = $field->field;
                $this->sheet->setCellValue($this->cell((int)$colIndex + 1, $excelRowIndex), $row->$fieldName);
            }
            $excelRowIndex++;
        }

        //Format
        //Auto column width
        foreach ($this->table->fields() as $index => $field){
            $range = $this->column($index + 1);
            $this->sheet->getColumnDimension($range)->setAutoSize(true);
        }

        //Header
        $range = $this->range(1, 1, count($this->table->fields()), 1);
        $this->sheet->getStyle($range)->getFont()->setBold(true);
        $this->sheet->getStyle($range)->getFont()->setSize(12);
        $this->sheet->getStyle($range)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $this->sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    private function cell(int $column, int $row){
        return $this->columns[$column] . $row;
    }

    private function range(int $col1, int $row1, int $col2, int $row2){
        return $this->cell($col1, $row1) . ":" . $this->cell($col2, $row2);
    }

    private function column(int $column){
        return $this->columns[$column];
    }
}