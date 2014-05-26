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
class ImapMessage implements ResourceInterface
{
    /**
     * Output sub-folders
     */
    const IMAP_SUB_FOLDER             = 'imap';
    const IMAP_ATTACHMENTS_SUB_FOLDER = 'attachments';

    /**
     * Unique identifier for a given result object
     *
     * @var string
     */
    protected $uniqueResourceId;

    /**
     * The message object
     *
     * @var ZendMessage
     */
    protected $message;

    /**
     * String representation of this resource object
     *
     * @var string
     */
    protected $stringRepresentation;

    /**
     * Saves the current resource to the given path. Returns true if move action was successful; false otherwise.
     *
     * @param string $outputFolder the output folder path for this resource
     *
     * @throws \Msl\ResourceProxy\Exception\ResourceMoveContentException
     *
     * @return bool
     */
    public function moveToOutputFolder($outputFolder)
    {
        try {
            if ($this->message->isMultipart()) {
                // Getting the resource content (subject + body of the email)
                $contentPart = $this->message->getPart(1);
                $content = $contentPart->getContent();

                // Check for attachment
                // Getting second part of message object
                $part = $this->message->getPart(2);

                // Get the attachment file name
                if ($part->getHeaders()->has('Content-Disposition')) {
                    $fileName = $part->getHeaderField('Content-Disposition', 'filename');

                    // Get the attachment and decode
                    $attachment = base64_decode($part->getContent());

                    // Save the attachment
                    $attachmentFileName = $this->getAttachmentFileName($fileName, $outputFolder);
                    $finalAttOutputDirectory = $this->getAttachmentFileFolder($outputFolder);
                    if (!file_exists($finalAttOutputDirectory)) {
                        mkdir($finalAttOutputDirectory, 0775, true);
                    }
                    file_put_contents($attachmentFileName, $attachment);
                }
            } else {
                // Getting the resource content (subject + body of the email)
                $content = $this->message->getContent();
            }

            // Writing content to file
            // Setting the file name (output folder + imap sub folder + current object string representation + timestamp)
            $outputFileName = $this->getContentFileName($outputFolder);
            // Writing the content to the output file
            $finalOutputDirectory = $this->getContentFileFolder($outputFolder);
            if (!file_exists($finalOutputDirectory)) {
                mkdir($finalOutputDirectory, 0775, true);
            }
            file_put_contents($outputFileName, $content);
        } catch (\Exception $e) {
            throw new Exception\ResourceMoveContentException(
                sprintf(
                    'Error while moving the content of resource \'%s\' to the output folder \'%s\'. Error message is: \'%s\'.',
                    $this->toString(),
                    $outputFolder,
                    $e->getMessage()
                )
            );
        }
        return true;
    }

    /**
     * Returns the unique resource id for the given resource object
     *
     * @return string
     */
    public function getUniqueResourceId()
    {
        return $this->uniqueResourceId;
    }

    /**
     * Sets the unique resource id for the given resource object
     *
     * @param mixed $uniqueResourceId
     */
    public function setUniqueResourceId($uniqueResourceId)
    {
        $this->uniqueResourceId = $uniqueResourceId;
    }

    /**
     * Returns the remote resource handler
     *
     * @return mixed
     */
    public function getRemoteResourceHandler()
    {
        return $this->message;
    }


    /**
     * Initializes a Resource object
     *
     * @param string $uniqueResourceId the unique id for the given resource object
     * @param mixed $object            the remote resource handler object
     *
     * @throws BadResourceConfigurationException
     *
     * @return void
     */
    public function init($uniqueResourceId, $object)
    {
        // Setting unique resource id
        $this->uniqueResourceId = $uniqueResourceId;

        // Setting message object
        if (!$object instanceof ZendMessage) {
            throw new BadResourceConfigurationException(
                sprintf(
                    'Remote resource handler is expected to be an instance of \'\Zend\Mail\Storage\Message\', but got \'%s\'.',
                    get_class($object)
                )
            );
        }
        $this->message = $object;
    }

    /**
     * Returns a string representation for the resource object (used in the output file name)
     *
     * @return string
     */
    public function toString()
    {
        if (empty($this->stringRepresentation)) {
            $this->stringRepresentation = sprintf('imap_resource_%s', $this->getUniqueResourceId());
        }
        return $this->stringRepresentation;
    }

    /**
     * Returns the content file name as the concatenation of the given output folder + imap sub folder + current object string representation + timestamp.
     *
     * @param string $outputFolder the output folder
     *
     * @return string
     */
    protected function getContentFileName($outputFolder)
    {
        // Returning the content file name (output folder + imap sub folder + current object string representation + timestamp)
        return sprintf(
            "%s%s%s_%s",
            $this->getContentFileFolder($outputFolder),
            DIRECTORY_SEPARATOR,
            $this->toString(),
            time()
        );
    }

    /**
     * Returns the content file folder as the concatenation of the given output folder + imap sub folder.
     *
     * @param string $baseOutputFolder the base output folder
     *
     * @return string
     */
    protected function getContentFileFolder($baseOutputFolder)
    {
        // Returning the content file name (output folder + imap sub folder)
        return
            rtrim($baseOutputFolder, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            self::IMAP_SUB_FOLDER
        ;
    }

    /**
     * Returns the attachment file name as the concatenation of the given output folder + imap sub folder + current object string representation + timestamp.
     *
     * @param string $originalAttachmentFileName the base attachment file name
     * @param string $baseOutputFolder           the base output folder
     *
     * @return string
     */
    protected function getAttachmentFileName($originalAttachmentFileName, $baseOutputFolder)
    {
        // Getting extension from original file name
        $pathInfo = pathinfo($originalAttachmentFileName);
        $extension = '';
        if (isset($pathInfo['extension'])) {
            $extension = $pathInfo['extension'];
        }
        // Returning the attachment file name (output folder + imap sub folder + imap attachment sub folder
        // + original attachment file name + current object string representation + timestamp + extension)
        $attachmentFileName = sprintf(
            "%s%s%s_%s_%s.%s",
            $this->getAttachmentFileFolder($baseOutputFolder),
            DIRECTORY_SEPARATOR,
            $pathInfo['filename'],
            $this->toString(),
            time(),
            $extension
        );
        return rtrim($attachmentFileName, '.');
    }

    /**
     * Returns the attachment file folder as the concatenation of the given output folder + imap sub folder + imap attachment sub folder.
     *
     * @param string $baseOutputFolder the base output folder
     *
     * @return string
     */
    protected function getAttachmentFileFolder($baseOutputFolder)
    {
        // Returning the content file name (output folder + imap sub folder + imap attachment sub folder)
        return
            rtrim($baseOutputFolder, DIRECTORY_SEPARATOR) .
            DIRECTORY_SEPARATOR .
            self::IMAP_SUB_FOLDER .
            DIRECTORY_SEPARATOR .
            self::IMAP_ATTACHMENTS_SUB_FOLDER
        ;
    }


}