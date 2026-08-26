import { cn } from '@/lib/utils';
import type { StreamName } from '@/types/jadual';

/** One place decides what a stream looks like, so cells and legends never disagree. */
export function streamClass(stream: StreamName): string {
    switch (stream) {
        case 'ALPHA':
            return 'stream-alpha';
        case 'BETA':
            return 'stream-beta';
        case 'GAMMA':
            return 'stream-gamma';
        default:
            return 'bg-muted border-border';
    }
}

export function StreamDot({
    stream,
    className,
}: {
    stream: StreamName;
    className?: string;
}) {
    return (
        <span
            className={cn(
                'size-2 rounded-full border',
                streamClass(stream),
                className,
            )}
        />
    );
}
