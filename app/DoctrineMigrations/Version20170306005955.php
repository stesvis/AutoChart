<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170306005955 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tasks ADD modified_by_user_id INT DEFAULT NULL, ADD modified_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597DD5BE62E FOREIGN KEY (modified_by_user_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_50586597DD5BE62E ON tasks (modified_by_user_id)');
        $this->addSql('ALTER TABLE vehicles ADD modified_by_user_id INT DEFAULT NULL, ADD modified_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE vehicles ADD CONSTRAINT FK_1FCE69FADD5BE62E FOREIGN KEY (modified_by_user_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_1FCE69FADD5BE62E ON vehicles (modified_by_user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597DD5BE62E');
        $this->addSql('DROP INDEX IDX_50586597DD5BE62E ON tasks');
        $this->addSql('ALTER TABLE tasks DROP modified_by_user_id, DROP modified_at');
        $this->addSql('ALTER TABLE vehicles DROP FOREIGN KEY FK_1FCE69FADD5BE62E');
        $this->addSql('DROP INDEX IDX_1FCE69FADD5BE62E ON vehicles');
        $this->addSql('ALTER TABLE vehicles DROP modified_by_user_id, DROP modified_at');
    }
}
