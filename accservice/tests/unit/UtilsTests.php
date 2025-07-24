<?php

use app\components\utils\Utils;

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../components/utils/Utils.php');

class UtilsTests extends \PHPUnit_Framework_TestCase
{
    public function lenToMaskDataProvider() {
        return [
            [21, '255.255.248.0'],
            [0, '0.0.0.0'],
            [32, '255.255.255.255'],
            [234, false],
            [-1, false]
        ];
    }

    /**
     * @dataProvider lenToMaskDataProvider
     */
    public function testLenToMask($len, $expected)
    {
        $result = Utils::lenToMask($len);
        $this->assertEquals($expected, $result);
    }

    public function maskToDecDataProvider() {
        return [
            ['255.255.248.0', 21],
            ['0.0.0.0', 0],
            ['255.255.255.255', 32],
        ];
    }

    /**
     * @dataProvider maskToDecDataProvider
     */
    public function testMaskToDec($mask, $expected)
    {
        $result = Utils::maskToDec($mask);
        $this->assertEquals($expected, $result);
    }


    public function getRealNumTypeDataProvider() {
        return [
            ['0', 'int'],
            ['07', 'int'],
            ['0asdf', 'int'],
            ['03asdf', 'int'],
            ['879', 'int'],
            ['879.', 'int'],
            ['879.0', 'int'],
            ['.0', 'int'],
            ['.00', 'int'],
            ['.234', 'float'],
            ['.0234', 'float'],
            ['.234asffs', 'float'],
            ['879sfjs', 'int'],
            ['412.4312', 'float'],
            ['412.4312asfd', 'float'],
            ['', false],
            ['.asd234', false],
            ['asdf', false],
            ['sdfsd1844674', false],
            ['asv18446.51616', false],
        ];
    }

    /**
     * @dataProvider getRealNumTypeDataProvider
     */
    public function testGetRealNumType($numString, $expected) {
        $result = Utils::getRealNumType($numString);
        $this->assertEquals($expected, $result);
    }

}