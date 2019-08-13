<?php
declare(strict_types=1);

namespace GimmeUrl;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Request context serves as a server request abstraction and is responsible
 * for building base URL containing scheme, host and port.
 */
class RequestContext
{
    public const DEFAULT_HTTP_PORT = 80;
    public const DEFAULT_HTTPS_PORT = 443;
    public const DEFAULT_PORTS = [
        self::DEFAULT_HTTP_PORT,
        self::DEFAULT_HTTPS_PORT
    ];

    private $scheme;
    private $host;
    private $httpPort;
    private $httpsPort;
    private $isSecure;

    private function __construct(
        string $scheme,
        string $host,
        ?int $httpPort,
        ?int $httpsPort,
        bool $isSecure = false
    ) {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->httpPort = $httpPort ?? static::DEFAULT_HTTP_PORT;
        $this->httpsPort = $httpsPort ?? static::DEFAULT_HTTPS_PORT;
        $this->isSecure = $isSecure;
    }

    public static function fromUri(UriInterface $uri, bool $isSecure = false): self
    {
        return new static(
            $uri->getScheme(),
            $uri->getHost(),
            $isSecure ? static::DEFAULT_HTTP_PORT : $uri->getPort(),
            $isSecure ? $uri->getPort() : static::DEFAULT_HTTPS_PORT,
            $isSecure
        );
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        return static::fromUri($request->getUri(), static::isSecureRequest($request));
    }

    private static function isSecureRequest(ServerRequestInterface $request): bool
    {
        $https = $request->getServerParams()['HTTPS'] ?? '';

        return !empty($https) && strtolower($https) !== 'off';
    }

    public function buildBaseUrl(): string
    {
        $port = $this->isDefaultPort() ? '' : ':' . $this->getPort();

        return sprintf('%s://%s%s', $this->scheme, $this->host, $port);
    }

    private function getPort(): int
    {
        return $this->isSecure ? $this->httpsPort : $this->httpPort;
    }

    private function isDefaultPort(): bool
    {
        return in_array($this->getPort(), static::DEFAULT_PORTS, true);
    }
}
