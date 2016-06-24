<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds like and dislike fields on reaction table
 */
class Version20160623061538 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE reaction ADD `likes` INT DEFAULT NULL, ADD `dislikes` INT DEFAULT NULL;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE reaction DROP `likes`, DROP `dislikes`;");
    }
}
