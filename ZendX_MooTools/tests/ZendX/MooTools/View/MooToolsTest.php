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
 * @package     ZendX_MooTools
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: MooToolsTest.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_MooTools_View_MooToolsTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/MooTools.php";
require_once "ZendX/MooTools/View/Helper/MooTools.php";

class ZendX_MooTools_View_MooToolsTest extends PHPUnit_Framework_TestCase
{
	private $view = null;
	private $helper = null;

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

	public function setUp()
	{
        Zend_Registry::_unsetInstance();
        $this->view   = $this->getView();
        $this->helper = new ZendX_MooTools_View_Helper_MooTools_Container();
        $this->helper->setView($this->view);
        Zend_Registry::set('ZendX_MooTools_View_Helper_MooTools', $this->helper);
	}

	public function tearDown()
	{
		ZendX_MooTools_View_Helper_MooTools::disableNoSafeMode();
	}

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('ZendX/MooTools/View/Helper/', 'ZendX_MooTools_View_Helper');
        return $view;
    }
    

    public function testHelperSuccessfulCallForward()
    {
        $this->helper->addJavascript('alert()');
    }
 
    public function testShouldAllowSpecifyingMoToolsVersion()
    {
        $this->helper->setVersion('1.2.4');
        $this->assertEquals('1.2.4', $this->helper->getVersion());
    }
    
    public function testShouldUseDefaultSupportedVersionWhenNotSpecifiedOtherwise()
    {
    	$this->assertEquals(ZendX_MooTools::DEFAULT_MOOTOOLS_VERSION, $this->helper->getVersion());
    }
    
    public function testShouldBeNotEnabledByDefault()
    {
    	$this->assertFalse($this->helper->isEnabled());
    }
    
    public function testUsingLocalPath()
    {
    	$this->helper->setLocalPath("/js/mootools.js");
    	$this->assertFalse($this->helper->useCDN());
    	$this->assertFalse($this->helper->isEnabled());
    	$this->assertTrue($this->helper->useLocalPath());
    	$this->assertContains("/js/mootools.js", $this->helper->getLocalPath());

    	$render = $this->helper->__toString();
    	$this->assertNotContains("/js/mootools.js", $render);
    }
    
   public function testNoConflictShouldBeDisabledDefault()
    {
    	$this->assertFalse(ZendX_MooTools_View_Helper_MooTools::getNoSafeMode());
    }
    
    
    public function testUsingNoConflictMode()
    {
    	ZendX_MooTools_View_Helper_MooTools::enableNoSafeMode();
    	$this->helper->setVersion("1.2.4");
        $this->helper->enable();
    	$render = $this->helper->__toString();

    	$this->assertContains('var $ = document.id;', $render);
    }
    
    public function testDefaultRenderModeShouldIncludeAllBlocks()
    {
    	$this->assertEquals(ZendX_MooTools::RENDER_ALL, $this->helper->getRenderMode());
    }
    
    public function testShouldAllowSettingRenderMode()
    {
    	$this->helper->setRenderMode(1);
    	$this->assertEquals(1, $this->helper->getRenderMode());
    	$this->helper->setRenderMode(2);
    	$this->assertEquals(2, $this->helper->getRenderMode());
    	$this->helper->setRenderMode(4);
    	$this->assertEquals(4, $this->helper->getRenderMode());
    }
    
    
    public function testShouldAllowUsingAddOnLoadStack()
    {
    	$this->helper->addOnLoad('$(document).alert();');
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getOnLoadActions());
    }
    
    
    public function testShouldAllowStackingMultipleOnLoad()
    {
    	$this->helper->addOnLoad("1");
    	$this->helper->addOnLoad("2");
    	$this->assertEquals(2, count($this->helper->getOnLoadActions()));
    }

    public function testShouldAllowCaptureOnLoad()
    {
    	$this->helper->onLoadCaptureStart();
    	echo '$(document).alert();';
    	$this->helper->onLoadCaptureEnd();
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getOnLoadActions());
    }
    
    
    public function testShouldAllowUsingAddDomReadyStack()
    {
    	$this->helper->addDomReady('$(document).alert();');
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getDomReadyActions());
    }
    
    public function testShouldAllowStackingMultipleDomReady()
    {
    	$this->helper->addDomReady("1");
    	$this->helper->addDomReady("2");
    	$this->assertEquals(2, count($this->helper->getDomReadyActions()));
    }
    
    public function testShouldAllowCaptureDomReady()
    {
    	$this->helper->domReadyCaptureStart();
    	echo '$(document).alert();';
    	$this->helper->domReadyCaptureEnd();
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getDomReadyActions());
    }
    
    
    public function testShouldAllowCaptureJavascript()
    {
    	$this->helper->javascriptCaptureStart();
    	echo '$(document).alert();';
    	$this->helper->javascriptCaptureEnd();
    	$this->assertEquals(array('$(document).alert();'), $this->helper->getJavascript());

    	$this->helper->clearJavascript();
    	$this->assertEquals(array(), $this->helper->getJavascript());
    }
    
    /**
     * @expectedException Zend_Exception
     */
    public function testShouldDisallowNestingCapturesWithException()
    {
    	$this->helper->javascriptCaptureStart();
    	$this->helper->javascriptCaptureStart();
    }
    
    /**
     * @expectedException Zend_Exception
     */
    public function testShouldDisallowNestingCapturesWithException2()
    {
    	$this->helper->onLoadCaptureStart();
    	$this->helper->onLoadCaptureStart();

    	$this->setExpectedException('Zend_Exception');
    }

    /**
     * @expectedException Zend_Exception
     */
    public function testShouldDisallowNestingCapturesWithException3()
    {
    	$this->helper->domReadyCaptureStart();
    	$this->helper->domReadyCaptureStart();

    	$this->setExpectedException('Zend_Exception');
    }
    
    
  public function testAddJavascriptFiles()
    {
    	$this->helper->addJavascriptFile('/js/test.js');
    	$this->helper->addJavascriptFile('/js/test2.js');
    	$this->helper->addJavascriptFile('http://example.com/test3.js');

    	$this->assertEquals(array('/js/test.js', '/js/test2.js', 'http://example.com/test3.js'), $this->helper->getJavascriptFiles());
    }

    public function testAddedJavascriptFilesCanBeCleared()
    {
    	$this->helper->addJavascriptFile('/js/test.js');
    	$this->helper->addJavascriptFile('/js/test2.js');
    	$this->helper->addJavascriptFile('http://example.com/test3.js');

    	$this->helper->clearJavascriptFiles();
    	$this->assertEquals(array(), $this->helper->getJavascriptFiles());
    }

    public function testAddedJavascriptFilesRender()
    {
    	$this->helper->addJavascriptFile('/js/test.js');
    	$this->helper->addJavascriptFile('/js/test2.js');
    	$this->helper->addJavascriptFile('http://example.com/test3.js');

        $this->helper->enable();

    	$render = $this->helper->__toString();
    	$this->assertContains('src="/js/test.js"', $render);
    	$this->assertContains('src="/js/test2.js"', $render);
    	$this->assertContains('src="http://example.com/test3.js', $render);
    }
    
    
    public function testAddStylesheet()
    {
    	$this->helper->addStylesheet('test.css');
    	$this->helper->addStylesheet('test2.css');

    	$this->assertEquals(array('test.css', 'test2.css'), $this->helper->getStylesheets());
    }

    public function testShouldAddJavascriptOnlyOnce()
    {
    	$this->helper->addJavascript("alert();");
    	$this->helper->addJavascript("alert();");

    	$this->assertEquals(1, count($this->helper->getJavascript()));
    }
    
   public function testShouldAddDelimWhenNoneGiven()
    {
    	$this->helper->addJavascript("alert()");

    	$this->assertEquals(array('alert();'), $this->helper->getJavascript());
    }
    
    
    public function testShouldRenderNothingOnDisable()
    {
    	$this->helper->setVersion("1.2.4");
    	$this->helper->addJavascriptFile("test.js");
    	$this->helper->disable();
    	$this->assertEquals(strlen(''), strlen($this->helper->__toString()));
    }
    
    
    public function testShouldAllowBasicSetupWithCDN()
    {
        $this->helper->enable();
    	$this->helper->setVersion("1.2.4");
    	$this->helper->addJavascriptFile("test.js");

    	$render = $this->helper->__toString();

    	$this->assertTrue($this->helper->useCDN());
    	$this->assertContains('mootools-yui-compressed.js', $render);
    	$this->assertContains('1.2.4', $render);
    	$this->assertContains('test.js', $render);
    	$this->assertContains('<script type="text/javascript"', $render);
    }
    
   public function testShouldAllowUseRenderMode()
    {
        $this->helper->enable();
    	$this->helper->setVersion("1.2.4");
    	$this->helper->setMooreLocalPath('/script/mootools/moore.js');
    	$this->helper->addJavascriptFile("test.js");
    	$this->helper->addJavascript("helloWorld();");
    	$this->helper->addStylesheet("test.css");
    	$this->helper->addOnLoad("alert();");
    	$this->helper->addDomReady("alertDom();");

    	// CHeck CDN Usage
    	$this->assertTrue($this->helper->useCDN());

    	// Test with Render No Parts
    	$this->helper->setRenderMode(0);
    	$this->assertEquals(strlen(''), strlen(trim($this->helper->__toString())));

    	// Test Render Only Library
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_LIBRARY);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertNotContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

    	
    	// Test Render Only AddOnLoad
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_MOOTOOLS_ON_LOAD);
    	$render = $this->helper->__toString();
    	$this->assertNotContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

    	
    	// Test Render Only Javascript
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_SOURCES);
    	$render = $this->helper->__toString();
    	$this->assertNotContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertContains("test.js", $render);
    	$this->assertNotContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

    	// Test Render Only Javascript
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_STYLESHEETS);
    	$render = $this->helper->__toString();
    	$this->assertNotContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertNotContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);

		// Test Render Library and AddOnLoad
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_LIBRARY | ZendX_MooTools::RENDER_MOOTOOLS_ON_LOAD);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertContains("alert();", $render);
    	$this->assertNotContains("helloWorld();", $render);


        // Test Render Library and AddDomReady
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_LIBRARY | ZendX_MooTools::RENDER_MOOTOOLS_DOM_READY);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertNotContains("test.css", $render);
    	$this->assertNotContains("test.js", $render);
    	$this->assertContains("alertDom();", $render);
    	$this->assertNotContains("helloWorld();", $render);
    	
    	
    	// Test Render ALL and moore Library
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_ALL);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertContains("alertDom();", $render);
    	$this->assertContains('/script/mootools/moore.js', $render);
    	
    	// Test Render All
    	$this->helper->setRenderMode(ZendX_MooTools::RENDER_ALL);
    	$render = $this->helper->__toString();
    	$this->assertContains("1.2.4/mootools-yui-compressed.js", $render);
    	$this->assertContains("test.js", $render);
    	$this->assertContains("alert();", $render);
    	$this->assertContains("helloWorld();", $render);
    	
    	
    	 
    }
    
    public function testClearAddOnLoadStack()
    {
        $this->helper->addOnLoad("foo");
        $this->helper->addOnLoad("bar");
        $this->helper->addOnLoad("baz");

        $this->assertEquals(array("foo", "bar", "baz"), $this->helper->getOnLoadActions());

        $this->helper->clearOnLoadActions();
        $this->assertEquals(array(), $this->helper->getOnLoadActions());
    }
    
    
    public function testClearDomReadyStack()
    {
        $this->helper->addDomReady("foo");
        $this->helper->addDomReady("bar");
        $this->helper->addDomReady("baz");

        $this->assertEquals(array("foo", "bar", "baz"), $this->helper->getDomReadyActions());

        $this->helper->clearDomReadyActions();
        $this->assertEquals(array(), $this->helper->getDomReadyActions());
    }
    
    
    public function testStylesheetShouldRenderCorrectClosingBracketBasedOnHtmlDoctypeDefinition()
    {
        $this->helper->addStylesheet("test.css");
        $this->view->doctype("HTML4_STRICT");

        $assert = '<link rel="stylesheet" href="test.css" type="text/css" media="screen">';
        $this->helper->enable();
        $this->assertContains($assert, $this->helper->__toString());

    }
    
     public function testStylesheetShouldRenderCorrectClosingBracketBasedOnXHtmlDoctypeDefinition()
    {
        $this->helper->addStylesheet("test.css");
        $this->view->doctype("XHTML1_STRICT");

        $assert = '<link rel="stylesheet" href="test.css" type="text/css" media="screen" />';
        $this->helper->enable();
        $this->assertContains($assert, $this->helper->__toString());
    }
    
    public function testIncludeMooToolsLibraryFromSslPath()
    {
        $this->helper->setCdnSsl(true);
        $this->helper->enable();

        $this->assertContains(ZendX_MooTools::CDN_BASE_GOOGLE_SSL, $this->helper->__toString());
    }
    
    public function testMooToolsyGoogleCdnPathIsBuiltCorrectly()
    {
        $mooToolsCdnPath = "http://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js";
        
        $this->helper->setVersion("1.2.4");
        $this->helper->enable();

        $this->assertContains($mooToolsCdnPath, $this->helper->__toString());
    }
    
    
    public function testMooToolGoogleCdnSslPathIsBuiltCorrectly()
    {
        $mooToolsCdnPath = "https://ajax.googleapis.com/ajax/libs/mootools/1.2.4/mootools-yui-compressed.js";
        $this->helper->setCdnSsl(true);
        $this->helper->useCdn();
        $this->helper->setVersion("1.2.4");
        $this->helper->enable();

        
        $this->assertContains($mooToolsCdnPath, $this->helper->__toString());
    }
    
   public function testMooToolsMoreLibraryEnable()
    {
        $this->helper->mooreEnable();
        $this->assertTrue($this->helper->mooreIsEnabled());
    }
    
    public function testMooToolsMoreLibraryDisable()
    {
        $this->helper->mooreEnable();
        $this->helper->mooreDisable();
        $this->assertFalse($this->helper->mooreIsEnabled());
    }
    
    
    public function testMooToolsMooreLocalPath(){
       
        $this->helper->setMooreLocalPath('/script/mootools/moore.js');
        $this->assertTrue($this->helper->mooreIsEnabled());
        $this->assertContains('/script/mootools/moore.js', $this->helper->getMooreLocalPath());
    }
    
    
  /**
     * @expectedException InvalidArgumentException
     */
    public function testMootoolsMooreLcalPathNullOrEmpty()
    {
        
        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->helper->setMooreLocalPath('');
        //$render = $this->helper->__toString();
        
    }
}