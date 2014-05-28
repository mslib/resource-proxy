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
     * @param string $sourceId   the source id for this resource (the source being the remote server in which the resource is allocated)
     * @param string $resourceId the resource id for the given resource object
     * @param mixed  $resource   the remote resource handler object (e.g. \Zend\Mail\Storage\Message)
     *
     * @return void
     */
    public function init($sourceId, $resourceId, $resource);

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
    public function getResourceId();

    /**
     * Returns the remote resource handler: object representation of a resource allocated on a remote server (e.g. an email server, a file server, etc.)
     *
     * @return mixed
     */
    public function getRemoteResourceHandler();

    /**
     * Returns the unique source id for the given resource object (the source being the remote server in which the resource is allocated)
     *
     * @return string
     */
    public function getSourceId();

    /**
     * Returns a string representation for the resource object (used in the output file name)
     *
     * @return string
     */
    public function toString();
} 