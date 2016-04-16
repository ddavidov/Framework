<?php
/**
 * @package     ZOOlanders
 * @version     3.3.16
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgSystemZlframework extends JPlugin
{
	public $app;

	/**
	 * @var \Zoolanders\Container\Container
	 */
	protected $container;

	protected $autoloadLanguage = true;

	function onAfterInitialise()
	{
		require_once JPATH_LIBRARIES . '/zoolanders/include.php';
		$this->container = Zoolanders\Container\Container::getInstance();

		$this->app = $this->container->zoo->getApp();

		// check and perform installation tasks
		if (!$this->container->installation->checkInstallation()) return; // must go after language, elements path and helpers

		// register events
		$this->app->event->register('TypeEvent');
		$this->app->event->dispatcher->connect('type:coreconfig', array($this, 'coreConfig'));
		$this->app->event->dispatcher->connect('application:sefparseroute', array($this, 'sefParseRoute'));
		$this->app->event->dispatcher->connect('type:beforesave', array($this, 'typeBeforeSave'));

		// perform admin tasks
		if ($this->container->system->application->isAdmin()) {
			$this->container->system->document->addStylesheet('zlfw:assets/css/zl_ui.css');
		}

		// init ZOOmailing if installed
		if ($path = $this->app->path->path('root:plugins/acymailing/zoomailing/zoomailing')) {

			// register path and include
			$this->app->path->register($path, 'zoomailing');
			require_once($path . '/init.php');
		}

		// load ZL Fields, workaround for first time using ZL elements
		if ($this->app->zlfw->isTheEnviroment('zoo-type-edit')) $this->app->zlfield->loadAssets();

		// load Separator ZL Field integration
		if ($this->app->zlfw->isTheEnviroment('zoo-type')) {
			$this->app->document->addStylesheet('elements:separator/assets/zlfield.css');
			$this->app->document->addScript('elements:separator/assets/zlfield.min.js');
			$this->app->document->addScriptDeclaration('jQuery(function($) { $("body").ZOOtoolsSeparatorZLField({ enviroment: "' . $this->app->zlfw->getTheEnviroment() . '" }) });');
		}
	}

	/**
	 * Actions for type:beforesave event
	 */
	public function typeBeforeSave($event, $arguments = array())
	{
		$type = $event->getSubject();
		$elements = $type->config->get('elements');

		// search for decrypted passwords and encrypt
		array_walk_recursive($elements, 'plgSystemZlframework::_find_and_encrypt');

		// save result
		$type->config->set('elements', $elements);
	}

	protected static function _find_and_encrypt(&$item, $key)
	{
		$matches = array();
		if (preg_match('/zl-decrypted\[(.*)\]/', $item, $matches)) {
			$item = 'zl-encrypted[' . App::getInstance('zoo')->zlfw->crypt($matches[1], 'encrypt') . ']';
		}
	}

	/**
	 * Setting the Core Elements
	 */
	public function coreConfig($event, $arguments = array())
	{
		$config = $event->getReturnValue();
		$config['_itemlinkpro'] = array('name' => 'Item Link Pro', 'type' => 'itemlinkpro');
		$config['_staticcontent'] = array('name' => 'Static Content', 'type' => 'staticcontent');
		$event->setReturnValue($config);
	}

	/**
	 *  sefParseRoute
	 */
	public function sefParseRoute($event)
	{
		$app_id = $this->app->request->getInt('app_id', null);
		$app = $this->app->table->application->get($app_id);

		// check if was loaded
		if (!$app) return;

		$group = $app->getGroup();
		if ($router = $this->app->path->path("applications:$group/router.php")) {
			require_once $router;
			$class = 'ZLRouter' . ucfirst($group);
			$routerClass = new $class;
			$routerClass->parseRoute($event);
		}
	}
}