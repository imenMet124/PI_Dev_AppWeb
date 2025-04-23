<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240415000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial schema for users, departments and photos';
    }

    public function up(Schema $schema): void
    {
        // Create department table
        $this->addSql('CREATE TABLE department (
            iyedIdDep INT AUTO_INCREMENT NOT NULL,
            iyedNomDep VARCHAR(255) NOT NULL,
            iyedDescriptionDep TEXT DEFAULT NULL,
            iyedLocationDep VARCHAR(255) DEFAULT NULL,
            iyedManagerId INT DEFAULT NULL,
            PRIMARY KEY(iyedIdDep)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create user table
        $this->addSql('CREATE TABLE user (
            iyedIdUser INT AUTO_INCREMENT NOT NULL,
            iyedNomUser VARCHAR(255) NOT NULL,
            iyedEmailUser VARCHAR(255) NOT NULL,
            iyedPhoneUser VARCHAR(20) NOT NULL,
            iyedPasswordUser VARCHAR(255) NOT NULL,
            iyedRoleUser ENUM(\'RESPONSABLE_RH\', \'CHEF_PROJET\', \'EMPLOYE\') NOT NULL,
            iyedPositionUser VARCHAR(255) NOT NULL,
            iyedSalaireUser DECIMAL(10,2) NOT NULL,
            iyedDateEmbaucheUser DATE NOT NULL,
            iyedStatutUser ENUM(\'ACTIVE\', \'INACTIVE\', \'ON_LEAVE\', \'SUSPENDED\', \'TERMINATED\', \'RESIGNED\', \'RETIRED\', \'PROBATION\', \'CONTRACT_ENDED\') NOT NULL,
            iyedIdDepUser INT DEFAULT NULL,
            UNIQUE INDEX UNIQ_8D93D6496B6B5FBA (iyedEmailUser),
            INDEX IDX_8D93D6496B6B5FBA (iyedIdDepUser),
            PRIMARY KEY(iyedIdUser)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create userphoto table
        $this->addSql('CREATE TABLE userphoto (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            photo_path VARCHAR(255) NOT NULL,
            INDEX IDX_4B7E4C4AA76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A6B6B5FBA FOREIGN KEY (iyedManagerId) REFERENCES user (iyedIdUser) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496B6B5FBA FOREIGN KEY (iyedIdDepUser) REFERENCES department (iyedIdDep) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE userphoto ADD CONSTRAINT FK_4B7E4C4AA76ED395 FOREIGN KEY (user_id) REFERENCES user (iyedIdUser) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A6B6B5FBA');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496B6B5FBA');
        $this->addSql('ALTER TABLE userphoto DROP FOREIGN KEY FK_4B7E4C4AA76ED395');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE userphoto');
    }
} 