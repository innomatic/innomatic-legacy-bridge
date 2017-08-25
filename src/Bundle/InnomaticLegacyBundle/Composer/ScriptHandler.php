<?php
/**
 * Innomatic
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  2015-2017 Innoteam Srl
 * @license    https://innomatic.atlassian.net/wiki/display/IMP/Innomatic+License New BSD License
 * @link       http://www.innomatic.io
 */
namespace Innomatic\Bundle\InnomaticLegacyBundle\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as DistributionBundleScriptHandler;
use Composer\Script\Event;

class ScriptHandler extends DistributionBundleScriptHandler
{
    /**
     * Installs the legacy assets under the web root directory.
     *
     * For better interoperability, assets are copied instead of symlinked by default.
     *
     * @param $event Event A instance
     */
    public static function installAssets(Event $event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];
        $webDir = $options['symfony-web-dir'];

        $symlink = '';
        if ($options['symfony-assets-install'] === 'symlink') {
            $symlink = '--symlink ';
        } elseif ($options['symfony-assets-install'] === 'relative') {
            $symlink = '--symlink --relative ';
        }

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not install assets.'.PHP_EOL;
            return;
        }

        if (!is_dir($webDir)) {
            echo 'The symfony-web-dir ('.$webDir.') specified in composer.json was not found in '.getcwd().', can not install assets.'.PHP_EOL;
            return;
        }

        static::executeCommand($event, $appDir, 'innomatic:legacy:assets_install '.$symlink.escapeshellarg($webDir));
    }
}
