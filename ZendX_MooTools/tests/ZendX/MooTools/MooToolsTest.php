<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: JQueryTest.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

require_once dirname(__FILE__)."/../../TestHelper.php";



require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "Zend/Form.php";
require_once "ZendX/MooTools.php";

class ZendX_MooTools_MooToolsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("ZendX_MooTools_MooToolsTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testShouldAllowEnableView()
    {
        $view = new Zend_View();
        ZendX_MooTools::enableView($view);

        $this->assertTrue( false !== ($view->getPluginLoader('helper')->getPaths('ZendX_MooTools_View_Helper')) );
    }

    public function testEcncodeJson(){
    
         $view = new Zend_View();
        ZendX_MooTools::enableView($view);
        $list = array('a','b','c');
        $assert = ZendX_MooTools::encodeJson($list);
        
       
        $this->assertContains('["a","b","c"]', $this->helper->__toString());
    }
    
}

/*if (PHPUnit_MAIN_METHOD == 'ZendX_MooTools_MooToolsTest::main') {
    ZendX_MooTools_MooToolsTest::main();
}*/