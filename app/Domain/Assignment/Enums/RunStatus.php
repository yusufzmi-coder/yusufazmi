<?php

declare(strict_types=1);

namespace App\Domain\Assignment\Enums;

enum RunStatus: string
{
    case Running = 'running';
    case Draft = 'draft';
    case Committed = 'committed';
    case Discarded = 'discarded';
    case Expired = 'expired';
    /** Committed, then rolled back by an admin. */
    case Reverted = 'reverted';
    case Failed = 'failed';
}
