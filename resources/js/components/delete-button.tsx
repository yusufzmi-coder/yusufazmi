import { router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface Props {
    url: string;
    title: string;
    description: string;
    label?: string;
}

/** Deletions always go through a confirm step -- they are not undoable from the UI. */
export function DeleteButton({ url, title, description, label }: Props) {
    const [open, setOpen] = useState(false);
    const [busy, setBusy] = useState(false);

    const confirm = () => {
        setBusy(true);
        router.delete(url, {
            preserveScroll: true,
            onFinish: () => {
                setBusy(false);
                setOpen(false);
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="ghost" size="sm" aria-label={label ?? 'Buang'}>
                    <Trash2 className="size-4 text-destructive" />
                    {label && <span>{label}</span>}
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    <DialogDescription>{description}</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" onClick={() => setOpen(false)}>
                        Batal
                    </Button>
                    <Button
                        variant="destructive"
                        onClick={confirm}
                        disabled={busy}
                    >
                        {busy ? 'Membuang…' : 'Ya, buang'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
