<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221004152252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hero (id INT AUTO_INCREMENT NOT NULL, power_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, alter_ego VARCHAR(100) DEFAULT NULL, INDEX IDX_51CE6E86AB4FC384 (power_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE power (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hero ADD CONSTRAINT FK_51CE6E86AB4FC384 FOREIGN KEY (power_id) REFERENCES power (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hero DROP FOREIGN KEY FK_51CE6E86AB4FC384');
        $this->addSql('DROP TABLE hero');
        $this->addSql('DROP TABLE power');
    }
}
