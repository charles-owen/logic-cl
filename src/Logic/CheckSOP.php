<?php
/**
 * @file
 * Check that a string is in correct sum of products form.
 */

namespace Logic;

/**
 * Check that a string is in correct sum of products form
 * and matches some other provided tree.
 */
class CheckSOP {
	public function __construct() {
		if(func_num_args() == 1 && is_array(func_get_arg(0))) {
			$this->terms = func_get_arg(0);
		} else {
			$this->terms = func_get_args();
		}
	}

	/**
	 * Check that a string is in SOP form
	 * @param $given A string that should be in sum of products form
	 */
	public function validate($given, $canonical=true) {
		$tree = new Tree($this->terms);
		try {
			$tree->parse($given);
		} catch(TreeParseException $ex) {
			return $this->fail($ex->getMessage());
		}

		if(!$tree->validate_sop($canonical)) {
			return $this->fail("Expression is not a sum of products");
		}

		return true;
	}

	/**
	 * @param $expected
	 * @param $given
	 * @param bool $canonical
	 * @return bool True if valid and equivalent
	 */
	public function check($expected, $given, $canonical=true) {
		$tree = new Tree($this->terms);
		try {
			$tree->parse($given);
		} catch(TreeParseException $ex) {
			return $this->fail($ex->getMessage());
		}

		if(!$tree->validate_sop($canonical)) {
			return $this->fail("Expression is not a sum of products");
		}

		$treeExpected = new Tree($this->terms);
		try {
			$treeExpected->parse($expected);
		} catch(TreeParseException $ex) {
			return $this->fail("Given expression is invalid: " . $ex->getMessage());
		}

		if($tree->minterms() != $treeExpected->minterms()) {
			return $this->fail("Expression does not match expected expression");
		}

		return true;
	}

	private function fail($msg) {
		$this->msg = $msg;
		return false;
	}

	public function get_msg() {
		return $this->msg;
	}

	private $terms;
	private $msg = "No Error";
}