<?php

use PHPUnit\Framework\TestCase;

use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;

use lewiscowles\Rfc\Envelope;
use lewiscowles\Rfc\FormBody;
use lewiscowles\Rfc\NodeInterface;

class BasicTest extends TestCase
{
    const JOE_BLOW_TEXT = 'content-disposition: form-data; name="submitter"'."\n\n".'Joe Blow';
    const FILE1_TEXT_FLAT = 'content-disposition: form-data; name="file1"; filename="file1.txt"'."\n".'content-type: text/plain'."\n\n".
                            '...contents of file1.txt...';
    const FILE1_TEXT_MULTI = 'content-disposition: attachment; filename="file1.txt"'."\n".'content-type: text/plain'."\n\n".
                       '...contents of file1.txt...';
    const FILE2_TEXT_MULTI = 'content-disposition: attachment; filename="file2.gif"'."\n".'content-type: image/gif'."\n".
                       'content-transfer-encoding: binary'."\n\n".'...contents of file2.gif...';
    const ENVELOPE_MULTI = 'content-disposition: form-data; name="pics"'."\n".'content-type: multipart/mixed, boundary=';

    public function setup()
    {
        $this->body = new FormBody(new Envelope('ROOT'));
    }

    /**
     * @test
     */
    public function it_can_be_passed_to_a_psr7_compatible_request_as_body() {
        $request = new Request('POST', 'https://www.github.com');
        $out = $request->withBody(stream_for("$this->body"));
        $this->assertNotEquals(
            $request->getBody()->getContents(),
            $out->getBody()->getContents(),
            "The Body Has Not Been Modified"
        );
    }

    /**
     * @test
     */
    public function it_accepts_a_form_input() {
        $this->body->addFormInput('number', $this->getValue('number'));
    }

    /**
     * @test
     */
    public function it_accepts_many_parts() {
        $this->body->addFormInput('number', $this->getValue('number'));
        $this->body->addFormInput('float', $this->getValue('float'));
        $this->body->addFormInput('string', $this->getValue('string'));
    }

    /**
     * @test
     */
    public function it_produces_the_output_described_in_rfc_example1() {
        $this->body->addFormInput('submitter', 'Joe Blow');
        $this->body->addAttachment('file1', stream_for('...contents of file1.txt...'), 'text/plain', 'file1.txt');
        $body = "{$this->body}";

        $this->assertContains(self::JOE_BLOW_TEXT, $body);
        $this->assertContains(self::JOE_BLOW_TEXT, $body);
        $this->assertContains(self::FILE1_TEXT_FLAT, $body);
    }

    /**
     * @test
     */
    public function it_produces_the_output_described_in_rfc_example2() {
        $this->body->addFormInput('submitter', 'Joe Blow');

        // note when adding multiple attachments, the value changes after the first is added.
        $this->body->addAttachment('pics', stream_for('...contents of file1.txt...'), 'text/plain', 'file1.txt');
        $this->body->addAttachment('pics', stream_for('...contents of file2.gif...'), 'image/gif', 'file2.gif');

        $body = "{$this->body}";

        $this->assertContains(self::JOE_BLOW_TEXT, $body);
        $this->assertContains(self::ENVELOPE_MULTI, $body);
        $this->assertContains(self::FILE1_TEXT_MULTI, $body);
        $this->assertContains(self::FILE2_TEXT_MULTI, $body);
        //file_put_contents(__DIR__ . '/../log/out', $body);
    }

    protected function getValue($type) {
        switch(strtolower($type)) {
            case 'number':
                return rand(PHP_INT_MIN, PHP_INT_MAX);
            case 'float':
                return lcg_value()*(float)(rand(0,99));
            case 'string':
                return 'string';
            case 'file':
                return 'filefilefilefile';
        }
        return true;
    }
}
