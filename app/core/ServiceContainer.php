<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;

class ServiceContainer
{
    private static ?ServiceContainer $instance = null;
    private array $services = [];
    private array $singletons = [];
    private array $config = [];

    private function __construct()
    {
        $this->loadConfiguration();
    }

    public static function getInstance(): ServiceContainer
    {
        if (self::$instance === null) {
            self::$instance = new ServiceContainer();
        }
        return self::$instance;
    }

    private function loadConfiguration(): void
    {
        $configFile = __DIR__ . '/../services.yml';
        if (file_exists($configFile)) {
            $this->config = Yaml::parseFile($configFile);
        }
    }

    public function get(string $serviceName): object
    {
        // Vérifier si c'est un singleton déjà instancié
        if (isset($this->singletons[$serviceName])) {
            return $this->singletons[$serviceName];
        }

        // Chercher dans la configuration
        $serviceConfig = $this->findServiceConfig($serviceName);
        
        if (!$serviceConfig) {
            throw new \Exception("Service '$serviceName' non trouvé dans la configuration");
        }

        $className = $serviceConfig['class'];
        
        if (!class_exists($className)) {
            throw new \Exception("Classe '$className' non trouvée");
        }

        // Résoudre les dépendances
        $dependencies = [];
        if (isset($serviceConfig['dependencies'])) {
            foreach ($serviceConfig['dependencies'] as $dependency) {
                $dependencies[] = $this->get($dependency);
            }
        }

        // Instancier le service
        $service = empty($dependencies) 
            ? new $className() 
            : new $className(...$dependencies);

        // Stocker si singleton
        if ($serviceConfig['singleton'] ?? false) {
            $this->singletons[$serviceName] = $service;
        }

        return $service;
    }

    private function findServiceConfig(string $serviceName): ?array
    {
        $sections = ['repositories', 'services', 'controllers'];
        
        foreach ($sections as $section) {
            if (isset($this->config['services'][$section][$serviceName])) {
                return $this->config['services'][$section][$serviceName];
            }
        }
        
        return null;
    }

    public function getConfig(string $key = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function has(string $serviceName): bool
    {
        return $this->findServiceConfig($serviceName) !== null;
    }
}
