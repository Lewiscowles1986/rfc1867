<?php

namespace lewiscowles\Rfc;

use lewiscowles\Rfc\Envelope;
use lewiscowles\Rfc\FormInput;
use lewiscowles\Rfc\Attachment;
use lewiscowles\Rfc\NodeInterface;

use Psr\Http\Message\RequestInterface;

use GuzzleHttp\Psr7\Stream;
use function GuzzleHttp\Psr7\stream_for;

Final class FormBody {

    private $state;
    const HTTP_METHOD = "POST";

    public function __construct(Envelope $initialState) {
        $this->state = $initialState;
    }

    public function addAttachment(string $name, Stream $value, string $mimeType, string $fileName) {
        $this->state->add(
            new Attachment(
                $fileName,
                $name,
                $value,
                $mimeType,
                $this->attachmentType($name)
            )
        );
    }

    private function attachmentType($name) {
        if($this->state->exists($name)) {
            return NodeInterface::DISPOSITION_ATTACHMENT;
        }
        return NodeInterface::DISPOSITION_FORMDATA;
    }

    public function addFormInput(string $name, string $value) {
        $this->state->add(new FormInput($name, $value));
    }

    public function __toString() {
        return "{$this->state}";
    }

    public function submit(RequestInterface $request) {
        return $request
            ->withBody(
                stream_for("$this->state")
            )
            ->withMethod(self::HTTP_METHOD);
    }
}
