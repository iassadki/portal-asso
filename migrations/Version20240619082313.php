<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619082313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association ADD cotisation_id INT DEFAULT NULL, ADD gallerie_id INT NOT NULL, ADD evenement_id INT DEFAULT NULL, ADD liste_users LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC3EAA84B1 FOREIGN KEY (cotisation_id) REFERENCES cotisation (id)');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC8FE05BFA FOREIGN KEY (gallerie_id) REFERENCES galerie (id)');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CCFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD8521CC3EAA84B1 ON association (cotisation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD8521CC8FE05BFA ON association (gallerie_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD8521CCFD02F13 ON association (evenement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC3EAA84B1');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC8FE05BFA');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CCFD02F13');
        $this->addSql('DROP INDEX UNIQ_FD8521CC3EAA84B1 ON association');
        $this->addSql('DROP INDEX UNIQ_FD8521CC8FE05BFA ON association');
        $this->addSql('DROP INDEX UNIQ_FD8521CCFD02F13 ON association');
        $this->addSql('ALTER TABLE association DROP cotisation_id, DROP gallerie_id, DROP evenement_id, DROP liste_users');
    }
}
