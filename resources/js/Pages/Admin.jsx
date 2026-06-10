import React, { useState } from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import { ShieldAlert, MapPin, Users, Coffee, Activity, ArrowLeft } from 'lucide-react';
import Navbar from '../Components/Navbar';
import CustomCursor from '../Components/CustomCursor';

export default function Admin({ users = [], coffeeShops = [], communities = [] }) {
  const { auth } = usePage().props;
  const user = auth?.user || null;

  const [activeTab, setActiveTab] = useState(() => {
    if (typeof window !== 'undefined') {
      const params = new URLSearchParams(window.location.search);
      return params.get('tab') || 'users';
    }
    return 'users';
  });

  const handleTabChange = (tabId) => {
    setActiveTab(tabId);
    if (typeof window !== 'undefined') {
      const url = new URL(window.location.href);
      url.searchParams.set('tab', tabId);

      url.searchParams.delete('page');
      window.history.replaceState({}, '', url.toString());
    }
  };

  const getPaginatedUrl = (url, tabId) => {
    if (!url) return '#';
    try {
      const parsed = new URL(url, window.location.origin);
      parsed.searchParams.set('tab', tabId);
      return parsed.pathname + parsed.search;
    } catch (e) {
      return url;
    }
  };

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
          <div className="flex flex-wrap gap-2">
            <a
              href="/admin/dashboard-lama"
              className="magnetic inline-flex items-center gap-2 border-2 border-[#C19A6B] bg-[#C19A6B] px-4 py-2 font-mono text-[10px] font-black uppercase tracking-wider text-[#1A0F0A] hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors"
            >
              ⚙️ Panel CRUD Lama
            </a>
            <Link
              href="/dashboard"
              className="magnetic inline-flex items-center gap-2 border-2 border-[#1A0F0A] bg-white px-4 py-2 font-mono text-[10px] font-black uppercase tracking-wider hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors"
            >
              <ArrowLeft size={16} /> Kembali ke Lounge
            </Link>
          </div>
        </div>

        {/* BENTO STATS GRID */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-6 shadow-[6px_6px_0px_0px_#C19A6B] flex flex-col justify-between h-32">
            <div className="flex justify-between items-center">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em]">Total Pengguna</p>
              <Users size={20} className="text-[#C19A6B]" />
            </div>
            <p className="font-clash text-4xl font-black">{users?.total || 0}</p>
          </div>
          <div className="border-2 border-[#1A0F0A] bg-[#C19A6B] text-[#1A0F0A] p-6 shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col justify-between h-32">
            <div className="flex justify-between items-center">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em]">Coffee Shop</p>
              <Coffee size={20} />
            </div>
            <p className="font-clash text-4xl font-black">{coffeeShops?.total || 0}</p>
          </div>
          <div className="border-2 border-[#1A0F0A] bg-white text-[#1A0F0A] p-6 shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col justify-between h-32">
            <div className="flex justify-between items-center">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Komunitas Skena</p>
              <Activity size={20} className="text-[#C19A6B]" />
            </div>
            <p className="font-clash text-4xl font-black">{communities?.total || 0}</p>
          </div>
        </div>

        {/* TABBED INTERFACE */}
        <div className="border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A]">
          <div className="flex overflow-x-auto border-b-2 border-[#1A0F0A] bg-[#FAF6F0] custom-scrollbar">
            {tabs.map(tab => (
              <button
                key={tab.id}
                onClick={() => handleTabChange(tab.id)}
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
              <>
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
                  {(users?.data || []).map((usr, i) => (
                    <tr key={usr.id || i} className="border-b border-[#1A0F0A]/10 hover:bg-[#FAF6F0]/50 transition-colors">
                      <td className="p-4 font-bold uppercase truncate max-w-[200px]">{usr.name || usr.username || `USER_${i}`}</td>
                      <td className={`p-4 font-bold uppercase ${usr.role === 'admin' ? 'text-[#C19A6B]' : ''}`}>{usr.role || 'user'}</td>
                      <td className="p-4 text-center font-bold">0</td>
                      <td className="p-4">
                        <a href="/admin/dashboard-lama" className="magnetic border border-[#1A0F0A] bg-white px-3 py-1 font-black uppercase text-[10px] hover:bg-[#1A0F0A] hover:text-[#FAF6F0]">Kelola</a>
                      </td>
                    </tr>
                  ))}
                  {(!users?.data || users.data.length === 0) && (
                    <tr><td colSpan="4" className="p-8 text-center text-[#1A0F0A]/50 font-bold uppercase">Data Kosong</td></tr>
                  )}
                </tbody>
              </table>
              {users?.links && (
                <div className="flex justify-end gap-2 p-4 bg-white border-t border-[#1A0F0A]/10">
                  {users.links.map(link => (
                    <Link key={link.label} href={getPaginatedUrl(link.url, 'users')} dangerouslySetInnerHTML={{ __html: link.label }} className={`px-3 py-1 text-xs font-mono uppercase border border-[#1A0F0A] ${link.active ? 'bg-[#1A0F0A] text-white' : 'bg-white hover:bg-[#FAF6F0]'}`} />
                  ))}
                </div>
              )}
              </>
            )}

            {activeTab === 'shops' && (
              <>
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
                  {(coffeeShops?.data || []).map((cafe) => (
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
                        <Link href={`/admin/coffee-shops/${cafe.id}/edit`} className="magnetic border border-[#1A0F0A] bg-white px-3 py-1 font-black uppercase text-[10px] hover:bg-[#1A0F0A] hover:text-[#FAF6F0]">Edit</Link>
                      </td>
                    </tr>
                  ))}
                  {(!coffeeShops?.data || coffeeShops.data.length === 0) && (
                    <tr><td colSpan="5" className="p-8 text-center text-[#1A0F0A]/50 font-bold uppercase">Data Kosong</td></tr>
                  )}
                </tbody>
              </table>
              {coffeeShops?.links && (
                <div className="flex justify-end gap-2 p-4 bg-white border-t border-[#1A0F0A]/10">
                  {coffeeShops.links.map(link => (
                    <Link key={link.label} href={getPaginatedUrl(link.url, 'shops')} dangerouslySetInnerHTML={{ __html: link.label }} className={`px-3 py-1 text-xs font-mono uppercase border border-[#1A0F0A] ${link.active ? 'bg-[#1A0F0A] text-white' : 'bg-white hover:bg-[#FAF6F0]'}`} />
                  ))}
                </div>
              )}
              </>
            )}

            {activeTab === 'communities' && (
              <>
              <table className="w-full text-left font-mono text-sm">
                <thead className="bg-[#1A0F0A] text-[#FAF6F0] text-[10px] uppercase tracking-[0.16em]">
                  <tr>
                    <th className="p-4 border-r border-[#FAF6F0]/20">Nama Komunitas</th>
                    <th className="p-4 border-r border-[#FAF6F0]/20">Ketua</th>
                    <th className="p-4 border-r border-[#FAF6F0]/20">Domisili</th>
                    <th className="p-4 border-r border-[#FAF6F0]/20">Status</th>
                    <th className="p-4">Aksi</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#1A0F0A]/10 bg-white">
                  {communities?.data?.map((kom, idx) => (
                    <tr key={kom.id} className="hover:bg-[#FAF6F0] transition-colors">
                      <td className="p-4 font-bold uppercase">{kom.nama_komunitas}</td>
                      <td className="p-4">{kom.ketua}</td>
                      <td className="p-4">{kom.domisili}</td>
                      <td className="p-4">
                        <span className={`px-2 py-1 text-[10px] uppercase font-black ${kom.status === 'aktif' ? 'bg-[#C19A6B] text-white' : 'bg-[#1A0F0A] text-white'}`}>
                          {kom.status || 'Aktif'}
                        </span>
                      </td>
                      <td className="p-4 flex gap-2">
                        {kom.status === 'pending' ? (
                          <>
                            <button
                              onClick={() => {
                                if (confirm(`Setujui pembuatan komunitas "${kom.nama_komunitas}"?`)) {
                                  router.post(`/admin/komunitas/${kom.id}/approve`);
                                }
                              }}
                              className="px-3 py-1 border border-[#1A0F0A] bg-emerald-600 hover:bg-[#1A0F0A] text-white text-[10px] font-bold uppercase transition-colors"
                            >
                              Setujui
                            </button>
                            <button
                              onClick={() => {
                                if (confirm(`Tolak dan hapus pengajuan komunitas "${kom.nama_komunitas}"?`)) {
                                  router.post(`/admin/komunitas/${kom.id}/reject`);
                                }
                              }}
                              className="px-3 py-1 border border-red-600 bg-red-600 hover:bg-[#1A0F0A] text-white text-[10px] font-bold uppercase transition-colors"
                            >
                              Tolak
                            </button>
                          </>
                        ) : (
                          <>
                            <button className="px-3 py-1 border border-[#1A0F0A] text-[10px] font-bold uppercase hover:bg-[#1A0F0A] hover:text-white transition-colors">Edit</button>
                            <button className="px-3 py-1 border border-red-600 text-red-600 text-[10px] font-bold uppercase hover:bg-red-600 hover:text-white transition-colors">Del</button>
                          </>
                        )}
                      </td>
                    </tr>
                  ))}
                  {(!communities?.data || communities.data.length === 0) && (
                    <tr><td colSpan="5" className="p-8 text-center text-[#1A0F0A]/50 font-bold uppercase">Belum ada komunitas.</td></tr>
                  )}
                </tbody>
              </table>
              {communities?.links && (
                <div className="flex justify-end gap-2 p-4 bg-white border-t border-[#1A0F0A]/10">
                  {communities.links.map(link => (
                    <Link key={link.label} href={getPaginatedUrl(link.url, 'communities')} dangerouslySetInnerHTML={{ __html: link.label }} className={`px-3 py-1 text-xs font-mono uppercase border border-[#1A0F0A] ${link.active ? 'bg-[#1A0F0A] text-white' : 'bg-white hover:bg-[#FAF6F0]'}`} />
                  ))}
                </div>
              )}
              </>
            )}
          </div>
        </div>
      </main>
    </div>
  );
}
