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
use Symfony\Contracts\Translation\TranslatorInterface;

class SyncOperationsController
{
    private OperationTagsSynchronizer $synchronizer;
    private UrlGeneratorInterface $router;
    private TranslatorInterface $translator;

    public function __construct(
        OperationTagsSynchronizer $synchronizer,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router
    ) {
        $this->synchronizer = $synchronizer;
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @Route("/admin/sync-operations", name="sync_operations", methods={"GET"})
     */
    public function __invoke(Session $session): Response
    {
        $synced = $this->synchronizer->applyRulesOnAllOperations();

        if ($synced) {
            $session->getFlashBag()->add('success', $this->translator->trans('admin.sync_operations.success', [
                '%synced%' => $synced,
            ]));
        } else {
            $session->getFlashBag()->add('info', $this->translator->trans('admin.sync_operations.no_operations_synced'));
        }

        return new RedirectResponse($this->router->generate('easyadmin'));
    }
}
