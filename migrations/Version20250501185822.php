<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501185822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ensures proper entity relationships for Question and Option entities';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Add foreign key constraints if they don't exist
        $this->addSql('
            CREATE INDEX IF NOT EXISTS IDX_B6F7494E853CD175 ON question (quiz_id);
        ');

        $this->addSql('
            CREATE INDEX IF NOT EXISTS IDX_5A8600B01E27F6BF ON `option` (question_id);
        ');

        // Ensure all options have valid questions
        $this->addSql('
            UPDATE `option` o
            JOIN question q ON o.question_id = q.id
            SET o.question_id = q.id
            WHERE q.deleted_at IS NULL
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

        // Drop the indexes if they exist
        $this->addSql('
            DROP INDEX IF EXISTS IDX_B6F7494E853CD175 ON question;
        ');

        $this->addSql('
            DROP INDEX IF EXISTS IDX_5A8600B01E27F6BF ON `option`;
        ');
    }
}
