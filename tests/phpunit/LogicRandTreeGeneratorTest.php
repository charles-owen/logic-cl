<?php
/** @file
 * @cond
 * Unit tests for the class LogicTree
 */
namespace CL\Logic;

class LogicRandTreeGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function test_trees() {
		$gen = new RandTreeGenerator("A", "B", "C");

		$gen->level(RandTreeGenerator::AndNode, 2, 3);
		$gen->level(RandTreeGenerator::OrNode, 1, 3);
		$gen->level(RandTreeGenerator::AndNode, 1, 3);

		for($i=0; $i<10; $i++) {
			$tree = $gen->create();
			echo "\n" . $tree->expression();
			print_r($tree->minterms());
		}

	}

	public function test_trees2() {
		$gen = new RandTreeGenerator("A", "B", "C", "D");

		$gen->level(RandTreeGenerator::AndNode, 2, 3);
		$gen->level(RandTreeGenerator::OrNode, 1, 3);
		$gen->level(RandTreeGenerator::AndNode, 1, 3);

		for($i=0; $i<10; $i++) {
			$tree = $gen->create();
			echo "\n" . $tree->expression();
			print_r($tree->minterms());
		}

	}
}

/// @endcond
