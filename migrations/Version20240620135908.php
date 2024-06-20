<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620135908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cotisation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, asso_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, INDEX IDX_AE64D2EDA76ED395 (user_id), INDEX IDX_AE64D2ED792C8628 (asso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cotisation ADD CONSTRAINT FK_AE64D2EDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cotisation ADD CONSTRAINT FK_AE64D2ED792C8628 FOREIGN KEY (asso_id) REFERENCES association (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cotisation DROP FOREIGN KEY FK_AE64D2EDA76ED395');
        $this->addSql('ALTER TABLE cotisation DROP FOREIGN KEY FK_AE64D2ED792C8628');
        $this->addSql('DROP TABLE cotisation');
    }
}
