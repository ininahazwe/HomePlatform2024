<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211118194315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, school_id INT DEFAULT NULL, level_id INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_8157AA0FC32A47EE (school_id), INDEX IDX_8157AA0F5FB14BA7 (level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FC32A47EE FOREIGN KEY (school_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F5FB14BA7 FOREIGN KEY (level_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE file ADD profile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610CCFA12B8 ON file (profile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610CCFA12B8');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP INDEX IDX_8C9F3610CCFA12B8 ON file');
        $this->addSql('ALTER TABLE file DROP profile_id');
    }
}
