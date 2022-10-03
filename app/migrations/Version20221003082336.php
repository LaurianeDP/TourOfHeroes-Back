<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221003082336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hero ADD power_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hero ADD CONSTRAINT FK_51CE6E86AB4FC384 FOREIGN KEY (power_id) REFERENCES power (id)');
        $this->addSql('CREATE INDEX IDX_51CE6E86AB4FC384 ON hero (power_id)');
        $this->addSql('ALTER TABLE power DROP FOREIGN KEY FK_AB8A01A0AAB40E2D');
        $this->addSql('DROP INDEX IDX_AB8A01A0AAB40E2D ON power');
        $this->addSql('ALTER TABLE power DROP heroes_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hero DROP FOREIGN KEY FK_51CE6E86AB4FC384');
        $this->addSql('DROP INDEX IDX_51CE6E86AB4FC384 ON hero');
        $this->addSql('ALTER TABLE hero DROP power_id');
        $this->addSql('ALTER TABLE power ADD heroes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE power ADD CONSTRAINT FK_AB8A01A0AAB40E2D FOREIGN KEY (heroes_id) REFERENCES hero (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AB8A01A0AAB40E2D ON power (heroes_id)');
    }
}
