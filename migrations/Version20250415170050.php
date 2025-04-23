<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415170050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_photo (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, photo_path VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F6757F40A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_photo ADD CONSTRAINT FK_F6757F40A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE userphoto DROP FOREIGN KEY userphoto_ibfk_1');
        $this->addSql('DROP TABLE userphoto');
        $this->addSql('ALTER TABLE department MODIFY iyedIdDep INT NOT NULL');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY department_ibfk_1');
        $this->addSql('DROP INDEX idx_manager_id ON department');
        $this->addSql('DROP INDEX `primary` ON department');
        $this->addSql('ALTER TABLE department ADD description VARCHAR(1000) DEFAULT NULL, DROP iyedDescriptionDep, CHANGE iyedIdDep id INT AUTO_INCREMENT NOT NULL, CHANGE iyedManagerId manager_id INT DEFAULT NULL, CHANGE iyedNomDep name VARCHAR(255) NOT NULL, CHANGE iyedLocationDep location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD1DE18A783E3463 ON department (manager_id)');
        $this->addSql('ALTER TABLE department ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user MODIFY iyedIdUser INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY user_ibfk_1');
        $this->addSql('DROP INDEX iyedIdDepUser ON user');
        $this->addSql('DROP INDEX iyedEmailUser ON user');
        $this->addSql('DROP INDEX `primary` ON user');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(255) NOT NULL, ADD email VARCHAR(180) NOT NULL, ADD phone VARCHAR(20) DEFAULT NULL, ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD password VARCHAR(255) NOT NULL, ADD position VARCHAR(255) NOT NULL, ADD salary DOUBLE PRECISION NOT NULL, ADD status VARCHAR(255) NOT NULL, DROP iyedNomUser, DROP iyedEmailUser, DROP iyedPhoneUser, DROP iyedPasswordUser, DROP iyedRoleUser, DROP iyedPositionUser, DROP iyedSalaireUser, DROP iyedStatutUser, CHANGE iyedIdUser id INT AUTO_INCREMENT NOT NULL, CHANGE iyedIdDepUser department_id INT DEFAULT NULL, CHANGE iyedDateEmbaucheUser hire_date DATE NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE INDEX IDX_8D93D649AE80F5DF ON user (department_id)');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE userphoto (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, photo_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX user_id (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE userphoto ADD CONSTRAINT userphoto_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (iyedIdUser) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_photo DROP FOREIGN KEY FK_F6757F40A76ED395');
        $this->addSql('DROP TABLE user_photo');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE department MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A783E3463');
        $this->addSql('DROP INDEX UNIQ_CD1DE18A783E3463 ON department');
        $this->addSql('DROP INDEX `PRIMARY` ON department');
        $this->addSql('ALTER TABLE department ADD iyedDescriptionDep TEXT DEFAULT NULL, DROP description, CHANGE id iyedIdDep INT AUTO_INCREMENT NOT NULL, CHANGE name iyedNomDep VARCHAR(255) NOT NULL, CHANGE location iyedLocationDep VARCHAR(255) DEFAULT NULL, CHANGE manager_id iyedManagerId INT DEFAULT NULL');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT department_ibfk_1 FOREIGN KEY (iyedManagerId) REFERENCES user (iyedIdUser) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX idx_manager_id ON department (iyedManagerId)');
        $this->addSql('ALTER TABLE department ADD PRIMARY KEY (iyedIdDep)');
        $this->addSql('ALTER TABLE user MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AE80F5DF');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('DROP INDEX IDX_8D93D649AE80F5DF ON user');
        $this->addSql('DROP INDEX `PRIMARY` ON user');
        $this->addSql('ALTER TABLE user ADD iyedNomUser VARCHAR(255) NOT NULL, ADD iyedEmailUser VARCHAR(255) NOT NULL, ADD iyedPhoneUser VARCHAR(20) NOT NULL, ADD iyedPasswordUser VARCHAR(255) NOT NULL, ADD iyedRoleUser VARCHAR(255) NOT NULL, ADD iyedPositionUser VARCHAR(255) NOT NULL, ADD iyedSalaireUser NUMERIC(10, 2) NOT NULL, ADD iyedStatutUser VARCHAR(255) NOT NULL, DROP name, DROP email, DROP phone, DROP roles, DROP password, DROP position, DROP salary, DROP status, CHANGE id iyedIdUser INT AUTO_INCREMENT NOT NULL, CHANGE hire_date iyedDateEmbaucheUser DATE NOT NULL, CHANGE department_id iyedIdDepUser INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT user_ibfk_1 FOREIGN KEY (iyedIdDepUser) REFERENCES department (iyedIdDep) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX iyedIdDepUser ON user (iyedIdDepUser)');
        $this->addSql('CREATE UNIQUE INDEX iyedEmailUser ON user (iyedEmailUser)');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (iyedIdUser)');
    }
}
