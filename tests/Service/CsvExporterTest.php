<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\CsvExporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporterTest extends TestCase
{
    public function testExportProductsReturnsStreamedResponse()
    {
        $exporter = new CsvExporter();
        
        // Mock a Product (Stub)
        $product = $this->createMock(Product::class);
        $product->method('getName')->willReturn('Test Product');
        $product->method('getDescription')->willReturn('Test Description');
        $product->method('getPrice')->willReturn(10.00);

        $response = $exporter->exportProducts([$product]);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename="products.csv"', $response->headers->get('Content-Disposition'));
    }
}
