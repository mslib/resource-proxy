<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Exception;

/**
 * Exception thrown when it was not possible to find a SourceInterface instance for a given name
 *
 * @category  Exception
 * @package   Msl\ResourceProxy\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class SourceNotFoundException extends \Exception implements ResourceProxyExceptionInterface
{
    /**
     * The source name
     *
     * @var string
     */
    protected $sourceName;

    /**
     * Class Constructor
     *
     * @param array         $sourceName the source name
     * @param string        $message    the message for this exception
     * @param int           $code       the code for this exception
     * @param \Exception    $previous   the previous exception object
     */
    public function __construct($sourceName, $message = "", $code = 0, \Exception $previous = null)
    {
        // Setting object fields
        parent::__construct($message, $code, $previous);
        $this->sourceName = $sourceName;
    }

    /**
     * Sets the not found source name
     *
     * @param string $sourceName the source name
     */
    public function setSourceName($sourceName)
    {
        $this->sourceName = $sourceName;
    }

    /**
     * Returns the not found source name
     *
     * @return string
     */
    public function getSourceName()
    {
        return $this->sourceName;
    }
}