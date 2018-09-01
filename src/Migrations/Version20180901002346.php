<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180901002346 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE auto (id INT AUTO_INCREMENT NOT NULL, manufacturer VARCHAR(63) NOT NULL, origin_country VARCHAR(63) NOT NULL, release_year INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ad (id INT AUTO_INCREMENT NOT NULL, auto_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, price INT NOT NULL, date_begin DATE NOT NULL, date_end DATE NOT NULL, INDEX IDX_77E0ED581D55B925 (auto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ad ADD CONSTRAINT FK_77E0ED581D55B925 FOREIGN KEY (auto_id) REFERENCES auto (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ad DROP FOREIGN KEY FK_77E0ED581D55B925');
        $this->addSql('DROP TABLE auto');
        $this->addSql('DROP TABLE ad');
    }
}
