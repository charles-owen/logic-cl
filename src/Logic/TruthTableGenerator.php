<?php
/**
 * @file
 * Automatically create truth tables
 */

namespace CL\Logic;

/**
 * Automatically create truth tables
 */
class TruthTableGenerator {

	public function __construct(array $inputs, array $outputs) {
		$this->inputs = $inputs;
		$this->outputs = $outputs;
	}

	public function row(array $row) {
		$this->rows[] = $row;
	}

	public function present($div = null) {
		$html = '';

		if($div !== null) {
			$html .= '<div class="' . $div . '">';
		}

		$cls = 'truth-table';

		$html .= "<table class=\"$cls\"><tr>";
		foreach($this->inputs as $input) {
			$html .= "<th>$input</th>";
		}

		$first = true;
		foreach($this->outputs as $output) {
			if($first) {
				$html .= "<th class=\"border\">$output</th>";
				$first = false;
			} else {
				$html .= "<th>$output</th>";
			}
		}
		$html .= "</tr>";

		$inputcnt = count($this->inputs);
		foreach($this->rows as $row) {
			$html .= '<tr>';
			$col = 0;
			foreach($row as $cell) {
				if($col == $inputcnt) {
					$html .= "<td class=\"border\">$cell</td>";
				} else {
					$html .= "<td>$cell</td>";
				}

				$col++;
			}

			$html .= '</tr>';
		}


		$html .= '</table>';
		if($div !== null) {
			$html .= '</div>';
		}
		return $html;
	}

	private $inputs = null;
	private $outputs = null;
	private $rows = [];
}