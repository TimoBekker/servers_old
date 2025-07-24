<?php

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/mock/SIpdnsTableMock.php');

class IpdnsModelTests extends \PHPUnit_Framework_TestCase {

    private $ipdnstable;

    protected function setUp() {
        $this->ipdnstable = new SIpdnsTableMock();
    }

    protected function tearDown() {
        $this->ipdnstable = NULL;
    }

    public function testSaveData() {
        mb_internal_encoding("UTF-8");
        $this->ipdnstable->dns_name = 'abracadabra.ru';
        $this->ipdnstable->ip_address = '10.0.91.98';
        $result = $this->ipdnstable->validate();
        $this->assertEquals(true, $result);
    }

}
