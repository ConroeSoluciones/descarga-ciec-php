<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga;

use JsonException;
use Ramsey\Dev\Tools\TestCase as BaseTestCase;
use RuntimeException;

use function file_exists;
use function file_get_contents;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * A base test case for common test functionality
 */
class TestCase extends BaseTestCase
{
    /**
     * @return array{
     *     'query_id': string,
     *     'cfdi_folio': string,
     *     'rfc': string,
     *     'ciec': string,
     *     'cs': array{
     *         'rfc': string,
     *         'password': string
     *     }
     * }
     */
    protected function getSecrets(): array
    {
        $path = __DIR__ . '/_files/secrets.json';
        if (!file_exists($path)) {
            throw new RuntimeException('The secrets.json file was not found.');
        }

        try {
            // @phpstan-ignore return.type
            return (array) json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
