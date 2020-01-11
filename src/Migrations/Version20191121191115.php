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

final class Version20191121191115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Tag entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            <<<'SQL'
            CREATE TABLE operation_tag
            (
               operation_id INT NOT NULL,
               tag_id       INT NOT NULL,
               INDEX idx_1ca8a1bf44ac3583 (operation_id),
               INDEX idx_1ca8a1bfbad26311 (tag_id),
               PRIMARY KEY(operation_id, tag_id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            engine = innodb
            SQL
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE tags
            (
               id   INT auto_increment NOT NULL,
               name VARCHAR(255) NOT NULL,
               PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            engine = innodb
            SQL
        );

        $this->addSql('ALTER TABLE operation_tag ADD CONSTRAINT FK_1CA8A1BF44AC3583 FOREIGN KEY (operation_id) REFERENCES operations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation_tag ADD CONSTRAINT FK_1CA8A1BFBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operation_tag DROP FOREIGN KEY FK_1CA8A1BFBAD26311');
        $this->addSql('DROP TABLE operation_tag');
        $this->addSql('DROP TABLE tags');
    }
}
