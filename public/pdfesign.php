<?php
  $AttachedFileName = $_GET['filename'];
$dir="/qa.exmedc.com/public/public/uploads/docusign/".$AttachedFileName;
if (!file_exists($dir)) {
	die("The file you requested could not be found.");
}
// Set headers so the browser believes it's downloading a PDF file
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=$AttachedFileName");
$filesize = filesize($dir);
header("Content-Length: $filesize");
// Read the file and output it to the browser
readfile($dir);

?>
