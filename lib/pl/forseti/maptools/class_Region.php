<?php

class Region {
	const DEFAULT_REGISTRY_FILE = '../registry.csv';

	private $bodyRadius;
	private $mapLevel;
	private $name;
	private $w;
	private $e;
	private $n;
	private $s;

	public function __construct($name, $registryFile = DEFAULT_REGISTRY_FILE) {
		if (($fHandle = fopen($registryFile, 'r')) !== FALSE) {
			$data = fgetcsv($fHandle, 0, ',');
			$this->bodyRadius = $data[1];
			while(($data = fgetcsv($fHandle, 0, ',')) !== FALSE) {
				if ($data[0] == $name) {
					list($this->name, $this->w, $this->e, $this->n, $this->s) = $data;
				}
			}
			throw new Exception('Region not found in registry');
		} else {
			throw new Exception('Registry not found');
		}
	}

	public function getX() {
		$longitude = normalizeLongitude($this->w);
		return (pow(2,$this->mapLevel) * 1024) * $longitude / 360;
	}

	public function getW() {
		$longitude = normalizeLongitude($this->e);
		return (pow(2,$this->mapLevel) * 1024) * $longitude / 360;
	}

	public function getY() {
		$latitude = normalizeLatitude($this->n);
		return (pow(2,$this->mapLevel) * 1024) * $latitude / 180;
	}

	public function getH() {
		$latitude = normalizeLatitude($this->s);
		return (pow(2,$this->mapLevel) * 1024) * $latitude / 180;
	}

	private function normalizeLatitude($latitude) {
		return $latitude + 90;
	}

	private function normalizeLongitude($longitude) {
		return $longitude + 180;
	}
}
?>
