<?php
/**
 * @file
 * AND node in expression tree
 */

namespace CL\Logic;

/**
 * AND node in expression tree
 */
class AndNode extends Node {
	/**
	 * AndNode constructor.
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
		foreach($this->children as $child) {
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
		return 2;
	}

	/**
	 * Compute the solution for this node
	 * @return string
	 */
	public function solve() {
		$ret = true;
		foreach($this->children as $child) {
			$ret = $ret && $child->solve();
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
			if($child instanceof AndNode) {
				$children = array_merge($children, $child->children);
			} else {
				$children[] = $child;
			}
		}

		$this->children = $children;
	}

	/**
	 * Validate that this node is a product of terms or terms'
	 */
	public function validate_product($tree, $canonical) {
		/*
		 * For each term, flag the term as not yet seen
		 * in the expression. We are allowed to see a term
		 * only once. And, if canonical, we must see all terms.
		 */
		$terms = $tree->terms;
		foreach($terms as $term) {
			$term->seen = false;
		}

		foreach($this->children as $child) {
			$term = null;

			if($child instanceof NotNode && $child->get_child() instanceof TermNode) {
				// Child is a not of a term, get it.
				$term = $child->get_child();
			} else if($child instanceof TermNode) {
				// Child is a term, also ok
				$term = $child;
			} else {
				// Invalid child
				return false;
			}

			// We are allowed to see a term only once in an expression
			if($term->seen) {
				return false;
			}

			$term->seen = true;
		}

		if($canonical) {
			// Ensure all terms have been seen
			foreach($terms as $term) {
				if(!$term->seen) {
					return false;
				}
			}
		}

		return true;
	}

	private $children;
}