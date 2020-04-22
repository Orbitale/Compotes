<?php

namespace App\Controller\Admin;

use App\Entity\Operation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OperationCrudController extends AbstractCrudController
{
    public static $entityFqcn = Operation::class;

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(100)
            ->overrideTemplate('crud/index', 'easy_admin/Triage/list.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        $operationDate = DateTimeField::new('operationDate', 'admin.operations.fields.operationDate');
        $type = TextField::new('type', 'admin.operations.fields.type');
        $typeDisplay = TextField::new('typeDisplay');
        $details = TextareaField::new('details', 'admin.operations.fields.details');
        $amountInCents = IntegerField::new('amountInCents');
        $hash = TextField::new('hash');
        $state = TextField::new('state');
        $bankAccount = AssociationField::new('bankAccount');
        $tags = AssociationField::new('tags');
        $amount = NumberField::new('amount', 'admin.operations.fields.amount');
        $initialDetails = Field::new('initialDetails');
        $id = IntegerField::new('id', 'admin.operations.fields.id');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $operationDate, $type, $details, $amount];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $operationDate, $type, $typeDisplay, $details, $amountInCents, $hash, $state, $bankAccount, $tags];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$operationDate, $type, $typeDisplay, $details, $amountInCents, $hash, $state, $bankAccount, $tags];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$operationDate, $type, $amount, $initialDetails, $details];
        }
    }
}
