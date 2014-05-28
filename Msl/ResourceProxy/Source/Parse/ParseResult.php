<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Source\Parse;

/**
 * Parse Result object. It holds the result of a unique or global resource parse action.
 *
 * @category  Parse
 * @package   Msl\ResourceProxy\Source\Parse
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class ParseResult
{
    /**
     * Result flag
     *
     * @var bool
     */
    protected $result;

    /**
     * Result string
     *
     * @var string
     */
    protected $message;

    /**
     * Result code
     *
     * @var string
     */
    protected $code;

    /**
     * Class constructor
     *
     * @param string $message the result message
     * @param string $code    the result code
     * @param bool   $result  the result flag
     */
    public function __construct($message = "", $code = "", $result = true)
    {
        // Setting object fields
        $this->message  = $message;
        $this->code     = $code;
        $this->result   = $result;
    }

    /**
     * Sets the result code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Returns the result code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the result message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the result message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the result flag (true|false)
     *
     * @param boolean $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Returns the result flag (true|false)
     *
     * @return boolean
     */
    public function getResult()
    {
        return $this->result;
    }
} 