<?php

namespace VMFDS\VmfdsPrayerRequests\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Christoph Fischer <christoph.fischer@volksmission.de>, Volksmission Freudenstadt
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Christoph Fischer <christoph.fischer@volksmission.de>
 */
class PrayerRequestTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getAuthorReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getAuthor()
		);
	}

	/**
	 * @test
	 */
	public function setAuthorForStringSetsAuthor()
	{
		$this->subject->setAuthor('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'author',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPublicAuthorReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPublicAuthor()
		);
	}

	/**
	 * @test
	 */
	public function setPublicAuthorForStringSetsPublicAuthor()
	{
		$this->subject->setPublicAuthor('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'publicAuthor',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getRequestReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getRequest()
		);
	}

	/**
	 * @test
	 */
	public function setRequestForStringSetsRequest()
	{
		$this->subject->setRequest('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'request',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getStatusReturnsInitialValueForInt()
	{	}

	/**
	 * @test
	 */
	public function setStatusForIntSetsStatus()
	{	}

	/**
	 * @test
	 */
	public function getAudienceReturnsInitialValueForInt()
	{	}

	/**
	 * @test
	 */
	public function setAudienceForIntSetsAudience()
	{	}
}
