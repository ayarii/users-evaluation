<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230720225846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE departement CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE evaluation CHANGE id_user id_user VARCHAR(255) DEFAULT NULL, CHANGE id_departement id_departement INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY fk_departement');
        $this->addSql('ALTER TABLE user CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE resetToken resetToken LONGTEXT NOT NULL, CHANGE enabled enabled TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX fk_departement ON user');
        $this->addSql('CREATE INDEX IDX_8D93D649D9649694 ON user (id_departement)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT fk_departement FOREIGN KEY (id_departement) REFERENCES departement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE departement CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evaluation CHANGE id_user id_user VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE id_departement id_departement INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D9649694');
        $this->addSql('ALTER TABLE user CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE resetToken resetToken TEXT DEFAULT NULL, CHANGE enabled enabled TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('DROP INDEX idx_8d93d649d9649694 ON user');
        $this->addSql('CREATE INDEX fk_departement ON user (id_departement)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D9649694 FOREIGN KEY (id_departement) REFERENCES departement (id)');
    }
}
