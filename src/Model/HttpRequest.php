<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use JsonSerializable;

class HttpRequest
{
    private string $uri;

    private HttpMethod $method;

    private ?object $entity;


    private MediaType $acceptMediaType;

    /**
     * @var array<string, string|string[]>
     */
    private array $headers;

    public function __construct(
        string $uri,
        MediaType $acceptMediaType = MediaType::JSON,
        HttpMethod $method = HttpMethod::GET,
        ?object $entity = null,
    ) {
        $this->uri = $uri;
        $this->acceptMediaType = $acceptMediaType;
        $this->method = $method;
        $this->entity = $entity; // for post requests
        $this->headers = ['Accept' => $this->acceptMediaType->value];
        if ($entity instanceof JsonSerializable) {
            $this->headers['Content-Type'] = MediaType::JSON->value;
        }
    }

    public function setAcceptMediaType(MediaType $mediaType): self
    {
        $this->acceptMediaType = $mediaType;

        return $this;
    }

    public function getAcceptMediaType(): MediaType
    {
        return $this->acceptMediaType;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function getEntity(): ?object
    {
        return $this->entity;
    }

    public function addHeader(string $name, string $value): self
    {
        // override
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, string|string[]>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string|string[]|null
     */
    public function getHeader(string $name, ?string $default = null): string | array | null
    {
        return $this->headers[$name] ?? $default;
    }
}
