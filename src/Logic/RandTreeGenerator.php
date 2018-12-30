<?php
/**
 * @file
 * Create a random expression tree.
 */

namespace CL\Logic;

/**
 * Create a random expression tree.
 */
class RandTreeGenerator {
	const AndNode = 1;
	const OrNode = 2;

	public function __construct() {
		if(func_num_args() == 1 && is_array(func_get_arg(0))) {
			$this->terms = func_get_arg(0);
		} else {
			$this->terms = func_get_args();
		}
	}

	public function level($type, $min, $max) {
		$this->levels[] = array("type" => $type, "min" => $min, "max" => $max);
	}

	public function create() {
		$tree = new Tree($this->terms);
		do {
            $terms = $tree->terms;
            $term = $terms[mt_rand(0, count($terms)-1)];

			$node = $this->generate(0, $tree, $term);
			$tree->set_root($node);
		} while(count($tree->minterms()) == 0);


		return $tree;
	}

	private function generate($level, $tree, $term) {
		if($level >= count($this->levels)) {
		    // Leaf level, select one term
			if(mt_rand(0, 1) == 1) {
				return new NotNode($term);
			}

			return $term;
		}

		// How many on this level?
		$num = mt_rand($this->levels[$level]["min"], $this->levels[$level]["max"]);
        $terms = $tree->terms;
        shuffle($terms);
		if($num == 1) {
			return $this->generate($level+1, $tree, $terms[0]);
		} else {
			$node = $this->levels[$level]["type"] == self::OrNode ?
				new OrNode() : new AndNode();

			for($i=0; $i<$num; $i++) {
				$node->add_child($this->generate($level+1, $tree, $terms[$i % $num]));
			}

			return $node;
		}

	}


	private $terms;
	private $levels = [];
}