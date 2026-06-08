import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { ShieldAlert, MapPin, Users, Coffee, Activity, ArrowLeft } from 'lucide-react';
import Navbar from '../Components/Navbar';
import CustomCursor from '../Components/CustomCursor';

export default function Admin({ users = [], coffeeShops = [], communities = [] }) {
  const { auth } = usePage().props;
  const user = auth?.user || null;

  const [activeTab, setActiveTab] = useState('users');

  if (!user || user.role !== 'admin') return null;

  const tabs = [
    { id: 'users', label: 'Data Pengguna', icon: <Users size={16} /> },
    { id: 'shops', label: 'Direktori Kedai', icon: <Coffee size={16} /> },
    { id: 'communities', label: 'Komunitas', icon: <Activity size={16} /> },
  ];

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Control Panel Admin" />
      <Navbar current="admin" />
      <CustomCursor />

      <main className="pt-24 pb-12 px-4 md:px-8 max-w-[1440px] mx-auto">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
          <div>
            <h1 className="font-clash text-4xl font-black uppercase">Control Panel</h1>
            <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B] mt-2">Mode Dewa: Aktif</p>
          </div>
          <Link
            href="/dashboard"
            className="magnetic inline-flex items-center gap-2 border-2 border-[#1A0F0A] bg-white px-4 py-2 font-mono text-[10px] font-black uppercase tracking-wider hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors"
          >
            <ArrowLeft size={16} /> Kembali ke Lounge
          </Link>
        </div>

        {/* BENTO STATS GRID */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-6 shadow-[6px_6px_0px_0px_#C19A6B] flex flex-col justify-between h-32">
            <div className="flex justify-between items-center">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em]">Total Pengguna</p>
              <Users size={20} className="text-[#C19A6B]" />
            </div>
            <p className="font-clash text-4xl font-black">{users.length}</p>
          </div>
          <div className="border-2 border-[#1A0F0A] bg-[#C19A6B] text-[#1A0F0A] p-6 shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col justify-between h-32">
            <div className="flex justify-between items-center">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em]">Coffee Shop</p>
              <Coffee size={20} />
            </div>
            <p className="font-clash text-4xl font-black">{coffeeShops.length}</p>
          </div>
          <div className="border-2 border-[#1A0F0A] bg-white text-[#1A0F0A] p-6 shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col justify-between h-32">
            <div className="flex justify-between items-center">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Komunitas Skena</p>
              <Activity size={20} className="text-[#C19A6B]" />
            </div>
            <p className="font-clash text-4xl font-black">{communities.length || 0}</p>
          </div>
        </div>

        {/* TABBED INTERFACE */}
        <div className="border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A]">
          <div className="flex overflow-x-auto border-b-2 border-[#1A0F0A] bg-[#FAF6F0] custom-scrollbar">
            {tabs.map(tab => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`magnetic shrink-0 flex items-center gap-2 px-6 py-4 font-mono text-[10px] font-black uppercase tracking-wider border-r-2 border-[#1A0F0A] transition-colors ${
                  activeTab === tab.id ? 'bg-[#1A0F0A] text-[#FAF6F0]' : 'bg-transparent text-[#1A0F0A] hover:bg-[#1A0F0A]/10'
                }`}
              >
                {tab.icon} {tab.label}
              </button>
            ))}
          </div>

          <div className="p-0 overflow-x-auto">
            {activeTab === 'users' && (
              <table className="min-w-full font-mono text-xs text-left">
                <thead className="border-b-2 border-[#1A0F0A] bg-[#FAF6F0] text-[#1A0F0A]/60">
                  <tr>
                    <th className="p-4 font-black uppercase tracking-wider">Nama/Username</th>
                    <th className="p-4 font-black uppercase tracking-wider">Role</th>
                    <th className="p-4 font-black uppercase tracking-wider text-center">Reports</th>
                    <th className="p-4 font-black uppercase tracking-wider">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {users.map((usr, i) => (
                    <tr key={usr.id || i} className="border-b border-[#1A0F0A]/10 hover:bg-[#FAF6F0]/50 transition-colors">
                      <td className="p-4 font-bold uppercase truncate max-w-[200px]">{usr.name || usr.username || `USER_${i}`}</td>
                      <td className={`p-4 font-bold uppercase ${usr.role === 'admin' ? 'text-[#C19A6B]' : ''}`}>{usr.role || 'user'}</td>
                      <td className="p-4 text-center font-bold">0</td>
                      <td className="p-4">
                        <button className="magnetic border border-[#1A0F0A] bg-white px-3 py-1 font-black uppercase text-[10px] hover:bg-[#1A0F0A] hover:text-[#FAF6F0]">Kelola</button>
                      </td>
                    </tr>
                  ))}
                  {users.length === 0 && (
                    <tr><td colSpan="4" className="p-8 text-center text-[#1A0F0A]/50 font-bold uppercase">Data Kosong</td></tr>
                  )}
                </tbody>
              </table>
            )}

            {activeTab === 'shops' && (
              <table className="min-w-[800px] w-full font-mono text-xs text-left">
                <thead className="border-b-2 border-[#1A0F0A] bg-[#FAF6F0] text-[#1A0F0A]/60">
                  <tr>
                    <th className="p-4 font-black uppercase tracking-wider">Kedai</th>
                    <th className="p-4 font-black uppercase tracking-wider">Region</th>
                    <th className="p-4 font-black uppercase tracking-wider">Jam Buka</th>
                    <th className="p-4 font-black uppercase tracking-wider">Status</th>
                    <th className="p-4 font-black uppercase tracking-wider">Action</th>
                  </tr>
                </thead>
                <tbody>
                  {coffeeShops.map((cafe) => (
                    <tr key={cafe.id} className="border-b border-[#1A0F0A]/10 hover:bg-[#FAF6F0]/50 transition-colors">
                      <td className="p-4 font-bold uppercase text-[#C19A6B] truncate max-w-[200px]">{cafe.nama}</td>
                      <td className="p-4 font-bold uppercase truncate max-w-[150px]">{cafe.district_name || cafe.kecamatan?.name || cafe.kecamatan || cafe.daerah}</td>
                      <td className="p-4 font-bold uppercase whitespace-nowrap">{cafe.jam_buka} - {cafe.jam_tutup}</td>
                      <td className="p-4 font-bold uppercase">
                        {cafe.is_active ? (
                          <span className="bg-[#1A0F0A] text-[#C19A6B] px-2 py-1">Aktif</span>
                        ) : (
                          <span className="bg-red-900 text-white px-2 py-1">Draft</span>
                        )}
                      </td>
                      <td className="p-4">
                        <button className="magnetic border border-[#1A0F0A] bg-white px-3 py-1 font-black uppercase text-[10px] hover:bg-[#1A0F0A] hover:text-[#FAF6F0]">Edit</button>
                      </td>
                    </tr>
                  ))}
                  {coffeeShops.length === 0 && (
                    <tr><td colSpan="5" className="p-8 text-center text-[#1A0F0A]/50 font-bold uppercase">Data Kosong</td></tr>
                  )}
                </tbody>
              </table>
            )}

            {activeTab === 'communities' && (
              <div className="p-8 text-center text-[#1A0F0A]/50 font-mono text-sm font-bold uppercase">
                <ShieldAlert className="mx-auto mb-2 opacity-50" size={32} />
                Fitur Manajemen Komunitas Belum Aktif
              </div>
            )}
          </div>
        </div>
      </main>
    </div>
  );
}
