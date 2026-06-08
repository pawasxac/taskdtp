import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { ShieldAlert, Terminal } from 'lucide-react';
import Navbar from '../../Components/Navbar';

export default function Gateway() {
  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#1A0F0A] selection:text-[#FAF6F0]">
      <Head title="Admin Gateway" />
      <Navbar current="admin" />
      <div className="container mx-auto flex min-h-[calc(100vh-76px)] max-w-7xl items-center justify-center px-4 py-6 md:px-8">
        <div className="w-full max-w-3xl border-2 border-[#1A0F0A] bg-white p-5 shadow-[6px_6px_0px_0px_#1A0F0A] md:p-6">
          <div className="mb-5 flex items-center gap-3 border-b-2 border-[#1A0F0A] pb-3 font-mono text-[10px] font-bold uppercase tracking-[0.18em]">
            <ShieldAlert className="text-[#C19A6B]" />
            <span>Admin Gate // Akses Bebas</span>
          </div>

          <h1 className="font-clash text-3xl font-black uppercase leading-tight md:text-4xl">Pilih Mode Akses.</h1>
          <p className="mt-3 max-w-2xl text-sm leading-6 text-[#1A0F0A]/70 md:text-base">
            Mau turun ke dashboard buat nimbrung santai, atau langsung gas ke panel manajemen? Dua-duanya sekarang bisa
            diakses tanpa bikin kamu nyangkut.
          </p>

          <div className="mt-5 flex flex-col gap-4">
            <Link
              href="/dashboard"
              className="group flex items-center justify-between border-2 border-[#1A0F0A] p-4 shadow-[3px_3px_0px_0px_#1A0F0A] transition-all hover:-translate-y-0.5 hover:translate-x-0.5 hover:bg-[#1A0F0A] hover:text-[#FAF6F0]"
            >
              <div>
                <h2 className="font-clash text-xl font-black uppercase md:text-2xl">Gate A: Dashboard</h2>
                <p className="mt-2 font-mono text-[10px] font-bold uppercase tracking-[0.14em] opacity-80">
                  Balik ke ruang ngobrol, DM, forum, dan notifikasi.
                </p>
              </div>
              <Terminal size={26} className="hidden md:block opacity-40 transition-opacity group-hover:opacity-100" />
            </Link>

            <Link
              href="/admin/management"
              className="group flex items-center justify-between border-2 border-[#1A0F0A] p-4 shadow-[3px_3px_0px_0px_#C19A6B] transition-all hover:-translate-y-0.5 hover:translate-x-0.5 hover:bg-[#C19A6B]"
            >
              <div>
                <h2 className="font-clash text-xl font-black uppercase md:text-2xl">Gate B: Management</h2>
                <p className="mt-2 font-mono text-[10px] font-bold uppercase tracking-[0.14em] opacity-80">
                  Buka panel admin buat ngurus data coffee shop, komunitas, dan user.
                </p>
              </div>
              <ShieldAlert size={26} className="hidden md:block opacity-40 transition-opacity group-hover:opacity-100" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
