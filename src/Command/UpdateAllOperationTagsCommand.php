<?php

declare(strict_types=1);

/*
 * This file is part of the Compotes package.
 *
 * (c) Alex "Pierstoval" Rock <pierstoval@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
            $io->success(\sprintf('Updated %d operations!', $updatedCount));
        } else {
            $io->comment('No tag rule was applied.');
        }

        return 0;
    }
}
