<?php

/**
 * Abstract class to add shortcode support for eazymatch
 */
abstract class emol_shortcode {
	function createContent() {
		return $this->getContent();
	}

	abstract public function getContent();
}