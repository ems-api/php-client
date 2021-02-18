<?php declare(strict_types=1);

namespace EmsApi\Http;

use EmsApi\Base;
use EmsApi\Params;
use ReflectionException;

/**
 * Class Response
 * @package EmsApi\Http
 */
class Response extends Base
{
    /**
     * @var string $url the url from where the response came back.
     */
    public $url = '';
    
    /**
     * @var Params $headers the headers that came back in the response.
     */
    public $headers;
    
    /**
     * @var string $contentType the content type of the response.
     */
    public $contentType = '';

    /**
     * @var string $httpMessage the returned http message.
     */
    public $httpMessage = '';
    
    /**
     * @var int $curlCode the curl response code.
     */
    public $curlCode = 0;
    
    /**
     * @var string $curlMessage the curl response message.
     */
    public $curlMessage = '';
    
    /**
     * @var bool $storeCurlInfo whether to store the curl info from the response.
     */
    public $storeCurlInfo = false;
    
    /**
     * @var Params $curlInfo the curl info returned in the response.
     */
    public $curlInfo;
    
    /**
     * @var Params $body the body of the response.
     */
    public $body;
    
    /**
     * @var array the list of http status codes definitions.
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC-reschke-http-status-308-07
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];
    
    /**
     * @var Request
     */
    public $request;
    
    /**
     * @var int the returned http code.
     */
    private $_httpCode = 0;

    /**
     * Contructor.
     *
     * @param Request $request
     *
     * @throws ReflectionException
     */
    public function __construct(Request $request)
    {
        $this->populate($request->params->toArray());
    }
    
    /**
     * Set the http code and http message based on the received response
     *
     * @param int $code
     * @return Response
     */
    public function setHttpCode(int $code): self
    {
        $this->_httpCode = $code = (int)$code;
        $this->httpMessage = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : null;
        return $this;
    }
    
    /**
     * Get the received http code.
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->_httpCode;
    }

    /**
     * Whether the response contains a curl error.
     *
     * @return bool
     */
    public function getIsCurlError(): bool
    {
        return (int)$this->curlCode > 0;
    }
    
    /**
     * Whether the response contains a http error.
     *
     * @return bool
     */
    public function getIsHttpError(): bool
    {
        return (int)$this->_httpCode < 200 || (int)$this->_httpCode >= 300;
    }
    
    /**
     * Whether the response is successful.
     *
     * @return bool
     */
    public function getIsSuccess(): bool
    {
        return $this->getIsCurlError() === false && $this->getIsHttpError() === false;
    }
    
    /**
     * Whether the response is not successful
     *
     * @return bool
     */
    public function getIsError(): bool
    {
        return $this->getIsSuccess() === false;
    }
    
    /**
     * If there is a http error or a curl error, retrieve the error message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        if ($this->getIsCurlError()) {
            return (string)$this->curlMessage;
        }
        
        if ($this->getIsHttpError()) {
            return (string)$this->httpMessage;
        }
        
        return '';
    }
    
    /**
     * If there is a http error or a curl error, retrieve the error code.
     *
     * @return int
     */
    public function getCode(): int
    {
        if ($this->getIsCurlError()) {
            return (int)$this->curlCode;
        }
        
        if ($this->getIsHttpError()) {
            return (int)$this->_httpCode;
        }
        
        return 0;
    }

    /**
     * Calls all the setters and populate the class based on the given array.
     *
     * @param array $params
     *
     * @return Response
     * @throws ReflectionException
     */
    public function populate(array $params = []): self
    {
        if ($this->storeCurlInfo) {
            $this->curlInfo = new Params($params);
        }

        $this->populateFromArray($params);
        return $this;
    }
}
