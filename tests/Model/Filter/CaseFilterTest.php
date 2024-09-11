<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model\Filter;

use Csfacturacion\Descarga\Model\Filter\CaseFilter;
use PHPUnit\Framework\TestCase;

class CaseFilterTest extends TestCase
{
    public function testCases(): void
    {
        self::assertEquals(CaseFilter::RECIBIDAS, CaseFilter::from('recibidas'));
        self::assertEquals(CaseFilter::EMITIDAS, CaseFilter::from('emitidas'));
        self::assertEquals(CaseFilter::TODAS, CaseFilter::from('todas'));
    }
}
