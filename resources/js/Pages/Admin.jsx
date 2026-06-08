import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { ShieldAlert, MapPin } from 'lucide-react';
import Navbar from '../Components/Navbar';

export default function Admin({ users = [], coffeeShops = [], communities = [] }) {
  const { auth } = usePage().props;
  const user = auth?.user || null;

  if (!user || user.role !== 'admin') return null;

  return (
    <div className="min-h-screen bg-[#1A0F0A] text-[#FAF6F0] selection:bg-[#FAF6F0] selection:text-[#1A0F0A]">
      <Head title="Admin Panel" />
      <Navbar current="admin" />
      <main className="px-4 py-6 md:px-8">
        <div className="mx-auto max-w-7xl space-y-4">
          <header className="border-2 border-[#FAF6F0] bg-[#1A0F0A] p-4 shadow-[6px_6px_0px_0px_#C19A6B]">
            <div className="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">
                  Pusat Kontrol
                </p>
                <h1 className="mt-2 break-words font-clash text-2xl font-black uppercase md:text-3xl">Kontrol Panel Admin</h1>
                <p className="mt-2 text-sm leading-6 text-white/75 md:text-base">
                  Kelola pengguna, coffee shop, dan data komunitas tanpa layout yang meledak.
                </p>
              </div>
              <Link
                href="/dashboard"
                className="inline-flex items-center justify-center border-2 border-[#FAF6F0] bg-white px-4 py-2.5 font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#1A0F0A] shadow-[3px_3px_0px_0px_#C19A6B]"
              >
                Kembali ke Dashboard
              </Link>
            </div>
          </header>

          <div className="grid gap-4 2xl:grid-cols-2">
            <div className="border-2 border-white/80 bg-white/5">
              <div className="border-b-2 border-white/20 bg-white/10 p-4 font-mono text-[10px] font-bold uppercase tracking-[0.18em] text-white">
                <div className="flex items-center gap-3">
                  <ShieldAlert size={16} className="text-[#C19A6B]" /> Data Pengguna Aktif
                </div>
              </div>
              <div className="overflow-x-auto">
                <table className="min-w-full font-mono text-[11px] text-left">
                  <thead className="border-b border-white/20 bg-black/20 text-white/60">
                    <tr>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Nama</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Role</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em] text-center">Reports</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    {users.map((usr, i) => (
                      <tr key={usr.id || i} className="border-b border-white/10">
                        <td className="max-w-[200px] truncate p-3 font-bold uppercase">{usr.name || usr.username || `USER_${i}`}</td>
                        <td className="p-3 font-bold text-[#C19A6B]">{usr.role || 'user'}</td>
                        <td className="p-3 text-center font-bold">0</td>
                        <td className="p-3">
                          <button className="font-bold uppercase text-white/70 transition-colors hover:text-white">Lihat</button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            <div className="border-2 border-white/80 bg-white/5">
              <div className="border-b-2 border-white/20 bg-white/10 p-4 font-mono text-[10px] font-bold uppercase tracking-[0.18em] text-white">
                <div className="flex items-center gap-3">
                  <MapPin size={16} className="text-[#C19A6B]" /> Direktori Spaces
                </div>
              </div>
              <div className="overflow-x-auto">
                <table className="min-w-[760px] w-full font-mono text-[11px] text-left">
                  <thead className="border-b border-white/20 bg-black/20 text-white/60">
                    <tr>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Shop</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Region</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Address</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Hours</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Price</th>
                      <th className="p-3 font-bold uppercase tracking-[0.14em]">Lat/Long</th>
                    </tr>
                  </thead>
                  <tbody>
                    {coffeeShops.map((cafe) => (
                      <tr key={cafe.id} className="border-b border-white/10">
                        <td className="max-w-[180px] truncate p-3 font-bold uppercase text-[#C19A6B]">{cafe.nama}</td>
                        <td className="p-3 font-bold">{cafe.district_name || cafe.kecamatan?.name || cafe.kecamatan || cafe.daerah}</td>
                        <td className="max-w-[240px] truncate p-3 font-bold" title={cafe.alamat}>{cafe.alamat}</td>
                        <td className="whitespace-nowrap p-3 font-bold">{cafe.jam_buka} - {cafe.jam_tutup}</td>
                        <td className="whitespace-nowrap p-3 font-bold">Rp{cafe.harga_min} - {cafe.harga_max}</td>
                        <td className="whitespace-nowrap p-3 font-bold">{cafe.latitude}, {cafe.longitude}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
