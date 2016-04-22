<?php

	class LtvReport {

		public function __construct($commission, $period, $db) {
			$this->firstBooking = array();
			$this->ltvData = array();
			$this->commission = $commission;
			$this->period = $period;
			$this->db = $db;
			$this->recordFirstBooking();
			$this->recordLtvData();
		}

		/**
		* get first booking for all the bookers for spaces only
		* @params
		* @returns void
		*/
		public function getFirstBooking() {
			$sql = "
				SELECT
					MIN(bookingitems.end_timestamp) as 'first_booking',
					strftime('%Y', bookingitems.end_timestamp, 'unixepoch') as 'year',
					strftime('%m', bookingitems.end_timestamp, 'unixepoch') as 'month',
					bookings.booker_id as 'booker'
				FROM
          			bookingitems
					INNER JOIN bookings ON bookingitems.booking_id = bookings.id
					INNER JOIN spaces on bookingitems.item_id = spaces.item_id
					GROUP BY bookings.booker_id
					ORDER BY bookingitems.end_timestamp
			";
			return $result = $this->db->prepare($sql)->run();
		}

		/**
		* record first booking details
		* @params
		* @returns void
		*/
		public function recordFirstBooking() {
			$result = $this->getFirstBooking();
			foreach ($result as $key => $value) {
				if (!isset($this->firstBooking[$value->year])) $this->firstBooking[$value->year] = array();
				if (!isset($this->firstBooking[$value->year][$value->month])) $this->firstBooking[$value->year][$value->month] = array();
      			array_push($this->firstBooking[$value->year][$value->month], $value->booker);
			}
		}

		/**
		* Get ltv report details
		* @params
		* @returns void
		*/
		public function getLtvData($year, $month, $users) {
			$start_date = strtotime("1/$month/$year");
			$end_date = strtotime("+$this->period months", $start_date);
			$id_search = implode(',', $users);
			$number_new_in_month = sizeof($users);
			$sql = "
				SELECT
					ROUND(AVG(bookingitems.locked_total_price), 2) as 'avg_turnover',
					ROUND(AVG(bookingitems.locked_total_price) * $this->commission, 2) as 'ltv',
					COUNT(1) / $number_new_in_month as 'avg_bookings'
				FROM 
					bookings
					INNER JOIN bookingitems ON bookings.id = bookingitems.booking_id
					INNER JOIN spaces  on bookingitems.item_id = spaces.item_id
				WHERE 
					bookings.booker_id IN ($id_search)
					AND bookingitems.end_timestamp BETWEEN $start_date AND $end_date
			";
			return $result = $this->db->prepare($sql)->run();
		}

		/**
		* record ltv report data
		* @params
		* @returns void
		*/
		public function recordLtvData() {
			foreach ($this->firstBooking as $year => $months) {
				foreach ($months as $month => $users) {
					$result = $this->getLtvData($year,$month,$users);
					foreach ($result as $key => $val) {
          				if (!isset($this->ltvData[$year])) $this->ltvData[$year] = array();
						if (!isset($this->ltvData[$year][$month])) {
							$this->ltvData[$year][$month] = array(
								"bookers"		=>	sizeof($users),
								"avgBooking"	=>	$val->avg_bookings,
								"avgTurnover"	=>	$val->avg_turnover,
								"ltv"			=>	$val->ltv
							);
						}
					}
				}
			}
		}
	}
?>
