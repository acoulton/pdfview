<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');

/**
 * Tests View_PDF classs
 *
 * [!!] Note that there are significant test sequence dependencies in this test
 * case because of the underlying global state of DOMPDF and therefore of the
 * View_PDF class. When adding tests, take great care that you understand the
 * required sequence and global state context at any given point.
 *
 * @group pdfview
 * @group pdfview.core
 *
 * @package    PDFView
 * @category   Tests
 * @author     Andrew Coulton
 * @copyright  (c) 2012 Andrew Coulton
 * @license    http://kohanaframework.org/license
 */
class PDFView_ViewTest extends Unittest_TestCase
{
	protected static $old_modules = array();
	protected static $expect_dompdf_presence = FALSE;

	/**
	 * Setups the filesystem for test view files
	 *
	 * @return null
	 */
	public static function setupBeforeClass()
	{
		self::$old_modules = Kohana::modules();

		$new_modules = self::$old_modules+array(
			'test_views' => realpath(dirname(__FILE__).'/../test_data/')
		);
		Kohana::modules($new_modules);
	}

	/**
	 * Restores the module list
	 *
	 * @return null
	 */
	public static function teardownAfterClass()
	{
		Kohana::modules(self::$old_modules);
	}

	/**
	 * Verify the dompdf state as a precondition on every test
	 */
	public function setUp()
	{
		parent::setUp();
		$this->verify_dompdf_state();
	}

	/**
	 * Check whether DOMPDF is or is not loaded yet
	 */
	protected function verify_dompdf_state()
	{
		$state = self::$expect_dompdf_presence;
		$this->assertEquals($state, class_exists('DOMPDF', FALSE), "Verfiying whether DOMPDF class exists");
		$this->assertEquals($state, defined('DOMPDF_DIR'), "Verifying whether DOMPDF_DIR is defined");
	}

	/**
	 * Provider for test_exception_on_missing_view
	 *
	 * @return array
	 */
	public function provider_exception_on_missing_view()
	{
		return array(
			array('exists', FALSE),
			array('exists.css', FALSE),
			array('doesnt_exist', TRUE),
		);
	}

	/**
	 * Calling with an invalid view file throws an exception
	 *
	 * @dataProvider provider_exception_on_missing_view
	 */
	public function test_exception_on_missing_view($path, $expects_exception)
	{
		try
		{
			$view = new View_PDF($path);
			$this->assertSame(FALSE, $expects_exception);
		}
		catch(View_Exception $e)
		{
			$this->assertSame(TRUE, $expects_exception);
		}
	}


	/**
	 * Tests that default property values are loaded from kohana config
	 *
	 */
	public function test_loads_default_dompdf_options_from_config()
	{
	    $this->markTestIncomplete();
	}


	/**
	 * Tests that the factory method creates a new instance of View_PDF each
	 * time.
	 */
	public function test_factory_creates_new_instance()
	{
		$view = View_PDF::factory('exists');
		$view2 = View_PDF::factory('exists');
		$this->assertInstanceOf('View_PDF', $view);
		$this->assertInstanceOf('View_PDF', $view2);
		$this->assertNotSame($view, $view2);
	}

	/**
	 * dompdf is not included until required - to allow configuration of
	 * dompdf properties
	 *
	 * @depends test_factory_creates_new_instance
	 */
	public function test_dompdf_not_loaded_until_required()
	{
		// The actual test for this is handled by the verify_dompdf_state in the
		// setUp hook
	}

	/*
	 * ------------------------------------------------------------------------
	 * The next test will trigger inclusion of the DOMPDF library - which defines
	 * constants for major configuration values. Any tests of DOMPDF configuration
	 * must go above this point.
	 * -------------------------------------------------------------------------
	 */

	/*
	 * View_PDF::dompdf() returns an instance of the class
	 */
	public function test_dompdf_returns_instance()
	{
		self::$expect_dompdf_presence = TRUE;
		$view = View_PDF::factory('exists');
		$this->assertInstanceOf('DOMPDF', $view->dompdf());
	}

	/**
	 * Tests that options are defined and recognised by dompdf
	 *
	 * @depends test_dompdf_options_can_be_set_and_read
	 */
	public function test_options_are_assigned_when_initialised()
	{
		$this->assertEquals('braille', DOMPDF_DEFAULT_MEDIA_TYPE);
	}
}
