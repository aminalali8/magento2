<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlCache\Setup;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\Data\ConfigData;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Setup\ConfigOptionsListInterface;
use Magento\Framework\Setup\Option\TextConfigOption;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Math\Random;

/**
 * GraphQl Salt option.
 */
class ConfigOptionsList implements ConfigOptionsListInterface
{
    /**
     * @var Random
     */
    private $random;

    /**
     * Deployment configuration
     *
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @param Random $random
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        Random $random,
        DeploymentConfig $deploymentConfig
    ) {
        $this->random = $random;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return [
            new TextConfigOption(
                ConfigOptionsListConstants::INPUT_KEY_SALT,
                TextConfigOption::FRONTEND_WIZARD_TEXT,
                ConfigOptionsListConstants::CONFIG_PATH_SALT,
                'GraphQl Salt'
            ),
        ];
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createConfig(array $data, DeploymentConfig $deploymentConfig)
    {
        $currentIdSalt = $this->deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_SALT);

        $configData = new ConfigData(ConfigFilePool::APP_ENV);

        // Use given salt if set, else use current
        $id_salt = $data[ConfigOptionsListConstants::INPUT_KEY_SALT] ?? $currentIdSalt;

        // If there is no id_salt given or currently set, generate a new one
        $id_salt = $id_salt ?? $this->random->getRandomString(ConfigOptionsListConstants::STORE_KEY_RANDOM_STRING_SIZE);

        if (!$this->isDataEmpty($data, ConfigOptionsListConstants::INPUT_KEY_SALT)) {
            $configData->set(ConfigOptionsListConstants::CONFIG_PATH_SALT, $id_salt);
        }

        return [$configData];
    }

    /**
     * @inheritdoc
     */
    public function validate(array $options, DeploymentConfig $deploymentConfig)
    {
        return [];
    }

    /**
     * Check if data ($data) with key ($key) is empty
     *
     * @param array $data
     * @param string $key
     * @return bool
     */
    private function isDataEmpty(array $data, $key)
    {
        if (isset($data[$key]) && $data[$key] !== '') {
            return false;
        }

        return true;
    }
}
