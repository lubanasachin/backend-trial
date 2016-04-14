<?php
date_default_timezone_set('UTC');
require_once(__DIR__ . '/errors.php');
require_once(__DIR__ . '/include.php');
require_once(__DIR__ .'/Reports.php');
$_POST = json_decode(file_get_contents('php://input'), true);
$period = $_POST['period'];
$commission = $_POST['commission'];
$report = new Reports($period,$commission,$db);
list($start,$end) = $report->getMonths();
$report->getLtvReportData($start,$end);
$reportData = $report->curateLtvReportData();
echo $reportData;
?>
