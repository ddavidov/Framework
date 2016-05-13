<?php

namespace Zoolanders\Framework\Listener\Environment;

use Zoolanders\Framework\Listener\Listener;

class LoadFieldAssets extends Listener
{
    /**
     * @param \Zoolanders\Framework\Event\Environment\Init $event
     */
    public function handle(\Zoolanders\Framework\Event\Environment\Init $event)
    {
        // perform admin tasks
        if ($event->is('zoo-type-edit')) {
            // init vars
            $cid  = $event->getRequest()->get('cid.0', 'string', '');
            $type = $cid ? $cid : $event->getRequest()->get('type', 'string', '');

            $url = $this->container->link->link(array('option' => 'com_zoo', 'controller' => 'zlframework', 'format' => 'raw', 'type' => $type), false);
            $enviroment_args = json_decode($event->getRequest()->get('enviroment_args', 'string', ''), true);
            $enviroment_args = $this->container->data->create($enviroment_args);

            // load zlfield assets
            $this->container->document->addStylesheet('zlfield:zlfield.css');
            $this->container->document->addStylesheet('zlfield:layouts/field/style.css');
            $this->container->document->addStylesheet('zlfield:layouts/separator/style.css');
            $this->container->document->addStylesheet('zlfield:layouts/wrapper/style.css');
            $this->container->document->addScript('zlfield:zlfield.js');

            if ($event->getRequest()->getVar('enviroment', false) == 'module') {
                $this->container->document->addScript('libraries:jquery/jquery-ui.custom.min.js');
                $this->container->document->addStylesheet('libraries:jquery/jquery-ui.custom.css');
                $this->container->document->addScript('libraries:jquery/plugins/timepicker/timepicker.js');
                $this->container->document->addStylesheet('libraries:jquery/plugins/timepicker/timepicker.css');
            }

            // workaround for jQuery 1.9 transition
            $this->container->document->addScript('zlfw:assets/js/jquery.plugins/jquery.migrate.min.js');

            // load libraries
            $this->container->zoo->getApp()->zlfw->zlux->loadMainAssets();
            $this->container->zoo->getApp()->zlfw->loadLibrary('qtip');
            $this->container->document->addStylesheet('zlfw:assets/libraries/zlux/zlux.css');

            // load wk2 assets
            if ($this->container->filesystem->has(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit.xml')) {
                $wk_manifest = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit.xml');

                if (version_compare($wk_manifest->version, '2.0', '>=') &&
                    $app = @include(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit-app.php')) {
                    $app->trigger('init.admin', array($app));
                }
            }

            // init scripts
            $javascript = "jQuery(function($){ $('body').ZLfield({ url: '{$url}', type: '{$type}', enviroment: '{$event->getRequest()->get('enviroment', '')}', enviroment_args: '{$enviroment_args}' }); });";
            $this->container->document->addScriptDeclaration($javascript);
        }
    }
}