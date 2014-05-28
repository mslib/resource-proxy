<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Source\Email;

/**
 * Storage interface: basic behaviour for all storage classes
 *
 * @category  Email
 * @package   Msl\ResourceProxy\Source\Email
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
interface StorageInterface
{
    /**
     * Returns an instance of a Zend\Mail\Storage\AbstractStorage child class
     *
     * @param array $storageConfig the storage config
     *
     * @return \Zend\Mail\Storage\AbstractStorage
     */
    public function getStorageInstance(array $storageConfig);
} 