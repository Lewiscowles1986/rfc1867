<?php

namespace lewiscowles\Rfc\Encoder;

use Http\Message\StreamFactory;
use lewiscowles\Rfc\NodeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Legacy encoder implementation, relying on the __toString method of nodes.
 */
class Legacy implements EncoderInterface {
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
        return $this->streamFactory->createStream((string)$node);
    }
}
