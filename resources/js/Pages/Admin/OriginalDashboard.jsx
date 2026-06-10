import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { 
    Coffee, Users, MessageSquare, Star, 
    MapPin, Activity, List, ArrowUpRight, BarChart 
} from 'lucide-react';
import Navbar from '../../Components/Navbar';

export default function OriginalDashboard({ stats }) {
    const { auth } = usePage().props;
    const user = auth?.user || null;

    if (!user || user.role !== 'admin') return null;

    const btnPrimary = "magnetic inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-6 py-3 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] transition-all duration-300 hover:bg-[#1A0F0A] hover:text-[#FAF6F0]";

    return (
        <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
            <Head title="Skena Command Center" />
            <Navbar current="admin" />
            
            <main className="px-4 py-12 md:px-8 max-w-7xl mx-auto space-y-16">
                
                {/* Header Section */}
                <div className="border-b-4 border-[#1A0F0A] pb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <div className="inline-flex items-center gap-2 border-2 border-[#1A0F0A] bg-white px-3 py-1 font-mono text-[10px] font-black uppercase tracking-widest mb-4">
                            <Activity size={14} className="text-[#C19A6B]" /> System Real-time
                        </div>
                        <h2 className="font-clash text-4xl md:text-5xl font-black uppercase">Command Center</h2>
                        <p className="font-mono text-sm uppercase tracking-wider text-[#1A0F0A]/70 mt-2">
                            Kendali Penuh Database & Interaksi Skena
                        </p>
                    </div>
                    <Link href="/admin/management" className={btnPrimary}>
                        Buka CMS Teman <ArrowUpRight size={16} />
                    </Link>
                </div>

                {/* Primary Stats */}
                <div>
                    <h3 className="font-clash text-2xl font-black uppercase mb-6 flex items-center gap-2">
                        <BarChart size={24} className="text-[#C19A6B]" /> Metrik Utama
                    </h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {/* Stat Card 1 */}
                        <div className="border-2 border-[#1A0F0A] bg-white p-6 shadow-[6px_6px_0px_0px_#1A0F0A] hover:-translate-y-1 transition-transform">
                            <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B] mb-2 flex items-center gap-2">
                                <Coffee size={14} /> Coffee Shops
                            </p>
                            <p className="font-clash text-5xl font-black">{stats.coffee_shops_count}</p>
                        </div>
                        {/* Stat Card 2 */}
                        <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-6 shadow-[6px_6px_0px_0px_#C19A6B] hover:-translate-y-1 transition-transform">
                            <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B] mb-2 flex items-center gap-2">
                                <Users size={14} /> Total Skena
                            </p>
                            <p className="font-clash text-5xl font-black">{stats.users_count}</p>
                        </div>
                        {/* Stat Card 3 */}
                        <div className="border-2 border-[#1A0F0A] bg-[#C19A6B] p-6 shadow-[6px_6px_0px_0px_#1A0F0A] hover:-translate-y-1 transition-transform">
                            <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] mb-2 flex items-center gap-2">
                                <MapPin size={14} /> Komunitas
                            </p>
                            <p className="font-clash text-5xl font-black">{stats.komunitas_count}</p>
                        </div>
                        {/* Stat Card 4 */}
                        <div className="border-2 border-[#1A0F0A] bg-white p-6 shadow-[6px_6px_0px_0px_#1A0F0A] hover:-translate-y-1 transition-transform">
                            <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B] mb-2 flex items-center gap-2">
                                <Star size={14} /> Rating Rata-rata
                            </p>
                            <p className="font-clash text-5xl font-black">{stats.avg_rating}<span className="text-2xl text-[#1A0F0A]/50">/5</span></p>
                        </div>
                    </div>
                </div>

                {/* Secondary Engagement Stats */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="border-2 border-[#1A0F0A] bg-white p-8 relative overflow-hidden group hover:shadow-[8px_8px_0px_0px_#1A0F0A] transition-all">
                        <div className="absolute -right-8 -bottom-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <MessageSquare size={160} />
                        </div>
                        <h4 className="font-clash text-2xl font-black uppercase mb-6">Engagement & Interaksi</h4>
                        <div className="grid grid-cols-2 gap-8">
                            <div>
                                <p className="font-mono text-xs uppercase tracking-widest text-[#1A0F0A]/50 mb-1">Total Reviews</p>
                                <p className="font-clash text-4xl font-black">{stats.reviews_count}</p>
                            </div>
                            <div>
                                <p className="font-mono text-xs uppercase tracking-widest text-[#1A0F0A]/50 mb-1">Forum Posts</p>
                                <p className="font-clash text-4xl font-black">{stats.community_posts_count}</p>
                            </div>
                            <div>
                                <p className="font-mono text-xs uppercase tracking-widest text-[#1A0F0A]/50 mb-1">Rasio Aktif</p>
                                <p className="font-clash text-4xl font-black">{stats.engagement_rate}%</p>
                            </div>
                            <div>
                                <p className="font-mono text-xs uppercase tracking-widest text-[#1A0F0A]/50 mb-1">Avg Member/Grup</p>
                                <p className="font-clash text-4xl font-black">{stats.avg_per_community}</p>
                            </div>
                        </div>
                    </div>

                    <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-8 flex flex-col justify-between">
                        <div>
                            <h4 className="font-clash text-2xl font-black uppercase mb-2 text-[#C19A6B]">Pusat Kendali Lanjutan</h4>
                            <p className="font-mono text-xs uppercase tracking-wider text-white/50 mb-8 leading-relaxed">
                                Membutuhkan manajemen data yang lebih spesifik? Buka panel admin full untuk memoderasi tempat ngopi, user, atau event gathering.
                            </p>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div className="border border-white/20 p-4">
                                <p className="font-mono text-[10px] text-white/50 uppercase mb-1">Total Anggota</p>
                                <p className="font-clash text-3xl font-black">{stats.community_members_count}</p>
                            </div>
                            <div className="border border-white/20 p-4">
                                <p className="font-mono text-[10px] text-white/50 uppercase mb-1">Global Chats</p>
                                <p className="font-clash text-3xl font-black">{stats.global_chats_count || 0}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Modul Administrasi */}
                <div>
                    <h3 className="font-clash text-2xl font-black uppercase mb-6 flex items-center gap-2 border-b-2 border-[#1A0F0A] pb-4">
                        <List size={24} className="text-[#1A0F0A]" /> Modul Database
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        {[
                            { title: 'Coffee Shops', desc: 'Kelola direktori tempat', link: '/admin/coffee-shops' },
                            { title: 'Komunitas', desc: 'Monitor forum diskusi', link: '/admin/komunitas' },
                            { title: 'Posts Moderation', desc: 'Kontrol konten forum', link: '/admin/community-posts' },
                            { title: 'Global Chats', desc: 'Pantau obrolan global', link: '/admin/global-chats' },
                            { title: 'Reviews', desc: 'Moderasi ulasan kedai', link: '/admin/coffee-shop-reviews' },
                            { title: 'User Management', desc: 'Hak akses & akun', link: '/admin/login-monitor' },
                            { title: 'Area / Kecamatan', desc: 'Mapping wilayah', link: '/admin/kecamatan' },
                            { title: 'Memberships', desc: 'Data keanggotaan', link: '/admin/community-members' },
                        ].map((item, i) => (
                            <a 
                                key={i} 
                                href={item.link} 
                                className="group block border-2 border-[#1A0F0A] bg-white p-5 hover:bg-[#C19A6B] hover:text-[#1A0F0A] transition-colors"
                            >
                                <h4 className="font-clash text-lg font-black uppercase mb-1 flex justify-between items-center">
                                    {item.title} <ArrowUpRight size={16} className="opacity-0 -translate-x-2 translate-y-2 group-hover:opacity-100 group-hover:translate-x-0 group-hover:translate-y-0 transition-all" />
                                </h4>
                                <p className="font-mono text-[10px] uppercase tracking-wider text-[#1A0F0A]/60 group-hover:text-[#1A0F0A]/80">{item.desc}</p>
                            </a>
                        ))}
                    </div>
                </div>

            </main>
        </div>
    );
}
