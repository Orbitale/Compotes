<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200111225430 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add "parent" field to Tag entity.';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tags ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426727ACA70 FOREIGN KEY (parent_id) REFERENCES tags (id)');
        $this->addSql('CREATE INDEX IDX_6FBC9426727ACA70 ON tags (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426727ACA70');
        $this->addSql('DROP INDEX IDX_6FBC9426727ACA70 ON tags');
        $this->addSql('ALTER TABLE tags DROP parent_id');
    }
}
