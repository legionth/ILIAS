<?php
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class ilCertificateMigrationJobTest extends PHPUnit_Framework_TestCase
{
	public function testRun()
	{
		$container = $this->getMockBuilder('\ILIAS\DI\Container')
			->disableOriginaleConstructor()
			->getMock();

		$database = $this->getMockBuilder('ilDBInterface')
			->disableOriginaleConstructor()
			->getMock();

		$container->method('database')->willReturn($database);

		$job = new ilCertificateMigrationJob();
		$job->setDIC($container);

		$observer = $this->getMockbuilder('Observer')
			->disableOriginalConstructor()
			->getMock();

		$input = array(100);
		$job->run($input, $observer);
	}
}
