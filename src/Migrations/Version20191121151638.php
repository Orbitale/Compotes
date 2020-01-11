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

final class Version20191121151638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Operation entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            <<<'SQL'
            CREATE TABLE operations
            (
               id              INT auto_increment NOT NULL,
               operation_date  DATETIME NOT NULL comment '(DC2Type:datetime_immutable)',
               type            VARCHAR(255) NOT NULL,
               type_display    VARCHAR(255) NOT NULL,
               details         LONGTEXT NOT NULL,
               amount_in_cents INT NOT NULL,
               PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            engine = innodb;
            SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE operations');
    }
}
