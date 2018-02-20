<?php

namespace lewiscowles\Rfc;

use Psr\Http\Message\StreamInterface;

/**
 * Encodes a NodeInterface into a multipart/form-data compatible StreamInterface
 */
interface EncoderInterface {
    /**
     * Encodes the input Node
     *
     * @param NodeInterface $node
     *
     * @return StreamInterface
     */
    public function encode(NodeInterface $node): StreamInterface;
}
