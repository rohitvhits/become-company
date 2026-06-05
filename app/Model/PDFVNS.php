<?php
namespace App\Model;

require_once base_path('/TCPDF/tcpdf.php');
require_once base_path('/TCPDF/tcpdi.php');
use TCPDF;
use TCPDI;
class PDFVNS extends TCPDI {
    var $_tplIdx;
    var $numPages;
    public $footerTextName = '';
    public $footerTextDOB = '';
    public $headerTextName = '';
    protected $isFirstPageHeader = true;
    public $dynamicTop = 10;

    public function setDynamicTop($y)
    {
        $this->dynamicTop = $y;
    }

    public function Header()
{
    $imageFile = public_path('header.png');

    // Add header image
    $this->Image($imageFile, 0, 0, 210, 0, '', '', '', false, 300);

    // Only add text for the first page
    if ($this->PageNo() == 1) {
        $this->SetFont('helvetica', '', 10);
         $this->Ln(5); // space below image
    //     $this->SetY(20);
        $this->Cell(0, 0, $this->headerTextName, 0, 1, 'L', 0, '', 0, false, 'M', 'M');
    }
   
}

    // (Optional) You can override Footer() as well
    public function Footer() {
        if ($this->getPage() != $this->getAliasNbPages()) {
            $imageFile = public_path('footer_final.png');
    
            // Footer image height
            $footerHeight = 25; // mm
    
            // Position footer image
            $this->SetY(-$footerHeight);
            $this->SetX(0);
            $this->Image($imageFile, -5, $this->GetY()+1, $this->getPageWidth()+10, $footerHeight, '', '', '', false, 300);
    
            // Dynamic footer text above the image
            $this->SetFont('helvetica', '', 10);
            $this->SetY(-$footerHeight + 10); // 10mm above image
            // $this->Cell(0, 0, 'ASDASDASD', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            $this->Cell(0, 0,$this->footerTextName, 0, 1, 'L', 0, '', 0, false, 'M', 'M');
            $this->Ln(3);
            $this->Cell(0, 0, $this->footerTextDOB, 0, 1, 'L', 0, '', 0, false, 'M', 'M');
            // Page number below the image
            $this->SetY(-4); // slightly above bottom margin
            
        }
    }
    
}
