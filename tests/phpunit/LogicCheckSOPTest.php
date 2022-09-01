<?php
/** @file
 * @cond
 * Unit tests for the class LogicTree
 */
namespace CL\Logic;

class LogicCheckSOPTest extends \PHPUnit\Framework\TestCase
{
	public function test_validate() {
		$check = new CheckSOP("A", "B", "C");

		// Valid SOP
		$this->assertTrue($check->validate("A'B'C' + A'B'C + A'BC' + A'BC + A B'C' + AB' C + ABC' + ABC"));

		// Syntax error
		$this->assertFalse($check->validate("fda"));

		// Not SOP
		$this->assertFalse($check->validate("(A+B)C"));

		// Not canonical
		$this->assertFalse($check->validate("A'B' + A'B' + A'BC' + A'BC + A B'C' + AB' C + ABC' + ABC"));

		// Redundant terms
		$this->assertFalse($check->validate("A'B'C' + A'B'C + A'BC' + A'BC + A B'C' + AB' C + ABC' + ABC + A'B'C'"));

		// Redundant terms
		$this->assertFalse($check->validate("A'B'C' + A'B'C + AA'BC' + A'BC + A B'C' + AB' C + ABC' + ABC"));
	}

	public function test_check() {
		$check = new CheckSOP("A", "B", "C");


		$this->assertTrue($check->check(
			"A'B'C' + A'B'C + A'BC' + A'BC + A B'C' + AB' C + ABC' + ABC",
			"A'B'C' + A'B'C + A'BC' + A'BC + A B'C' + AB' C + ABC' + ABC"));

		// Order shuffled
		$this->assertTrue($check->check(
			"A'B'C' + A'B'C + A'BC' + A'BC + A B'C' + AB' C + ABC' + ABC",
			"A'B'C' + A'BC' + ABC' + A'BC + A B'C' + A'B'C + AB' C + ABC"
			));

		// Not equivalent
		$this->assertFalse($check->check(
			"A'B'C' + A'BC' + ABC' + A'BC + A B'C' + A'B'C + AB' C + ABC",
			"A'B'C' + A'B'C + A'BC' + A'BC + AB' C + ABC' + ABC"));

		$this->assertTrue($check->check("ABC", "ABC"));

		// Some strange unicode character that looks like a hash mark?
		$this->assertTrue($check->check("A’BC+A’BC’+ABC", "A'BC'+A'BC+ABC"));

        $this->assertTrue($check->check("AB'C", "AB'C"));
        $this->assertTrue($check->check("AB&#039;C", "AB'C"));
        $this->assertTrue($check->check("AB&#039;C", "AB&#039;C"));
        $this->assertTrue($check->check("AB'C", "AB&#039;C"));

    }
}

/// @endcond
