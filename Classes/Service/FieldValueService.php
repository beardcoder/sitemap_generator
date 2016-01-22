<?php
/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
namespace Markussom\SitemapGenerator\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class UrlService
 */
class FieldValueService
{

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject = null;

    /**
     * SitemapRepository constructor.
     * @SuppressWarnings(superglobals)
     */
    public function __construct()
    {
        $this->contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    /**
     * Uses the page's cObj instance to resolve the field's value.
     *
     * @param string $fieldName The name of the field to get.
     * @param $typoScriptUrlEntry
     * @param array $row
     * @SuppressWarnings(superglobals)
     *
     * @return string The field's value.
     */
    public function getFieldValue(
        $fieldName,
        $typoScriptUrlEntry,
        $row
    ) {
        // support for cObject if the value is a configuration
        if (is_array($typoScriptUrlEntry[$fieldName . '.'])) {
            $this->contentObject->start($row, $typoScriptUrlEntry['table']);
            return $this->contentObject->cObjGetSingle(
                $typoScriptUrlEntry[$fieldName],
                $typoScriptUrlEntry[$fieldName . '.']
            );
        }

        return $row[$typoScriptUrlEntry[$fieldName]];
    }
}
