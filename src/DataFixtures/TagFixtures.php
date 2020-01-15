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

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Orbitale\Component\DoctrineTools\AbstractFixture;

class TagFixtures extends AbstractFixture implements ORMFixtureInterface
{
    protected function getEntityClass(): string
    {
        return Tag::class;
    }

    protected function getMethodNameForReference(): string
    {
        return 'getName';
    }

    protected function getReferencePrefix(): ?string
    {
        return 'tag-';
    }

    protected function getObjects(): array
    {
        $getParentClosure = function ($parent) {
            return function () use ($parent) {
                return $this->getReference('tag-'.$parent);
            };
        };

        return [
            // Earnings
            ['name' => $earningsParent = 'Recettes'],
            ['name' => 'Recette-a-categoriser', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Aides-et-allocations', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Autres-revenus', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Cheque-recu', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Dividendes', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Deblocage-emprunt', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Depôt-argent', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Interets', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Loyers', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Pensions', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Remboursement', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Revenus-de-placement', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Salaires-et-revenus-activite', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Virement-interne-reçu', 'parent' => $getParentClosure($earningsParent)],
            ['name' => 'Virement-recu', 'parent' => $getParentClosure($earningsParent)],

            // Expenses
            ['name' => $expensesParent = 'Depenses'],
            ['name' => $parent = 'A-categoriser', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Autres-depenses-a-categoriser', 'parent' => $getParentClosure($parent)],
            ['name' => 'Cheque-emis', 'parent' => $getParentClosure($parent)],
            ['name' => 'Retrait-especes', 'parent' => $getParentClosure($parent)],
            ['name' => 'Virement-interne', 'parent' => $getParentClosure($parent)],
            ['name' => 'Virement-emis', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Abonnements-et-telephonie', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Abonnements-autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Internet-tv', 'parent' => $getParentClosure($parent)],
            ['name' => 'Telephone', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Autres-depenses', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Avocats-notaires-comptabilite', 'parent' => $getParentClosure($parent)],
            ['name' => 'Divers', 'parent' => $getParentClosure($parent)],
            ['name' => 'Dons-caritatifs', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais-professionnels', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Banque', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Banque-autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Epargne', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais-bancaires', 'parent' => $getParentClosure($parent)],
            ['name' => 'Prelevement-carte-debit-differe', 'parent' => $getParentClosure($parent)],
            ['name' => 'Remboursement-emprunt', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Enfants', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Activites-enfants', 'parent' => $getParentClosure($parent)],
            ['name' => 'Argent-de-poche', 'parent' => $getParentClosure($parent)],
            ['name' => 'Creche-baby-sitter', 'parent' => $getParentClosure($parent)],
            ['name' => 'Enfants-autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Pension-alimentaire', 'parent' => $getParentClosure($parent)],
            ['name' => 'Scolarite-etudes', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Impôts-et-taxes', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Amendes', 'parent' => $getParentClosure($parent)],
            ['name' => 'Contributions-sociales-(csg-crds)', 'parent' => $getParentClosure($parent)],
            ['name' => 'Impôt-sur-la-fortune', 'parent' => $getParentClosure($parent)],
            ['name' => 'Impôt-sur-le-revenu', 'parent' => $getParentClosure($parent)],
            ['name' => 'Impôts-et-taxes-autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Taxe-habitation', 'parent' => $getParentClosure($parent)],
            ['name' => 'Taxe-fonciere', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Logement', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Assurance-habitation', 'parent' => $getParentClosure($parent)],
            ['name' => 'Autres-charges', 'parent' => $getParentClosure($parent)],
            ['name' => 'Bricolage-et-jardinage', 'parent' => $getParentClosure($parent)],
            ['name' => 'Chauffage', 'parent' => $getParentClosure($parent)],
            ['name' => 'Eau', 'parent' => $getParentClosure($parent)],
            ['name' => 'Electricite-gaz', 'parent' => $getParentClosure($parent)],
            ['name' => 'Logement-autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Loyer', 'parent' => $getParentClosure($parent)],
            ['name' => 'Mobilier-electromenager-deco-', 'parent' => $getParentClosure($parent)],
            ['name' => 'Pret-immobilier', 'parent' => $getParentClosure($parent)],
            ['name' => 'Travaux-reparation-entretien', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Loisirs-et-sorties', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Divertissements-sorties-culturelles', 'parent' => $getParentClosure($parent)],
            ['name' => 'Loisirs-et-sorties-autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Musique-livres-films', 'parent' => $getParentClosure($parent)],
            ['name' => 'Restaurants-bars', 'parent' => $getParentClosure($parent)],
            ['name' => 'Sport', 'parent' => $getParentClosure($parent)],
            ['name' => 'Voyages-vacances', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Sante', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Mutuelle', 'parent' => $getParentClosure($parent)],
            ['name' => 'Medecins', 'parent' => $getParentClosure($parent)],
            ['name' => 'Opticien', 'parent' => $getParentClosure($parent)],
            ['name' => 'Pharmacie', 'parent' => $getParentClosure($parent)],
            ['name' => 'Sante-autres', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Transports-et-vehicules', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Assurance-vehicule', 'parent' => $getParentClosure($parent)],
            ['name' => 'Billet-avion, billet de train', 'parent' => $getParentClosure($parent)],
            ['name' => 'Carburant', 'parent' => $getParentClosure($parent)],
            ['name' => 'Credit-auto', 'parent' => $getParentClosure($parent)],
            ['name' => 'Entretien-vehicule', 'parent' => $getParentClosure($parent)],
            ['name' => 'Location-de-vehicule', 'parent' => $getParentClosure($parent)],
            ['name' => 'Peage', 'parent' => $getParentClosure($parent)],
            ['name' => 'Stationnement', 'parent' => $getParentClosure($parent)],
            ['name' => 'Taxi-vtc', 'parent' => $getParentClosure($parent)],
            ['name' => 'Transports-en-commun', 'parent' => $getParentClosure($parent)],
            ['name' => 'Transports-et-vehicules-autres', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Vie-quotidienne', 'parent' => $getParentClosure($expensesParent)],
            ['name' => 'Achat-multimedia-high-tech', 'parent' => $getParentClosure($parent)],
            ['name' => 'Achats-shopping', 'parent' => $getParentClosure($parent)],
            ['name' => 'Aide-a-domicile', 'parent' => $getParentClosure($parent)],
            ['name' => 'Alimentation-supermarche', 'parent' => $getParentClosure($parent)],
            ['name' => 'Cadeaux', 'parent' => $getParentClosure($parent)],
            ['name' => 'Coiffeur-cosmetique-soins', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais-animaux', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais-postaux', 'parent' => $getParentClosure($parent)],
            ['name' => 'Habillement', 'parent' => $getParentClosure($parent)],
            ['name' => 'Tabac-presse', 'parent' => $getParentClosure($parent)],
            ['name' => 'Vie-quotidienne-autres', 'parent' => $getParentClosure($parent)],
        ];
    }
}
