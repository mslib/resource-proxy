<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Resource;

/**
 * Basic behaviour for all Resource instances.
 *
 * @category  Resource
 * @package   Msl\ResourceProxy\Resource
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
interface ResourceInterface
{
    /**
     * Initializes a Resource object
     *
     * @param string $uniqueResourceId  the unique id for the given resource object
     * @param mixed  $object            the remote resource handler object
     *
     * @throws \Exception
     *
     * @return void
     */
    public function init($uniqueResourceId, $object);

    /**
     * Saves the current resource to the given path. Returns true if move action was successful; false otherwise.
     *
     * @param string $outputFolder the output folder path for this resource
     *
     * @throws \Msl\ResourceProxy\Exception\ResourceMoveContentException
     *
     * @return bool
     */
    public function moveToOutputFolder($outputFolder);

    /**
     * Returns the unique resource id for the given resource object
     *
     * @return string
     */
    public function getUniqueResourceId();

    /**
     * Returns the remote resource handler
     *
     * @return mixed
     */
    public function getRemoteResourceHandler();

    /**
     * Returns a string representation for the resource object (used in the output file name)
     *
     * @return string
     */
    public function toString();
} 