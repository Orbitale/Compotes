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

namespace App\Form\Type;

use App\DTO\ImportOperations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportOperationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class)
            ->add('csvColumns', DragDropOrderedListType::class)
            ->add('csvEscapeCharacter', TextType::class, [
                'attr' => [
                    'maxlength' => '1',
                ],
            ])
            ->add('csvDelimiter', TextType::class, [
                'attr' => [
                    'maxlength' => '1',
                ],
            ])
            ->add('csvSeparator', TextType::class, [
                'attr' => [
                    'maxlength' => '1',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImportOperations::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'import_operations';
    }
}
