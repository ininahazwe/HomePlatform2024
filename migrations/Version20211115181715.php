<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115181715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36109393F8FE');
        $this->addSql('DROP INDEX IDX_8C9F36109393F8FE ON file');
        $this->addSql('ALTER TABLE file CHANGE partner_id partner_logo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36103E657757 FOREIGN KEY (partner_logo_id) REFERENCES partner (id)');
        $this->addSql('CREATE INDEX IDX_8C9F36103E657757 ON file (partner_logo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36103E657757');
        $this->addSql('DROP INDEX IDX_8C9F36103E657757 ON file');
        $this->addSql('ALTER TABLE file CHANGE partner_logo_id partner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36109393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('CREATE INDEX IDX_8C9F36109393F8FE ON file (partner_id)');
    }
}
