<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201182056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file ADD project_avatar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361097047EC7 FOREIGN KEY (project_avatar_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_8C9F361097047EC7 ON file (project_avatar_id)');
        $this->addSql('ALTER TABLE project ADD is_published TINYINT(1) DEFAULT NULL, ADD intro LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361097047EC7');
        $this->addSql('DROP INDEX IDX_8C9F361097047EC7 ON file');
        $this->addSql('ALTER TABLE file DROP project_avatar_id');
        $this->addSql('ALTER TABLE project DROP is_published, DROP intro');
    }
}
