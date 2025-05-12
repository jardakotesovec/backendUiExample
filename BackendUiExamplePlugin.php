<?php

/**
 * @file BackendUiExamplePlugin.inc.php
 *
 * Copyright (c) 2023 Simon Fraser University
 * Copyright (c) 2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Backend Ui Example Plugin
 */
namespace APP\plugins\generic\backendUiExample;

 use PKP\plugins\GenericPlugin;
 use PKP\plugins\Hook;
 use APP\core\Application;
 use APP\template\TemplateManager;


class BackendUiExamplePlugin extends GenericPlugin {
    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
    public function register($category, $path, $mainContextId = null)
    {
        if (parent::register($category, $path, $mainContextId)) {
            if ($this->getEnabled($mainContextId)) {

                Hook::add('Template::Settings::website', [$this, 'callbackShowWebsiteSettingsTabs']);
                Hook::add('TemplateManager::display', [$this, 'callbackTemplateManagerDisplay']);

                $request = Application::get()->getRequest();
                $templateMgr = TemplateManager::getManager($request);
                $this->addJavaScript($request, $templateMgr);
                $templateMgr->addStyleSheet('backendUiExampleStyle',"{$request->getBaseUrl()}/{$this->getPluginPath()}/public/build/style.css", [
                    'contexts' => ['backend']
                ] );
            }
            return true;
        }
        return false;
    }

    public function addJavaScript($request, $templateMgr)
    {
        $templateMgr->addJavaScript(
            'MyComponent1',
            "{$request->getBaseUrl()}/{$this->getPluginPath()}/public/build/build.iife.js",
            [
                'inline' => false,
                'contexts' => ['backend'],
                'priority' => TemplateManager::STYLE_SEQUENCE_LAST
            ]
        );
    }


    /**
     * Extend the website settings tabs to include example plugin
     *
     * @param string $hookName The name of the invoked hook
     * @param array $args Hook parameters
     *
     * @return bool Hook handling status
     */
    public function callbackShowWebsiteSettingsTabs($hookName, $args)
    {
        $templateMgr = $args[1];
        $output = & $args[2];

        $output .= $templateMgr->fetch($this->getTemplateResource('settingsTab.tpl'));
        // Permit other plugins to continue interacting with this hook
        return false;
    }

    /**
     * Add state for your component when needed
     *
     * @param string $hookName The name of the invoked hook
     * @param array $args Hook parameters
     *
     * @return bool Hook handling status
     */
    public function  callbackTemplateManagerDisplay($hookName, $args)
    {
        $templateMgr = $args[0];
        $request = Application::get()->getRequest();
        $dispatcher = $request->getDispatcher();
        $context = $request->getContext();

        if($request->getRequestedOp() == 'settings') {
            $templateMgr->setState([
                'backendUiExampleData' => [
                    'apiUrl' => $dispatcher->url(
                        $request,
                        Application::ROUTE_API,
                        $context->getData('urlPath'),
                    )
                ]
            ]);   
        }
        // Permit other plugins to continue interacting with this hook
        return false;
    }


    /**
     * Get the display name of this plugin.
     * @return String
     */
    function getDisplayName() {
        return __('plugins.generic.backendUiExample.displayName');
    }

    /**
     * Get a description of the plugin.
     */
    function getDescription() {
        return __('plugins.generic.backendUiExample.description');
    }
}
