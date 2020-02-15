<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Operation;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200215101535 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds a "hash" to all operations, to uniquely identify them.';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations ADD hash VARCHAR(255) DEFAULT NULL');

        $operations = $this->connection->query(<<<SQL
            SELECT id, operation_date, type, type_display, details, amount_in_cents
            FROM operations
        SQL);
        foreach ($operations as $operation) {
            $this->addSql(<<<SQL
                UPDATE operations
                SET hash = :hash
                WHERE id = :id
            SQL, [
                'id' => $operation['id'],
                'hash' => Operation::computeHash(
                    $operation['type'],
                    $operation['type_display'],
                    $operation['details'],
                    new \DateTimeImmutable($operation['operation_date']),
                    (int) $operation['amount_in_cents']
                )
            ]);
        }

        $this->addSql('ALTER TABLE operations CHANGE hash hash VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations DROP hash');
    }
}
