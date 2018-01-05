<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class My_export {

  function toExcel($filename, $header, $data) {
    include_once APPPATH . 'third_party/phpoffice/PHPExcel.php';

    $objPHPExcel = new PHPExcel();
    $objPHPWorkSheet = $objPHPExcel->setActiveSheetIndex(0);

    $row = 1;
    $col = 0;
    foreach ($header as $field) {
      $objPHPWorkSheet->setCellValueByColumnAndRow($col, $row, $field);
      $col ++;
    }
    foreach ($data as $rowData) {
      $col = 0;
      $row ++;
      foreach ($rowData as $value) {
        $objPHPWorkSheet->setCellValueByColumnAndRow($col, $row, $value);
        $col ++;
      }
    }
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
//    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
//    header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }

  function toCSV($filename, $header, $data) {
    $f = fopen('php://memory', 'w');

    fputcsv($f, $header);

    foreach ($data as $rowData) {
      fputcsv($f, $rowData);
    }

    fseek($f, 0);

    // tell the browser it's going to be a csv file
    header('Content-Type: application/csv');
    // tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename="' . $filename . '.csv";');
    // make php send the generated csv lines to the browser
    fpassthru($f);
  }

}
