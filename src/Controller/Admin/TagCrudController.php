<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TagCrudController extends AbstractCrudController
{
    public static $entityFqcn = Tag::class;

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(100);
    }

    public function configureFields(string $pageName): iterable
    {
        $translatedNames = Field::new('translatedNames');
        $parent = AssociationField::new('parent');
        $id = IntegerField::new('id', 'ID');
        $name = TextField::new('name');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $parent];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $parent];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$translatedNames, $parent];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$translatedNames, $parent];
        }
    }
}
