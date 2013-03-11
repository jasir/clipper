<?php

namespace jasir\Clipper;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Clipper.php';

class ClipperTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Clipper;
	 */
	private $object;

	public function setup() {
		parent::setup();
		$this->object = $this->createClipper('<!--start-->', '<!--end-->');
	}

	/**
	 * @expectedException jasir\Clipper\ClipperLogicException
	 */
	public function test_start_mark_must_not_be_same_as_end_mark() {
		$this->createClipper('<start>', '<start>');
	}

	function test_nomarks()                                 {
		$this->assertEquals(NULL, $this->object->clip('some text', 'start', 'end'));
	}

	function test_clipping() {

	$text = <<<EOF
one<!--start-->take
this<!--end-->
no
<!--start-->...<!--end-->
EOF;

	$expect = <<<EOF
take
this...
EOF;

		$this->assertEquals($expect, $this->object->clip($text));

	}

	function test_nestedclip() {
			   //012345678901234567890012345678901234567890
		$text = "no <s>y <s>Y1 <ee>Y2 <ee>no <s>yesend <ee>noend";
		$expect ="y Y1 Y2 yesend ";

		$clipper = $this->createClipper('<s>', '<ee>');
		$this->assertEquals($expect, $clipper->clip($text));

	}

	/**
	 * @expectedException jasir\Clipper\ClipperException
	 */
	function test_clip_badly_nested_throws_exception() {

	$text = <<<EOF
one<!--start-->take
this<!--start-->boy<!--end-->
<!--end-->
no
<!--start-->
EOF;

	$expect = <<<EOF
take
this,boy.
EOF;

		$this->assertEquals($expect, $this->object->clip($text));

	}
	/**
	 * @expectedException jasir\Clipper\ClipperException
	 */
	function test_clip_badly_nested_throws_exception2() {

	$text = <<<EOF
Vole <s>...<e>...<e>...<s>...
EOF;
		$clipper = $this->createClipper('<s>', '<e>');
		$clipper->clip($text);

	}

	function test_excludeClip() {
		$text = <<<EOF
outside<s>inside<ee>outside<s>inside<s>inside<ee>inside<ee>outside
EOF;
		$clipper = $this->createClipper("<s>", "<ee>");
		$this->assertEquals('insideinsideinsideinside', $clipper->clip($text));
		$this->assertEquals('outsideoutsideoutside', $clipper->excludeClip($text));
	}

	function test_clip_marks_can_contain_slashes() {
		$text = "<body>wow</body>";
		$clipper = $this->createClipper('<body>', '</body>');
		$this->assertEquals('wow', $clipper->clip($text));
	}

	private function createClipper($startMark, $endMark) {
		return new Clipper($startMark, $endMark);
	}
}