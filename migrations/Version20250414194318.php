<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414194318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affectation (id_affectation INT AUTO_INCREMENT NOT NULL, id_tache INT DEFAULT NULL, id_emp INT DEFAULT NULL, date_affectation DATE DEFAULT NULL, INDEX IDX_F4DD61D37D026145 (id_tache), INDEX IDX_F4DD61D3AFAF5C55 (id_emp), PRIMARY KEY(id_affectation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet (id_projet INT AUTO_INCREMENT NOT NULL, nom_projet VARCHAR(150) NOT NULL, desc_projet LONGTEXT DEFAULT NULL, statut_projet VARCHAR(50) DEFAULT NULL, date_debut_projet DATE DEFAULT NULL, date_fin_projet DATE DEFAULT NULL, PRIMARY KEY(id_projet)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tache (id_tache INT AUTO_INCREMENT NOT NULL, id_projet INT DEFAULT NULL, titre_tache VARCHAR(150) NOT NULL, desc_tache LONGTEXT DEFAULT NULL, priorite VARCHAR(50) DEFAULT NULL, statut_tache VARCHAR(50) DEFAULT NULL, deadline DATE DEFAULT NULL, progression DOUBLE PRECISION DEFAULT NULL, INDEX IDX_9387207576222944 (id_projet), PRIMARY KEY(id_tache)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (iyed_id_user INT AUTO_INCREMENT NOT NULL, iyed_nom_user VARCHAR(255) NOT NULL, iyed_email_user VARCHAR(255) NOT NULL, iyed_phone_user VARCHAR(20) NOT NULL, iyed_password_user VARCHAR(255) NOT NULL, iyed_role_user VARCHAR(255) NOT NULL, iyed_position_user VARCHAR(255) NOT NULL, iyed_salaire_user NUMERIC(10, 2) NOT NULL, iyed_date_embauche_user DATE NOT NULL, iyed_statut_user VARCHAR(255) NOT NULL, iyed_id_dep_user INT DEFAULT NULL, PRIMARY KEY(iyed_id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D37D026145 FOREIGN KEY (id_tache) REFERENCES tache (id_tache)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3AFAF5C55 FOREIGN KEY (id_emp) REFERENCES user (iyed_id_user)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_9387207576222944 FOREIGN KEY (id_projet) REFERENCES projet (id_projet)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D37D026145');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3AFAF5C55');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_9387207576222944');
        $this->addSql('DROP TABLE affectation');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
