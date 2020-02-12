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
use App\Model\ImportOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportOperationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class)
            ->add('csvColumns', DragDropOrderedListType::class)
            ->add('csvEscapeCharacter', ChoiceType::class, [
                'choices' => ImportOptions::CSV_ESCAPE_CHARACTERS,
            ])
            ->add('csvDelimiter', ChoiceType::class, [
                'choices' => ImportOptions::CSV_DELIMITERS,
            ])
            ->add('csvSeparator', ChoiceType::class, [
                'choices' => ImportOptions::CSV_SEPARATORS,
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
