<?php

namespace lewiscowles\Rfc;

use lewiscowles\Rfc\Envelope;
use lewiscowles\Rfc\FormInput;
use lewiscowles\Rfc\Attachment;

use Psr\Http\Message\StreamInterface;


Final class FormBody {

    private $state;

    public function __construct(Envelope $initialState) {
        $this->state = $initialState;
    }

    public function addAttachment(string $name, StreamInterface $value, string $mimeType, string $fileName) {
        $this->state->add(
            new Attachment(
                $fileName,
                $name,
                $value,
                $mimeType
            )
        );
    }

    public function addFormInput(string $name, string $value) {
        $this->state->add(new FormInput($name, $value));
    }

    public function __toString() {
        return "{$this->state}";
    }
}
