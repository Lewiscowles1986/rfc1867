<?php

namespace lewiscowles\Rfc\Encoder;

use InvalidArgumentException;
use Http\Message\StreamFactory;
use lewiscowles\Rfc\NodeInterface;
use lewiscowles\Rfc\Node\Attachment as AttachmentNode;
use lewiscowles\Rfc\EncoderInterface;
use Psr\Http\Message\StreamInterface;

class Attachment implements EncoderInterface
{
    /**
     * Stream factory.
     *
     * @var StreamFactory
     */
    protected $streamFactory;

    /**
     * Class constructor.
     *
     * @param StreamFactory $streamFactory Stream factory.
     */
    public function __construct(StreamFactory $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    /**
     * @inheritDoc
     */
    public function encode(NodeInterface $node): StreamInterface
    {
        if (!$node instanceof AttachmentNode) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported NodeInterface type, "%s" given, "%s" expected',
                get_class($node),
                AttachmentNode::class
            ));
        }

        return $this->streamFactory->createStream(sprintf(
            "%s: %s;%s %s=\"%s\"\n%s: %s\n%s\n%s",
            NodeInterface::HEADER_DISPOSITION,
            // Todo : disposition attachment should be form-data in normal cases
            // but switch to attachment if contained by a nested multipart/mixed
            // enveloppe
            $node->getDisposition(),
            $node->headerName(),
            
            NodeInterface::ATTRIB_FILENAME,
            $node->getFilename(),
            NodeInterface::HEADER_CONTENT_TYPE,
            $node->getMime(),

            // If any other content encoding have to be implemented, this should
            // be done here (by delegating it to another component/interface)
            $node->contentEncoding(),
            $node->getContents()
        ));
    }
}
