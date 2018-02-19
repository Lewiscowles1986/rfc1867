<?php

namespace lewiscowles\Rfc\Encoder;

use InvalidArgumentException;
use Http\Message\StreamFactory;
use lewiscowles\Rfc\NodeInterface;
use lewiscowles\Rfc\Node\Envelope as EnvelopeNode;
use lewiscowles\Rfc\Node\Attachment as AttachmentNode;
use lewiscowles\Rfc\Node\FormInput as FormInputNode;
use lewiscowles\Rfc\EncoderInterface;
use Psr\Http\Message\StreamInterface;

class Envelope implements EncoderInterface
{
    /**
     * Stream factory.
     *
     * @var StreamFactory
     */
    protected $streamFactory;
    
    /**
     * FormInput Node encoder.
     *
     * @var EncoderInterface 
     */
    protected $formInputEncoder;
    
    /**
     * Attachment node encoder.
     *
     * @var EncoderInterface
     */
    protected $attachmentEncoder;

    /**
     * Class constructor.
     *
     * @param StreamFactory    $streamFactory     Stream factory.
     * @param EncoderInterface $formInputEncoder  FormInput Node encoder.
     * @param EncoderInterface $attachmentEncoder Attachment node encoder.
     */
    public function __construct(StreamFactory $streamFactory, EncoderInterface $formInputEncoder, EncoderInterface $attachmentEncoder)
    {
        $this->streamFactory = $streamFactory;
        $this->formInputEncoder  = $formInputEncoder;
        $this->attachmentEncoder = $attachmentEncoder;
    }

    /**
     * @inheritDoc
     */
    public function encode(NodeInterface $node): StreamInterface
    {
        if (!$node instanceof EnvelopeNode) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported NodeInterface type, "%s" given, "%s" expected',
                get_class($node),
                EnvelopeNode::class
            ));
        }

        $boundary = sprintf(
            "%016X",
            random_int(0,PHP_INT_MAX)
        );

        return $this->streamFactory->createStream(sprintf(
            "%s%s: %s, %s=--%s\n\n--%s\n%s--%s--%s",
            $node->getPrefix(),
            NodeInterface::HEADER_CONTENT_TYPE,
            $node->getType(),
            EnvelopeNode::ATTRIB_BOUNDARY,
            $boundary,
            $boundary,
            $this->encodeItems($node, $boundary),
            $boundary,
            $node->getType() == EnvelopeNode::TYPE_MIXED ? '' : "\n"
        ));
    }

    protected function encodeItems(EnvelopeNode $envelope, string $boundary): string
    {
        $parts = [];
        foreach ($envelope->getItems() as $node) {
            if ($node instanceof EnvelopeNode) {
                $parts[] = $this->encode($node) . "\n";
                continue;
            }

            if ($node instanceof AttachmentNode) {
                $parts[] = $this->attachmentEncoder->encode($node) . "\n";
                continue;
            }

            if ($node instanceof FormInputNode) {
                $parts[] = $this->formInputEncoder->encode($node) . "\n";
                continue;
            }

            throw new InvalidArgumentException(sprintf(
                'Unsupported NodeInterface type, "%s" given',
                get_class($node)
            ));
        }

        return implode("--${boundary}\n", $parts);
    }
}
