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

use App\Form\DTO\ImportOperations;
use App\Form\Type\ImportOperationsType;
use App\Model\ImportOptions;
use App\Operations\OperationsImporter;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use Twig\Environment;

class ImportOperationsController
{
    private Environment $twig;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private TranslatorInterface $translator;
    private OperationsImporter $importer;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        OperationsImporter $importer
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->importer = $importer;
    }

    /**
     * @Route("/import-operations", name="import_operations", methods={"GET", "POST"})
     */
    public function __invoke(Request $request): Response
    {
        $data = new ImportOperations();
        $form = $this->formFactory->create(ImportOperationsType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $created = $this->importer->importFile(
                    $data->file,
                    $data->csvColumns,
                    ImportOptions::create($data->getCsvSeparator(), $data->getCsvDelimiter(), $data->getCsvEscapeCharacter())
                );

                $this->flashBag->add(
                    $created > 0 ? 'success' : 'warning',
                    $this->translator->trans('import_operations.form.success', [
                        '%count%' => $created,
                    ])
                );

                return new RedirectResponse($request->getPathInfo());
            } catch (Throwable $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return new Response($this->twig->render('import_operations.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
