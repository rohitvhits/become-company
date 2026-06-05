<?php
namespace App;

require_once base_path('/TCPDF/tcpdf.php');
require_once base_path('/TCPDF/tcpdi.php');
use TCPDF;
use TCPDI;
class PDF extends TCPDI {
    var $_tplIdx;
    var $numPages;

    function Header() {}

    function Footer() {}

}
