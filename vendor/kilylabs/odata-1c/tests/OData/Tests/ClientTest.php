<?php

namespace Kily\Tools1C\OData\Tests;

use Kily\Tools1C\OData\Client;
use Kily\Tools1C\OData\Exception;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-12-26 at 17:34:33.
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if(!isset($_SERVER['URL_ODATA_1C'])) {
            $this->markTestSkipped(
                'You should define ODATA_URL_1C in phpunit.xml to pass this test'
            );
        }
        if(isset($_SERVER['LOGIN']) && isset($_SERVER['PASSWORD'])) 
            $auth = [
                $_SERVER['LOGIN'],
                $_SERVER['PASSWORD'],
            ];

        $this->object = new Client($_SERVER['URL_ODATA_1C'],array_filter([
            'auth' => $auth, 
            'timeout'  => 300,
        ]));
    }

    /**
     * @covers Kily\Tools1C\OData\Client::__construct
     */
    public function test__construct()
    {
        $client = new Client('http://1c.kily.ru/ut/odata/standard.odata/',[
            'auth' => [
                'login',
                'password',
            ],
            'timeout'  => 300,
        ]);

        $this->assertTrue(is_object($client));
    }

    /**
     * @covers Kily\Tools1C\OData\Client::__get
     * @dataProvider objectNameProvider
     */

    public function test__get($attr,$is_exception)
    {
        $this->markTestSkipped(
            'This test is no longer valid, but may be used in the future'
        );
        $e = false;
        try {
            $this->object->$attr;
        } catch(Exception $e) {
            $e = (boolean)$e;
        }
        $this->assertTrue($e === $is_exception);
    }

    /**
     * @covers Kily\Tools1C\OData\Client::get
     */
    public function testGet()
    {
        // test list
        $data = $this->object->{'Catalog_Номенклатура'}->top(1)->get();
        $this->assertTrue(is_object($data));
        $this->assertTrue(is_array($data->toArray()));
        $this->assertTrue(isset($data['value']));

        if(!isset($data['value']) || !count($data['value'])) { 
            $this->markTestSkipped(
                'There is no active Catalog_Номенклатура items, so we cant complete the test'
            );
        }
        $this->assertGreaterThan(0,count($data['value']));

        // test getting by Ref_Key
        $id = $data['value'][0]['Ref_Key'];
        $data = $this->object->{'Catalog_Номенклатура'}->top(1)->get($id);
        $this->assertTrue(is_object($data));
        $this->assertTrue(is_array($data->toArray()));
        $this->assertTrue(isset($data['Ref_Key']));
        $this->assertTrue($data['Ref_Key'] === $id);

        //test filter
        $data = $this->object->{'Catalog_Номенклатура'}->top(1)->get(null,"НаименованиеПолное ne '".addslashes($data['value'][0]['НаименованиеПолное'])."'");
        $this->assertTrue(is_object($data));
        $this->assertTrue(is_array($data->toArray()));
        $this->assertTrue(isset($data['value'][0]['Ref_Key']));
        $this->assertTrue($data['value'][0]['Ref_Key'] === $id);

    }

    /**
     * @covers Kily\Tools1C\OData\Client::create
     */
    public function testCreate()
    {
        if(!isset($_SERVER['ALLOW_UNSAFE_OPERATIONS']) || !$_SERVER['ALLOW_UNSAFE_OPERATIONS'])
            $this->markTestSkipped(
                'You should explictly allow tests with create,update and delete operations'
            );

        $product_data = [
            'Артикул'=>'CERTANLY_NONEXISTENT_123',
            'Description'=>'test test test nonexistent',
        ];

        $data = $this->object->{'Catalog_Номенклатура'}->create($product_data);
        $this->assertTrue($this->object->isOk());
    }


    /**
     * @covers Kily\Tools1C\OData\Client::update
     */
    public function testUpdate()
    {
        if(!isset($_SERVER['ALLOW_UNSAFE_OPERATIONS']) || !$_SERVER['ALLOW_UNSAFE_OPERATIONS'])
            $this->markTestSkipped(
                'You should explictly allow tests with update operations'
            );

        $data = $this->object->{'Catalog_Номенклатура'}->get(null,"Артикул eq 'CERTANLY_NONEXISTENT_123'");
        $this->assertTrue($this->object->isOk());
        $id = $data['value'][0]['Ref_Key'];

        $data = $this->object->{'Catalog_Номенклатура'}->update($id,[
            'Description'=>'Test description',
        ]);
        $this->assertTrue($this->object->isOk());
    }

    /**
     * @covers Kily\Tools1C\OData\Client::delete
     */
    public function testDelete()
    {
        if(!isset($_SERVER['ALLOW_UNSAFE_OPERATIONS']) || !$_SERVER['ALLOW_UNSAFE_OPERATIONS'])
            $this->markTestSkipped(
                'You should explictly allow tests with delete operations. WARNING! It could be DANGEROUS! Use it at your own risk!'
            );

        $data = $this->object->{'Catalog_Номенклатура'}->get(null,"Артикул eq 'CERTANLY_NONEXISTENT_123'");
        $this->assertTrue($this->object->isOk());
        $id = $data['value'][0]['Ref_Key'];
        $data = $this->object->{'Catalog_Номенклатура'}->delete($id);
        $this->assertTrue($this->object->isOk());

    }


    /**
     * @covers Kily\Tools1C\OData\Client::request
     */
    public function testRequest()
    {
        $this->object->{'Catalog_Номенклатура'};
        $data = $this->object->top(1)->request("GET");
        $this->assertTrue(is_object($data));
        $this->assertTrue(is_array($data->toArray()));
    }

    /**
     * @covers Kily\Tools1C\OData\Client::getErrorMessage
     */
    public function testGetErrorMessage()
    {
        $data = $this->object->{'Catalog_Номенклатураa'}->top(1)->get();
        $this->assertEquals('Not found',$this->object->getHttpErrorMessage());

        $data = $this->object->{'Catalog_Номенклатура'}->top(1)->get();
        $this->assertEquals('',$this->object->getErrorMessage());
    }

    /**
     * @covers Kily\Tools1C\OData\Client::getErrorCode
     */
    public function testGetErrorCode()
    {
        $data = $this->object->{'Catalog_Номенклатураa'}->top(1)->get();
        $this->assertEquals('404',$this->object->getHttpErrorCode());

        $data = $this->object->{'Catalog_Номенклатура'}->top(1)->get();
        $this->assertEquals('',$this->object->getErrorCode());
    }

    /**
     * @covers Kily\Tools1C\OData\Client::isOk
     */
    public function testIsOk()
    {
        $data = $this->object->{'Catalog_Номенклатураa'}->top(1)->get();
        $this->assertFalse($this->object->isOk());

        $data = $this->object->{'Catalog_Номенклатура'}->top(1)->get();
        $this->assertTrue($this->object->isOk());
    }

    public function objectNameProvider()
    {
        return [
            ['Catalog_Номенклатура',false],
            ['Document_СчетНаОплату',false],
            ['Document_',true],
            ['Invalid_',true],
            ['Invalid_Nonexistent',true],
            ['Ohloh_test_Nonexistent',true],
        ];
    }
}
