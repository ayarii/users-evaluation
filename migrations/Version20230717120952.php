<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230717120952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE affectationnotes (id INT AUTO_INCREMENT NOT NULL, critere_id INT DEFAULT NULL, user_id VARCHAR(255) DEFAULT NULL, note DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, INDEX critere_id (critere_id), INDEX user_id (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE badge (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE critere (id INT AUTO_INCREMENT NOT NULL, id_evaluation INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, ponderation INT NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, INDEX id_evaluation (id_evaluation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, id_session INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, INDEX id_session (id_session), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, id_user VARCHAR(255) DEFAULT NULL, id_departement INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, INDEX id_departement (id_departement), INDEX id_user (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affectationnotes ADD CONSTRAINT FK_B955B1B99E5F45AB FOREIGN KEY (critere_id) REFERENCES critere (id)');
        $this->addSql('ALTER TABLE affectationnotes ADD CONSTRAINT FK_B955B1B9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE critere ADD CONSTRAINT FK_7F6A8053B6A925A2 FOREIGN KEY (id_evaluation) REFERENCES evaluation (id)');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B63ED97CA4 FOREIGN KEY (id_session) REFERENCES session (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5756B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575D9649694 FOREIGN KEY (id_departement) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE user ADD id_departement INT DEFAULT NULL, ADD resetToken LONGTEXT NOT NULL, ADD enabled TINYINT(1) NOT NULL, ADD authCode VARCHAR(255) DEFAULT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D9649694 FOREIGN KEY (id_departement) REFERENCES departement (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D9649694 ON user (id_departement)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D9649694');
        $this->addSql('ALTER TABLE affectationnotes DROP FOREIGN KEY FK_B955B1B99E5F45AB');
        $this->addSql('ALTER TABLE affectationnotes DROP FOREIGN KEY FK_B955B1B9A76ED395');
        $this->addSql('ALTER TABLE critere DROP FOREIGN KEY FK_7F6A8053B6A925A2');
        $this->addSql('ALTER TABLE departement DROP FOREIGN KEY FK_C1765B63ED97CA4');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5756B3CA4B');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575D9649694');
        $this->addSql('DROP TABLE affectationnotes');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE critere');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP INDEX IDX_8D93D649D9649694 ON user');
        $this->addSql('ALTER TABLE user DROP id_departement, DROP resetToken, DROP enabled, DROP authCode, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
