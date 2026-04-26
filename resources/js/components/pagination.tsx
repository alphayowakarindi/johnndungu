import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    // Don't show if there's only one page
    if (links.length <= 3) return null;

    return (
        <div className="flex flex-wrap justify-center gap-1 mt-8 mb-12">
            {links.map((link, key) => (
                link.url === null ? (
                    <div
                        key={key}
                        className="px-4 py-2 text-xs border text-gray-300 cursor-default uppercase font-black"
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                ) : (
                    <Link
                        key={key}
                        href={link.url}
                        className={`px-4 py-2 text-xs border transition-all uppercase font-black ${link.active
                            ? 'bg-black text-white border-black'
                            : 'bg-white text-gray-500 hover:border-amber-600 hover:text-amber-600'
                            }`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                )
            ))}
        </div>
    );
}