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

namespace App\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;

class TranslatableStringListener implements EventSubscriberInterface
{
    private array $locales;

    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    public function addTranslatableFields(PostSetDataEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!$data) {
            foreach ($this->locales as $locale) {
                $data[$locale] = '';
            }
            $event->setData($data);
        }

        foreach ($data as $locale => $translatedString) {
            switch ($locale) {
                case 'fr': $label = '🇫🇷';

break;
                case 'en': $label = '🇬🇧';

break;
                default: $label = $locale;

break;
            }

            $form->add($locale, TextType::class, [
                'label' => $label,
                'data' => $translatedString,
            ]);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SET_DATA => 'addTranslatableFields',
        ];
    }
}
