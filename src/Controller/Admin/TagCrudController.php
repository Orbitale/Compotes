<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\DTO\AdminTagDTO;
use App\Form\DTO\EasyAdminDTOInterface;
use App\Form\DTO\TranslatableDTOInterface;
use App\Form\Type\TranslatableStringType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FormFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\FormInterface;

class TagCrudController extends AbstractCrudController
{
    use BaseDTOControllerTrait;

    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'name'])
            ->setPaginatorPageSize(100)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $parent = AssociationField::new('parent');
        $id = IntegerField::new('id', 'ID');
        $name = TextField::new('name');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $parent];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $parent];
        }
        if (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return [
                Field::new('translatedNames')
                    ->setTemplatePath('')//FIXME
                    ->setFormType(TranslatableStringType::class)
                ,
                $parent,
            ];
        }
    }


    protected function getDTOClass(): string
    {
        return AdminTagDTO::class;
    }

    /**
     * @param AdminTagDTO $dto
     */
    protected function createEntityFromDTO(EasyAdminDTOInterface $dto): object
    {
        return Tag::fromAdmin($dto);
    }

    /**
     * @param Tag         $entity
     * @param AdminTagDTO $dto
     */
    protected function updateEntityWithDTO(object $entity, EasyAdminDTOInterface $dto): object
    {
        $entity->updateFromAdmin($dto);

        $this->translateEntityFieldsFromDTO($entity, $dto);

        return $entity;
    }
}
