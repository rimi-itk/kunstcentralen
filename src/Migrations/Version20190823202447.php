<?php

declare(strict_types=1);

/*
 * This file is part of itk-dev/kunstcentralen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190823202447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE work_of_art ADD images JSON NOT NULL, CHANGE artist_id artist_id INT DEFAULT NULL, CHANGE location_id location_id INT DEFAULT NULL, CHANGE query query VARCHAR(2040) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE work_of_art DROP images, CHANGE artist_id artist_id INT DEFAULT NULL, CHANGE location_id location_id INT DEFAULT NULL, CHANGE query query VARCHAR(2040) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
