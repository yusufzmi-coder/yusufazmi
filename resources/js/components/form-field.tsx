import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

interface Props {
    id: string;
    label: string;
    error?: string;
    hint?: string;
    required?: boolean;
    className?: string;
    children: React.ReactNode;
}

export function FormField({
    id,
    label,
    error,
    hint,
    required,
    className,
    children,
}: Props) {
    return (
        <div className={cn('space-y-1.5', className)}>
            <Label htmlFor={id} className="text-xs">
                {label}
                {required && <span className="text-destructive"> *</span>}
            </Label>
            {children}
            {hint && !error && (
                <p className="text-[0.7rem] text-muted-foreground">{hint}</p>
            )}
            {error && <p className="text-xs text-destructive">{error}</p>}
        </div>
    );
}

export function NativeSelect({
    className,
    ...props
}: React.ComponentProps<'select'>) {
    return (
        <select
            className={cn(
                'h-9 w-full rounded-md border border-input bg-transparent px-2 text-sm shadow-xs',
                'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none',
                'disabled:cursor-not-allowed disabled:opacity-50',
                className,
            )}
            {...props}
        />
    );
}
