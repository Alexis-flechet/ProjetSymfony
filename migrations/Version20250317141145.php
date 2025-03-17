<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317141145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL)');
        $this->addSql('CREATE TABLE participe_event (participe_id INTEGER NOT NULL, event_id INTEGER NOT NULL, PRIMARY KEY(participe_id, event_id), CONSTRAINT FK_A9BA512BA71D81B9 FOREIGN KEY (participe_id) REFERENCES participe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A9BA512B71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A9BA512BA71D81B9 ON participe_event (participe_id)');
        $this->addSql('CREATE INDEX IDX_A9BA512B71F7E88B ON participe_event (event_id)');
        $this->addSql('CREATE TABLE participe_user (participe_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(participe_id, user_id), CONSTRAINT FK_4B46C185A71D81B9 FOREIGN KEY (participe_id) REFERENCES participe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4B46C185A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4B46C185A71D81B9 ON participe_user (participe_id)');
        $this->addSql('CREATE INDEX IDX_4B46C185A76ED395 ON participe_user (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE participe');
        $this->addSql('DROP TABLE participe_event');
        $this->addSql('DROP TABLE participe_user');
    }
}
