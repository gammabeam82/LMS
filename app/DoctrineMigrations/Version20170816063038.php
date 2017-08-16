<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170816063038 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE authors DROP CONSTRAINT fk_8e0c2a51a76ed395');
        $this->addSql('DROP INDEX idx_8e0c2a51a76ed395');
        $this->addSql('ALTER TABLE authors DROP user_id');
        $this->addSql('ALTER TABLE genres DROP CONSTRAINT fk_a8ebe516a76ed395');
        $this->addSql('DROP INDEX idx_a8ebe516a76ed395');
        $this->addSql('ALTER TABLE genres DROP user_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE authors ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE authors ADD CONSTRAINT fk_8e0c2a51a76ed395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8e0c2a51a76ed395 ON authors (user_id)');
        $this->addSql('ALTER TABLE genres ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE genres ADD CONSTRAINT fk_a8ebe516a76ed395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a8ebe516a76ed395 ON genres (user_id)');
    }
}
