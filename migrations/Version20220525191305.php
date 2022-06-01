<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220525191305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD images_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CD44F05E5 FOREIGN KEY (images_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9474526CD44F05E5 ON comment (images_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CD44F05E5');
        $this->addSql('DROP INDEX UNIQ_9474526CD44F05E5 ON comment');
        $this->addSql('ALTER TABLE comment DROP images_id');
    }
}
