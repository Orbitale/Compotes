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

namespace App\Controller\Admin;

use App\Entity\BankAccount;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BankAccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BankAccount::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'name', 'slug', 'currency'])
            ->setPaginatorPageSize(100)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $currency = TextField::new('currency');
        $id = IntegerField::new('id', 'ID');
        $slug = TextField::new('slug');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $slug, $currency];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $slug, $currency];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$name, $currency];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$name, $currency];
        }
    }
}
