<?php
/**
 * Get the IdMyGadget service (instantiating a global object, which is what we use in WP and joomla, in the process)
 * If service is not readily available, e.g. if the idMyGadget module is not installed or not active,
 *   Instantiate an object that will default to desktop and prevent whitescreens
 */
require_once 'JmwsIdMyGadgetModuleMissing.php';

class JmwsIdMyGadgetThemeService
{
	/**
	 * Location of the module's info.yml file.  We need to know if it's not installed and active.
	 */
	const IDMYGADGET_INFO_FILE = 'modules/jmws/idmygadget/idmygadget.info.yml';

	/**
	 * The service defined by the module.  We should try to use this service rather than the global object
	 */
	protected $idMyGadgetService = null;

	/**
	 * If we can't find the module's info file, we assume the code has not been downloaded.
	 */
	protected $idMyGadgetInfoFile = null;

	/**
	 * If we can't get the module's config options, we assume it is not enabled.
	 */
	protected $config = null;

	/**
	 * Set member variables that help us get the service, or else (if unable to) help us diagnose the issue
	 */
	public function __construct()
	{
		$this->config = \Drupal::config('idmygadget.settings');
		$this->idMyGadgetInfoFile = DRUPAL_ROOT . '/' . self::IDMYGADGET_INFO_FILE;
	}

	/**
	 * Returns the service from the module,
	 * If unable to do so, instantiate a module missing object that can help prevent whitescreens
	 */
	public function getService()
	{
		global $jmwsIdMyGadget;

		if ( ! isset($this->idMyGadgetService) || ! isset($jmwsIdMyGadget) )
		{
			if ( file_exists( $this->idMyGadgetInfoFile ) )     // check if module is installed (code is present)
			{
				$gadgetDetectorIndex = $this->config->get('idmygadget_gadget_detector');
				if ( isset($gadgetDetectorIndex) )               // check if module is enabled (installed in back end)
				{
					require_once DRUPAL_ROOT . '/modules/jmws/idmygadget/src/IdMyGadgetService.php';
					$this->idMyGadgetService = Drupal::service( 'idmygadget.idmygadget_service' );
				}
				else
				{
					$this->unableToGetService();
				}
			}
			else
			{
				$this->unableToGetService();
			}
		}

		return $this->idMyGadgetService;
	}

	/**
	 * Returns the error message, if there is one
	 */
	public function getErrorMessage()
	{
		global $jmwsIdMyGadget;
		return $jmwsIdMyGadget->errorMessage;
	}

	/**
	 * Instantiate the module missing object and set the error message
	 */
	protected function unableToGetService()
	{
		$this->instantiateModuleMissingObject();
		$this->setModuleMissingErrorMessage();
		$this->errorMessage = $jmwsIdMyGadget->errorMessage;
	}

	/**
	 * If the module is not available,
	 *   instantiate a module missing object (included in this theme) to keep us from whitescreening
	 */
	protected function instantiateModuleMissingObject()
	{
		global $jmwsIdMyGadget;
		require_once( 'JmwsIdMyGadgetModuleMissing.php' );
		$jmwsIdMyGadget = new JmwsIdMyGadgetModuleMissing();
		$jmwsIdMyGadget->errorMessage = IDMYGADGET_UNKNOWN_ERROR;
	}

	/**
	 * When the module is not available, we diagnose the issue and set the error message accordingly
	 */
	protected function setModuleMissingErrorMessage()
	{
		global $jmwsIdMyGadget;

		if ( file_exists($this->idMyGadgetInfoFile) )
		{
			$gadgetDetectorIndex = $this->config->get('idmygadget_gadget_detector');
			if ( isset($gadgetDetectorIndex) )
			{
				$jmwsIdMyGadget->errorMessage = IDMYGADGET_UNKNOWN_ERROR;
			}
			else
			{
				$jmwsIdMyGadget->errorMessage = IDMYGADGET_NOT_ACTIVE;
			}
		}
		else
		{
			$jmwsIdMyGadget->errorMessage = IDMYGADGET_NOT_INSTALLED;
		}
	}
}
