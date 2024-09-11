<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga\Model;

use Csfacturacion\Descarga\Util\Deserializable;
use JsonException;
use RuntimeException;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class HttpResponse
{
    /**
     * @param array<array-key, array<array-key, string>> $headers
     */
    public function __construct(
        private readonly string $rawResponse,
        private readonly int $code,
        private readonly array $headers = [],
    ) {
    }

    public function isOk(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    public function isInfo(): bool
    {
        return $this->code >= 100 && $this->code < 200;
    }

    public function isRedirect(): bool
    {
        return $this->code >= 300 && $this->code < 400;
    }

    public function isClientError(): bool
    {
        return $this->code >= 400 && $this->code < 500;
    }

    public function isServerError(): bool
    {
        return $this->code >= 500;
    }

    public function getRawResponse(): string
    {
        return $this->rawResponse;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param class-string<T> $class
     *
     * @return T|T[]
     *
     * @throws JsonException
     *
     * @template T of Deserializable
     */
    public function bodyAsModel(string $class): Deserializable | array
    {
        /** @var T|T[] $model */
        $model = $class::fromJson($this->rawResponse);

        return $model;
    }

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
    public function bodyAsArray(): array // @phpstan-ignore missingType.iterableValue
    {
        try {
            return (array) json_decode($this->rawResponse, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
