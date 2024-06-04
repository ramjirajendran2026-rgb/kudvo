<?php

namespace App\Data\Election;

use App\Enums\ElectionCollaboratorPermission;
use Spatie\LaravelData\Data;

class CollaboratorPermissionsData extends Data
{
    public function __construct(
        public ElectionCollaboratorPermission $preference = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $electors = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $ballot_setup = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $timing = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $payment = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $ballot_link_blasts = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $booth_tokens = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $monitor_tokens = ElectionCollaboratorPermission::NoAccess,
        public ElectionCollaboratorPermission $elector_logs = ElectionCollaboratorPermission::NoAccess,
    ) {
    }
}
