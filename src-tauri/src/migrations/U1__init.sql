CREATE TABLE `bank_accounts` (
    `id`       integer      NOT NULL PRIMARY KEY AUTOINCREMENT,
    `name`     varchar(255) NOT NULL,
    `slug`     varchar(255) NOT NULL,
    `currency` varchar(255) NOT NULL,
    UNIQUE (`slug`)
);

CREATE TABLE `operation_tag` (
    `operation_id` integer NOT NULL,
    `tag_id`       integer NOT NULL,
    PRIMARY KEY (`operation_id`, `tag_id`),
    CONSTRAINT `FK_1CA8A1BF44AC3583` FOREIGN KEY (`operation_id`) REFERENCES `operations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_1CA8A1BFBAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
);

CREATE TABLE `operations` (
    `id`                  integer      NOT NULL PRIMARY KEY AUTOINCREMENT,
    `operation_date`      datetime     NOT NULL,
    `type`                varchar(255) NOT NULL,
    `type_display`        varchar(255) NOT NULL,
    `details`             longtext     NOT NULL,
    `amount_in_cents`     integer      NOT NULL,
    `hash`                varchar(255) NOT NULL,
    `state`               varchar(255) NOT NULL DEFAULT 'ok',
    `bank_account_id`     integer      NOT NULL,
    `ignored_from_charts` integer      NOT NULL DEFAULT '0',
    CONSTRAINT `FK_2814534812CB990C` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`)
);

CREATE TABLE `tag_rule_tag` (
    `tag_rule_id` integer NOT NULL,
    `tag_id`      integer NOT NULL,
    PRIMARY KEY (`tag_rule_id`, `tag_id`),
    CONSTRAINT `FK_42748BAA467F2EFB` FOREIGN KEY (`tag_rule_id`) REFERENCES `tag_rules` (`id`),
    CONSTRAINT `FK_42748BAABAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`)
);

CREATE TABLE `tag_rules` (
    `id`               integer  NOT NULL PRIMARY KEY AUTOINCREMENT,
    `matching_pattern` longtext NOT NULL,
    `is_regex`         integer  NOT NULL DEFAULT '1'
);

CREATE TABLE `tags` (
    `id`        integer      NOT NULL PRIMARY KEY AUTOINCREMENT,
    `name`      varchar(255) NOT NULL,
    `parent_id` integer DEFAULT NULL,
    UNIQUE (`name`),
    CONSTRAINT `FK_6FBC9426727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `tags` (`id`)
);

INSERT INTO `tags`
VALUES (1, 'Recettes', NULL),
       (2, 'Recette-a-categoriser', 1),
       (3, 'Aides-et-allocations', 1),
       (4, 'Autres-revenus', 1),
       (5, 'Cheque-recu', 1),
       (6, 'Dividendes', 1),
       (7, 'Deblocage-emprunt', 1),
       (8, 'Depot-argent', 1),
       (9, 'Interets', 1),
       (10, 'Loyers', 1),
       (11, 'Pensions', 1),
       (12, 'Remboursement', 1),
       (13, 'Revenus-de-placement', 1),
       (14, 'Salaires-et-revenus-activite', 1),
       (15, 'Virement-interne-reçu', 1),
       (16, 'Virement-recu', 1),
       (17, 'Depenses', NULL),
       (18, 'A-categoriser', 17),
       (19, 'Autres-depenses-a-categoriser', 18),
       (20, 'Cheque-emis', 18),
       (21, 'Retrait-especes', 18),
       (22, 'Virement-interne', 18),
       (23, 'Virement-emis', 18),
       (24, 'Abonnements et telephonie', 17),
       (25, 'Abonnements-autres', 24),
       (26, 'Internet-tv', 24),
       (27, 'Telephone', 24),
       (28, 'Autres depenses', 17),
       (29, 'Avocats-notaires-comptabilite', 28),
       (30, 'Divers', 28),
       (31, 'Dons-caritatifs', 28),
       (32, 'Frais-professionnels', 28),
       (33, 'Banque', 17),
       (34, 'Banque-autres', 33),
       (35, 'Epargne', 33),
       (36, 'Frais-bancaires', 33),
       (37, 'Prelevement-carte-debit-differe', 33),
       (38, 'Remboursement-emprunt', 33),
       (39, 'Enfants', 17),
       (40, 'Activites-enfants', 39),
       (41, 'Argent-de-poche', 39),
       (42, 'Creche-baby-sitter', 39),
       (43, 'Enfants-autres', 39),
       (44, 'Pension-alimentaire', 39),
       (45, 'Scolarite-etudes', 39),
       (46, 'Impots-et-taxes', 17),
       (47, 'Amendes', 46),
       (48, 'Contributions-sociales-csg-crds', 46),
       (49, 'Impot-sur-la-fortune', 46),
       (50, 'Impot-sur-le-revenu', 46),
       (51, 'Impot-et-taxes-autres', 46),
       (52, 'Taxe-habitation', 46),
       (53, 'Taxe-fonciere', 46),
       (54, 'Logement', 17),
       (55, 'Assurance-habitation', 54),
       (56, 'Autres-charges', 54),
       (57, 'Bricolage-et-jardinage', 54),
       (58, 'Chauffage', 54),
       (59, 'Eau', 54),
       (60, 'Electricite-gaz', 54),
       (61, 'Logement-autres', 54),
       (62, 'Loyer', 54),
       (63, 'Mobilier-electromenager-deco', 54),
       (64, 'Pret-immobilier', 54),
       (65, 'Travaux-reparation-entretien', 54),
       (66, 'Loisirs et sorties', 17),
       (67, 'Divertissements-sorties-culturelles', 66),
       (68, 'Loisirs-et-sorties-autres', 66),
       (69, 'Musique-livres-films', 66),
       (70, 'Restaurants-bars', 66),
       (71, 'Sport', 66),
       (72, 'Voyages-vacances', 66),
       (73, 'Sante', 17),
       (74, 'Mutuelle', 73),
       (75, 'Medecins', 73),
       (76, 'Opticien', 73),
       (77, 'Pharmacie', 73),
       (78, 'Sante-autres', 73),
       (79, 'Transports et vehicules', 17),
       (80, 'Assurance-vehicule', 79),
       (81, 'Billet-avion, billet de train', 79),
       (82, 'Carburant', 79),
       (83, 'Credit-auto', 79),
       (84, 'Entretien-vehicule', 79),
       (85, 'Location-de-vehicule', 79),
       (86, 'Peage', 79),
       (87, 'Stationnement', 79),
       (88, 'Taxi-vtc', 79),
       (89, 'Transports-en-commun', 79),
       (90, 'Transports-et-vehicules-autres', 79),
       (91, 'Vie quotidienne', 17),
       (92, 'Achat-multimedia-high-tech', 91),
       (93, 'Achats-shopping', 91),
       (94, 'Aide-a-domicile', 91),
       (95, 'Alimentation-supermarche', 91),
       (96, 'Cadeaux', 91),
       (97, 'Coiffeur-cosmetique-soins', 91),
       (98, 'Frais-animaux', 91),
       (99, 'Frais-postaux', 91),
       (100, 'Habillement', 91),
       (101, 'Tabac-presse', 91),
       (102, 'Vie-quotidienne-autres', 91),
       (104, 'Remboursement-sante', 1),
       (105, 'Assurance-vie', 73),
       (106, 'Pret-conso', 28),
       (107, 'Petits plaisirs', 66)
;

CREATE INDEX "idx_tags_IDX_6FBC9426727ACA70" ON "tags" (`parent_id`);
CREATE INDEX "idx_operations_IDX_2814534812CB990C" ON "operations" (`bank_account_id`);
CREATE INDEX "idx_tag_rule_tag_idx_42748baa467f2efb" ON "tag_rule_tag" (`tag_rule_id`);
CREATE INDEX "idx_tag_rule_tag_idx_42748baabad26311" ON "tag_rule_tag" (`tag_id`);
CREATE INDEX "idx_operation_tag_idx_1ca8a1bf44ac3583" ON "operation_tag" (`operation_id`);
CREATE INDEX "idx_operation_tag_idx_1ca8a1bfbad26311" ON "operation_tag" (`tag_id`);
