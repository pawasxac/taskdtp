import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import Navbar from '../../Components/Navbar';

export default function OriginalDashboard({ stats }) {
    const { auth } = usePage().props;
    const user = auth?.user || null;

    if (!user || user.role !== 'admin') return null;

    return (
        <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] font-sans selection:bg-[#8b6f47] selection:text-white">
            <Head title="Dashboard Admin" />
            <Navbar current="admin" />
            
            <main className="px-4 py-8 md:px-8 max-w-7xl mx-auto space-y-12">
                
                {/* Section Header */}
                <div className="border-l-4 border-[#8b6f47] pl-4">
                    <h2 className="text-3xl font-black uppercase font-clash">Dashboard Admin</h2>
                    <p className="text-gray-600 mt-2">Ringkasan metrik platform dan status sistem real-time Brew & Breathe</p>
                </div>

                {/* Main Stats Grid */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <h4 className="text-gray-500 font-bold text-sm uppercase mb-2">☕ Coffee Shops</h4>
                        <p className="text-3xl font-black text-[#8b6f47]">{stats.coffee_shops_count}</p>
                    </div>
                    <div className="bg-[#1A0F0A] p-6 rounded-xl shadow-sm border border-gray-800 hover:shadow-md transition-shadow">
                        <h4 className="text-gray-400 font-bold text-sm uppercase mb-2">👥 Total Users</h4>
                        <p className="text-3xl font-black text-white">{stats.users_count}</p>
                    </div>
                    <div className="bg-[#8b6f47] p-6 rounded-xl shadow-sm border border-[#7a603c] hover:shadow-md transition-shadow">
                        <h4 className="text-[#ecd6b7] font-bold text-sm uppercase mb-2">🏘️ Komunitas</h4>
                        <p className="text-3xl font-black text-white">{stats.komunitas_count}</p>
                    </div>
                    <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <h4 className="text-gray-500 font-bold text-sm uppercase mb-2">⭐ Avg Rating</h4>
                        <p className="text-3xl font-black text-[#8b6f47]">{stats.avg_rating}/5</p>
                    </div>
                    <div className="bg-[#1A0F0A] p-6 rounded-xl shadow-sm border border-gray-800 hover:shadow-md transition-shadow">
                        <h4 className="text-gray-400 font-bold text-sm uppercase mb-2">💬 Reviews</h4>
                        <p className="text-3xl font-black text-white">{stats.reviews_count}</p>
                    </div>
                    <div className="bg-[#8b6f47] p-6 rounded-xl shadow-sm border border-[#7a603c] hover:shadow-md transition-shadow">
                        <h4 className="text-[#ecd6b7] font-bold text-sm uppercase mb-2">🤝 Anggota Komunitas</h4>
                        <p className="text-3xl font-black text-white">{stats.community_members_count}</p>
                    </div>
                    <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <h4 className="text-gray-500 font-bold text-sm uppercase mb-2">📝 Community Posts</h4>
                        <p className="text-3xl font-black text-[#8b6f47]">{stats.community_posts_count}</p>
                    </div>
                    <div className="bg-[#1A0F0A] p-6 rounded-xl shadow-sm border border-gray-800 hover:shadow-md transition-shadow">
                        <h4 className="text-gray-400 font-bold text-sm uppercase mb-2">🎯 Gathering Request</h4>
                        <p className="text-3xl font-black text-white">{stats.gathering_requests_count}</p>
                    </div>
                </div>

                {/* Platform Overview Section */}
                <div className="border-l-4 border-[#8b6f47] pl-4">
                    <h2 className="text-3xl font-black uppercase font-clash">Pusat Kontrol</h2>
                    <p className="text-gray-600 mt-2">Kelola semua aspek platform dari satu tempat</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {[
                        { title: '☕ Coffee Shops', desc: 'Kelola data kedai kopi dengan informasi lengkap', link: '/admin/coffee-shops' },
                        { title: '🏘️ Komunitas', desc: 'Kelola komunitas, anggota, dan struktur organisasi', link: '/admin/komunitas' },
                        { title: '📝 Community Posts', desc: 'Moderasi dan kelola postingan komunitas', link: '/admin/community-posts' },
                        { title: '🎯 Gathering', desc: 'Kelola permintaan event gathering', link: '/admin/gathering-requests' },
                        { title: '⭐ Reviews', desc: 'Monitor review coffee shop', link: '/admin/coffee-shop-reviews' },
                        { title: '👤 User Management', desc: 'Kelola akun user dan role', link: '/admin/login-monitor' },
                        { title: '📍 Kecamatan', desc: 'Kelola data wilayah geografis', link: '/admin/kecamatan' },
                        { title: '🤝 Members', desc: 'Lihat daftar anggota komunitas', link: '/admin/community-members' },
                    ].map((item, i) => (
                        <a key={i} href={item.link} className="block group bg-white hover:bg-[#FAF6F0] p-6 rounded-xl shadow-sm border border-gray-200 transition-all hover:border-[#8b6f47] hover:shadow-md">
                            <h3 className="text-lg font-bold text-[#1A0F0A] group-hover:text-[#8b6f47] transition-colors">{item.title}</h3>
                            <p className="text-sm text-gray-500 mt-2">{item.desc}</p>
                        </a>
                    ))}
                </div>

                {/* System Analytics */}
                <div className="border-l-4 border-[#8b6f47] pl-4">
                    <h2 className="text-3xl font-black uppercase font-clash">Statistik Lanjutan</h2>
                    <p className="text-gray-600 mt-2">Data mendalam untuk analisis platform</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-white p-6 rounded-xl border-t-4 border-[#8b6f47] shadow-sm hover:shadow-md transition-shadow">
                        <div className="font-bold text-gray-800 uppercase text-sm tracking-wider">📈 Engagement Rate</div>
                        <p className="text-4xl font-black text-[#8b6f47] my-4">{stats.engagement_rate}%</p>
                        <p className="text-sm text-gray-500">Rasio postingan terhadap pengguna</p>
                    </div>
                    <div className="bg-white p-6 rounded-xl border-t-4 border-[#8b6f47] shadow-sm hover:shadow-md transition-shadow">
                        <div className="font-bold text-gray-800 uppercase text-sm tracking-wider">⭐ Avg Shop Rating</div>
                        <p className="text-4xl font-black text-[#8b6f47] my-4">{stats.avg_rating}</p>
                        <p className="text-sm text-gray-500">Dari keseluruhan coffee shops</p>
                    </div>
                    <div className="bg-white p-6 rounded-xl border-t-4 border-[#8b6f47] shadow-sm hover:shadow-md transition-shadow">
                        <div className="font-bold text-gray-800 uppercase text-sm tracking-wider">👥 Member Activity</div>
                        <p className="text-4xl font-black text-[#8b6f47] my-4">{stats.avg_per_community}</p>
                        <p className="text-sm text-gray-500">Member rata-rata per komunitas</p>
                    </div>
                </div>

                {/* Switch to New Admin Button */}
                <div className="pt-8 border-t border-gray-200 flex justify-center">
                    <Link href="/admin/management" className="bg-[#1A0F0A] text-white px-8 py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-[#8b6f47] transition-all transform hover:-translate-y-1 hover:shadow-lg shadow-md flex items-center gap-3">
                        Lihat Dashboard Baru Buatan Teman
                    </Link>
                </div>
            </main>
        </div>
    );
}
