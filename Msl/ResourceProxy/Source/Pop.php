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
class Pop implements SourceInterface
{
    /**
     * Filter index constants NONE FOR THE MOMENT
     */

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
     * The POP Zend Storage object
     *
     * @var \Zend\Mail\Storage\Pop
     */
    protected $pop;

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
//TODO no filters implemented for pop

        // Setting zend pop configuration
        $popConfig = array(
            'host'     => $sourceConfig->getHost(),
            'user'     => $sourceConfig->getUsername(),
            'port'     => $sourceConfig->getPort(),
            'password' => $sourceConfig->getPassword()
        );
        if (!empty($cryptProtocol)) {
            $popConfig['ssl'] = $cryptProtocol;
        }

        // Initializing zend pop instance
        $this->pop = new ZendPop($popConfig);
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
        $maxMessage = $this->pop->countMessages();

        for($i=1; $i<=$maxMessage; $i++) {
            // Getting the message object
            $message = $this->pop->getMessage($i);

            if ($message instanceof \Zend\Mail\Storage\Message) {
                // Wrapping the result object into an appropriate ResourceInterface instance
                try {
                    $popMessage = new PopMessage();
                    $popMessage->init($i, $message);
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
     * (instance of Zend\Mail\Storage\Pop)
     *
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->pop;
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
        // Not needed for the moment
        return;
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
}