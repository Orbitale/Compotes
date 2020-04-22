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
            ->setTitle('🍎 Compotes 🍏');
    }

    public function configureCrud(): Crud
    {
        return Crud::new();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('admin.menu.account_operations', 'fa fa-folder-open');
        yield MenuItem::linktoRoute('admin.menu.analytics', 'fa fa-chart-bar', 'analytics');
        yield MenuItem::linkToCrud('admin.menu.operations', 'fa fa-money-check', Operation::class);
        yield MenuItem::linkToCrud('admin.menu.triage', 'fa fa-box-open', Operation::class);
        yield MenuItem::linkToCrud('admin.menu.bank_accounts', 'fa fa-piggy-bank', BankAccount::class);

        yield MenuItem::section('admin.menu.tags', 'fa fa-folder-open');
        yield MenuItem::linkToCrud('admin.menu.tag_rules', 'fa fa-ruler-vertical', TagRule::class);
        yield MenuItem::linkToCrud('admin.menu.tags', 'fa fa-tags', Tag::class);

        yield MenuItem::section('admin.menu.actions', 'fa fa-folder-open');
        yield MenuItem::linktoRoute('admin.menu.import_operations', 'fa fa-upload', 'import_operations');
        yield MenuItem::linktoRoute('admin.menu.sync_operations', 'fa fa-sync', 'sync_operations');
    }
}
