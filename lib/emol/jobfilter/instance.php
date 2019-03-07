<?php

class emol_jobfilter_instance {
	private $free = '';

	private $zipcode = '';

	private $range = 5000; // default: 5 km

	private $province = array();

	private $competence = array();

	private $status = array();

	/**
	 * @return mixed job filter array for the EazyCore
	 */
	function getFilterArray() {
		$filters = array();

		// add free search if added
		if ( ! empty( $this->free ) ) {
			$filters['free'] = array( $this->free );
		}

		if ( ! empty( $this->zipcode ) ) {
			$filters['location'] = array(
				'zipcode' => $this->zipcode,
				'range'   => $this->range
			);
		}

		if ( ! empty( $this->province ) ) {
			$filters['province'] = $this->province;
		}

		if ( ! empty( $this->competence ) ) {
			$filters['competence'] = $this->competence;
		}

		if ( ! empty( $this->status ) ) {
			$filters['status'] = $this->status;
		}

		return $filters;
	}

	public function setFree( $text ) {
		$this->free = $text;
	}

	public function setZipcode( $zipcode ) {
		$this->zipcode = $zipcode;
	}

	public function setRange( $range ) {
		$this->range = $range;
	}

	public function setProvince( $provinceArray ) {
		$this->province = $this->ensureNumericArray( $provinceArray );
	}

	public function setCompetence( $competenceArray ) {
		$this->competence = $this->ensureNumericArray( $competenceArray );
	}

	public function addCompetence( $competenceArray ) {
		$this->competence = array_merge( $this->competence, $this->ensureNumericArray( $competenceArray ) );
	}

	public function getCompetence() {
		return $this->competence;
	}

	public function setStatus( $statusArray ) {
		$this->status = $this->ensureNumericArray( $statusArray );
	}

	public function getStatus() {
		return $this->status;
	}

	/**
	 * normalized input for numeric arrays
	 */
	private function ensureNumericArray( $input ) {
		if ( ! is_array( $input ) ) {
			$input = array( $input );
		}

		if ( count( $input ) > 0 && ! is_numeric( $input[0] ) ) {
			if ( is_array( $input[0] ) && isset( $input[0]['id'] ) ) {
				$output = array();
				foreach ( $input as $item ) {
					$output [] = $item['id'];
				}
				$input = $output;
			} else {
				$input = array();
			}
		}

		return $input;
	}
}