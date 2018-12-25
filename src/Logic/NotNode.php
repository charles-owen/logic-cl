<?php
/**
 * Created by PhpStorm.
 * User: charl
 * Date: 1/1/2017
 * Time: 9:37 PM
 */

namespace Logic;


class NotNode extends Node {

	public function __construct($child) {
		$this->child = $child;
	}

	public function expression() {
		if($this->precedence() > $this->child->precedence()) {
			return "(" . $this->child->expression() . ")'";
		} else {
			return $this->child->expression() . "'";
		}

	}

	public function precedence() {
		return 3;
	}

	public function solve() {
		return !$this->child->solve();
	}

	public function optimize() {
		// First optimize the children
		if($this->child !== null) {
			$this->child->optimize();
		}

		if($this->child instanceof NotNode) {
			$this->child = $this->child->child;
		}
	}

	public function get_child() {
		return $this->child;
	}

	private $child;
}