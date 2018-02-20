<?php

namespace lewiscowles\Rfc\Node;

use lewiscowles\Rfc\NodeInterface;
use Psr\Http\Message\StreamInterface;

Final class Attachment extends AbstractNode {
    private $filename = '';
    private $stream;
    private $mime = '';
    private $disposition;

    const DEFAULT_MIME = "application/octet-stream";
    const BINARY_ENCODING = "content-transfer-encoding: binary\n";

    public function __construct(string $filename, string $name, StreamInterface $data, string $mime="", string $disposition="") {
        $this->filename = $filename;
        $this->stream = $data;
        $this->mime = $this->ifBlank($mime, self::DEFAULT_MIME);
        $this->disposition = $this->ifBlank(
            $disposition,
            self::DISPOSITION_FORMDATA
        );
        $this->name = $name;
    }

    public function __toString() {
        return sprintf(
            "%s: %s;%s %s=\"%s\"\n%s: %s\n%s\n%s",
            self::HEADER_DISPOSITION,
            $this->disposition,
            $this->headerName(),
            self::ATTRIB_FILENAME,
            $this->filename,
            self::HEADER_CONTENT_TYPE,
            $this->mime,
            $this->contentEncoding(),
            $this->getContents()
        );
    }

    public function getNested() : NodeInterface {
        return new Attachment(
            $this->filename,
            $this->name,
            $this->stream,
            $this->mime,
            self::DISPOSITION_ATTACHMENT
        );
    }

    public function headerName() {
        if($this->disposition === self::DISPOSITION_FORMDATA) {
            return sprintf(" %s=\"%s\";", self::ATTRIB_NAME, $this->name);
        }
        return "";
    }

    public function getMime() {
        return $this->mime;
    }

    public function isBinaryByMime(string $mimeType) {
        return (substr( $mimeType, 0, 5 ) !== "text/");
    }

    public function contentEncoding() {
        if($this->isBinaryByMime($this->mime)) {
            return self::BINARY_ENCODING;
        }
        return "";
    }

    public function getDisposition()
    {
        return $this->disposition;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getContents() {
        $out = "{$this->stream}";
        /*
        if($this->isBinaryByMime($this->mime)) {
            // TODO WorkOut Binary Encoding
        }
        */
        return $out;
    }

    private function ifBlank($value, $default) {
        return $value == "" ? $default : $value;
    }
}
