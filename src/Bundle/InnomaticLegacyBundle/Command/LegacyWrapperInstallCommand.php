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
namespace Innomatic\Bundle\InnomaticLegacyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LegacyWrapperInstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('innomatic:legacy:assets_install')
            ->setDescription('Installs assets from Innomatic legacy installation and wrapper scripts')
            ->addArgument(
                'target',
                InputOption::VALUE_OPTIONAL,
                'The target directory'
            )
            ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the assets instead of copying it')
            ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $legacyRootDir = rtrim($this->getContainer()->getParameter('innomatic_legacy.root_dir'), '/');

        //$output->writeln( sprintf( "Installing Innomatic legacy assets from $legacyRootDir using the <comment>%s</comment> option", $input->getOption( 'symlink' ) ? 'symlink' : 'hard copy' ) );


        $name = $input->getArgument('target');

        if ($name) {
            $text = 'Test '.$name[0];
        } else {
            $text = 'Test';
        }

        $output->writeln('<info>Information</info>');
        $output->writeln($text);
    }
}