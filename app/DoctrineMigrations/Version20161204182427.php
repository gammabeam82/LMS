<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161204182427 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE genres_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE genres (id INT NOT NULL, user_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8EBE516A76ED395 ON genres (user_id)');
        $this->addSql('ALTER TABLE genres ADD CONSTRAINT FK_A8EBE516A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE books ADD genre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A924296D31F FOREIGN KEY (genre_id) REFERENCES genres (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4A1B2A924296D31F ON books (genre_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE books DROP CONSTRAINT FK_4A1B2A924296D31F');
        $this->addSql('DROP SEQUENCE genres_id_seq CASCADE');
        $this->addSql('DROP TABLE genres');
        $this->addSql('DROP INDEX IDX_4A1B2A924296D31F');
        $this->addSql('ALTER TABLE books DROP genre_id');
    }
}
