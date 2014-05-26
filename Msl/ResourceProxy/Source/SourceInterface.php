<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Source;

/**
 * Basic behaviour for all Source instances.
 *
 * @category  Source
 * @package   Msl\ResourceProxy\Source
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
interface SourceInterface
{
    /**
     * Sets all the required parameters to configure a given Source instance.
     *
     * @param SourceConfig $sourceConfig
     *
     * @return void
     */
    public function setConfig(SourceConfig $sourceConfig);

    /**
     * Returns an Iterator instance for a list of Resource instances.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getContentIterator();

    /**
     * Returns the name of the current Source instance.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the source object (i.e. the object that connects to a remote data source and retrieves resources).
     *
     * @return mixed
     */
    public function getSourceObject();

    /**
     * Action to be run after the resource has been retrieved from the remote source.
     *
     * @param bool $success
     *
     * @return mixed
     */
    public function postParseGlobalAction($success = true);

    /**
     * Action to be run after a single set of data retrieved from the remote source has been parsed.
     *
     * @param string $uniqueId Unique id of the single set to be treated (e.g. unique id of a message in a mail box)
     * @param bool   $success  True if the data has been parsed correctly; false otherwise.
     *
     * @return mixed
     */
    public function postParseUnitAction($uniqueId, $success = true);

    /**
     * Returns a string representation for the source object
     *
     * @return string
     */
    public function toString();
}