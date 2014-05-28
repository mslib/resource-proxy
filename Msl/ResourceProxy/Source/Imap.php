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

use Zend\Mail\Storage\Imap as ZendImap;
use Zend\Mail\Storage as ZendStorage;
use Msl\ResourceProxy\Exception;
use Msl\ResourceProxy\Resource\ImapMessage;
use Msl\ResourceProxy\Source\Parse\ParseResult;

/**
 * Source implementation for IMAP connections.
 *
 * @category  Source
 * @package   Msl\ResourceProxy\Source
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class Imap extends Email\AbstractAccount
{
    /**
     * Filter index constants
     */
    const MESSAGES_STATUS_FILTER = 'message_status';
    const FOLDER_FILTER          = 'folder';

    /**
     * Sets all the required parameters to configure a given Source instance.
     *
     * @param SourceConfig $sourceConfig
     *
     * @throws \Msl\ResourceProxy\Exception\BadSourceConfigurationException
     *
     * @return void
     */
    public function setConfig(SourceConfig $sourceConfig)
    {
        // FILTERS
        // 1. Setting message status filter
        $filters = $sourceConfig->getFilter();
        if (isset($filters[self::MESSAGES_STATUS_FILTER])) {
            $messageStatus = $filters[self::MESSAGES_STATUS_FILTER];
            if ($messageStatus === self::UNREAD_ONLY_MESSAGES_FILTER) {
                $this->unreadOnly = true;
            }
        }
        // 2. Setting folder filter
        if (isset($filters[self::FOLDER_FILTER])) {
            $this->folder = $filters[self::FOLDER_FILTER];
        }

        // Calling parent method to set the rest of the required configuration
        parent::setConfig($sourceConfig);
    }

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
                // Adding only unread messages to result iterator
                if ($this->unreadOnly && $message->hasFlag(ZendStorage::FLAG_SEEN)) {
                    continue;
                }

                // Wrapping the result object into an appropriate ResourceInterface instance
                try {
                    $imapMessage = new ImapMessage();
                    $imapMessage->init($this->getName(), $i, $message);
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
                $output->append($imapMessage);
            }
        }

        // Returning the result iterator
        return $output;
    }

    /**
     * Action to be run after a single set of data retrieved from the remote source has been parsed.
     *
     * @param string $uniqueId Unique id of the single set to be treated (e.g. unique id of a message in a mail box)
     * @param bool   $success  True if the data of a given resource have been downloaded and used correctly; false otherwise.
     *
     * @return \Msl\ResourceProxy\Source\Parse\ParseResult|void
     */
    public function postParseUnitAction($uniqueId, $success = true)
    {
        // Setting parsed message flag to SEEN
        $result = new ParseResult();
        try {
            if ($success) {
                $this->storage->setFlags($uniqueId, array(ZendStorage::FLAG_SEEN));
            } else {
                $this->storage->setFlags($uniqueId, array(ZendStorage::FLAG_RECENT));
            }
        } catch (\Exception $e) {
            $result->setResult(false);
            $result->setMessage($e->getMessage());
            $result->setCode($e->getCode());
        }

        // Returning the result object
        return $result;
    }

    /**
     * Returns a string representation for the source object
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
            'Imap Source Object [host:\'%s\'][port:\'%s\'][user:\'%s\']',
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
        return new ZendImap($storageConfig);
    }
}