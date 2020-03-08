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

final class Version20200308133351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Operation::$bankAccount relation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations ADD bank_account_id INT DEFAULT 1');
        $this->addSql('CREATE INDEX IDX_2814534812CB990C ON operations (bank_account_id)');

        $this->addSql('ALTER TABLE operations CHANGE bank_account_id bank_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE operations ADD CONSTRAINT FK_2814534812CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_accounts (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations DROP FOREIGN KEY FK_2814534812CB990C');
        $this->addSql('DROP INDEX IDX_2814534812CB990C ON operations');
        $this->addSql('ALTER TABLE operations DROP bank_account_id');
    }
}
