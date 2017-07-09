<?php

namespace lewiscowles\Rfc;

interface NodeInterface {

    const DISPOSITION_FORMDATA = "form-data";
    const DISPOSITION_ATTACHMENT = "attachment";

    const HEADER_DISPOSITION = "content-disposition";
    const HEADER_CONTENT_TYPE = "content-type";
    const ATTRIB_NAME = "name";
    const ATTRIB_FILENAME = "filename";

    public function __toString();

    public function add(NodeInterface $node);

    public function getName();

    public function getNested();
}
