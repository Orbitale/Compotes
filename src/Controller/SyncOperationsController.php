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
use App\Operations\TriageSynchronizer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SyncOperationsController
{
    private OperationTagsSynchronizer $synchronizer;
    private TriageSynchronizer $triageSynchronizer;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $router;

    public function __construct(
        OperationTagsSynchronizer $synchronizer,
        TriageSynchronizer $triageSynchronizer,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router
    ) {
        $this->synchronizer = $synchronizer;
        $this->triageSynchronizer = $triageSynchronizer;
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @Route("/admin/sync-operations", name="sync_operations", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $pendingTriage = $this->triageSynchronizer->syncOperations();
        $synced = $this->synchronizer->applyRulesOnAllOperations();

        /** @var Session $session */
        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        if ($synced) {
            $flashBag->add('success', $this->translator->trans('admin.sync_operations.success', [
                '%count%' => $synced,
            ]));
        } else {
            $flashBag->add('info', $this->translator->trans('admin.sync_operations.no_operations_synced'));
        }

        if ($pendingTriage) {
            $flashBag->add('warning', $this->translator->trans('admin.sync_operations.pending_triage', [
                '%count%' => $pendingTriage,
            ]));
        }

        return new RedirectResponse($this->router->generate('easyadmin'));
    }
}
