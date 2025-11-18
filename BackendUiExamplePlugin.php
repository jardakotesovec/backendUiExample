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

use APP\core\Application;
use PKP\plugins\GenericPlugin;
use APP\template\TemplateManager;
use PKP\plugins\Hook;
use PKP\components\forms\FormComponent;
use PKP\components\forms\FieldText;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use PKP\core\PKPBaseController;
use PKP\handler\APIHandler;
use PKP\security\Role;

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

                // To Attach smarty template to the settings template
                Hook::add('Template::Settings::website', [$this, 'callbackShowWebsiteSettingsTabs']);
                // To Attach data to the page, so it can be passed 
                Hook::add('TemplateManager::display', [$this, 'callbackTemplateManagerDisplay']);


                // Registering build file for JS and CSS to be loaded
                $request = Application::get()->getRequest();
                $templateMgr = TemplateManager::getManager($request);
                $this->addJavaScript($request, $templateMgr);
                $templateMgr->addStyleSheet('backendUiExampleStyle',"{$request->getBaseUrl()}/{$this->getPluginPath()}/public/build/build.css", [
                    'contexts' => ['backend']
                ] );

                // Add steps to submission wizard
                Hook::add('TemplateManager::display', $this->addToSubmissionWizardSteps(...));


                 $this->addRoute();
            }
            return true;
        }
        return false;
    }

    public function addJavaScript($request, $templateMgr)
    {
        $templateMgr->addJavaScript(
            'BackendUiExample',
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
     * Create a simple example form using FormComponent
     *
     * @return FormComponent
     */
    public function getExampleForm()
    {
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $dispatcher = $request->getDispatcher();

        $site = $request->getSite();
        $locales = $site->getSupportedLocaleNames();
        $locales = array_map(fn (string $locale, string $name) => ['key' => $locale, 'label' => $name], array_keys($locales), $locales);

        // Create the form
        $form = new FormComponent(
            'examplePluginForm',
            'POST',
            $dispatcher->url(
                $request,
                Application::ROUTE_API,
                $context->getData('urlPath'),
                'examplePlugin/saveSettings'
            ),
            $locales
        );

        // Add text field
        $form->addField(new FieldText('exampleTitle', [
            'label' => __('plugins.generic.backendUiExample.form.title'),
            'isRequired' => true,
            'groupId' => 'exampleSettings',
            'value' => '',
        ]));

        return $form;
    }

    /**
     * Add state for your component when needed
     *
     * @param string $hookName The name of the invoked hook
     * @param array $args Hook parameters
     *
     * @return bool Hook handling status
     */
    public function callbackTemplateManagerDisplay($hookName, $args)
    {
        $templateMgr = $args[0];
        $request = Application::get()->getRequest();
        $dispatcher = $request->getDispatcher();
        $context = $request->getContext();
        if($request->getRequestedOp() == 'settings') {
            // Get the form configuration
            $form = $this->getExampleForm();

            
            $templateMgr->setState([
                'backendUiExampleData' => [
                    'customForm' => $form->getConfig()
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

    public function addRoute(): void
    {
        Hook::add('APIHandler::endpoints::submissions', function(string $hookName, PKPBaseController &$apiController, APIHandler $apiHandler): bool {
            // This allow to add a route on fly without defining a api controller
            // Through this allow quick add/modify routes, it's better to use
            // controller based appraoch which is more structured and understandable
            $apiHandler->addRoute(
                'GET',
                'ithenticate',
                function (IlluminateRequest $request): JsonResponse {

                    $fileIds = $request->query('fileIds');
                    $fileStatuses = [];
                    foreach ($fileIds as $fileId) {
                        $fileId = trim($fileId);
                        if (!empty($fileId)) {
                            // Here you would typically query your database or service
                            // to get the actual status of each file.
                            // For this example, we'll use a mock status
                            $fileStatuses[$fileId] = '75%';
                        }
                    }

                    return response()->json($fileStatuses, Response::HTTP_OK);
                },
                'test.onfly',
                [
                    Role::ROLE_ID_SITE_ADMIN,
                    Role::ROLE_ID_MANAGER,
                    Role::ROLE_ID_SUB_EDITOR,
                ]
            );
            
            
            return false;
        });
    }

    /**
     * Inject a funding section into the submission wizard steps UI.
     *
     * @param string $hookName
     * @param array $params
     *
     * @return bool Hook return value
     */
    function addToSubmissionWizardSteps($hookName, $params) {
        error_log('add to submission wizard steps');
        $request = Application::get()->getRequest();

        if ($request->getRequestedPage() !== 'submission') {
            return;
        }

        if ($request->getRequestedOp() === 'saved') {
            return;
        }

        $submission = $request
            ->getRouter()
            ->getHandler()
            ->getAuthorizedContextObject(Application::ASSOC_TYPE_SUBMISSION);

        if (!$submission || !$submission->getData('submissionProgress')) {
            return;
        }

        /** @var TemplateManager $templateMgr */
        $templateMgr = $params[0];

        $steps = $templateMgr->getState('steps');
        $steps = array_map(function($step) use ($submission) {
            if ($step['id'] === 'files') {
                $step['sections'][] = [
                    'id' => 'buiCustom',
                    'name' => 'Custom section',
                    'description' => 'custom description',
                    'component' => 'BuiSubmissionWizardExample',
                    'props' => ['submissionId' => $submission->getId()]
                ];
            }
            return $step;
        }, $steps);

        $templateMgr->setState([
            'steps' => $steps,
        ]);

        // Add custom component to reviewSteps
        $reviewSteps = $templateMgr->getTemplateVars('reviewSteps') ?: [];
        $reviewSteps[] = [
            'id' => 'buiReviewCustom',
            'component' => 'BuiSubmissionWizardReviewExample',
            'props' => [
                'submissionId' => $submission->getId(),
                'title' => "'Custom Review Component'"
            ]
        ];
        $templateMgr->assign('reviewSteps', $reviewSteps);

        return false;
    }


}
