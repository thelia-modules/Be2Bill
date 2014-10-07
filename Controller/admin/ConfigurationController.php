<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 02/10/2014
 * Time: 15:50
 */

namespace Be2Bill\Controller\admin;


use Be2Bill\Form\ConfigForm;
use Be2Bill\Model\Be2billConfigQuery;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

class ConfigurationController extends BaseAdminController
{
    public function configure()
    {

        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'Be2Bill', AccessManager::UPDATE)) {
            return $response;
        }

        $configForm = new ConfigForm($this->getRequest());
        $message = null;
        try {

            $form = $this->validateForm($configForm);
            $data = $form->getData($form);

            foreach ($data as $name => $value) {
                Be2billConfigQuery::set($name, $value);
            }

            // Log configuration modification
            $this->adminLogAppend(
                "be2bill.configuration.message",
                AccessManager::UPDATE,
                sprintf("Be2bill configuration updated")
            );

            // Redirect to the success URL,
            if ($this->getRequest()->get('save_mode') == 'stay') {
                // If we have to stay on the same page, redisplay the configuration page/
                $route = '/admin/module/Be2Bill';
            } else {
                // If we have to close the page, go back to the module back-office page.
                $route = '/admin/modules';
            }

            $this->redirect(URL::getInstance()->absoluteUrl($route));

        } catch (FormValidationException $e) {
            $message = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        if (null !== $message) {
            $this->setupFormErrorContext(
                'Be2Bill Configuration',
                $message,
                $configForm,
                $e
            );

            $response = $this->render(
                'module-configure',
                ['module_code' => 'Be2Bill']
            );
        }

        return $response;

    }
} 