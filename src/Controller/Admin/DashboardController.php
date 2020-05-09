<?php

namespace App\Controller\Admin;

use App\Entity\BankAccount;
use App\Entity\Operation;
use App\Entity\Tag;
use App\Entity\TagRule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('🍎 Compotes 🍏')
        ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->addFormTheme('form/easyadmin_translatable_form.html.twig');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('admin.menu.account_operations', 'fas fa-folder-open');
        yield MenuItem::linktoRoute('admin.menu.analytics', 'fas fa-chart-bar', 'analytics');
        yield MenuItem::linkToCrud('admin.menu.operations', 'fas fa-money-check', Operation::class);
        yield MenuItem::linkToCrud('admin.menu.triage', 'fas fa-box-open', Operation::class);
        yield MenuItem::linkToCrud('admin.menu.bank_accounts', 'fas fa-piggy-bank', BankAccount::class);

        yield MenuItem::section('admin.menu.tags', 'fas fa-folder-open');
        yield MenuItem::linkToCrud('admin.menu.tag_rules', 'fas fa-ruler-vertical', TagRule::class);
        yield MenuItem::linkToCrud('admin.menu.tags', 'fas fa-tags', Tag::class);

        yield MenuItem::section('admin.menu.actions', 'fas fa-folder-open');
        yield MenuItem::linktoRoute('admin.menu.import_operations', 'fas fa-upload', 'import_operations');
        yield MenuItem::linktoRoute('admin.menu.sync_operations', 'fas fa-sync', 'sync_operations');
    }
}
