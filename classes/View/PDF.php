<?php use Dompdf\Dompdf;
use Dompdf\Options;

defined('SYSPATH') or die('No direct script access.');
/**
 * Render a view as a PDF.
 *
 * @author     Woody Gilk <woody.gilk@kohanaphp.com>
 * @copyright  (c) 2009 Woody Gilk
 * @license    MIT
 */
class View_PDF extends View {

	/**
	 * Constants for the names of the valid DOMPDF options
	 */
	const DOMPDF_FONT_DIR              ='font_dir';
	const DOMPDF_FONT_CACHE            ='font_cache';
	const DOMPDF_TEMP_DIR              ='temp_dir';
	const DOMPDF_ENABLE_FONTSUBSETTING ='is_font_subsetting_enabled';
	const DOMPDF_PDF_BACKEND           ='pdf_backend';
	const DOMPDF_PDFLIB_LICENSE        ='pdflib_license';
	const DOMPDF_DEFAULT_MEDIA_TYPE    ='default_media_type';
	const DOMPDF_DEFAULT_PAPER_SIZE    ='default_paper_size';
	const DOMPDF_DEFAULT_FONT          ='default_font';
	const DOMPDF_DPI                   ='dpi';
	const DOMPDF_ENABLE_PHP            ='is_php_enabled';
	const DOMPDF_ENABLE_JAVASCRIPT     ='is_javascript_enabled';
	const DOMPDF_ENABLE_REMOTE         ='is_remote_enabled';
	const DOMPDF_LOG_OUTPUT_FILE       ='log_output_file';
	const DOMPDF_FONT_HEIGHT_RATIO     ='font_height_ratio';
	const DOMPDF_ENABLE_HTML5PARSER    ='is_html5_parser_enabled';

	/**
	 * @var array An array of dompdf config options
	 */
	protected static $_options = NULL;

	/**
	 * @var Dompdf Internal reference to this instance's DOMPDF instance
	 */
	protected $_dompdf = NULL;

	/**
	 * Loads the dompdf options from the Kohana config system
	 *
	 * @return void
	 */
	public static function load_default_options()
	{
		self::$_options = Kohana::$config->load('dompdf.options');
	}


	/**
	 * Returns an instance of View_PDF, assigning a view and data if required
	 * @return View_PDF
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		// Initialise the options from config
		if (self::$_options === NULL)
		{
			self::load_default_options();
		}

		return new View_PDF($file, $data);
	}

	/**
	 * Gets the View's DOMPDF instance - initialises the library if required.
	 * @return DOMPDF
	 */
	public function dompdf()
	{
		if ( ! $this->_dompdf)
		{
		    $options = new Options();
            $options->set(self::$_options);
			$this->_dompdf = new Dompdf($options);
		}

		return $this->_dompdf;
	}

	/**
	 * Renders the view and returns the PDF content
	 * @return string
	 */
	public function render($file = NULL)
	{
		$this->_dompdf = NULL;
		// Render the HTML normally
		$html = parent::render($file);

		// Turn off strict errors, DOMPDF is stupid like that
		$ER = error_reporting(~E_STRICT);

		// Render the HTML to a PDF
		$pdf = $this->dompdf();
		$pdf->loadHtml($html);
		$pdf->render();

		// Restore error reporting settings
		error_reporting($ER);

		return $pdf->output();
	}

} // End View_PDF
