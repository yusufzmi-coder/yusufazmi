import { cn } from '@/lib/utils';

/** A utilisation bar that turns amber when tight and red when over capacity. */
export function Meter({
    percent,
    className,
}: {
    percent: number;
    className?: string;
}) {
    const capped = Math.min(percent, 100);
    const tone =
        percent > 100
            ? 'bg-destructive'
            : percent >= 90
              ? 'bg-[var(--chart-3)]'
              : 'bg-[var(--chart-2)]';

    return (
        <div className={cn('flex items-center gap-2', className)}>
            <div className="h-1.5 w-full min-w-16 overflow-hidden rounded-full bg-muted">
                <div
                    className={cn('h-full rounded-full transition-all', tone)}
                    style={{ width: `${capped}%` }}
                />
            </div>
            <span className="w-9 shrink-0 text-right text-xs text-muted-foreground tabular-nums">
                {percent}%
            </span>
        </div>
    );
}
