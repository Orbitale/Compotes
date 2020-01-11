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
            ['name' => $parent = 'A catégoriser'],
            ['name' => 'Autres dépenses à catégoriser', 'parent' => $getParentClosure($parent)],
            ['name' => 'Chèque émis', 'parent' => $getParentClosure($parent)],
            ['name' => 'Retrait d\'espèces', 'parent' => $getParentClosure($parent)],
            ['name' => 'Virement interne', 'parent' => $getParentClosure($parent)],
            ['name' => 'Virement émis', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Abonnements et téléphonie'],
            ['name' => 'Abonnements  autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Internet, tv', 'parent' => $getParentClosure($parent)],
            ['name' => 'Téléphone', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Autres dépenses'],
            ['name' => 'Avocats, notaires, comptabilité', 'parent' => $getParentClosure($parent)],
            ['name' => 'Divers', 'parent' => $getParentClosure($parent)],
            ['name' => 'Dons caritatifs', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais professionnels', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Banque'],
            ['name' => 'Banque  autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Epargne', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais bancaires', 'parent' => $getParentClosure($parent)],
            ['name' => 'Prélèvement carte débit différé', 'parent' => $getParentClosure($parent)],
            ['name' => 'Remboursement emprunt', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Enfants'],
            ['name' => 'Activités enfants', 'parent' => $getParentClosure($parent)],
            ['name' => 'Argent de poche', 'parent' => $getParentClosure($parent)],
            ['name' => 'Crèche, baby sitter', 'parent' => $getParentClosure($parent)],
            ['name' => 'Enfants  autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Pension alimentaire', 'parent' => $getParentClosure($parent)],
            ['name' => 'Scolarité, études', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Impôts et taxes'],
            ['name' => 'Amendes', 'parent' => $getParentClosure($parent)],
            ['name' => 'Contributions sociales (csg / crds)', 'parent' => $getParentClosure($parent)],
            ['name' => 'Impôt sur la fortune', 'parent' => $getParentClosure($parent)],
            ['name' => 'Impôt sur le revenu', 'parent' => $getParentClosure($parent)],
            ['name' => 'Impôts et taxes  autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Taxe d\'habitation', 'parent' => $getParentClosure($parent)],
            ['name' => 'Taxe foncière', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Logement'],
            ['name' => 'Assurance habitation', 'parent' => $getParentClosure($parent)],
            ['name' => 'Autres charges', 'parent' => $getParentClosure($parent)],
            ['name' => 'Bricolage et jardinage', 'parent' => $getParentClosure($parent)],
            ['name' => 'Chauffage', 'parent' => $getParentClosure($parent)],
            ['name' => 'Eau', 'parent' => $getParentClosure($parent)],
            ['name' => 'Electricité, gaz', 'parent' => $getParentClosure($parent)],
            ['name' => 'Logement  autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Loyer', 'parent' => $getParentClosure($parent)],
            ['name' => 'Mobilier, électroménager, déco .', 'parent' => $getParentClosure($parent)],
            ['name' => 'Prêt immobilier', 'parent' => $getParentClosure($parent)],
            ['name' => 'Travaux, réparation, entretien', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Loisirs et sorties'],
            ['name' => 'Divertissements, sorties culturelles', 'parent' => $getParentClosure($parent)],
            ['name' => 'Loisirs et sorties  autres', 'parent' => $getParentClosure($parent)],
            ['name' => 'Musique, livres, films', 'parent' => $getParentClosure($parent)],
            ['name' => 'Restaurants, bars', 'parent' => $getParentClosure($parent)],
            ['name' => 'Sport', 'parent' => $getParentClosure($parent)],
            ['name' => 'Voyages, vacances', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Santé'],
            ['name' => 'Mutuelle', 'parent' => $getParentClosure($parent)],
            ['name' => 'Médecins', 'parent' => $getParentClosure($parent)],
            ['name' => 'Opticien', 'parent' => $getParentClosure($parent)],
            ['name' => 'Pharmacie', 'parent' => $getParentClosure($parent)],
            ['name' => 'Santé  autres', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Transports et véhicules'],
            ['name' => 'Assurance véhicule', 'parent' => $getParentClosure($parent)],
            ['name' => 'Billet d\'avion, billet de train', 'parent' => $getParentClosure($parent)],
            ['name' => 'Carburant', 'parent' => $getParentClosure($parent)],
            ['name' => 'Crédit auto', 'parent' => $getParentClosure($parent)],
            ['name' => 'Entretien véhicule', 'parent' => $getParentClosure($parent)],
            ['name' => 'Location de véhicule', 'parent' => $getParentClosure($parent)],
            ['name' => 'Péage', 'parent' => $getParentClosure($parent)],
            ['name' => 'Stationnement', 'parent' => $getParentClosure($parent)],
            ['name' => 'Taxi, vtc', 'parent' => $getParentClosure($parent)],
            ['name' => 'Transports en commun', 'parent' => $getParentClosure($parent)],
            ['name' => 'Transports et véhicules  autres', 'parent' => $getParentClosure($parent)],

            ['name' => $parent = 'Vie quotidienne'],
            ['name' => 'Achat multimedia  high tech', 'parent' => $getParentClosure($parent)],
            ['name' => 'Achats, shopping', 'parent' => $getParentClosure($parent)],
            ['name' => 'Aide à domicile', 'parent' => $getParentClosure($parent)],
            ['name' => 'Alimentation, supermarché', 'parent' => $getParentClosure($parent)],
            ['name' => 'Cadeaux', 'parent' => $getParentClosure($parent)],
            ['name' => 'Coiffeur, cosmétique, soins', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais animaux', 'parent' => $getParentClosure($parent)],
            ['name' => 'Frais postaux', 'parent' => $getParentClosure($parent)],
            ['name' => 'Habillement', 'parent' => $getParentClosure($parent)],
            ['name' => 'Tabac, presse', 'parent' => $getParentClosure($parent)],
            ['name' => 'Vie quotidienne  autres', 'parent' => $getParentClosure($parent)],
        ];
    }
}
