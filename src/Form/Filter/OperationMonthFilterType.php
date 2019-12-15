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

namespace App\Form\Filter;

use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationMonthFilterType extends FilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'empty_value' => \date('m'),
            'choices' => [
                'This month' => 'this_month',
                'January' => '01',
                'February' => '02',
                'March' => '03',
                'April' => '04',
                'May' => '05',
                'June' => '06',
                'July' => '07',
                'August' => '08',
                'September' => '09',
                'October' => '10',
                'November' => '11',
                'December' => '12',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata): void
    {
        $data = $form->getData();
        if ('this_month' === $data) {
            $baseDate = new DateTimeImmutable('now');
        } else {
            // A number
            $baseDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', \sprintf(
                '%s-%s-1 00:00:00 +000',
                \date('Y'),
                $data
            ));
        }

        $firstDay = DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', \sprintf(
            '%s-%s-1 00:00:00 +000',
            $baseDate->format('Y'),
            $baseDate->format('m')
        ));

        $lastDay = DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', \sprintf(
            '%s-%s-%s 23:59:59 +000',
            $baseDate->format('Y'),
            $baseDate->format('m'),
            $baseDate->format('t')
        ));

        $queryBuilder
            ->where('entity.operationDate >= :first_day')
            ->andWhere('entity.operationDate <= :last_day')
            ->setParameter('first_day', $firstDay)
            ->setParameter('last_day', $lastDay)
        ;
    }
}
