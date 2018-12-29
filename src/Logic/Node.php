<?php
/**
 * @file
 * Abstract base class for nodes in a logic tree
 */

namespace CL\Logic;

/**
 * Abstract base class for nodes in a logic tree
 */
abstract class Node {
	/**
	 * Generate the expression for this node
	 * @return string Expression
	 */
	abstract function expression();

	/**
	 * Get the precedence order for this operator
	 * @return int Precedence
	 */
	abstract function precedence();

	/**
	 * Compute the solution for this node
	 * @return string
	 */
	abstract function solve();

	/**
	 * Optimize the tree
	 */
	public function optimize() {}
}