<?php

namespace Minhyung\KoreaDays\Tests\Feature;

use Minhyung\KoreaDays\Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

#[CoversClass(Service::class)]
class ServiceTest extends TestCase
{
    /** @var \Minhyung\KoreaDays\Service */
    private $service;

    protected function setUp(): void
    {
        $serviceKey = $_ENV['SERVICE_KEY'];
        if (! $serviceKey) {
            $this->markTestSkipped('Not exist service key');
        }

        parent::setUp();

        $this->service = new Service($serviceKey);
    }

    #[Test]
    #[TestWith([2024, 9, 3])]
    #[TestWith([2024, 10, 3])]
    public function testHolidays(int $year, int $month, int $count): void
    {
        $result = $this->service->getHolidays($year, $month);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('numOfRows', $result);
        $this->assertArrayHasKey('pageNo', $result);
        $this->assertArrayHasKey('totalCount', $result);
        $this->assertEquals($count, $result['totalCount']);
    }

    #[Test]
    #[TestWith([2024, 9, 3])]
    #[TestWith([2024, 10, 3])]
    public function testRestDays(int $year, int $month, int $count): void
    {
        $result = $this->service->getRestDays($year, $month);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('numOfRows', $result);
        $this->assertArrayHasKey('pageNo', $result);
        $this->assertArrayHasKey('totalCount', $result);
        $this->assertEquals($count, $result['totalCount']);
    }
}