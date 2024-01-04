<?php

namespace App;

use App\Models\Nomination;
use App\Models\Organisation;

class KudvoManager
{
    protected ?Nomination $nomination = null;

    protected ?Organisation $organisation = null;

    public function setNomination(?Nomination $nomination): void
    {
        $this->nomination = $nomination;

        $this->setOrganisation(organisation: $nomination?->organisation);
    }

    public function getNomination(): ?Nomination
    {
        return $this->nomination;
    }

    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }
}
