<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180215104309 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE books ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE books SET updated_at = created_at');
        $this->addSql('ALTER TABLE books ALTER COLUMN updated_at SET NOT NULL');
    }

    public function down(Schema $schema)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE books DROP updated_at');
    }
}
