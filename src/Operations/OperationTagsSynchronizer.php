<?php

declare(strict_types=1);

namespace App\Operations;

use App\Repository\OperationRepository;
use App\Repository\TagRepository;
use App\Repository\TagRuleRepository;
use Doctrine\ORM\EntityManagerInterface;

class OperationTagsSynchronizer
{
    private $operationRepository;
    private $tagRepository;
    private $tagRuleRepository;
    private $em;

    public function __construct(
        OperationRepository $operationRepository,
        TagRepository $tagRepository,
        TagRuleRepository $tagRuleRepository,
        EntityManagerInterface $em
    ) {
        $this->operationRepository = $operationRepository;
        $this->tagRepository = $tagRepository;
        $this->tagRuleRepository = $tagRuleRepository;
        $this->em = $em;
    }

    public function applyRulesOnAllOperations(): int
    {
        $rules = $this->tagRuleRepository->findAll();
        $this->tagRepository->findAll(); // Trick to automate loading all tags for all operations
        $operations = $this->operationRepository->findAll();

        $numberOfApplied = 0;

        foreach ($rules as $rule) {
            foreach ($operations as $operation) {
                $operation->applyRule($rule);
            }
        }

        if ($numberOfApplied) {
            $this->em->flush();
        }

        return $numberOfApplied;
    }
}
