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

/**
 * Source implementation for IMAP connections.
 *
 * @category  Source
 * @package   Msl\ResourceProxy\Source
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class Imap implements SourceInterface
{
    /**
     * Filter index constants
     */
    const MESSAGES_STATUS_FILTER = 'message_status';
    const FOLDER_FILTER          = 'folder';

    /**
     * Filter value constants
     */
    const UNREAD_ONLY_MESSAGES_FILTER = 'unread_only';

    /**
     * Cryptographic protocols constants
     */
    const SSL_CRYPT_PROTOCOL = 'SSL';
    const TSL_CRYPT_PROTOCOL = 'TSL';

    /**
     * The Source Name
     *
     * @var string
     */
    protected $name;

    /**
     * The IMAP Zend Storage object
     *
     * @var \Zend\Mail\Storage\Imap
     */
    protected $imap;

    /**
     * Message filter flag
     *
     * @var bool
     */
    protected $unreadOnly = false;

    /**
     * The folder to be selected
     *
     * @var bool
     */
    protected $folder;

    /**
     * The SourceConfig object for this source
     *
     * @var SourceConfig
     */
    protected $sourceConfig;

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
        // Setting the source config
        $this->sourceConfig = $sourceConfig;

        // Setting object fields from configuration
        $this->name = $sourceConfig->getName();

        // Checking if crypt protocol is accepted
        $cryptProtocol = $sourceConfig->getCryptProtocol();
        if (!empty($cryptProtocol)) {
            if ($cryptProtocol !== self::SSL_CRYPT_PROTOCOL && $cryptProtocol !== self::TSL_CRYPT_PROTOCOL) {
                throw new Exception\BadSourceConfigurationException(
                    sprintf(
                        'Unrecognized cryptographic protocol \'%s\'. Accepted values are:  \'%s\'.',
                        $cryptProtocol,
                        self::SSL_CRYPT_PROTOCOL
                    )
                );
            }
        }

        // Setting FILTERS
        // Setting message status filter
        $filters = $sourceConfig->getFilter();
        if (isset($filters[self::MESSAGES_STATUS_FILTER])) {
            $messageStatus = $filters[self::MESSAGES_STATUS_FILTER];
            if ($messageStatus === self::UNREAD_ONLY_MESSAGES_FILTER) {
                $this->unreadOnly = true;
            }
        }
        // Setting folder filter
        if (isset($filters[self::FOLDER_FILTER])) {
            $this->folder = $filters[self::FOLDER_FILTER];
        }

        // Setting zend imap configuration
        $imapConfig = array(
            'host'     => $sourceConfig->getHost(),
            'user'     => $sourceConfig->getUsername(),
            'port'     => $sourceConfig->getPort(),
            'password' => $sourceConfig->getPassword()
        );
        if (!empty($cryptProtocol)) {
            $imapConfig['ssl'] = $cryptProtocol;
        }
        if (!empty($this->folder)) {
            $imapConfig['folder'] = $this->folder;
        }

        // Initializing zend imap instance
        $this->imap = new ZendImap($imapConfig);
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
        $maxMessage = $this->imap->countMessages();

        for($i=1; $i<=$maxMessage; $i++) {
            // Getting the message object
            $message = $this->imap->getMessage($i);

            if ($message instanceof \Zend\Mail\Storage\Message) {
                // Adding only unread messages to result iterator
                if ($this->unreadOnly && $message->hasFlag(ZendStorage::FLAG_SEEN)) {
                    continue;
                }

                // Wrapping the result object into an appropriate ResourceInterface instance
                try {
                    $imapMessage = new ImapMessage();
                    $imapMessage->init($i, $message);
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
     * Returns the name of the current Source instance.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the source object (i.e. the object that connects to a remote data source and retrieves resources).
     * (instance of Zend\Mail\Storage\Imap)
     *
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->imap;
    }

    /**
     * Action to be run after the resource has been retrieved from the remote source.
     *
     * @param bool $success
     *
     * @return mixed
     */
    public function postParseGlobalAction($success = true)
    {
        // Not needed for the moment
        return;
    }

    /**
     * Action to be run after a single set of data retrieved from the remote source has been parsed.
     *
     * @param string $uniqueId Unique id of the single set to be treated (e.g. unique id of a message in a mail box)
     * @param bool   $success  True if the data has been parsed correctly; false otherwise.
     *
     * @return mixed
     */
    public function postParseUnitAction($uniqueId, $success = true)
    {
        // Setting parsed message flag to SEEN
        if ($success) {
            $this->imap->setFlags($uniqueId, array(ZendStorage::FLAG_SEEN));
        } else {
            $this->imap->setFlags($uniqueId, array(ZendStorage::FLAG_RECENT));
        }
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
}