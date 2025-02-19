<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219203540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment_user (appointment_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9E501E88E5B533F9 (appointment_id), INDEX IDX_9E501E88A76ED395 (user_id), PRIMARY KEY(appointment_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment_product_usage (appointment_id INT NOT NULL, product_usage_id INT NOT NULL, INDEX IDX_FFF9B86E5B533F9 (appointment_id), INDEX IDX_FFF9B86EAE08C0D (product_usage_id), PRIMARY KEY(appointment_id, product_usage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment_user ADD CONSTRAINT FK_9E501E88E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment_user ADD CONSTRAINT FK_9E501E88A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment_product_usage ADD CONSTRAINT FK_FFF9B86E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment_product_usage ADD CONSTRAINT FK_FFF9B86EAE08C0D FOREIGN KEY (product_usage_id) REFERENCES product_usage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment ADD service_id INT NOT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE38F844ED5CA9E6 ON appointment (service_id)');
        $this->addSql('ALTER TABLE product_usage ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_usage ADD CONSTRAINT FK_71B7252C4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_71B7252C4584665A ON product_usage (product_id)');
        $this->addSql('ALTER TABLE review ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment_user DROP FOREIGN KEY FK_9E501E88E5B533F9');
        $this->addSql('ALTER TABLE appointment_user DROP FOREIGN KEY FK_9E501E88A76ED395');
        $this->addSql('ALTER TABLE appointment_product_usage DROP FOREIGN KEY FK_FFF9B86E5B533F9');
        $this->addSql('ALTER TABLE appointment_product_usage DROP FOREIGN KEY FK_FFF9B86EAE08C0D');
        $this->addSql('DROP TABLE appointment_user');
        $this->addSql('DROP TABLE appointment_product_usage');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('DROP INDEX IDX_794381C6A76ED395 ON review');
        $this->addSql('ALTER TABLE review DROP user_id');
        $this->addSql('ALTER TABLE product_usage DROP FOREIGN KEY FK_71B7252C4584665A');
        $this->addSql('DROP INDEX IDX_71B7252C4584665A ON product_usage');
        $this->addSql('ALTER TABLE product_usage DROP product_id');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844ED5CA9E6');
        $this->addSql('DROP INDEX UNIQ_FE38F844ED5CA9E6 ON appointment');
        $this->addSql('ALTER TABLE appointment DROP service_id');
    }
}
