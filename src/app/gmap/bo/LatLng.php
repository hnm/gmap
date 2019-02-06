<?php
namespace gmap\bo;

use n2n\reflection\ObjectAdapter;

class LatLng extends ObjectAdapter {
	private $lat;
	private $lng;
	
	public function __construct($lat, $lng) {
		$this->lat = $lat;
		$this->lng = $lng;
	}
	
	public function getLat() {
		return $this->lat;
	}

	public function setLat($lat) {
		$this->lat = $lat;
	}

	public function getLng() {
		return $this->lng;
	}

	public function setLng($lng) {
		$this->lng = $lng;
	}
}