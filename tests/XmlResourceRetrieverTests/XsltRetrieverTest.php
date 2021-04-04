<?php

declare(strict_types=1);

namespace XmlResourceRetrieverTests;

use XmlResourceRetriever\XsltRetriever;

final class XsltRetrieverTest extends RetrieverTestCase
{
    public function testRetrieveRecursive(): void
    {
        $localPath = $this->buildPath('recursive');
        $this->pathToClear($localPath);
        $retriever = new XsltRetriever($localPath);
        $remote = 'http://localhost:8999/xslt/entities/ticket.xslt';
        $expectedRemotes = [
            $retriever->buildPath($remote),
            $retriever->buildPath('http://localhost:8999/xslt/articles/books.xslt'),
        ];

        // verify path of downloaded file
        $local = $retriever->retrieve($remote);
        $this->assertEquals($expectedRemotes[0], $local);
        // verify file exists
        foreach ($expectedRemotes as $expectedRemote) {
            $this->assertFileExists($expectedRemote);
        }
        $this->assertXmlFileEqualsXmlFile($local, $this->assetPath('expected-ticket.xslt'));
    }

    public function testRetrieveComplexStructure(): void
    {
        if (! is_dir($this->publicPath('www.sat.gob.mx'))) {
            $this->markTestSkipped('Must download complex structures from www.sat.gob.mx');
        }
        $localPath = $this->buildPath('SATXSLT');
        $this->pathToClear($localPath);
        $remotePrefix = 'http://localhost:8999/www.sat.gob.mx/sitio_internet/';
        $remote = $remotePrefix . 'cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt';
        $retriever = new XsltRetriever($localPath);
        $expectedRemotes = [
            'cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt',
            'cfd/2/cadenaoriginal_2_0/utilerias.xslt',
            'cfd/EstadoDeCuentaCombustible/ecc11.xslt',
            'cfd/donat/donat11.xslt',
            'cfd/divisas/divisas.xslt',
            'cfd/implocal/implocal.xslt',
            'cfd/leyendasFiscales/leyendasFisc.xslt',
            'cfd/pfic/pfic.xslt',
            'cfd/TuristaPasajeroExtranjero/TuristaPasajeroExtranjero.xslt',
            'cfd/nomina/nomina12.xslt',
            'cfd/cfdiregistrofiscal/cfdiregistrofiscal.xslt',
            'cfd/pagoenespecie/pagoenespecie.xslt',
            'cfd/aerolineas/aerolineas.xslt',
            'cfd/valesdedespensa/valesdedespensa.xslt',
            'cfd/consumodecombustibles/consumodecombustibles.xslt',
            'cfd/notariospublicos/notariospublicos.xslt',
            'cfd/vehiculousado/vehiculousado.xslt',
            'cfd/servicioparcialconstruccion/servicioparcialconstruccion.xslt',
            'cfd/renovacionysustitucionvehiculos/renovacionysustitucionvehiculos.xslt',
            'cfd/certificadodestruccion/certificadodedestruccion.xslt',
            'cfd/arteantiguedades/obrasarteantiguedades.xslt',
            'cfd/ComercioExterior11/ComercioExterior11.xslt',
            'cfd/ine/ine11.xslt',
            'cfd/iedu/iedu.xslt',
            'cfd/ventavehiculos/ventavehiculos11.xslt',
            'cfd/terceros/terceros11.xslt',
            'cfd/Pagos/Pagos10.xslt',
        ];
        // verify path of downloaded file
        $retriever->retrieve($remote);
        // verify file exists
        foreach ($expectedRemotes as $expectedRemote) {
            $this->assertFileExists($retriever->buildPath($remotePrefix . $expectedRemote));
        }
    }
}
