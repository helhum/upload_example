<?php
namespace Helhum\UploadExample\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel 
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
 * Test case for class Helhum\UploadExample\Controller\ExampleController.
 *
 * @author Helmut Hummel 
 */
class ExampleControllerTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var Helhum\UploadExample\Controller\ExampleController
	 */
	protected $subject;

	public function setUp() {
		$this->subject = $this->getMock('Helhum\\UploadExample\\Controller\\ExampleController', array('redirect', 'forward'), array(), '', FALSE);
	}

	public function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllExamplesFromRepositoryAndAssignsThemToView() {

		$allExamples = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$exampleRepository = $this->getMock('Helhum\\UploadExample\\Domain\\Repository\\ExampleRepository', array('findAll'), array(), '', FALSE);
		$exampleRepository->expects($this->once())->method('findAll')->will($this->returnValue($allExamples));
		$this->inject($this->subject, 'exampleRepository', $exampleRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('examples', $allExamples);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenExampleToView() {
		$example = new \Helhum\UploadExample\Domain\Model\Example();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('example', $example);

		$this->subject->showAction($example);
	}

	/**
	 * @test
	 */
	public function newActionAssignsTheGivenExampleToView() {
		$example = new \Helhum\UploadExample\Domain\Model\Example();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('newExample', $example);
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction($example);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenExampleToExampleRepository() {
		$example = new \Helhum\UploadExample\Domain\Model\Example();

		$exampleRepository = $this->getMock('Helhum\\UploadExample\\Domain\\Repository\\ExampleRepository', array('add'), array(), '', FALSE);
		$exampleRepository->expects($this->once())->method('add')->with($example);
		$this->inject($this->subject, 'exampleRepository', $exampleRepository);

		$flashMessageContainer = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\Controller\\FlashMessageContainer', array('add'), array(), '', FALSE);
		$this->inject($this->subject, 'flashMessageContainer', $flashMessageContainer);

		$this->subject->createAction($example);
	}

	/**
	 * @test
	 */
	public function createActionAddsMessageToFlashMessageContainer() {
		$example = new \Helhum\UploadExample\Domain\Model\Example();

		$exampleRepository = $this->getMock('Helhum\\UploadExample\\Domain\\Repository\\ExampleRepository', array('add'), array(), '', FALSE);
		$this->inject($this->subject, 'exampleRepository', $exampleRepository);

		$flashMessageContainer = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\Controller\\FlashMessageContainer', array('add'), array(), '', FALSE);
		$flashMessageContainer->expects($this->once())->method('add');
		$this->inject($this->subject, 'flashMessageContainer', $flashMessageContainer);

		$this->subject->createAction($example);
	}

	/**
	 * @test
	 */
	public function createActionRedirectsToListAction() {
		$example = new \Helhum\UploadExample\Domain\Model\Example();

		$exampleRepository = $this->getMock('Helhum\\UploadExample\\Domain\\Repository\\ExampleRepository', array('add'), array(), '', FALSE);
		$this->inject($this->subject, 'exampleRepository', $exampleRepository);

		$flashMessageContainer = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\Controller\\FlashMessageContainer', array('add'), array(), '', FALSE);
		$this->inject($this->subject, 'flashMessageContainer', $flashMessageContainer);

		$this->subject->expects($this->once())->method('redirect')->with('list');
		$this->subject->createAction($example);
	}
}
