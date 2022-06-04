<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115210537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about DROP slug, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361037232B9F');
        $this->addSql('DROP INDEX IDX_8C9F361037232B9F ON file');
        $this->addSql('ALTER TABLE file CHANGE about_photos_id about_images_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610E06DC218 FOREIGN KEY (about_images_id) REFERENCES about (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610E06DC218 ON file (about_images_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE about ADD slug VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610E06DC218');
        $this->addSql('DROP INDEX IDX_8C9F3610E06DC218 ON file');
        $this->addSql('ALTER TABLE file CHANGE about_images_id about_photos_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361037232B9F FOREIGN KEY (about_photos_id) REFERENCES about (id)');
        $this->addSql('CREATE INDEX IDX_8C9F361037232B9F ON file (about_photos_id)');
    }
}
