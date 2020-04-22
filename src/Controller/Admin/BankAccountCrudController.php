<?php

namespace App\Controller\Admin;

use App\Entity\BankAccount;
use App\Form\DTO\AdminBankAccountDTO;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BankAccountCrudController extends AbstractCrudController
{
    public static $entityFqcn = BankAccount::class;

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(100)
        ;
    }

    public function createEntity(string $entityFqcn)
    {
        return AdminBankAccountDTO::createEmpty();
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $currency = TextField::new('currency');
        $id = IntegerField::new('id', 'ID');
        $slug = TextField::new('slug');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $slug, $currency];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $slug, $currency];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $currency];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $currency];
        }
    }
}
