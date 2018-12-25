<?php
/**
 * Created by PhpStorm.
 * User: charl
 * Date: 1/1/2017
 * Time: 9:24 PM
 */

namespace Logic;


class TermNode extends Node {

	public function __construct($name, $value=false) {
		$this->name = $name;
		$this->value = $value;
	}

	public function __get($name) {
		switch($name) {
			case 'value':
				return $this->value;

			case 'name':
				return $this->name;

			case 'seen':
				return $this->seen;
		}
	}

	public function __set($name, $value) {
		switch($name) {
			case 'value':
				$this->value = $value;
				break;

			case 'name':
				$this->name = $value;
				break;

			case 'seen':
				$this->seen = $value;
		}
	}

	public function expression() {
		return $this->name;
	}

	public function precedence() {
		return 99;	// Literal is highest
	}

	public function solve() {
		return $this->value;
	}

	private $name;
	private $value;

	/// Flag to indicate that a term has been seen in an expression
	private $seen;
}