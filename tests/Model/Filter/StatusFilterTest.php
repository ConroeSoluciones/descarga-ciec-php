<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model\Filter;

use Csfacturacion\Descarga\Model\Filter\StatusFilter;
use Csfacturacion\Test\Descarga\TestCase;

class StatusFilterTest extends TestCase
{
    public function testCases(): void
    {
        self::assertEquals(StatusFilter::VIGENTE, StatusFilter::from('vigentes'));
        self::assertEquals(StatusFilter::CANCELADO, StatusFilter::from('cancelados'));
        self::assertEquals(StatusFilter::TODOS, StatusFilter::from('todos'));
    }
}
