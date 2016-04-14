<?php
	
	class Reports {

		private $bookers = [];
		private $bookings = [];

		private $period;
		private $commission;
		private $db;

		/**
		* constructor to define period & commission for the report
		* @inputs $period & $commision
		* @returns
		*/
		public function __construct($period,$commission,$db) {
			$this->period = ($period) ? $period : 3;
			$this->commission = ($commission) ? $commission : 0.1;
			$this->db = $db;
		}
		
		/**
		* get months for given period (Current date - given period)
		* @inputs
		* @returns array (starttime: timestamp, endtime: timestamp)
		*/
		public function getMonths() {
			$period = $this->period;
			$startDate = date("Y-m-01 00:00:00", strtotime("-$period months"));
			$endDate = date("Y-m-d", strtotime("+$period months",strtotime($startDate)));
			$endDate = date("Y-m-d 23:59:59", strtotime("-1 day",strtotime($endDate)));
			$sttime = strtotime($startDate);
			$edtime = strtotime($endDate);
			return array($sttime,$edtime);
		}

		/**
		* get LTV report data for given period
		* @inputs $sttime: UTC UNIXTIMESTAMP, $edtime: UTC UNIXTIMESTAMP
		* @returns void
		*/
		public function getLtvReportData($sttime, $edtime) {
			$query = "SELECT a.booker_id, strftime('%Y-%m', datetime(b.end_timestamp, 'unixepoch')) as btime, b.locked_total_price as amount ";
			$query.= "FROM bookings a inner join bookingitems b on a.id = b.booking_id inner join spaces c on b.item_id = c.item_id ";
			$query.= "WHERE b.end_timestamp >= $sttime and b.end_timestamp <= $edtime ";
			$query.= "ORDER BY b.end_timestamp";
			$result = $this->db->prepare($query)->run();

			$seen = array();
			foreach($result as $key => $val) {

				list($resBid,$resMnt,$resAmt) = array($val->booker_id,$val->btime,$val->amount);

				//record total bookings & total amount spend made by a booker for a given lifetime
				if(!isset($this->bookings['b_'.$resBid])) $this->bookings['b_'.$resBid] = array("bcnt" => 1, "bamt" => $resAmt);
				else {
					$this->bookings['b_'.$resBid]['bcnt']++;
					$this->bookings['b_'.$resBid]['bamt']+= $resAmt;
				}

				//skip, if booker details already processedd
				if(in_array($resBid,$seen)) continue;
				array_push($seen,$resBid);

				//record first booking by a booker in a given month
				if(isset($this->bookers[$resMnt])) array_push($this->bookers[$resMnt],$resBid);
				else $this->bookers[$resMnt] = array($resBid);
			}
		}

		/**
		* curate LTV report data for given period
		* @inputs 
		* @returns JSON
		*/
		public function curateLtvReportData() {
			$reportData = [];
			foreach($this->bookers as $mnt => $val) {
				$bookersCount = count($val);

				$totbookings = $totamount = 0;
				for($i=0;$i<$bookersCount;$i++) {
					$totbookings += $this->bookings['b_'.$val[$i]]['bcnt']; 
					$totamount += $this->bookings['b_'.$val[$i]]['bamt']; 
				}

				$bookingAvg = round(($totbookings / $bookersCount),2); 
				$amtAvg = round(($totamount / $bookersCount),2);
				$ltv = round(($amtAvg*$this->commission),2); 

				$reportData[] = array(
					'month'				=> $mnt,
					'bookers'			=> $bookersCount,
					'total_bookings'	=> $totbookings,
					'average_bookings'	=> $bookingAvg,
					'total_amount'		=> $totamount,
					'average_amount'	=> $amtAvg,
					'ltv'				=> $ltv
				);
			}	
			return json_encode($reportData);		
		}
	}

?>