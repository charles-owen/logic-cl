<?php
/** @file
 * @cond
 * Unit tests for the class LogicTree
 */
namespace CL\Logic;

class LogicRandTreeGeneratorTest extends \PHPUnit\Framework\TestCase
{
	public function test_trees() {
		mt_srand(12354);

		$gen = new RandTreeGenerator("A", "B", "C");

		$gen->level(RandTreeGenerator::AndNode, 2, 3);
		$gen->level(RandTreeGenerator::OrNode, 1, 3);
		$gen->level(RandTreeGenerator::AndNode, 1, 3);

		$tree = $gen->create();
		$this->assertEquals('(C\'BA\'+A\'+A)(C\'+C\'B\')', $tree->expression());
		$this->assertEquals([0, 2, 4, 6], $tree->minterms());

//		for($i=0; $i<10; $i++) {
//			$tree = $gen->create();
//			echo "\n" . $tree->expression();
//			print_r($tree->minterms());
//		}



	}

	public function test_trees2() {
		mt_srand(4800);

		$gen = new RandTreeGenerator("A", "B", "C", "D");

		$gen->level(RandTreeGenerator::AndNode, 2, 3);
		$gen->level(RandTreeGenerator::OrNode, 1, 3);
		$gen->level(RandTreeGenerator::AndNode, 1, 3);

		$tree = $gen->create();
		$this->assertEquals('(DC\'+BC)(BAD+A\'B\'D\')', $tree->expression());
		$this->assertEquals([13, 15], $tree->minterms());

//		for($i=0; $i<10; $i++) {
//			$tree = $gen->create();
//			echo "\n" . $tree->expression();
//			print_r($tree->minterms());
//		}
	}
}

/// @endcond
