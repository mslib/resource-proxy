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

use Zend\Mail\Storage\Pop3 as ZendPop;
use Zend\Mail\Storage as ZendStorage;
use Msl\ResourceProxy\Exception;
use Msl\ResourceProxy\Resource\PopMessage;

/**
 * Source implementation for POP connections.
 *
 * @category  Source
 * @package   Msl\ResourceProxy\Source
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class Pop extends Email\AbstractAccount
{
    /**
     * Returns an Iterator instance for a list of Resource instances.
     * (in this case, list of emails to be parsed)
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getContentIterator()
    {
        // Initializing output array
        $output = new \ArrayIterator();

        // Getting total number of messages
        $maxMessage = $this->storage->countMessages();

        for($i=1; $i<=$maxMessage; $i++) {
            // Getting the message object
            $message = $this->storage->getMessage($i);

            if ($message instanceof \Zend\Mail\Storage\Message) {
                // Wrapping the result object into an appropriate ResourceInterface instance
                try {
                    $popMessage = new PopMessage();
                    $popMessage->init($this->getName(), $i, $message);
                } catch (\Exception $e) {
                    throw new Exception\SourceGetContentException(
                        sprintf(
                            'Error while getting the content for the resource unique id \'%s\'. Exception is: \'%s\'.',
                            $i,
                            $e->getMessage()
                        )
                    );
                }

                // Adding the wrapping object to the result iterator
                $output->append($popMessage);
            }
        }

        // Returning the result iterator
        return $output;
    }

    /**
     * Returns a string representation for the source object
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
            'Pop Source Object [host:\'%s\'][port:\'%s\'][user:\'%s\']',
            $this->sourceConfig->getHost(),
            $this->sourceConfig->getPort(),
            $this->sourceConfig->getUsername()
        );
    }

    /**
     * Returns an instance of a Zend\Mail\Storage\AbstractStorage child class
     *
     * @param array $storageConfig the storage config
     *
     * @return \Zend\Mail\Storage\AbstractStorage
     */
    public function getStorageInstance(array $storageConfig)
    {
        return new ZendPop($storageConfig);
    }
}