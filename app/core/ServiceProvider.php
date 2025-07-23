<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;

class ServiceProvider
{
    private Container $container;
    private array $config;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->loadConfig();
    }

    /**
     * Charger la configuration YAML
     */
    private function loadConfig(): void
    {
        $configPath = dirname(__DIR__) . '/config/services.yml';
        if (!file_exists($configPath)) {
            throw new \Exception("Fichier de configuration des services introuvable: $configPath");
        }
        
        $yamlConfig = Yaml::parseFile($configPath);
        $this->config = $yamlConfig['services'] ?? [];
    }

    /**
     * Enregistrer tous les services depuis la configuration YAML
     */
    public function register(): void
    {
        // Enregistrer les repositories
        $this->registerCategory('repositories');
        
        // Enregistrer les services
        $this->registerCategory('services');
        
        // Enregistrer les controllers
        $this->registerCategory('controllers');
    }

    /**
     * Enregistrer une catégorie de services
     */
    private function registerCategory(string $category): void
    {
        if (!isset($this->config[$category])) {
            return;
        }

        foreach ($this->config[$category] as $serviceName => $serviceConfig) {
            $className = $serviceConfig['class'];
            $isSingleton = $serviceConfig['singleton'] ?? false;

            if ($isSingleton) {
                $this->container->singleton($className, $className);
            } else {
                $this->container->bind($className, $className);
            }

            // Enregistrer aussi avec le nom court pour compatibilité
            if ($isSingleton) {
                $this->container->singleton($serviceName, $className);
            } else {
                $this->container->bind($serviceName, $className);
            }
        }
    }

    /**
     * Obtenir la configuration des dépendances pour un service
     */
    public function getDependencies(string $serviceName): array
    {
        foreach (['repositories', 'services', 'controllers'] as $category) {
            if (isset($this->config[$category][$serviceName]['dependencies'])) {
                return $this->config[$category][$serviceName]['dependencies'];
            }
        }
        
        return [];
    }

    /**
     * Obtenir la configuration complète
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
