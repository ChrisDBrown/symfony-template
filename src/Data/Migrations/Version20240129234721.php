<?php

declare(strict_types=1);

namespace App\Data\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240129234721 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return 'Set up profile table';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE profile (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql("COMMENT ON COLUMN profile.id IS '(DC2Type:uuid)'");
        $this->addSql("COMMENT ON COLUMN profile.created_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql("COMMENT ON COLUMN profile.updated_at IS '(DC2Type:datetime_immutable)'");
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // No down migration should be added, follow expand-contract practices
    }
}
