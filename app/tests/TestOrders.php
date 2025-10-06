<?php
namespace app\tests;

require_once __DIR__ . '/../lib/Autoloader.php';

use PHPUnit\Framework\TestCase;

class TestOrders extends TestCase
{
    public function testEmpty()
    {
        $order = new \OrderHP(['id' => 1, 'rapport_print' => 2.5]);
        $this->assertEquals(1, $order->id);
        $this->assertSame(2.5, $order->rapportPrint());

        $order = new \OrderHPTest(['id' => 1, 'rapport_print' => 2.5]);
        $this->assertEquals(1, $order->id);
        $this->assertSame(2.5, $order->rapportPrint());

        $order = new \OrderFlexo(['id' => 1, 'rapport_print' => 2.5]);
        $this->assertEquals(1, $order->id);
        $this->assertSame(2.5, $order->rapportPrint());

        $order = new \OrderOffset(['id' => 1, 'rapport_print' => 2.5]);
        $this->assertEquals(1, $order->id);
        $this->assertSame(2.5, $order->rapportPrint());
    }

    public function testGetOneFromDB()
    {
        $id = 9;
        $order = \OrderHP::fetchItem($id);
        $this->assertNotNull($order);
        $this->assertEquals($id, $order->id);
        $order->calcData();
        $this->assertIsNumeric($order->rapportPrint());

        $id = 2;
        $order = \OrderHPTest::fetchItem($id);
        $this->assertNotNull($order);
        $this->assertEquals($id, $order->id);
        $order->calcData();
        $this->assertIsNumeric($order->rapportPrint());

        $id = 30261;
        $order = \OrderFlexo::fetchItem($id);
        $this->assertNotNull($order);
        $this->assertEquals($id, $order->id);
        $order->calcData();
        $this->assertIsNumeric($order->rapportPrint());

        $id = 2170;
        $order = \OrderOffset::fetchItem($id);
        $this->assertNotNull($order);
        $this->assertEquals($id, $order->id);
        $order->calcData();
        $this->assertIsNumeric($order->rapportPrint());
    }

    public function testGetListFromDB()
    {
        $params['limit'] = 30;
        $params['returnArray'] = false;
        $params['withNoAddData'] = true;
        $params['withConsumptionMP'] = true;
        $params['withProtectedCover'] = true;

        $items = \OrderHP::fetchListItems($params);
        $this->assertNotNull($items);
        $this->assertIsArray($items);

        $items = \OrderHPTest::fetchListItems($params);
        $this->assertNotNull($items);
        $this->assertIsArray($items);

        $items = \OrderFlexo::fetchListItems($params);
        $this->assertNotNull($items);
        $this->assertIsArray($items);

        $items = \OrderOffset::fetchListItems($params);
        $this->assertNotNull($items);
        $this->assertIsArray($items);
    }
}
