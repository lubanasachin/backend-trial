<!doctype html>
<html class="no-js">
	<head>
		<title>LTV Report</title>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
		<meta name="keywords" content="report,ltv,long term value">
		<meta name="description" content="Long Term Value Report">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<link rel="stylesheet" type="text/css" href="styles/main.css">
	</head>
	<body>
	<?php
		date_default_timezone_set('UTC');
		require_once(__DIR__ . '/errors.php');
		require_once(__DIR__ . '/include.php');
		require_once(__DIR__ . '/getReport.php');		
		$period 		= (isset($_POST['period'])) ? $_POST['period'] : 12;
		$commission 	= (isset($_POST['commission'])) ? $_POST['commission'] : 10;
		$reportTitle 	= "Report: LTV";
		$ltv = new LtvReport($commission/100, $period, $db);
		$rowCount = 0;
	?>	
		<div>
			<div id="envelope">
				<header>
					<h2>LTV REPORT</h2>
			    </header>
			    <hr>
			    <form name="ltvform" id="ltvform" action="" method="post">
			        <label>Period (months)</label>
			        <select name="period" id="period">
			            <option value='' selected='true'>Select</option>
			            <option value="3"  <?php if($period == 3) echo "selected='true'"; ?> >3</option>
			            <option value="12" <?php if($period == 12) echo "selected='true'"; ?> >12</option>
			            <option value="18" <?php if($period == 18) echo "selected='true'"; ?> >18</option>
			        </select>
			        <label>Commission (%)</label>
			        <input type="text" name="commission" placeholder="commission" id="commission" value="<?=$commission?>">
			        <input type="button" value="Get Report"  onclick="validateForm();">
			    </form>
			</div>
			<table><tr><td><strong>Period:</strong> <?=$period?> months</td><td><strong>Commission:</strong> <?=$commission?>%</td></tr></table>
			<table border="1">
			    <thead>
			        <tr>
			            <th>Month</th>
			            <th>Bookers</th>
			            <th>Booking (avg)</th>
			            <th>Turnover (avg)</th>
			            <th>LTV</th>
			        </tr>
			    </thead>
			    <tbody>
					<?php
					foreach($ltv->ltvData as $year => $months):
					  	foreach($months as $month => $ltvData):
					    	$rowCount++;
					?>
			                <tr>
			                  <td><?php echo safe("$month - $year") ?></td>
			                  <td><?php echo safe($ltvData["bookers"]) ?></td>
			                  <td><?php echo safe($ltvData["avgBooking"]) ?></td>
			                  <td><?php echo safe($ltvData["avgTurnover"]) ?></td>
			                  <td><?php echo safe($ltvData["ltv"]) ?></td>
			                </tr>
					<?php
						endforeach;
					endforeach;
					?>
				</tbody>
				<tfoot><tr><td colspan="4" align="right"><strong>Total rows: </strong></td><td><?php echo $rowCount ?></td></tr></tfoot>
			</table>
		</div>
	</body>
</html>
<script src="js/app.js"></script>
