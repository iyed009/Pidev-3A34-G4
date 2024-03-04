<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20240303122137.php
final class Version20240303122137 extends AbstractMigration
========
final class Version20240227130957 extends AbstractMigration
>>>>>>>> origin/SaadLouay-3A34-G4:migrations/Version20240227130957.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20240303122137.php
        $this->addSql('CREATE TABLE activite (id INT AUTO_INCREMENT NOT NULL, salle_id INT DEFAULT NULL, utilisateur_id VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, date DATETIME NOT NULL, nbr_max INT NOT NULL, coach VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_B8755515DC304035 (salle_id), INDEX IDX_B8755515FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
========
        $this->addSql('CREATE TABLE activite (id INT AUTO_INCREMENT NOT NULL, salle_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date DATETIME NOT NULL, nbr_max INT NOT NULL, coach VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image_activte VARCHAR(255) NOT NULL, INDEX IDX_B8755515DC304035 (salle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activite_user (activite_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FA43CF3B9B0F88B1 (activite_id), INDEX IDX_FA43CF3BA76ED395 (user_id), PRIMARY KEY(activite_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
>>>>>>>> origin/SaadLouay-3A34-G4:migrations/Version20240227130957.php
        $this->addSql('CREATE TABLE categorie_p (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, descrption VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, date_evenement DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, categorie_p_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, quantite INT NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, INDEX IDX_D34A04ADA452E4F5 (categorie_p_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
<<<<<<<< HEAD:migrations/Version20240303122137.php
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, utilisateur_id VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, num_tele INT NOT NULL, etat VARCHAR(255) NOT NULL, sujet VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, prenom VARCHAR(255) DEFAULT NULL, INDEX IDX_CE606404FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, utilisateur_id VARCHAR(255) DEFAULT NULL, id_reclamation_id INT DEFAULT NULL, date DATETIME NOT NULL, reponse VARCHAR(255) NOT NULL, INDEX IDX_5FB6DEC7FB88E14F (utilisateur_id), UNIQUE INDEX UNIQ_5FB6DEC7100D1FDF (id_reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id VARCHAR(255) NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, utilisateur_id VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) NOT NULL, addresse VARCHAR(255) NOT NULL, num_tel INT NOT NULL, capacite INT NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_4E977E5CFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, evenement_id INT DEFAULT NULL, utilisateur_id VARCHAR(255) DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, nbre_ticket INT NOT NULL, INDEX IDX_97A0ADA3FD02F13 (evenement_id), INDEX IDX_97A0ADA3FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, num_tele INT NOT NULL, password VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
========
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, reponse_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, num_tele INT NOT NULL, etat VARCHAR(255) NOT NULL, sujet VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, UNIQUE INDEX UNIQ_CE606404CF18BB82 (reponse_id), INDEX IDX_CE606404FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, date DATETIME NOT NULL, reponse VARCHAR(255) NOT NULL, INDEX IDX_5FB6DEC7FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, addresse VARCHAR(255) NOT NULL, num_tel INT NOT NULL, capacite INT NOT NULL, description LONGTEXT NOT NULL, nbr_client INT NOT NULL, logo_salle VARCHAR(255) NOT NULL, INDEX IDX_4E977E5CFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, evenement_id INT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, nbre_ticket INT NOT NULL, INDEX IDX_97A0ADA3FD02F13 (evenement_id), INDEX IDX_97A0ADA3FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, num_tele INT NOT NULL, mot_de_pass VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
>>>>>>>> origin/SaadLouay-3A34-G4:migrations/Version20240227130957.php
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activite ADD CONSTRAINT FK_B8755515DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE activite_user ADD CONSTRAINT FK_FA43CF3B9B0F88B1 FOREIGN KEY (activite_id) REFERENCES activite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activite_user ADD CONSTRAINT FK_FA43CF3BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA452E4F5 FOREIGN KEY (categorie_p_id) REFERENCES categorie_p (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7100D1FDF FOREIGN KEY (id_reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B8755515DC304035');
        $this->addSql('ALTER TABLE activite_user DROP FOREIGN KEY FK_FA43CF3B9B0F88B1');
        $this->addSql('ALTER TABLE activite_user DROP FOREIGN KEY FK_FA43CF3BA76ED395');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA452E4F5');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404FB88E14F');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7FB88E14F');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7100D1FDF');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5CFB88E14F');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FD02F13');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FB88E14F');
        $this->addSql('DROP TABLE activite');
        $this->addSql('DROP TABLE activite_user');
        $this->addSql('DROP TABLE categorie_p');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
