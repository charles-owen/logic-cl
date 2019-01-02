<?php
/** @file
 * @cond
 * Unit tests for the class LogicTree
 */
namespace CL\Logic;

class LogicTreeTest extends \PHPUnit\Framework\TestCase
{
	public function test_not() {
		$tree = new Tree();

		$a = $tree->add_term('A');

		$tree->set_root($a);

		$this->assertFalse($tree->solve(array(false)));
		$this->assertTrue($tree->solve(array(true)));

		$an = new NotNode($a);
		$tree->set_root($an);

		$this->assertTrue($tree->solve(array(false)));
		$this->assertFalse($tree->solve(array(true)));
	}

	public function test_and() {
		$tree = new Tree();

		$a = $tree->add_term('A');
		$b = $tree->add_term('B');

		$ab = new AndNode($a, $b);
		$tree->set_root($ab);

		$this->assertFalse($tree->solve(array(false, false)));
		$this->assertFalse($tree->solve(array(false, true)));
		$this->assertFalse($tree->solve(array(true, false)));
		$this->assertTrue($tree->solve(array(true, true)));
	}




	public function test_tree1() {
		$tree = new Tree();

		// Add terms
		$a = $tree->add_term('A');
		$b = $tree->add_term('B');
		$c = $tree->add_term('C');

		$an = new NotNode($a);

		$anb = new AndNode($an, $b);
		$anbc = new OrNode($anb, $c);

		$tree->set_root($anbc);

		$this->assertEquals("A'B+C", $tree->expression());

		$this->assertFalse($tree->solve(array(false, false, false)));
		$this->assertTrue($tree->solve(array(false, false, true)));
		$this->assertTrue($tree->solve(array(false, true, false)));
		$this->assertTrue($tree->solve(array(false, true, true)));
		$this->assertFalse($tree->solve(array(true, false, false)));
		$this->assertTrue($tree->solve(array(true, false, true)));
		$this->assertFalse($tree->solve(array(true, true, false)));
		$this->assertTrue($tree->solve(array(true, true, true)));

		$this->assertEquals(array(1, 2, 3, 5, 7), $tree->minterms());

		$this->assertEquals("A'B'C+A'BC'+A'BC+AB'C+ABC", $tree->minterm_expression());
	}

	public function test_tree2() {
		$tree = new Tree();

		// Add terms
		$a = $tree->add_term('A');
		$b = $tree->add_term('B');
		$c = $tree->add_term('C');

		$cn = new NotNode($c);

		$bcn = new OrNode($b, $cn);
		$abcn = new AndNode($a, $bcn);

		$tree->set_root($abcn);

		$this->assertEquals("A(B+C')", $tree->expression());
	}

	public function test_tree3() {
		$tree = new Tree();

		// Add terms
		$a = $tree->add_term('A');
		$b = $tree->add_term('B');
		$c = $tree->add_term('C');

		$abc = new AndNode($a, $b, $c);
		$abcn = new NotNode($abc);
		$tree->set_root($abcn);

		$this->assertEquals("(ABC)'", $tree->expression());
	}

	public function test_to_binary_array() {
		$this->assertEquals(array(true, true, true, true), Tree::to_binary_array(15, 4));
		$this->assertEquals(array(false, true, true, false), Tree::to_binary_array(6, 4));
	}

	public function test_parse() {
		$tree = new Tree('A', 'B', 'C');

		$tree->parse("A");
		$this->assertEquals("A", $tree->expression());

		$tree->parse("C(A+B'(C+A)')C'");
		$this->assertEquals("C(A+B'(C+A)')C'", $tree->expression());

		$tree->parse("A'B+C");
		$this->assertEquals("A'B+C", $tree->expression());

		$tree->parse("(ABC)'");
		$this->assertEquals("(ABC)'", $tree->expression());

		$tree->parse("A(B+C')");
		$this->assertEquals("A(B+C')", $tree->expression());

		$tree->parse("a(b+c')");
		$this->assertEquals("A(B+C')", $tree->expression());

		$tree->parse("A'B'C' + A'B'C + A'BC' + A'BC + A B'C' + AB' C + ABC' + ABC");
		$this->assertEquals("A'B'C'+A'B'C+A'BC'+A'BC+AB'C'+AB'C+ABC'+ABC", $tree->expression());

	}

	/**
	 * @expectedException CL\Logic\TreeParseException
	 */
	public function test_parse_syntax() {
		$tree = new Tree('A', 'B', 'C');

		$tree->parse("A(");
	}


	/**
	 * @expectedException CL\Logic\TreeParseException
	 */
	public function test_parse_syntax2() {
		$tree = new Tree('A', 'B', 'C');

		$tree->parse("X");
	}

	public function test_minterm_to_expression() {
		$tree = new Tree(array('A', 'B', 'C'));
		$this->assertEquals("A'B'C'", $tree->minterm_to_expression(0));
		$this->assertEquals("ABC", $tree->minterm_to_expression(7));

	}
}

/// @endcond
