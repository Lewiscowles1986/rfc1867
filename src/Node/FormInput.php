<?php

namespace lewiscowles\Rfc\Node;

use lewiscowles\Rfc\NodeInterface;

Final class FormInput extends AbstractNode {
    private $value;

    public function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }

    public function __toString() {
        return sprintf(
            "%s: %s; %s=\"%s\"\n\n%s",
            self::HEADER_DISPOSITION,
            self::DISPOSITION_FORMDATA,
            self::ATTRIB_NAME,
            $this->name,
            $this->value
        );
    }

    public function getNested() : NodeInterface {
        return new FormInput($this->name, $this->value);
    }

    public function getValue()
    {
        return $this->value;
    }
}
