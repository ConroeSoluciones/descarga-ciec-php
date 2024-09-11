<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model\Filter;

use Csfacturacion\Descarga\Model\Filter\DocTypeFilter;
use PHPUnit\Framework\TestCase;

class DocTypeFilterTest extends TestCase
{
    public function testCases(): void
    {
        self::assertEquals(DocTypeFilter::CFDI, DocTypeFilter::from('cfdi'));
        self::assertEquals(DocTypeFilter::RETENCION, DocTypeFilter::from('retencion'));
    }
}
