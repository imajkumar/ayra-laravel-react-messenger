import { Link } from '@inertiajs/react';
import AppLogo from '@/components/app-logo';

interface AuthCardLayoutProps {
    children: React.ReactNode;
}

export default function AuthCardLayout({ children }: AuthCardLayoutProps) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-background p-6">
            <div className="w-full max-w-sm">
                <Link href="/" className="flex items-center gap-2 self-center font-medium">
                    <AppLogo />
                    <span className="text-lg">Laravel</span>
                </Link>
                <div className="mt-8">{children}</div>
            </div>
        </div>
    );
}
