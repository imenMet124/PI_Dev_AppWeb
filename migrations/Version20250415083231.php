<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415083231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, candidat_id INT DEFAULT NULL, job_offer_id INT NOT NULL, status VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, submitted_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', cv_snapshot_path VARCHAR(255) DEFAULT NULL, INDEX IDX_A45BDDC18D0EB82 (candidat_id), INDEX IDX_A45BDDC13481D195 (job_offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC18D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC13481D195 FOREIGN KEY (job_offer_id) REFERENCES job_offer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC18D0EB82');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC13481D195');
        $this->addSql('DROP TABLE application');
    }
}
