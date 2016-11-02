<?php
namespace VMFDS\VmfdsPrayerRequests\Tests\Unit\Controller;
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
 * Test case for class VMFDS\VmfdsPrayerRequests\Controller\PrayerRequestController.
 *
 * @author Christoph Fischer <christoph.fischer@volksmission.de>
 */
class PrayerRequestControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \VMFDS\VmfdsPrayerRequests\Controller\PrayerRequestController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('VMFDS\\VmfdsPrayerRequests\\Controller\\PrayerRequestController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllPrayerRequestsFromRepositoryAndAssignsThemToView()
	{

		$allPrayerRequests = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$prayerRequestRepository = $this->getMock('VMFDS\\VmfdsPrayerRequests\\Domain\\Repository\\PrayerRequestRepository', array('findAll'), array(), '', FALSE);
		$prayerRequestRepository->expects($this->once())->method('findAll')->will($this->returnValue($allPrayerRequests));
		$this->inject($this->subject, 'prayerRequestRepository', $prayerRequestRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('prayerRequests', $allPrayerRequests);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenPrayerRequestToView()
	{
		$prayerRequest = new \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('prayerRequest', $prayerRequest);

		$this->subject->showAction($prayerRequest);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenPrayerRequestToPrayerRequestRepository()
	{
		$prayerRequest = new \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest();

		$prayerRequestRepository = $this->getMock('VMFDS\\VmfdsPrayerRequests\\Domain\\Repository\\PrayerRequestRepository', array('add'), array(), '', FALSE);
		$prayerRequestRepository->expects($this->once())->method('add')->with($prayerRequest);
		$this->inject($this->subject, 'prayerRequestRepository', $prayerRequestRepository);

		$this->subject->createAction($prayerRequest);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenPrayerRequestToView()
	{
		$prayerRequest = new \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('prayerRequest', $prayerRequest);

		$this->subject->editAction($prayerRequest);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenPrayerRequestInPrayerRequestRepository()
	{
		$prayerRequest = new \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest();

		$prayerRequestRepository = $this->getMock('VMFDS\\VmfdsPrayerRequests\\Domain\\Repository\\PrayerRequestRepository', array('update'), array(), '', FALSE);
		$prayerRequestRepository->expects($this->once())->method('update')->with($prayerRequest);
		$this->inject($this->subject, 'prayerRequestRepository', $prayerRequestRepository);

		$this->subject->updateAction($prayerRequest);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenPrayerRequestFromPrayerRequestRepository()
	{
		$prayerRequest = new \VMFDS\VmfdsPrayerRequests\Domain\Model\PrayerRequest();

		$prayerRequestRepository = $this->getMock('VMFDS\\VmfdsPrayerRequests\\Domain\\Repository\\PrayerRequestRepository', array('remove'), array(), '', FALSE);
		$prayerRequestRepository->expects($this->once())->method('remove')->with($prayerRequest);
		$this->inject($this->subject, 'prayerRequestRepository', $prayerRequestRepository);

		$this->subject->deleteAction($prayerRequest);
	}
}
