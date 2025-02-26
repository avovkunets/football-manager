<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225173454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates team and player tables with a foreign key constraint and cascade delete on team removal.';
    }

    public function up(Schema $schema): void
    {
        // Create the team table
        $this->addSql(<<<SQL
            CREATE TABLE team (
                id SERIAL NOT NULL,
                name VARCHAR(255) NOT NULL,
                city VARCHAR(255) NOT NULL,
                year_founded INT NOT NULL,
                stadium_name VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);

        // Create the player table
        $this->addSql(<<<SQL
            CREATE TABLE player (
                id SERIAL NOT NULL,
                team_id INT NOT NULL,
                first_name VARCHAR(255) NOT NULL,
                last_name VARCHAR(255) NOT NULL,
                age INT NOT NULL,
                position VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);

        // Create an index on team_id for faster lookups
        $this->addSql(<<<SQL
            CREATE INDEX IDX_98197A65296CD8AE ON player (team_id)
        SQL);

        // Add foreign key constraint with cascade delete: deleting a team deletes its players
        $this->addSql(<<<SQL
            ALTER TABLE player 
            ADD CONSTRAINT FK_98197A65296CD8AE 
            FOREIGN KEY (team_id) REFERENCES team (id) 
            ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Remove the foreign key constraint first
        $this->addSql(<<<SQL
            ALTER TABLE player 
            DROP CONSTRAINT FK_98197A65296CD8AE
        SQL);

        // Drop the player table
        $this->addSql(<<<SQL
            DROP TABLE player
        SQL);

        // Drop the team table
        $this->addSql(<<<SQL
            DROP TABLE team
        SQL);
    }
}
