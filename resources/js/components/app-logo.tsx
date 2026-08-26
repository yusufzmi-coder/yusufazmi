import { GraduationCap } from 'lucide-react';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-9 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground">
                <GraduationCap className="size-5" />
            </div>
            <div className="grid flex-1 text-left leading-tight">
                <span className="truncate text-sm font-bold tracking-tight">
                    JADUAL KELAS
                </span>
                <span className="truncate text-xs text-sidebar-foreground/60">
                    STUDENT AUTO
                </span>
            </div>
        </>
    );
}
