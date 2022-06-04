<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211110200323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD about_photos_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361037232B9F FOREIGN KEY (about_photos_id) REFERENCES about (id)');
        $this->addSql('CREATE INDEX IDX_8C9F361037232B9F ON file (about_photos_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about DROP description');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361037232B9F');
        $this->addSql('DROP INDEX IDX_8C9F361037232B9F ON file');
        $this->addSql('ALTER TABLE file DROP about_photos_id');
    }
}
