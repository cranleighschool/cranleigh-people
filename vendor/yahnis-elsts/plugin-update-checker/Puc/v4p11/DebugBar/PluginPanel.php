<?php

if (! class_exists('Puc_v4p11_DebugBar_PluginPanel', false)) {
    class Puc_v4p11_DebugBar_PluginPanel extends Puc_v4p11_DebugBar_Panel
    {
        /**
         * @var Puc_v4p11_Plugin_UpdateChecker
         */
        protected $updateChecker;

        protected function displayConfigHeader()
        {
            $this->row('Plugin file', htmlentities($this->updateChecker->pluginFile));
            parent::displayConfigHeader();
        }

        protected function getMetadataButton()
        {
            $requestInfoButton = '';
            if (function_exists('get_submit_button')) {
                $requestInfoButton = get_submit_button(
                    'Request Info',
                    'secondary',
                    'puc-request-info-button',
                    false,
                    ['id' => $this->updateChecker->getUniqueName('request-info-button')]
                );
            }

            return $requestInfoButton;
        }

        protected function getUpdateFields()
        {
            return array_merge(
                parent::getUpdateFields(),
                ['homepage', 'upgrade_notice', 'tested']
            );
        }
    }
}
