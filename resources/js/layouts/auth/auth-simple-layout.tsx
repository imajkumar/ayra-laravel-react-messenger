import { Link } from '@inertiajs/react';
import AppLogo from '@/components/app-logo';

interface AuthSimpleLayoutProps {
    children: React.ReactNode;
}

export default function AuthSimpleLayout({ children }: AuthSimpleLayoutProps) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background p-6">
            <div className="w-full max-w-sm">
                <Link href="/" className="flex flex-col items-center gap-2 font-medium">
                    <AppLogo />
                    <span className="text-lg">Laravel</span>
                </Link>
                <div className="mt-8">{children}</div>
            </div>
        </div>
    );
}
