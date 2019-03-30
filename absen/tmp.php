<?php
    include 'query_helper_tmp.php';

    //echo q_attlog_override_value("Year(tgl)","id");

    require '../Snappy/Pdf.php';
    //use Knp\Snappy\Pdf;

    $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="file.pdf"');
echo $snappy->getOutput('http://www.github.com');
?>