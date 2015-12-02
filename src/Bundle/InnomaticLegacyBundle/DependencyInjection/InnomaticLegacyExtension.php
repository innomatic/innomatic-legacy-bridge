<?php
/**
 * Innomatic
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  2014-2015 Innoteam Srl
 * @license    http://www.innomatic.io/license/ New BSD License
 * @link       http://www.innomatic.io
 */
namespace Innomatic\Bundle\InnomaticLegacyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class InnomaticLegacyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set if the Innomatic legacy is enabled
        $container->setParameter('innomatic_legacy.enabled', $config['enabled']);
        if ($config['enabled']) {
            $loader = new Loader\YamlFileLoader(
                $container,
                new FileLocator(__DIR__ . '/../Resources/config')
            );
            $loader->load('services.yml');

            // Set the Innomatic legacy root directory
            $container->setParameter('innomatic_legacy.root_dir', $config['root_dir']);
        }
    }
}
