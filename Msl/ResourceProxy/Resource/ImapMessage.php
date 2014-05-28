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

use \Zend\Mail\Storage\Message as ZendMessage;
use Msl\ResourceProxy\Exception;

/**
 * Class ImapMessage represents a message from an imap account
 *
 * @category  Resource
 * @package   Msl\ResourceProxy\Resource
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class ImapMessage extends Email\Message
{
    /**
     * Output sub-folders
     */
    const MESSAGE_SUB_FOLDER = 'imap';

    /**
     * ToString constants
     */
    const MESSAGE_TO_STRING_PREFIX = 'imap_resource_';
}