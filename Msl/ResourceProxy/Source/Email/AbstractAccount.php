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

use Msl\ResourceProxy\Source\Parse\ParseResult;
use Msl\ResourceProxy\Source\SourceInterface;
use Msl\ResourceProxy\Source\SourceConfig;
use Zend\Mail\Storage\AbstractStorage as ZendStorage;
use Msl\ResourceProxy\Exception;

/**
 * Abstract Account class: object representation of an email account to which to connect through different protocols (imap, pop, etc.)
 *
 * @category  Email
 * @package   Msl\ResourceProxy\Source\Email
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
abstract class AbstractAccount implements SourceInterface, StorageInterface
{
    /**
     * Cryptographic protocols constants
     */
    const SSL_CRYPT_PROTOCOL = 'SSL';
    const TSL_CRYPT_PROTOCOL = 'TSL';

    /**
     * Filter value constants
     */
    const UNREAD_ONLY_MESSAGES_FILTER = 'unread_only';

    /**
     * The Source Name
     *
     * @var string
     */
    protected $name;

    /**
     * The Zend Storage object
     *
     * @var \Zend\Mail\Storage\AbstractStorage
     */
    protected $storage;

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

    /*************************************************
     *   C O N F I G U R A T I O N   M E T H O D S   *
     *************************************************/
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

        // Setting zend imap configuration
        $storageConfig = array(
            'host'     => $sourceConfig->getHost(),
            'user'     => $sourceConfig->getUsername(),
            'port'     => $sourceConfig->getPort(),
            'password' => $sourceConfig->getPassword()
        );
        if (!empty($cryptProtocol)) {
            $storageConfig['ssl'] = $cryptProtocol;
        }
        if (!empty($this->folder)) {
            $storageConfig['folder'] = $this->folder;
        }

        // Initializing zend imap instance
        $this->storage = $this->getStorageInstance($storageConfig);
    }

    /*******************************************
     *   P O S T   P A R S E   M E T H O D S   *
     *******************************************/
    /**
     * Action to be run after the resource has been retrieved from the remote source.
     *
     * @param bool $success True if all the data have been downloaded and used correctly; false otherwise.
     *
     * @return \Msl\ResourceProxy\Source\Parse\ParseResult|void
     */
    public function postParseGlobalAction($success = true)
    {
        // Default result
        return new ParseResult();
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
        // Default result
        return new ParseResult();
    }

    /*************************************
     *   G E N E R A L   M E T H O D S   *
     *************************************/
    /**
     * Returns the source object (i.e. the object that connects to a remote data source and retrieves resources).
     * (instance of Zend\Mail\Storage\Pop, Zend\Mail\Storage\Imap, etc)
     *
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->storage;
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
     * Returns a string representation for the source object
     *
     * @return string
     */
    public function toString()
    {
        return sprintf(
            'Storage Source Object [host:\'%s\'][port:\'%s\'][user:\'%s\']',
            $this->sourceConfig->getHost(),
            $this->sourceConfig->getPort(),
            $this->sourceConfig->getUsername()
        );
    }
} 