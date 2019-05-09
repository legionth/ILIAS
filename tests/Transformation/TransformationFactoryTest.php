<?php

/* Copyright (c) 2017 Stefan Hecken <stefan.hecken@concepts-and-training.de> Extended GPL, see docs/LICENSE */

use ILIAS\Refinery\Transformation;
use PHPUnit\Framework\TestCase;

/**
 * TestCase for the factory of transformations
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 */
class TransformationFactoryTest extends TestCase {
	protected function setUp(): void{
		$this->f = new Transformation\Factory();
	}

	protected function tearDown(): void {
		$this->f = null;
	}

	public function testAddLabels() {
		$add_label = $this->f->addLabels(array("A", "B", "C"));
		$this->assertInstanceOf(Transformation\Transformation::class, $add_label);
	}

	public function testToData() {
		$data = $this->f->toData('password');
		$this->assertInstanceOf(Transformation\Transformation::class, $data);
	}

	public function testToDataWrongType() {
		try	{
			$data = $this->f->toData('no_such_type');
			$this->assertFalse("This should not happen");
		}
		catch(\InvalidArgumentException $e){
			$this->assertTrue(true);
		}
	}

}
