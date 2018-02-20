<?php

namespace lewiscowles\Rfc\Encoder;

use InvalidArgumentException;
use Http\Message\StreamFactory;
use lewiscowles\Rfc\NodeInterface;
use lewiscowles\Rfc\Node\FormInput as FormInputNode;
use lewiscowles\Rfc\EncoderInterface;
use Psr\Http\Message\StreamInterface;

class FormInput implements EncoderInterface
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
        if (!$node instanceof FormInputNode) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported NodeInterface type, "%s" given, "%s" expected',
                get_class($node),
                FormInputNode::class
            ));
        }

        return $this->streamFactory->createStream(sprintf(
            "%s: %s; %s=\"%s\"\n\n%s",
            NodeInterface::HEADER_DISPOSITION,
            NodeInterface::DISPOSITION_FORMDATA,
            NodeInterface::ATTRIB_NAME,
            $node->getName(),
            $node->getValue()
        ));
    }
}
