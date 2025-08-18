import { Link } from '@inertiajs/react';
import AppLogo from '@/components/app-logo';

interface AuthSplitLayoutProps {
    children: React.ReactNode;
}

export default function AuthSplitLayout({ children }: AuthSplitLayoutProps) {
    return (
        <div className="flex min-h-screen">
            {/* Left side - Brand */}
            <div className="hidden w-1/2 bg-muted lg:block">
                <div className="flex h-full flex-col justify-between p-10">
                    <Link href="/" className="relative z-20 flex items-center text-lg font-medium">
                        <AppLogo />
                        <span className="ml-2">Laravel</span>
                    </Link>
                    <div className="relative z-20">
                        <blockquote className="space-y-2">
                            <p className="text-lg">
                                "This library has saved me countless hours of work and helped me deliver stunning designs to my clients faster than ever before."
                            </p>
                            <footer className="text-sm">Sofia Davis</footer>
                        </blockquote>
                    </div>
                </div>
            </div>

            {/* Right side - Form */}
            <div className="flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-8">
                <div className="mx-auto w-full max-w-sm">
                    <Link href="/" className="relative z-20 flex items-center justify-center lg:hidden">
                        <AppLogo />
                        <span className="ml-2 text-lg font-medium">Laravel</span>
                    </Link>
                    <div className="mt-8">{children}</div>
                </div>
            </div>
        </div>
    );
}
