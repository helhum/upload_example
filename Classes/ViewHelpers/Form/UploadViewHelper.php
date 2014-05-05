<?php
namespace Helhum\UploadExample\ViewHelpers\Form;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel <helmut.hummel@typo3.org>
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
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class UploadViewHelper
 */
class UploadViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\UploadViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Security\Cryptography\HashService
	 * @inject
	 */
	protected $hashService;

	/**
	 * @var \TYPO3\CMS\Extbase\Property\PropertyMapper
	 * @inject
	 */
	protected $propertyMapper;

	/**
	 * Renders the upload field with possible resource pointer
	 *
	 * @return string
	 * @api
	 */
	public function render() {
		$output = '';

		$resource = $this->getUploadedResource();
		if ($resource !== NULL) {
			$resourcePointerIdAttribute = '';
			if ($this->hasArgument('id')) {
				$resourcePointerIdAttribute = ' id="' . htmlspecialchars($this->arguments['id']) . '-file-reference"';
			}
			$resourcePointerValue = $resource->getUid();
			if ($resourcePointerValue === NULL) {
				// Newly created file reference which is not persisted yet.
				// Use the file UID instead, but prefix it with "file:" to communicate this to the type converter
				$resourcePointerValue = 'file:' . $resource->getOriginalResource()->getOriginalFile()->getUid();
			}
			$output .= '<input type="hidden" name="' . $this->getName() . '[submittedFile][resourcePointer]" value="' . htmlspecialchars($this->hashService->appendHmac((string)$resourcePointerValue)) . '"' . $resourcePointerIdAttribute . ' />';

			$this->templateVariableContainer->add('resource', $resource);
			$output .= $this->renderChildren();
			$this->templateVariableContainer->remove('resource');
		}

		$output .= parent::render();
		return $output;
	}


	/**
	 * Returns a previously uploaded resource.
	 * If errors occurred during property mapping for this property, NULL is returned
	 *
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected function getUploadedResource() {
		if ($this->getMappingResultsForProperty()->hasErrors()) {
			return NULL;
		}
		$resource = $this->getValue(FALSE);
		if ($resource instanceof \TYPO3\CMS\Extbase\Domain\Model\FileReference) {
			return $resource;
		}
		return $this->propertyMapper->convert($resource, 'TYPO3\\CMS\\Extbase\\Domain\\Model\\FileReference');
	}


} 