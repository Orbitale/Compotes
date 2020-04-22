<?php

namespace App\Controller\Admin;

use App\Entity\TagRule;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class TagRuleCrudController extends AbstractCrudController
{
    public static $entityFqcn = TagRule::class;

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(100);
    }

    public function configureFields(string $pageName): iterable
    {
        $matchingPattern = TextareaField::new('matchingPattern');
        $isRegex = BooleanField::new('isRegex');
        $tags = AssociationField::new('tags')->setTemplatePath('easy_admin/field_with_tags.html.twig');
        $id = IntegerField::new('id', 'ID');
        $regex = Field::new('regex');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $matchingPattern, $isRegex, $tags];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $matchingPattern, $regex, $tags];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$matchingPattern, $isRegex, $tags];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$matchingPattern, $isRegex, $tags];
        }
    }
}
