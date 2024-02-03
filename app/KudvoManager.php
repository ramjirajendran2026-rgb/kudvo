<?php

namespace App;

use App\Models\Election;
use App\Models\Nomination;
use App\Models\Organisation;
use Cookie;

class KudvoManager
{
    protected ?Election $election = null;

    protected ?Nomination $nomination = null;

    protected ?Organisation $organisation = null;

    public function getElection(): ?Election
    {
        return $this->election;
    }

    public function getNomination(): ?Nomination
    {
        return $this->nomination;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setElection(?Election $election): void
    {
        $this->election = $election;

        $this->setOrganisation(organisation: $election?->organisation);
    }

    public function setNomination(?Nomination $nomination): void
    {
        $this->nomination = $nomination;

        $this->setOrganisation(organisation: $nomination?->organisation);
    }

    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

    public function isBoothDevice(?Election $election = null): bool
    {
        $election ??= $this->getElection();

        return filled($election) && Cookie::get(key: 'election_booth_device') == $election->getKey();
    }
}
