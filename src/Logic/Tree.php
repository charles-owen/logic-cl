<?php
/**
 * @file
 * Boolean logic expression tree class
 */

namespace CL\Logic;

/**
 * Boolean logic expression tree class
*/
class Tree {
	/**
	 * Tree constructor.
	 *
	 * Will accept parameters for the tree in these forms:
	 * new Tree("A", "B", "C").
	 * new Tree(array(A", "B", "C");
	 */
	public function __construct() {
		if(func_num_args() == 1 && is_array(func_get_arg(0))) {
			foreach(func_get_arg(0) as $term) {
				$this->add_term($term);
			}
		} else {
			foreach(func_get_args() as $term) {
				$this->add_term($term);
			}
		}
	}

	/**
	 * Add a term to the tree.
	 *
	 * Example: $tree->add_term("A", false);
	 *
	 * @param $name Name of the term
	 * @param bool $value Value (boolean)
	 * @return TermNode Created term node
	 */
	public function add_term($name, $value=false) {
		$term = new TermNode($name, $value);
		$this->terms[] = $term;
		return $term;
	}

	/**
	 * Add multiple terms at one time.
	 * Example: $tree->add_terms("A", "B", "C");
	 * Or: $tree->add_terms(array("A", "B", "C"));
	 */
	public function add_terms() {
		if(func_num_args() == 1 && is_array(func_get_arg(0))) {
			foreach(func_get_arg(0) as $term) {
				$this->add_term($term);
			}
		} else {
			foreach(func_get_args() as $term) {
				$this->add_term($term);
			}
		}
	}

	/**
	 * Set the tree root node
	 * @param $root Reference to a Node object
	 */
	public function set_root($root) {
		$this->root = $root;
	}

	public function get_root() {
		return $this->root;
	}

	/**
	 * Create an expression for the current tree.
	 * @return string Tree logic equation expression
	 */
	public function expression() {
		return $this->root === null ? "" : $this->root->expression();
	}

	/**
	 * Solve the tree equation
	 * @param array $values Array of boolean values
	 * @return bool Expression result
	 */
	public function solve(array $values) {
		// Set the terms
		assert(count($values) == count($this->terms));
		for($i=0; $i<count($values); $i++) {
			$this->terms[$i]->value = $values[$i];
		}

		return $this->root === null ? true : $this->root->solve();
	}

	public function get_terms() {
		return $this->terms;
	}

	/**
	 * Determine the minterms for this equation
	 * @return array of minterms.
	 */
	public function minterms() {
		$s = count($this->terms);
		$n = pow(2, $s);
		$ret = array();
		for($i=0; $i<$n; $i++) {
			if($this->solve(self::to_binary_array($i, $s))) {
				$ret[] = $i;
			}
		}

		return $ret;
	}

	public function minterm_expression() {
		$minterms = $this->minterms();
		$first = true;
		$ret = '';
		foreach($minterms as $minterm) {
			if(!$first) {
				$ret .= "+";
			} else {
				$first = false;
			}

			$ret .= $this->minterm_to_expression($minterm);
		}

		return $ret;
	}

	/**
	 * Optimize the tree. This collapses any cascading AND/OR/NOT into single nodes
	 */
	public function optimize() {
		if($this->root !== null) {
			$this->root->optimize();
		}
	}

	/**
	 * Validate that the supplied expression for this tree is a valid
	 * sum of products representation.
	 * @param $canonical - Must have all terms
	 */
	public function validate_sop($canonical=true) {
		if($this->root === null) {
			return false;
		}

		if($this->root instanceof OrNode) {
			return $this->root->validate_sop($this, $canonical);
		}

		if($this->root instanceof AndNode) {
			return $this->root->validate_product($this, $canonical);
		}

		return false;
	}

	/**
	 * Convert a number into an appropriate size array of booleans
	 *
	 * Example: 13 => array(true, true, false, true)
	 *
	 * @param $num Number to convert
	 * @param $bits Size of result
	 * @return array Of binary values.
	 */
	public static function to_binary_array($num, $bits) {
		$n = pow(2, $bits);
		$b = array();

		for($i=0; $i<$bits; $i++) {
			$n >>= 1;
			$b[] = ($num & $n) != 0;
		}

		return $b;
	}

	public static function to_minterms_list($num, $bits, $m) {
		$n = pow(2, $bits);
		$b = self::to_binary_array($num, $n);
		$minterms = array();

		for($i=0; $i<$n; $i++) {
			if($b[$i]) {
				$minterms[] = $m ? "m$i" : "$i";
			}
		}

		return $minterms;
	}

	public function minterms_to_expression(array $minterms) {
		$exp = '';
		$first = true;
		foreach($minterms as $minterm) {
			if(!$first) {
				$exp .= "+";
			} else {
				$first = false;
			}

			$exp .= $this->minterm_to_expression($minterm);
		}

		return $exp;
	}

	public function minterm_to_expression($minterm) {
		$bin = self::to_binary_array($minterm, count($this->terms));
		$ret = '';
		for($i=0; $i<count($this->terms); $i++) {
			$ret .= $this->terms[$i]->name;
			if(!$bin[$i]) {
				$ret .= "'";
			}
		}

		return $ret;
	}

	/*
	 * Grammar for top-down recursive descent parser:
	 *
	 * E = TF
	 * F = +TF | e
	 * T = (E)HT | lHT | e
	 * H = 'H | e
	 *
	 * l = literal
	 */

	/**
	 * @param $exp
	 * @throws TreeParseException If there is a parsing error
	 */
	public function parse($exp) {
		$this->exp = $exp;
		$this->exp_loc = 0;

		$this->root = $this->E($this->root);
		if($this->token() !== null) {
			throw new TreeParseException("Syntax error in expression near offset $this->exp_loc");
		}

		$this->optimize();
	}

	private $exp = null;
	private $exp_loc = 0;

	private function E() {
		$t = $this->T();
		$f = $this->F();
		if($t !== null) {
			if($f !== null) {
				return new OrNode($t, $f);
			} else {
				return $t;
			}
		}

		return null;
	}

	private function F() {
		$token = $this->token();
		if($token === '+') {
			$this->advance();
			$t = $this->T();
			$f = $this->F();
			if($t !== null) {
				if($f !== null) {
					return new OrNode($t, $f);
				} else {
					return $t;
				}
			}
		}

		return null;
	}

	private function T() {
		$token = $this->token();
		if($token === '(') {
			$this->advance();
			$e = $this->E();
			$token = $this->token();
			if($token !== ")") {
				// Syntax error
				throw new TreeParseException("Syntax error");
			}
			$this->advance();
			if($this->H(false)) {
				$e = new NotNode($e);
			}
			$t = $this->T();

			$node = new AndNode($e);
			if($t !== null) {
				$node->add_child($t);
			}

			return $node;
		} else {
			$term = $this->get_term($token);
			if($term !== null) {
				$this->advance();
				if($this->H(false)) {
					$term = new NotNode($term);
				}
				$t = $this->T();

				if($t !== null) {
					$node = new AndNode($term);
					$node->add_child($t);
					return $node;
				}

				return $term;
			}

		}

		return null;
	}

	private function H($state) {
		$token = $this->token();
		if(ord($token) == 226) {
			$this->advance();
			$token = $this->token();
			if(ord($token) == 128) {
				$this->advance();
				$token = $this->token();
				if(ord($token) == 153) {
					$this->advance();
					$token = $this->token();
					return !$this->H($state);
				}

			}

			return $state;
		}
		else if($token === "'") {
			$this->advance();
			return !$this->H($state);
		}

		return $state;
	}

	private function token() {
		while($this->exp_loc < strlen($this->exp)) {
			$char = substr($this->exp, $this->exp_loc, 1);
			if($char !== ' ') {
				return $char;
			}

			$this->exp_loc++;
		}

		return null;		// End of string
	}

	private function advance() {
		$this->exp_loc++;
	}

	private function get_term($name) {
		foreach($this->terms as $term) {
			if(strcasecmp($term->name, $name) == 0) {
				return $term;
			}
		}

		return null;
	}

	private $terms = array();
	private $root = null;
}