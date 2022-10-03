<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221003082006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hero DROP power');
        $this->addSql('ALTER TABLE power ADD heroes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE power ADD CONSTRAINT FK_AB8A01A0AAB40E2D FOREIGN KEY (heroes_id) REFERENCES hero (id)');
        $this->addSql('CREATE INDEX IDX_AB8A01A0AAB40E2D ON power (heroes_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hero ADD power VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE power DROP FOREIGN KEY FK_AB8A01A0AAB40E2D');
        $this->addSql('DROP INDEX IDX_AB8A01A0AAB40E2D ON power');
        $this->addSql('ALTER TABLE power DROP heroes_id');
    }
}
