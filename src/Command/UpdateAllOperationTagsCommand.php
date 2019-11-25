<?php

declare(strict_types=1);

namespace App\Command;

use App\Operations\OperationTagsSynchronizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateAllOperationTagsCommand extends Command
{
    protected static $defaultName = 'operations:update-tags';

    private $synchronizer;

    public function __construct(OperationTagsSynchronizer $synchronizer)
    {
        parent::__construct(self::$defaultName);
        $this->synchronizer = $synchronizer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $updatedCount = $this->synchronizer->applyRulesOnAllOperations();

        if ($updatedCount) {
            $io->success(sprintf('Updated %d operations!', $updatedCount));
        } else {
            $io->comment('No tag rule was applied.');
        }

        return 0;
    }
}
