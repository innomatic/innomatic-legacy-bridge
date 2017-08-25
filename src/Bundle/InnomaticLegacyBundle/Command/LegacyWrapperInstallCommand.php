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
namespace Innomatic\Bundle\InnomaticLegacyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

class LegacyWrapperInstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('innomatic:legacy:assets_install')
            ->setDescription('Installs assets from Innomatic legacy installation and wrapper scripts')
            ->addArgument(
                'target',
                InputOption::VALUE_REQUIRED,
                'The target directory'
            )
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the assets instead of copying it')
            ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetArg = rtrim($input->getArgument('target'), '/');
        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        $filesystem = $this->getContainer()->get('filesystem');
        $legacyRootDir = rtrim($this->getContainer()->getParameter('innomatic_legacy.root_dir'), '/');

        $output->writeln(sprintf("Installing Innomatic legacy assets from $legacyRootDir using the <comment>%s</comment> option", $input->getOption('symlink') ? 'symlink' : 'hard copy'));

        $symlink = $input->getOption('symlink');

        $targetDir = "$targetArg/shared";
        $originDir = "$legacyRootDir/innomatic/shared";

        $filesystem->remove($targetDir);

        if ($symlink) {
            if ($input->getOption('relative')) {
                $originDir = $filesystem->makePathRelative($originDir, realpath($targetArg));
            }

            try {
                $filesystem->symlink($originDir, $targetDir);
            } catch (IOException $e) {
                $symlink = false;
                $output->writeln('It looks like your system doesn\'t support symbolic links, so will fallback to hard copy instead!');
            }
        }

        if (!$symlink) {
            $filesystem->mkdir($targetDir, 0777);
            // We use a custom iterator to ignore VCS files
            $currentDir = getcwd();
            chdir(realpath($targetArg));
            $filesystem->mirror($originDir, 'shared', Finder::create()->in($originDir));
            chdir($currentDir);
        }
    }
}