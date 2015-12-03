<?php
/**
 * Innomatic
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright  2015 Innoteam Srl
 * @license    https://innomatic.atlassian.net/wiki/display/IMP/Innomatic+License New BSD License
 * @link       http://www.innomatic.io
 */
namespace Innomatic\Bundle\InnomaticLegacyBundle\Cache;

use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Innomatic\Core\MVC\Legacy\Kernel;
use Innomatic\Datatransfer\Cache\CacheGarbageCollector;

/**
 * Purger for Innomatic legacy file based cache.
 * Hooks into cache:clear command, which is also used by composer install and update.
 */
class LegacyCachePurger implements CacheClearerInterface
{
    /**
     * @var \Innomatic\Core\MVC\Legacy\Kernel
     */
    private $legacyKernel;

    public function __construct(
        Kernel $legacyKernel,
        $legacyRootDir
    ) {
        $this->legacyKernel = $legacyKernel;
    }

    /**
     * Clears Innomatic legacy cache.
     *
     * @param string $cacheDir The cache directory, not used by Innomatic legacy.
     */
    public function clear($cacheDir)
    {
        $this->legacyKernel->runCallback(
            function () {
                $gc = new CacheGarbageCollector();
                $gc->emptyCache();
            }
        );
    }
}
