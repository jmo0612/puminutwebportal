<?php

require_once('../TCPDF-master/tcpdf.php');
include '../module.php';
include 'tcpdf_bootstrap.php';

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        //$this->SetAutoPageBreak(false, 0);
        // set bacground image
        //$img_file = 'images/draft.png';
        //$this->Image($img_file, 0, 0, 0, 0, 'PNG', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        //$this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        //$this->setPageMark();

        // Set font
        $this->setAlpha(0.5);
        $this->SetFont('helvetica', 'B', 80);
        // Title
        $this->Cell(0, 80, '<< Draft >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
    }


    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Hal '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

//echo getPost('contentRep');
// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MYPDF('l', PDF_UNIT, 'F4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('e-absen PUPR');
$pdf->SetTitle('Rekapitulasi Daftar Hadir Bulan '.getPost('bulan').' '.getPost('tahun'));
$pdf->SetSubject('Rekapitulasi Daftar Hadir Bulan '.getPost('bulan').' '.getPost('tahun'));
$pdf->SetKeywords('Rekapitulasi,Daftar Hadir,Bulan,'.getPost('bulan').','.getPost('tahun'));

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 021', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin($pdf->getPageHeight('')/2);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', getPost('font'));

// add a page
$pdf->AddPage();

// create some HTML content
$html = getBootstrap().getPost('contentRep');

// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);



$pdf->setPrintHeader(false);

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Rekap_Absen_Bulan_'.getPost('bulan').'_'.getPost('tahun').'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

?>
