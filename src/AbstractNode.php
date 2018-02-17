<?php

namespace lewiscowles\Rfc;

use lewiscowles\Rfc\Envelope;
use lewiscowles\Rfc\NodeInterface;

use NotImplemented;

abstract class AbstractNode implements NodeInterface {
    protected $name;
    protected $contentDisposition = '';

    public function __toString() {
        throw new NotImplemented("error");
    }

    public function add(NodeInterface $node) {
        $list = [$node->getNested(), $this->getNested()];
        $envelope = new Envelope(
            $node->getName(),
            Envelope::TYPE_MIXED,
            $list
        );
        return $envelope;
    }

    public function getName() {
        return $this->name;
    }

    public function getNested() {
        throw new NotImplemented("error");
    }

    protected function getContentDisposition() {
        return $this->contentDisposition;
    }
}
