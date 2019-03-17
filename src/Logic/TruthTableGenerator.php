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
	/**
	 * TruthTableGenerator constructor.
	 * @param array $inputs Number of table inputs
	 * @param array $outputs Number of table outputs
	 */
	public function __construct(array $inputs, array $outputs) {
		$this->inputs = $inputs;
		$this->outputs = $outputs;
	}

	/**
	 * Add a row to the table
	 * @param array $row Row to add
	 */
	public function row(array $row) {
		$this->rows[] = $row;
	}

	/**
	 * Present the table in HTML
	 * @param string|null $div Optional class to add to div tag
	 * @return string HTML
	 */
	public function present($div = null, $cls = null) {
		$html = '';

		if($div !== null) {
			$html .= '<div class="' . $div . '">';
		}

		$tableClass = $cls !== null ? 'truth-table ' . $cls : 'truth-table';

		$html .= "<table class=\"$tableClass\"><tr>";
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