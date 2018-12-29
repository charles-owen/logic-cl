<?php
/**
 * @file
 * Expression node representing NOT
 */

namespace CL\Logic;

/**
 * Expression node representing NOT
 */
class NotNode extends Node {
	/**
	 * NotNode constructor.
	 * @param Node $child
	 */
	public function __construct($child) {
		$this->child = $child;
	}

	/**
	 * Generate the expression for this node
	 * @return string Expression
	 */
	public function expression() {
		if($this->precedence() > $this->child->precedence()) {
			return "(" . $this->child->expression() . ")'";
		} else {
			return $this->child->expression() . "'";
		}

	}

	/**
	 * Get the precedence order for this operator
	 * @return int Precedence
	 */
	public function precedence() {
		return 3;
	}

	/**
	 * Compute the solution for this node
	 * @return string
	 */
	public function solve() {
		return !$this->child->solve();
	}

	/**
	 * Optimize the tree
	 */
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