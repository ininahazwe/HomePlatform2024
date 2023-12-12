<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231119175812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE about (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, intro LONGTEXT DEFAULT NULL, description LONGTEXT DEFAULT NULL, titre2 VARCHAR(255) DEFAULT NULL, description2 LONGTEXT DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, accroche VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE accordion (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, intro LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_497DD634C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dictionnaire (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) DEFAULT NULL, value TINYTEXT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE edition (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, intro LONGTEXT DEFAULT NULL, ordre INT DEFAULT NULL, status TINYINT(1) DEFAULT NULL, year VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, sujet VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, user_id INT DEFAULT NULL, project_id INT DEFAULT NULL, edition_avatar_id INT DEFAULT NULL, edition_illustration_id INT DEFAULT NULL, partenaires_id INT DEFAULT NULL, about_images_id INT DEFAULT NULL, profile_id INT DEFAULT NULL, team_id INT DEFAULT NULL, project_avatar_id INT DEFAULT NULL, messages_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, nom_fichier VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_8C9F3610BCF5E72D (categorie_id), INDEX IDX_8C9F3610A76ED395 (user_id), INDEX IDX_8C9F3610166D1F9C (project_id), INDEX IDX_8C9F36109D778967 (edition_avatar_id), INDEX IDX_8C9F3610F602349B (edition_illustration_id), INDEX IDX_8C9F361038898CF5 (partenaires_id), INDEX IDX_8C9F3610E06DC218 (about_images_id), INDEX IDX_8C9F3610CCFA12B8 (profile_id), INDEX IDX_8C9F3610296CD8AE (team_id), INDEX IDX_8C9F361097047EC7 (project_avatar_id), INDEX IDX_8C9F3610A5905F5A (messages_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_user (group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A4C98D39FE54D947 (group_id), INDEX IDX_A4C98D39A76ED395 (user_id), PRIMARY KEY(group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_DB021E96F624B39D (sender_id), INDEX IDX_DB021E96E92F8F78 (recipient_id), INDEX IDX_DB021E96727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaires (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, status INT DEFAULT NULL, ordre INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, school_id INT DEFAULT NULL, level_id INT DEFAULT NULL, user_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, lindedin VARCHAR(255) DEFAULT NULL, degree VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_8157AA0FC32A47EE (school_id), INDEX IDX_8157AA0F5FB14BA7 (level_id), UNIQUE INDEX UNIQ_8157AA0FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile_tag (profile_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_AD2B1EBCCCFA12B8 (profile_id), INDEX IDX_AD2B1EBCBAD26311 (tag_id), PRIMARY KEY(profile_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, editor_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, statut INT DEFAULT NULL, intro LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_2FB3D0EE6995AC4C (editor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_categorie (project_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_584193E3166D1F9C (project_id), INDEX IDX_584193E3BCF5E72D (categorie_id), PRIMARY KEY(project_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_user (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B4021E51166D1F9C (project_id), INDEX IDX_B4021E51A76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_project (tag_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_1D82FD44BAD26311 (tag_id), INDEX IDX_1D82FD44166D1F9C (project_id), PRIMARY KEY(tag_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, role VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, civilite VARCHAR(255) DEFAULT NULL, is_deleted TINYINT(1) DEFAULT NULL, is_mentor TINYINT(1) DEFAULT NULL, last_connection DATETIME DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634C54C8C93 FOREIGN KEY (type_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36109D778967 FOREIGN KEY (edition_avatar_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610F602349B FOREIGN KEY (edition_illustration_id) REFERENCES edition (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361038898CF5 FOREIGN KEY (partenaires_id) REFERENCES partenaires (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610E06DC218 FOREIGN KEY (about_images_id) REFERENCES about (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361097047EC7 FOREIGN KEY (project_avatar_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A5905F5A FOREIGN KEY (messages_id) REFERENCES messages (id)');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_user ADD CONSTRAINT FK_A4C98D39A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96E92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96727ACA70 FOREIGN KEY (parent_id) REFERENCES messages (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FC32A47EE FOREIGN KEY (school_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F5FB14BA7 FOREIGN KEY (level_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_tag ADD CONSTRAINT FK_AD2B1EBCCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_tag ADD CONSTRAINT FK_AD2B1EBCBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE6995AC4C FOREIGN KEY (editor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project_categorie ADD CONSTRAINT FK_584193E3166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_categorie ADD CONSTRAINT FK_584193E3BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_user ADD CONSTRAINT FK_B4021E51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tag_project ADD CONSTRAINT FK_1D82FD44BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_project ADD CONSTRAINT FK_1D82FD44166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634C54C8C93');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610BCF5E72D');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A76ED395');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610166D1F9C');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36109D778967');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610F602349B');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361038898CF5');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610E06DC218');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610CCFA12B8');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610296CD8AE');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361097047EC7');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A5905F5A');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39FE54D947');
        $this->addSql('ALTER TABLE group_user DROP FOREIGN KEY FK_A4C98D39A76ED395');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96F624B39D');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96E92F8F78');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96727ACA70');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FC32A47EE');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0F5FB14BA7');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0FA76ED395');
        $this->addSql('ALTER TABLE profile_tag DROP FOREIGN KEY FK_AD2B1EBCCCFA12B8');
        $this->addSql('ALTER TABLE profile_tag DROP FOREIGN KEY FK_AD2B1EBCBAD26311');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE6995AC4C');
        $this->addSql('ALTER TABLE project_categorie DROP FOREIGN KEY FK_584193E3166D1F9C');
        $this->addSql('ALTER TABLE project_categorie DROP FOREIGN KEY FK_584193E3BCF5E72D');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51166D1F9C');
        $this->addSql('ALTER TABLE project_user DROP FOREIGN KEY FK_B4021E51A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE tag_project DROP FOREIGN KEY FK_1D82FD44BAD26311');
        $this->addSql('ALTER TABLE tag_project DROP FOREIGN KEY FK_1D82FD44166D1F9C');
        $this->addSql('DROP TABLE about');
        $this->addSql('DROP TABLE accordion');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE dictionnaire');
        $this->addSql('DROP TABLE edition');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE group_user');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE partenaires');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE profile_tag');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_categorie');
        $this->addSql('DROP TABLE project_user');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_project');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE user');
    }
}
