<?php
/**
 * @file
 * OR node in an expression tree
 */

namespace CL\Logic;

/**
 * OR node in an expression tree
 */
class OrNode extends Node {
	/**
	 * OrNode constructor.
	 */
	public function __construct() {
		$this->children = func_get_args();
	}

	/**
	 * Generate the expression for this node
	 * @return string Expression
	 */
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

	/**
	 * Get the precedence order for this operator
	 * @return int Precedence
	 */
	public function precedence() {
		return 1;
	}

	/**
	 * Compute the solution for this node
	 * @return string
	 */
	public function solve() {
		$ret = false;
		foreach($this->children as $child) {
			$ret = $ret || $child->solve();

		}
		return $ret;
	}

	/**
	 * Add a new child node
	 * @param Node $child Child to add
	 */
	public function add_child($child) {
		$this->children[] = $child;
	}

	/**
	 * Optimize the tree
	 */
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

	/**
	 * Get children of this node
	 * @return array of Children
	 */
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