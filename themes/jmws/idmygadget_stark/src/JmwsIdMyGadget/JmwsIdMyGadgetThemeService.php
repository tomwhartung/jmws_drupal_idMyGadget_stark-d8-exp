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

	protected $idMyGadgetService = null;
	protected $config = null;
	protected $idMyGadgetInfoFile = null;

	/**
	 * If the $jmwsIdMyGadget is not yet instantiated,
	 *   Get the module's service, if possible, instantiating it as a side effect
	 */
	public function __construct()
	{
		$this->config = \Drupal::config('idmygadget.settings');
		$this->idMyGadgetInfoFile = DRUPAL_ROOT . '/' . self::IDMYGADGET_INFO_FILE;
	}

	public function getService()
	{
		global $jmwsIdMyGadget;

		if ( isset($jmwsIdMyGadget) )
		{
			unset( $jmwsIdMyGadget->errorMessage );
		}
		else if ( file_exists( $idMyGadgetInfoFile ) )     // check if module is installed
		{
			$gadgetDetectorIndex = $this->config->get('idmygadget_gadget_detector');
			if ( isset($this->gadgetDetectorIndex) )        // check if module is enabled
			{
				require_once DRUPAL_ROOT . '/modules/jmws/idmygadget/src/IdMyGadgetService.php';
				$this->idMyGadgetService = Drupal::service( 'idmygadget.idmygadget_service' );
			}
			else
			{
				$this->instantiateModuleMissingObject();
				$this->setModuleMissingErrorMessage();
			}
		}
		else
		{
			$this->instantiateModuleMissingObject();
			$this->setModuleMissingErrorMessage();
		}

		return $this->idMyGadgetService;
	}

	protected function instantiateModuleMissingObject()
	{
		global $jmwsIdMyGadget;
		require_once( 'JmwsIdMyGadgetModuleMissing.php' );
		$jmwsIdMyGadget = new JmwsIdMyGadgetModuleMissing();
		$jmwsIdMyGadget->errorMessage = IDMYGADGET_UNKNOWN_ERROR;
	}

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

		drupal_set_message( t($jmwsIdMyGadget->errorMessage), 'error' );
	}

}
