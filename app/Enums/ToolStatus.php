<?php

namespace App\Enums;

enum ToolStatus: string
{
    case Available = 'available';
    case Reserved  = 'reserved';
    case Out       = 'out';
    /** DR-2.2 — tool has been archived; hidden from listings but not deleted. */
    case Archived  = 'archived';

    /**
     * Human-readable label for display purposes.
     */
    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Reserved  => 'Reserved',
            self::Out       => 'Out',
            self::Archived  => 'Archived',
        };
    }

    /**
     * Tailwind colour token used in the UI badge.
     */
    public function colour(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Reserved  => 'yellow',
            self::Out       => 'red',
            self::Archived  => 'zinc',
        };
    }
}
