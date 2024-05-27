<?php

declare(strict_types=1);

namespace NitroPack\NitroPack\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use NitroPack\NitroPack\Api\PurgeManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurgeByUrl extends Command
{
    private const URLS = 'urls';

    /**
     * @var PurgeManagementInterface
     */
    private $purgeManagementInterface;

    /**
     * @var State
     */
    private $state;

    /**
     * @param PurgeManagementInterface $purgeManagementInterface
     * @param State $state
     */
    public function __construct(
        PurgeManagementInterface $purgeManagementInterface,
        State $state
    )
    {
        $this->purgeManagementInterface = $purgeManagementInterface;
        $this->state = $state;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('nitropack:purge:url');
        $this->setDescription('Clears NitroPack cache for specific urls.');
        $this->addArgument(
            self::URLS,
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'URL address'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        if ($urls = $input->getArgument(self::URLS)) {
            try {
                $this->state->setAreaCode(Area::AREA_FRONTEND);
                $this->purgeManagementInterface->purgeByUrl($urls);
                $output->writeln('<info>Successfully purged NitroPack cache for urls: ' . implode(',', $urls) . '</info>');

            } catch (\Exception $exception) {
                $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
                $exitCode = 1;
            }
        }

        return $exitCode;
    }
}

