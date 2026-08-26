import { usePage } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { toast } from 'sonner';

interface FlashProps {
    flash?: { success?: string | null; error?: string | null };
    [key: string]: unknown;
}

/**
 * Turns the shared `flash` bag into a toast exactly once per message. The ref guard
 * matters because Inertia re-renders on partial reloads and would otherwise repeat
 * the same toast.
 */
export function useFlash(): void {
    const { flash } = usePage<FlashProps>().props;
    const last = useRef<string | null>(null);

    useEffect(() => {
        const message = flash?.success ?? flash?.error;

        if (!message || message === last.current) {
            return;
        }

        last.current = message;

        if (flash?.success) {
            toast.success(flash.success);
        } else if (flash?.error) {
            toast.error(flash.error, { duration: 8000 });
        }
    }, [flash]);
}
