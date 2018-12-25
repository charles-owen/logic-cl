<?php
/**
 * Created by PhpStorm.
 * User: charl
 * Date: 1/1/2017
 * Time: 9:34 PM
 */

namespace Logic;


class OrNode extends Node {
	public function __construct() {
		$this->children = func_get_args();
	}

	public function expression() {
		$ret = '';
		$first = true;
		foreach($this->children as $child) {
			if(!$first) {
				$ret .= '+';
			} else {
				$first = false;
			}

			if($this->precedence() > $child->precedence()) {
				$ret .= "(" . $child->expression() . ")";
			} else {
				$ret .= $child->expression();
			}
		}
		return $ret;
	}

	public function precedence() {
		return 1;
	}

	public function solve() {
		$ret = false;
		foreach($this->children as $child) {
			$ret = $ret || $child->solve();

		}
		return $ret;
	}

	public function add_child($child) {
		$this->children[] = $child;
	}

	public function optimize() {
		// First optimize the children
		foreach($this->children as $child) {
			$child->optimize();
		}

		$children = array();
		foreach($this->children as $child) {
			if($child instanceof OrNode) {
				$children = array_merge($children, $child->children);
			} else {
				$children[] = $child;
			}
		}

		$this->children = $children;
	}

	public function get_children() {
		return $this->children;
	}

	/**
	 * Validate that this node is the root of a Sum of Products representation
	 */
	public function validate_sop($tree, $canonical) {
		// All children must be AndNodes and validated as product of terms
		foreach($this->children as $child) {
			if(!($child instanceof AndNode)) {
				return false;
			}

			if(!$child->validate_product($tree, $canonical)) {
				return false;
			}
		}

		if($canonical) {
			// The number of children must match the number of
			// minterms for the tree.
			if(count($this->children) != count($tree->minterms())) {
				return false;
			}
		}

		return true;
	}

	private $children;
}