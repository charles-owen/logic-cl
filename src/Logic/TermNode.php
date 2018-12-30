<?php
/**
 * Terminal node in an expression tree.
 */

namespace CL\Logic;

/**
 * Terminal node in an expression tree.
 *
 * @cond
 * @property string value
 * @property string name
 * @property boolean seen
 * @endcond
 */
class TermNode extends Node {
	/**
	 * TermNode constructor.
	 * @param string $name Name of the terminal
	 * @param bool $value Value of the terminal
	 */
	public function __construct($name, $value=false) {
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * Property get magic method
	 *
	 * <b>Properties</b>
	 * Property | Type | Description
	 * -------- | ---- | -----------
	 *
	 * @param string $property Property name
	 * @return mixed
	 */
	public function __get($property) {
		switch($property) {
			case 'value':
				return $this->value;

			case 'name':
				return $this->name;

			case 'seen':
				return $this->seen;

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property ' . $property .
					' in ' . $trace[0]['file'] .
					' on line ' . $trace[0]['line'],
					E_USER_NOTICE);
				return null;
		}
	}

	/**
	 * Property set magic method
	 *
	 * <b>Properties</b>
	 * Property | Type | Description
	 * -------- | ---- | -----------
	 *
	 * @param string $property Property name
	 * @param mixed $value Value to set
	 */
	public function __set($property, $value) {
		switch($property) {
			case 'value':
				$this->value = $value;
				break;

			case 'name':
				$this->name = $value;
				break;

			case 'seen':
				$this->seen = $value;
				break;

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property ' . $property .
					' in ' . $trace[0]['file'] .
					' on line ' . $trace[0]['line'],
					E_USER_NOTICE);
				break;
		}
	}

	/**
	 * Generate the expression for this node
	 * @return string Expression
	 */
	public function expression() {
		return $this->name;
	}

	/**
	 * Get the precedence order for this operator
	 * @return int Precedence
	 */
	public function precedence() {
		return 99;	// Literal is highest
	}

	/**
	 * Compute the solution for this node
	 * @return string
	 */
	public function solve() {
		return $this->value;
	}

	private $name;
	private $value;

	/// Flag to indicate that a term has been seen in an expression
	private $seen;
}