<?php
namespace jasir\Clipper;

class Clipper {

	private $startMark, $endMark;

	public function __construct($startMark, $endMark) {

		if ($startMark === $endMark) {
			throw new ClipperException('Start mark and end mark must not be the same');
		}

		$this->startMark = $startMark;
		$this->endMark = $endMark;
	}

	/**
	 * Clip and return part of text between marks. Marks can be nested.
	 *
	 * @param string $text
	 * @return string
	 * @throws ClipperException when bad structure
	 */
	public function clip($text) {
		return $this->clipRegions($text, $this->buildRegions($text));
	}

	/**
	 * Clip and return text outside clipping marks. Marks can be nested.
	 *
	 * @param string $text
	 * @return string
	 * @throws ClipperException when bad structure
	 */
	public function excludeClip($text) {
		return $this->clipRegions($text, $this->invertRegions($text, $this->buildRegions($text)));
	}

	/* --- internal --- */

	private function buildRegions($text) {
		$s = preg_quote($this->startMark, '/');
		$e = preg_quote($this->endMark, '/');

		preg_match_all("/$s/", $text, $startMarks, PREG_OFFSET_CAPTURE);
		preg_match_all("/$e/", $text, $endMarks, PREG_OFFSET_CAPTURE);

		$startMarks = $startMarks[0];
		$endMarks = $endMarks[0];

		if (count($startMarks) !== count($endMarks)) {
			throw new ClipperException('Count of start mark do not match counts of end marks.');
		}

		$marks = array();

		foreach ($startMarks as $mark) {
			$marks[$mark[1]] = array('t' => 's', 'start' => $mark[1], 'end' => $mark[1] + strlen($mark[0]));
		}

		foreach( $endMarks as $mark) {
			$marks[$mark[1]] = array('t' => 'e', 'start' => $mark[1], 'end' => $mark[1] + strlen($mark[0]));
		}

		ksort($marks);

		$regions = array();
		$sc = 0;

		foreach ($marks as $o => $mark) {
			if ($mark['t'] === 's') {
				if ($sc > 0) {
					$regions[] = array('start' => $o, 'end' => $o);
				}
				$sc++;
				$regions[] = array('start' => $mark['start'], 'end' => $mark['end']);
			} else {
				$sc--;
				if ($sc < 0) {
					throw new ClipperException('Badly nested marks.');
				}
				$regions[] = array('start' => $mark['start'], 'end' => $mark['end']);
				if ($sc > 0) {
					$regions[] = array('start' => $mark['end'], 'end' => $mark['end']);
				}
			}
		}
		return $regions;
	}

	private function invertRegions($text, $regions) {
		array_unshift($regions, array('start' => 0, 'end' => 0));
		$regions[] = array('start' => strlen($text), 'end' => strlen($text));
		return $regions;
	}

	private function clipRegions($text, $regions) {

		if (count($regions) === 0) {
			return '';
		}

		$previous = NULL;
		$result = '';

		foreach ($regions as $region) {
			if ($previous) {
				$result .= substr($text, $previous['end'], $region['start'] - $previous['end']);
				$previous = NULL;
				continue;
			}
			$previous = $region;
		}
		return $result;
	}

}

class ClipperException extends \RuntimeException {

}
