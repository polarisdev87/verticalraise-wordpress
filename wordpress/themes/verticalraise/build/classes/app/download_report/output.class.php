<?php

namespace classes\app\download_report;

class Output
{

    /**
     * Spreadsheet Output.
     * @param obj $sheet
     * @param obj $data
     */
    public function output($sheet, $pages) {
        ob_end_clean();
        
//        echo "<pre>";
//        print_r($sheet);
//        print_r($pages);
//        echo "</pre>";
//        exit();

        header('Content-disposition: attachment; filename="' . \XLSXWriter::sanitize_filename( $sheet->filename ) . '"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        $writer = new \XLSXWriter();
        $writer->setAuthor( $sheet->author );
        $writer->writeSheet( $pages->page1, 'Participants', $sheet->header1 );
        $writer->writeSheet( $pages->page2, 'Donors', $sheet->header2 );
        
        //print_r($writer);
        $writer->writeToStdOut();
        
        exit(0);
    }
    
}