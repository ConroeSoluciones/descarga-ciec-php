<h1 align="center">csfacturacion/descarga-ciec-php</h1>

<p align="center">
    <strong>API sencilla para interactuar con el servicio de descarga masiva mediante CIEC de CSFacturacion</strong>
</p>

<!--
TODO: Make sure the following URLs are correct and working for your project.
      Then, remove these comments to display the badges, giving users a quick
      overview of your package.

<p align="center">
    <a href="https://github.com/ConroeSoluciones/descarga-ciec-php"><img src="https://img.shields.io/badge/source-csfacturacion/descarga--ciec--php-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/csfacturacion/descarga-ciec-php"><img src="https://img.shields.io/packagist/v/csfacturacion/descarga-ciec-php.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/csfacturacion/descarga-ciec-php.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ConroeSoluciones/descarga-ciec-php/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/csfacturacion/descarga-ciec-php.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ConroeSoluciones/descarga-ciec-php/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/ConroeSoluciones/descarga-ciec-php/continuous-integration.yml?branch=main&style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ConroeSoluciones/descarga-ciec-php"><img src="https://img.shields.io/codecov/c/gh/ConroeSoluciones/descarga-ciec-php?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ConroeSoluciones/descarga-ciec-php"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2FConroeSoluciones%2Fdescarga-ciec-php%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>
-->


## About

<!--
TODO: Use this space to provide more details about your package. Try to be
      concise. This is the introduction to your package. Let others know what
      your package does and how it can help them build applications.
-->


SDK PHP para interactuar con el servicio de descarga masiva mediante CIEC de CSFacturacion. El SDK es simple
de usar ya que solo ofrece 2 sencillas interfaces para la descarga y consulta de resultados.


## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require csfacturacion/descarga-ciec-php
```


## Usage

### Descarga

Descargar es muy sencillo, para ello utilice la interface `DescargaCiecApi`:

```php
<?php
use Csfacturacion\Descarga\DescargaCiec;
use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\ParametersBuilder;

// credenciales de contratacion CSFacturacion
$descargaCiec = new DescargaCiec(new Credenciales('BBB010101BBB', 'FOO_BAR'));

// construir parametros de consulta con ParamsBuilder

$params = (new ParametersBuilder())
            ->accesoSat(new Credenciales('AAA010101AAA', 'CIEC'))
            ->tipoDoc(DocTypeFilter::CFDI) // CFDI convencional
            ->caso(CaseFilter::RECIBIDAS) // Emitidos
            ->fechaInicio(new DateTimeImmutable('first day of January 2024')) // mes de enero
            ->fechaFin(new DateTimeImmutable('last day of January 2024'))
            ->status(StatusFilter::CANCELADO) // solo los cancelados
            ->build();

// query contiene todou lo necesario para consultar el estatus y resultados
$query = $descargaCiec->makeQuery($params);
```

#### Consulta previa

Es posible trabajar sobre los resultados de una consulta realizada previamente, para ello:

```php
use Csfacturacion\Descarga\DescargaCiec;
use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\ParametersBuilder;
use Csfacturacion\Descarga\Model\Uuid;

// credenciales de contratacion CSFacturacion
$descargaCiec = new DescargaCiec(new Credenciales('BBB010101BBB', 'FOO_BAR'));
$query = $descargaCiec->search(new Uuid('a87c1d56-52f3-4680-a5cb-ddddb5786964'))
```

#### Consulta mediante folios (UUID)

Puede descargar XML o Metadata mediante el folio fiscal de los comprobantes:

```php
use Csfacturacion\Descarga\DescargaCiec;
use Csfacturacion\Descarga\Model\Credenciales;
use Csfacturacion\Descarga\Model\ParametersBuilder;
use Csfacturacion\Descarga\Model\Uuid;

// credenciales de contratacion CSFacturacion
$descargaCiec = new DescargaCiec(new Credenciales('BBB010101BBB', 'FOO_BAR'));
$query = $descargaCiec->byFolios([new Uuid('a87c1d56-52f3-4680-a5cb-ddddb5786964')])
```

### Obtener Resultados

Para obtener resultados de peticiones existentes, utilice la interface `QueryRetrieverApi`:

#### Progreso de la consulta
```php
<?php

use Csfacturacion\Descarga\QueryRetrieverApi;

/**@var QueryRetrieverApi $query */
while (!$query->isFinished()) {
    // do something
    send_status($query->getProgress()->getStatus(), $channel);
    // estatus
    $query->getProgress()->getStatus();
    // encontrados al momento
    $query->getProgress()->getFound();
}
```
#### Resumen de la consulta
Las siguientes acciones solo pueden llevarse a cabo una vez finalice la consulta:

```php
<?php

use Csfacturacion\Descarga\QueryRetrieverApi;

/**@var QueryRetrieverApi $query */
$s = $query->getSummary();
// cancelados encontrados
$s->getCancelados();
// total encontrados
$s->getTotal();
// total de páginas para consulta mediante paginacion
$s->getPages();
// ¿Hubo XML que no pudieron ser descargados?
$s->hasMissingXml();
// resultados mediante paginación

```

#### Descarga de resultados

```php
<?php

use Csfacturacion\Descarga\QueryRetrieverApi;
use Csfacturacion\Descarga\Model\Uuid;

/**@var QueryRetrieverApi $query */

// Descargar todos los XML en un ZIP
$query->asZip(__DIR__ . '/storage/cfdi/foo.zip');

// tambien, puede especificar un callback a la descarga del ZIP
$query->asZip(__DIR__ . '/storage/cfdi/foo.zip', function (int $totalBytes, int $currentBytes, array $extra){
    show_progress($totalBytes, $currentBytes);
});

// Mediante Páginacion JSON

if ($query->hasResults()) {
    $p = $query->getSummary()->getPages();
    for($i = 1; $i <= $p; $i++) {
        $cfdiList[] = $query->getResults($i);
    }
    // hacer algo con los CFDI Meta
}


$toSearch = new Uuid('1ad7605f-4ea6-4a48-b180-baa022220a83');

// Consultar CFDI Individual
$cfdi = $query->getCfdi($toSearch);
// XML Individual
$xml = $query->getXml($toSearch);
```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md](SECURITY.md) for instructions on submitting a vulnerability report.






## Copyright and License

csfacturacion/descarga-ciec-php is copyright © [CSFacturacion](https://csfacturacion.com)
and licensed for use under the terms of the
MIT License (MIT). Please see [LICENSE](LICENSE) for more information.


