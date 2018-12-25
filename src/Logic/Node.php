<?php
/**
 * @file Abstract base class for nodes in a logic tree
 */

namespace Logic;


abstract class Node {
	abstract function expression();
	abstract function precedence();
	abstract function solve();

	public function optimize() {}
}