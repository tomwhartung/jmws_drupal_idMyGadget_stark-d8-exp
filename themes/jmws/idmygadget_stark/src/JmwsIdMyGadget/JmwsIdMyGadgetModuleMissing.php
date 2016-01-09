<?php
/**
 * Defines a class we can use to prevent crashing (with a null pointer error)
 * when device detection is not readily available, such as when the idMyGadget
 * module is not installed or not active.
 */
if( !defined('DS') )
{
	define('DS', DIRECTORY_SEPARATOR);
}
/**
 * Error message prologue
 */
define( 'IDMYGADGET_ERROR_PROLOG',
	'<div class="idmygadget-error"><p>This theme depends on the ' .
	'<a class="idmygadget-error" href="https://github.com/tomwhartung/jmws_idMyGadget_for_drupal" target="_blank">' .
		'jmws_idMyGadget_for_drupal</a> module.</p>' );
/**
 * Error message for when the plugin is not installed
 */
define( 'IDMYGADGET_NOT_INSTALLED',
	IDMYGADGET_ERROR_PROLOG . '<p>It appears this module is <span class="idmygadget-error">not installed</span>.</p>' .
	'<p>Please <span class="idmygadget-error">install and activate the module,</span> which is available on github, or use a different theme.</p></div>' );
/**
 * Error message for when the plugin is not active
 */
define( 'IDMYGADGET_NOT_ACTIVE',
	IDMYGADGET_ERROR_PROLOG . '<p>It appears this module is <span class="idmygadget-error">installed but not active</span>.</p>' .
	'<p>Please <span class="idmygadget-error">activate the module</span> in the Drupal administration console, or use a different theme.</p></div>' );
/**
 * Error message for when there is an unknown error (bug?)
 */
define( 'IDMYGADGET_UNKNOWN_ERROR',
	IDMYGADGET_ERROR_PROLOG .
	'<p>The jmwsIdMyGadget object is missing, so the jmws_idMyGadget_for_drupal module must be broken.</p>' .
	'<p>Please fix the module or use a different theme.</p></div>' );

class JmwsIdMyGadgetModuleMissing
{
	/**
	 * Error message, set only when there's an error
	 * @var type String
	 */
	public $errorMessage = '';

	/**
	 * Valid values for the gadget string.  Use invalid values at your own risk!
	 */
	const GADGET_STRING_DETECTOR_NOT_INSTALLED = 'Detector Not Installed';
	const GADGET_STRING_UNKNOWN_DEVICE = 'Unknown Device';
	const GADGET_STRING_DESKTOP = 'Desktop';
	const GADGET_STRING_TABLET = 'Tablet';
	const GADGET_STRING_PHONE = 'Phone';

	public $supportedGadgetDetectors = array();
	public $supportedThemes = array();

	/**
	 * A string that represents the gadget being used
	 */
	protected $gadgetString = '';

	/**
	 * Constructor: nothing to see here
	 */
	public function __construct()
	{
		$this->setGadgetString();
	}

	/**
	 * When this class is being used, device detection is not enabled
	 */
	public function isEnabled()
	{
		return FALSE;
	}

	/**
	 * The gadget string is read-only!
	 */
	public function getGadgetString()
	{
		return $this->gadgetString;   // set in constructor
	}
	public function getGadgetStringChar()
	{
		return '?';
	}

	/**
	 * For now, when there is no detection, assume we are on a desktop..
	 * @return string gadgetString
	 */
	protected function setGadgetString()
	{
		$this->gadgetString = self::GADGET_STRING_DESKTOP;
		return $this->gadgetString;
	}

	/**
	 * The gadgetDetectorString is not available, return a suitable substitute
	 */
	public function getGadgetDetectorString()
	{
		return self::GADGET_STRING_DETECTOR_NOT_INSTALLED;
	}
	public function getGadgetDetectorStringChar()
	{
		return '?';
	}
	/**
	 * For development only! Please remove when code is stable.
	 * Displaying some values that can help us make sure we haven't inadvertently
	 * broken something while we are actively working on this.
	 * @return string
	 */
	public function getSanityCheckString()
	{
		$returnValue = '?/?/?/(module missing)';
		return $returnValue;
	}
}
