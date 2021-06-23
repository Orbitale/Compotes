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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200322211857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            <<<'SQL'
            CREATE TABLE ext_translations (
                id INT AUTO_INCREMENT NOT NULL,
                locale VARCHAR(8) NOT NULL,
                object_class VARCHAR(255) NOT NULL,
                field VARCHAR(32) NOT NULL,
                foreign_key VARCHAR(64) NOT NULL,
                content LONGTEXT DEFAULT NULL,
                INDEX translations_lookup_idx (
                    locale, object_class, foreign_key
                ),
                UNIQUE INDEX lookup_unique_idx (
                    locale, object_class, field, foreign_key
                ),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB
            ROW_FORMAT = DYNAMIC
        SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ext_translations');
    }
}
