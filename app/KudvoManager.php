<?php

namespace App;

use App\Enums\ElectionPanelState;
use App\Models\Election;
use App\Models\ElectionBoothToken;
use App\Models\Nomination;
use App\Models\Organisation;

class KudvoManager
{
    protected ?Election $election = null;

    protected ?Nomination $nomination = null;

    protected ?Organisation $organisation = null;

    protected ?ElectionPanelState $electionPanelState = null;

    protected ?ElectionBoothToken $electionBoothToken = null;

    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisation = $organisation;
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

    public function getElection(): ?Election
    {
        return $this->election;
    }

    public function setNomination(?Nomination $nomination): void
    {
        $this->nomination = $nomination;

        $this->setOrganisation(organisation: $nomination?->organisation);
    }

    public function getNomination(): ?Nomination
    {
        return $this->nomination;
    }

    public function setElectionPanelState(?ElectionPanelState $state): void
    {
        $this->electionPanelState = $state;
    }

    public function getElectionPanelState(): ?ElectionPanelState
    {
        return $this->electionPanelState;
    }

    public function setElectionBoothToken(?ElectionBoothToken $token): void
    {
        $this->electionBoothToken = $token;
    }

    public function getElectionBoothToken(): ?ElectionBoothToken
    {
        return $this->electionBoothToken;
    }

    public function isBoothDevice(): bool
    {
        return filled($this->getElectionBoothToken());
    }
}
