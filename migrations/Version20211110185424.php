<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211110185424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE edition (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD edition_avatar_id INT DEFAULT NULL, ADD edition_illustration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36109D778967 FOREIGN KEY (edition_avatar_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610F602349B FOREIGN KEY (edition_illustration_id) REFERENCES edition (id)');
        $this->addSql('CREATE INDEX IDX_8C9F36109D778967 ON file (edition_avatar_id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610F602349B ON file (edition_illustration_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36109D778967');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610F602349B');
        $this->addSql('DROP TABLE edition');
        $this->addSql('DROP INDEX IDX_8C9F36109D778967 ON file');
        $this->addSql('DROP INDEX IDX_8C9F3610F602349B ON file');
        $this->addSql('ALTER TABLE file DROP edition_avatar_id, DROP edition_illustration_id');
    }
}
