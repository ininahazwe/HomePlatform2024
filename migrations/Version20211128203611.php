<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211128203611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profile_tag (profile_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_AD2B1EBCCCFA12B8 (profile_id), INDEX IDX_AD2B1EBCBAD26311 (tag_id), PRIMARY KEY(profile_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profile_tag ADD CONSTRAINT FK_AD2B1EBCCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_tag ADD CONSTRAINT FK_AD2B1EBCBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile ADD facebook VARCHAR(255) DEFAULT NULL, ADD instagram VARCHAR(255) DEFAULT NULL, ADD twitter VARCHAR(255) DEFAULT NULL, ADD lindedin VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE profile_tag');
        $this->addSql('ALTER TABLE profile DROP facebook, DROP instagram, DROP twitter, DROP lindedin');
    }
}
