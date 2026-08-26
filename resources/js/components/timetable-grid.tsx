import { Link } from '@inertiajs/react';
import { motion } from 'motion/react';
import { Plus, User } from 'lucide-react';
import { streamClass } from '@/components/stream-badge';
import { cn } from '@/lib/utils';
import type { Grid } from '@/types/jadual';

interface Props {
    grid: Grid;
    compact?: boolean;
}

export function TimetableGrid({ grid, compact = false }: Props) {
    return (
        // Wide tables scroll inside their own container; the page never scrolls sideways.
        <div className="overflow-x-auto">
            <table className="w-full min-w-[52rem] border-separate border-spacing-0 text-sm">
                <thead>
                    <tr>
                        <th className="sticky left-0 z-10 w-28 bg-card px-3 py-3 text-left text-xs font-medium text-muted-foreground">
                            Masa
                        </th>
                        {grid.days.map((day) => (
                            <th
                                key={day.value}
                                className="px-2 py-3 text-center"
                            >
                                <div className="font-semibold text-foreground">
                                    {day.label}
                                </div>
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {grid.rows.map((row) => (
                        <tr key={row.slot_id}>
                            <td className="sticky left-0 z-10 border-t border-border bg-card px-3 py-2 align-top text-xs whitespace-pre-line text-muted-foreground">
                                {row.label.replace(' - ', '\n')}
                            </td>

                            {row.is_break ? (
                                <td
                                    colSpan={grid.days.length}
                                    className="border-t border-border bg-muted/60 py-3 text-center text-xs font-semibold tracking-widest text-muted-foreground"
                                >
                                    REHAT
                                </td>
                            ) : (
                                row.cells.map((cell) => (
                                    <td
                                        key={cell.day}
                                        className="border-t border-border p-1.5 align-top"
                                    >
                                        {cell.kelas ? (
                                            <motion.div
                                                initial={{ opacity: 0, y: 4 }}
                                                animate={{ opacity: 1, y: 0 }}
                                                transition={{ duration: 0.18 }}
                                            >
                                                <Link
                                                    href={`/jadual/kelas/${cell.kelas.id}`}
                                                    className={cn(
                                                        'block rounded-lg border p-2 transition hover:brightness-97',
                                                        streamClass(
                                                            cell.kelas.stream,
                                                        ),
                                                    )}
                                                >
                                                    <div className="flex items-center justify-between gap-2">
                                                        <span className="text-xs font-bold text-foreground">
                                                            {cell.kelas.nama}
                                                        </span>
                                                        <span className="inline-flex items-center gap-1 text-[0.65rem] text-foreground/60">
                                                            <User className="size-3" />
                                                            {cell.kelas.pelajar}
                                                        </span>
                                                    </div>
                                                    {!compact && (
                                                        <div className="mt-1 space-y-0.5 text-[0.7rem] leading-tight text-foreground/70">
                                                            <div className="truncate">
                                                                {cell.kelas
                                                                    .guru ??
                                                                    'Tiada guru'}
                                                            </div>
                                                            <div className="truncate">
                                                                {cell.kelas
                                                                    .bilik ??
                                                                    'Tiada bilik'}
                                                            </div>
                                                        </div>
                                                    )}
                                                </Link>
                                            </motion.div>
                                        ) : (
                                            <button
                                                type="button"
                                                className="flex w-full items-center justify-between rounded-lg border border-dashed border-border p-2 text-left text-[0.7rem] text-muted-foreground transition hover:border-primary/50 hover:text-foreground"
                                            >
                                                <span>
                                                    <span className="block font-semibold">
                                                        KOSONG
                                                    </span>
                                                    <span className="block">
                                                        Slot tersedia
                                                    </span>
                                                </span>
                                                <Plus className="size-3.5" />
                                            </button>
                                        )}
                                    </td>
                                ))
                            )}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
