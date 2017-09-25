<?php
	namespace Idax\Common\Classes;

	class Direction
	{
		private $dr;
		private $abbreviation;
		private $fullname;
		private $reversedr;
		private $oppositedr;

		private $directions = array(
				"Northbound"     => array("dr" => "N",  "abbr" => "NB",  "fullname" => "Northbound",     "oppositedr" => "S"),
				"Southbound"     => array("dr" => "S",  "abbr" => "SB",  "fullname" => "Southbound",     "oppositedr" => "N"),
				"Eastbound"      => array("dr" => "E",  "abbr" => "EB",  "fullname" => "Eastbound",      "oppositedr" => "W"),
				"Westbound"      => array("dr" => "W",  "abbr" => "WB",  "fullname" => "Westbound",      "oppositedr" => "E"),
				"NortheastBound" => array("dr" => "NE", "abbr" => "NEB", "fullname" => "NortheastBound", "oppositedr" => "SW"),
				"NorthwestBound" => array("dr" => "NW", "abbr" => "NWB", "fullname" => "NorthwestBound", "oppositedr" => "SE"),
				"SoutheastBound" => array("dr" => "SE", "abbr" => "SEB", "fullname" => "SoutheastBound", "oppositedr" => "NW"),
				"SouthwestBound" => array("dr" => "SW", "abbr" => "SWB", "fullname" => "SouthwestBound", "oppositedr" => "NE"),
				"N"              => array("dr" => "N",  "abbr" => "NB",  "fullname" => "Northbound",     "oppositedr" => "S"),
				"S"              => array("dr" => "S",  "abbr" => "SB",  "fullname" => "Southbound",     "oppositedr" => "N"),
				"E"              => array("dr" => "E",  "abbr" => "EB",  "fullname" => "Eastbound",      "oppositedr" => "W"),
				"W"              => array("dr" => "W",  "abbr" => "WB",  "fullname" => "Westbound",      "oppositedr" => "E"),
				"NE"             => array("dr" => "NE", "abbr" => "NEB", "fullname" => "NortheastBound", "oppositedr" => "SW"),
				"NW"             => array("dr" => "NW", "abbr" => "NWB", "fullname" => "NorthwestBound", "oppositedr" => "SE"),
				"SE"             => array("dr" => "SE", "abbr" => "SEB", "fullname" => "SoutheastBound", "oppositedr" => "NW"),
				"SW"             => array("dr" => "SW", "abbr" => "SWB", "fullname" => "SouthwestBound", "oppositedr" => "NE"),
				);

		public function __construct(
			$direction
			)
		{
			$this->dr = isset($this->directions[$direction]) ? $this->directions[$direction]['dr'] : NULL;
			$this->abbreviation = isset($this->directions[$direction]) ? $this->directions[$direction]['abbr'] : NULL;
			$this->fullname = isset($this->directions[$direction]) ? $this->directions[$direction]['fullname'] : NULL;
			$this->oppositedr = isset($this->directions[$direction]) ? $this->directions[$direction]['oppositedr'] : NULL;
		}

		public function GetDr()
		{
			return $this->dr;
		}

		public function GetAbbreviation()
		{
			return $this->abbreviation;
		}

		public function GetFullName()
		{
			return $this->fullname;
		}

		public function GetOppositeDr()
		{
			return $this->oppositedr;
		}
	}
?>
