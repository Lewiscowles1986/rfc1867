<?php

namespace lewiscowles\Rfc;

use Psr\Http\Message\RequestInterface;

class Manager
{
    /**
     * Node encoder.
     *
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * Class constructor.
     *
     * @param EncoderInterface|null $encoder Node encoder.
     */
    public function __construct(EncoderInterface $encoder = null)
    {
        $this->encoder = $encoder;
    }

    /**
     * Configures a RequestInterface so it contains the body defined by the
     * $body NodeInterface.
     *
     * @param RequestInterface $request
     * @param NodeInterface    $body    
     * 
     * @return RequestInterface
     */
    public function configureRequest(RequestInterface $request, NodeInterface $body)
    {
        return $request
            ->withBody($this->encoder->encode($body))
            ->withMethod('POST')
            // there we got a problem
            //->withHeader('Content-Type', 'multipart/form-data; boundary=')
        ;
    }
}
