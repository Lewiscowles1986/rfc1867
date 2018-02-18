<?php

namespace lewiscowles\Rfc\Node;

use lewiscowles\Rfc\NodeInterface;


Final class Envelope implements NodeInterface {

    const TYPE_FORM_DATA = 'multipart/form-data';
    const TYPE_MIXED = 'multipart/mixed';

    const ATTRIB_BOUNDARY = "boundary";

    private $items = [];
    private $boundary = '';
    private $type = '';
    private $name = '';

    public function __construct(string $name, string $type='', $items = []) {
        $this->items = $items;
        $this->name = $name;
        $this->type = $type !== '' ? $type : self::TYPE_FORM_DATA;
        $this->boundary = sprintf(
            "%016X",
            random_int(0,PHP_INT_MAX)
        );
    }

    public function __toString() {
        return sprintf(
            "%s%s: %s, %s=--%s\n\n--%s\n%s--%s--%s",
            $this->getPrefix(),
            self::HEADER_CONTENT_TYPE,
            $this->type,
            self::ATTRIB_BOUNDARY,
            $this->boundary,
            $this->boundary,
            $this->getItemsAsString(),
            $this->boundary,
            $this->type == self::TYPE_MIXED ? '' : "\n"
        );
    }

    public function add(NodeInterface $node) {

        $name = $node->getName();

        $node = $this->fixNodeContentDisposition($node, $name);

        if(!isset($this->items[$name])) {
            $this->items[$name] = $node;
        } else {
            if(get_class($this->items[$name]) === get_class($this)) {
                $this->items[$name]->add($node);
            } else {
                $this->items[$name] = $node->add($this->items[$name]);
            }
        }
        return $this->items[$name];
    }

    public function getName() : string {
        return $this->name;
    }

    public function setContentDisposition(string $disposition) {
        $this->contentDisposition = $disposition;
    }

    public function getNested() : NodeInterface {
        return new Envelope($this->name, self::TYPE_MIXED, $this->getItems());
    }

    private function exists($name) {
        return isset($this->items[$name]);
    }

    private function getPrefix() {
        if($this->type == self::TYPE_MIXED) {
            return sprintf(
                "%s: %s; %s=\"%s\"\n",
                self::HEADER_DISPOSITION,
                self::DISPOSITION_FORMDATA,
                self::ATTRIB_NAME,
                $this->name
            );
        }
        return "";
    }

    private function getItems() {
        return $this->items;
    }

    private function getItemsAsString() {
        return implode(
            sprintf("--%s\n", $this->boundary),
            array_map([$this, 'getItemString'], $this->items)
        );
    }

    private function getItemString($item) {
        return "{$item}\n";
    }

    private function fixNodeContentDisposition(NodeInterface $node, string $name) {
      if($this->exists($name)) {
        $node->setContentDisposition(NodeInterface::DISPOSITION_ATTACHMENT);
      } else {
        $node->setContentDisposition(NodeInterface::DISPOSITION_FORMDATA);
      }
      return $node;
    }


    public function addAttachment(string $name, StreamInterface $value, string $mimeType, string $fileName) {
        $this->add(
            new Attachment(
                $fileName,
                $name,
                $value,
                $mimeType
            )
        );
    }

    public function addFormInput(string $name, string $value) {
        $this->add(new FormInput($name, $value));
    }
}
