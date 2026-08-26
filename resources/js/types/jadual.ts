export type StreamName = 'ALPHA' | 'BETA' | 'GAMMA' | string;

export interface GridClass {
    id: number;
    nama: string;
    stream: StreamName;
    guru: string | null;
    bilik: string | null;
    pelajar: number;
    kapasiti: number;
}

export interface GridCell {
    day: string;
    kelas: GridClass | null;
}

export interface GridRow {
    slot_id: number;
    label: string;
    is_break: boolean;
    cells: GridCell[];
}

export interface Grid {
    days: { value: string; label: string }[];
    rows: GridRow[];
}

export interface StatBlock {
    jumlah: number;
    [key: string]: number;
}

export interface DashboardStats {
    pelajar: StatBlock;
    kelas: StatBlock;
    guru: StatBlock;
    bilik: StatBlock;
}

export interface RuleSummary {
    id: number;
    rule_type: string;
    name: string;
    is_active: boolean;
    weight: number;
    description: string;
}

export type RunAction = 'place' | 'move' | 'keep' | 'pinned' | 'unplaceable';

export interface RunStats {
    place: number;
    move: number;
    keep: number;
    pinned: number;
    unplaceable: number;
    violations: number;
    iterations?: number;
}
