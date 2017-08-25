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

class LegacyScriptCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('innomatic:legacy:script')
            ->addArgument('script', InputArgument::REQUIRED, 'Path to legacy script you want to run. Path must be relative to the Innomatic legacy root')
            ->addOption('legacy-help', null, InputOption::VALUE_NONE, 'Use this option if you want to display help for the legacy script')
            ->setDescription('Runs an Innomatic legacy script.')
            ->setHelp(
                <<<EOT
The command <info>%command.name%</info> runs a <info>legacy script</info>.
Passed <info>script</info> argument must be relative to Innomatic legacy scripts directory (e.g. applications.php).
EOT
            );

        // Ignore validation errors to avoid exceptions due to non declared options/arguments (those passed to the legacy script)
        $this->ignoreValidationErrors();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $legacyRootDir = $this->getContainer()->getParameter('innomatic_legacy.root_dir');
        $legacyScript = $input->getArgument('script');

        // Check if the given script exists
        if (!file_exists($legacyRootDir.'/innomatic/core/scripts/'.$legacyScript)) {
            throw new \InvalidArgumentException(sprintf('The script "%s" does not exist.', $input->getArgument('script')));
        }
        parent::initialize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $legacyScript = $input->getArgument('script');

        // Cleanup the input arguments as the legacy kernel expects the script to run as first argument
        foreach ($_SERVER['argv'] as $rawArg) {
            if ($rawArg === $legacyScript) {
                break;
            }

            array_shift($_SERVER['argv']);
            array_shift($GLOBALS['argv']);
        }

        if ($input->getOption('legacy-help')) {
            $_SERVER['argv'][] = '-h';
            $GLOBALS['argv'][] = '-h';
        }

        $output->writeln("<comment>Running script '$legacyScript' in Innomatic legacy context</comment>");

        $legacyKernel = $this->getContainer()->get('innomatic_legacy.kernel');

        $legacyKernel->runCallback(
            function () use ($legacyScript) {
                $argv = isset($GLOBALS['argv']) ? $GLOBALS['argv'] : [];
                $argc = isset($GLOBALS['argc']) ? $GLOBALS['argc'] : 0;

                include 'innomatic/core/scripts/'.$legacyScript;
            }
        );
    }
}
