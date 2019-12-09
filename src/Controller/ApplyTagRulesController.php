<?php

namespace App\Controller;

use App\Operations\OperationTagsSynchronizer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApplyTagRulesController
{
    private OperationTagsSynchronizer $synchronizer;
    private UrlGeneratorInterface $router;

    public function __construct(OperationTagsSynchronizer $synchronizer, UrlGeneratorInterface $router)
    {
        $this->synchronizer = $synchronizer;
        $this->router = $router;
    }

    /**
     * @Route("/admin/apply-rules", name="apply_rules")
     */
    public function __invoke(Session $session): Response
    {
        $synced = $this->synchronizer->applyRulesOnAllOperations();

        $session->getFlashBag()->add('success', \sprintf('Synced %d operations!', $synced));

        return new RedirectResponse($this->router->generate('easyadmin'));
    }
}
