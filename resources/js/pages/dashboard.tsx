import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes';
import { User, Phone, Calendar, Clock, Users, ChevronRight } from 'lucide-react';

export default function Dashboard({ voters }) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 md:p-8 bg-slate-50 dark:bg-slate-950">

                {/* Header Section */}
                <div className="flex items-center justify-between gap-4">
                    <h1 className="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        RoysAfya <span className="text-indigo-600 dark:text-indigo-400">Portal</span>
                    </h1>
                    <div className="flex items-center gap-3 bg-white dark:bg-slate-900 p-2 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
                        <div className="bg-emerald-500 p-2 rounded-xl text-white shadow-lg shadow-emerald-200">
                            <Users className="size-4 md:size-5" />
                        </div>
                        <div className="pr-2 md:pr-4">
                            <p className="text-[10px] uppercase font-bold text-slate-400">Total Members</p>
                            <p className="text-lg md:text-xl font-black text-slate-800 dark:text-slate-200">{voters.length}</p>
                        </div>
                    </div>
                </div>

                {/* Mobile View: Cards (Hidden on MD screens and above) */}
                <div className="flex flex-col gap-4 md:hidden">
                    {voters.length > 0 ? (
                        voters.map((voter) => (
                            <div key={voter.id} className="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                                <div className="flex justify-between items-start mb-4">
                                    <div className="flex items-center gap-3">
                                        <div className="flex size-10 items-center justify-center rounded-2xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                                            <User className="size-5" />
                                        </div>
                                        <div>
                                            <h3 className="font-bold text-slate-900 dark:text-white uppercase text-sm tracking-tight">{voter.name}</h3>
                                            <div className="flex items-center gap-1.5 text-slate-500 text-xs mt-0.5">
                                                <Phone className="size-3" />
                                                <span className="font-mono">{voter.phone}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-800">
                                    <div className="flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300">
                                        <Calendar className="size-3.5 text-indigo-500" />
                                        {voter.registered_at}
                                    </div>
                                    <div className="flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                        <Clock className="size-3" />
                                        {voter.time}
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <EmptyState />
                    )}
                </div>

                {/* Desktop View: Table (Hidden on small screens) */}
                <div className="hidden md:block rounded-[2rem] border border-slate-200 bg-white shadow-2xl shadow-slate-200/50 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none overflow-hidden">
                    <table className="w-full text-left">
                        <thead>
                            <tr className="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                <th className="px-6 py-5 text-xs font-black uppercase text-slate-400 tracking-widest">Member</th>
                                <th className="px-6 py-5 text-xs font-black uppercase text-slate-400 tracking-widest">Contact</th>
                                <th className="px-6 py-5 text-xs font-black uppercase text-slate-400 tracking-widest text-right">Registration</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-50 dark:divide-slate-800">
                            {voters.length > 0 ? (
                                voters.map((voter) => (
                                    <tr key={voter.id} className="group hover:bg-indigo-50/50 dark:hover:bg-indigo-900/5 transition-all">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-4">
                                                <div className="flex size-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400 group-hover:rotate-6 transition-transform">
                                                    <User className="size-5" />
                                                </div>
                                                <span className="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-tight">{voter.name}</span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 font-mono text-sm font-medium text-slate-600 dark:text-slate-400">
                                            {voter.phone}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex flex-col items-end">
                                                <span className="font-bold text-slate-800 dark:text-slate-200 text-sm">{voter.registered_at}</span>
                                                <span className="text-xs text-slate-400">{voter.time}</span>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr><td colSpan="3"><EmptyState /></td></tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}

function EmptyState() {
    return (
        <div className="flex flex-col items-center gap-3 py-20">
            <Users className="size-12 text-slate-200 animate-bounce" />
            <p className="text-slate-400 font-bold uppercase tracking-widest text-xs">Waiting for first dial...</p>
        </div>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};