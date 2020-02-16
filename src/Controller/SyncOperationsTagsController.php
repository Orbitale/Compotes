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

namespace App\Controller;

use App\Operations\OperationTagsSynchronizer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SyncOperationsTagsController
{
    private OperationTagsSynchronizer $synchronizer;
    private UrlGeneratorInterface $router;

    public function __construct(OperationTagsSynchronizer $synchronizer, UrlGeneratorInterface $router)
    {
        $this->synchronizer = $synchronizer;
        $this->router = $router;
    }

    /**
     * @Route("/admin/sync-operations-tags", name="sync_operations_tags", methods={"GET"})
     */
    public function __invoke(Session $session): Response
    {
        $synced = $this->synchronizer->applyRulesOnAllOperations();

        $session->getFlashBag()->add('success', \sprintf('Synced %d operations!', $synced));

        return new RedirectResponse($this->router->generate('easyadmin'));
    }
}
