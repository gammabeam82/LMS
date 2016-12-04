<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161204113718 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE authors ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE authors ADD CONSTRAINT FK_8E0C2A51A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8E0C2A51A76ED395 ON authors (user_id)');
        $this->addSql('ALTER TABLE books ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A92A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4A1B2A92A76ED395 ON books (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE authors DROP CONSTRAINT FK_8E0C2A51A76ED395');
        $this->addSql('DROP INDEX IDX_8E0C2A51A76ED395');
        $this->addSql('ALTER TABLE authors DROP user_id');
        $this->addSql('ALTER TABLE books DROP CONSTRAINT FK_4A1B2A92A76ED395');
        $this->addSql('DROP INDEX IDX_4A1B2A92A76ED395');
        $this->addSql('ALTER TABLE books DROP user_id');
    }
}
