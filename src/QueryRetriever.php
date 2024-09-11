<?php

declare(strict_types=1);

namespace Csfacturacion\Descarga;

use Csfacturacion\Descarga\Error\CfdiNotFoundError;
use Csfacturacion\Descarga\Error\HttpComunicationError;
use Csfacturacion\Descarga\Error\NotEnoughResultsError;
use Csfacturacion\Descarga\Error\QueryNotReadyYetError;
use Csfacturacion\Descarga\Error\XmlNotFoundError;
use Csfacturacion\Descarga\Model\CfdiMeta;
use Csfacturacion\Descarga\Model\MediaType;
use Csfacturacion\Descarga\Model\Progress;
use Csfacturacion\Descarga\Model\Summary;
use Csfacturacion\Descarga\Model\Uuid;
use Csfacturacion\Descarga\Util\HttpClient;
use Csfacturacion\Descarga\Util\RequestFactory;
use JsonException;
use LogicException;
use RuntimeException;

class QueryRetriever implements QueryRetrieverApi
{
    private Uuid $folio;
    private HttpClient $httpClient;
    private RequestFactory $requestFactory;
    private ?Summary $summary;

    public function __construct(Uuid $folio, HttpClient $httpClient, RequestFactory $factory)
    {
        $this->folio = $folio;
        $this->httpClient = $httpClient;
        $this->requestFactory = $factory;
        $this->summary = null;
    }

    /**
     * @throws HttpComunicationError
     */
    public function getProgress(): Progress
    {
        $resp = $this->httpClient->send($this->requestFactory->newProgressRequest($this->folio));
        if ($resp->getCode() !== 200) {
            // no deberia ocurrir
            throw new LogicException("La consulta con folio: {$resp->getCode()} no existe");
        }

        try {
            /** @var Progress $progress */
            $progress = $resp->bodyAsModel(Progress::class);

            return $progress;
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function isFinished(): bool
    {
        return $this->getProgress()->getStatus()->isFinished();
    }

    public function isFailed(): bool
    {
        return $this->getProgress()->getStatus()->isFailed();
    }

    public function isToRepeat(): bool
    {
        return $this->getProgress()->getStatus()->isToRepeat();
    }

    public function hasResults(): bool
    {
        return $this->getSummary()->getTotal() > 0;
    }

    public function getFolio(): Uuid
    {
        return $this->folio;
    }

    public function getSummary(): Summary
    {
        if ($this->summary !== null) {
            return $this->summary;
        }
        $this->finishedValidate();

        $response = $this->httpClient->send($this->requestFactory->newSummaryRequest($this->folio));
        try {
            /** @var Summary $summary */
            $summary = $response->bodyAsModel(Summary::class);
            $this->summary = $summary;

            return $summary;
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getResults(int $page): array
    {
        $this->finishedValidate();
        $totalPages = $this->getSummary()->getPages();
        if ($page > $totalPages) {
            throw new NotEnoughResultsError(
                "Hay un total de $totalPages y se solicito la pagina $page",
            );
        }

        $response = $this->httpClient->send($this->requestFactory->newPageRequest($this->folio, $page));

        try {
            /** @var CfdiMeta[] $data */
            $data = $response->bodyAsModel(CfdiMeta::class);

            return $data;
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getCfdi(Uuid $folio): CfdiMeta
    {
        $response = $this->httpClient->send($this->requestFactory->newCfdiRequest($folio, MediaType::JSON));
        if (!$response->isOk()) {
            throw new CfdiNotFoundError($folio);
        }

        try {
            /** @var CfdiMeta $model */
            $model = $response->bodyAsModel(CfdiMeta::class);

            return $model;
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function getXml(Uuid $folio): string
    {
        $response = $this->httpClient->send($this->requestFactory->newCfdiRequest($folio, MediaType::XML));
        if (!$response->isOk()) {
            throw new XmlNotFoundError($folio);
        }

        if ($response->getRawResponse() === '') {
            throw new XmlNotFoundError($folio);
        }

        return $response->getRawResponse();
    }

    public function asZip(string $filepath, ?callable $downloadCallback = null): void
    {
        $this->httpClient->sendAndSave(
            $this->requestFactory->newZipRequest($this->folio),
            $filepath,
            $downloadCallback,
        );
    }

    /**
     * @throws QueryNotReadyYetError
     */
    private function finishedValidate(): void
    {
        if (!$this->isFinished()) {
            throw new QueryNotReadyYetError($this->folio);
        }
    }
}
