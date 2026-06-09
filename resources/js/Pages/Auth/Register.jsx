import React, { useEffect, useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft, AtSign, CheckCircle2, LockKeyhole, Mail, Sparkles, UserRound } from 'lucide-react';

export default function Register() {
  const { flash } = usePage().props;
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
  });
  const [toast, setToast] = useState(null);

  useEffect(() => {
    if (flash?.success) setToast({ type: 'success', message: flash.success });
    if (flash?.error)   setToast({ type: 'error',   message: flash.error });
  }, [flash?.success, flash?.error]);

  useEffect(() => {
    if (!toast) return undefined;
    const timer = window.setTimeout(() => setToast(null), 3200);
    return () => window.clearTimeout(timer);
  }, [toast]);

  const handleSubmit = (event) => {
    event.preventDefault();
    post('/register', {
      onFinish: () => reset('password', 'password_confirmation'),
    });
  };

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Daftar Santai" />

      {toast && (
        <div className="fixed right-6 top-6 z-50 w-full max-w-sm">
          <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A] transition-all duration-300 ease-out">
            <div className="flex items-start gap-3">
              <div className="relative mt-0.5">
                <span className="absolute inset-0 animate-ping rounded-full bg-[#C19A6B]/50" />
                <CheckCircle2 size={18} className={`relative z-10 ${toast.type === 'error' ? 'text-red-500' : 'text-[#1A0F0A]'}`} />
              </div>
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.22em] text-[#C19A6B]">
                  {toast.type === 'error' ? 'Error' : 'Sukses'}
                </p>
                <p className="mt-1 text-sm leading-6">{toast.message}</p>
              </div>
            </div>
          </div>
        </div>
      )}

      <div className="mx-auto grid min-h-screen max-w-7xl lg:grid-cols-[0.92fr_1.08fr]">
        <section className="border-b-2 border-[#1A0F0A] bg-[#1A0F0A] px-6 py-12 text-white lg:border-b-0 lg:border-r-2">
          <Link
            href="/"
            className="inline-flex items-center gap-2 border-2 border-white bg-[#C19A6B] px-4 py-3 font-mono text-xs font-black uppercase tracking-[0.18em] text-[#1A0F0A] shadow-[4px_4px_0px_0px_#FFFFFF]"
          >
            <ArrowLeft size={16} />
            Balik Nongkrong
          </Link>

          <div className="mt-12">
            <p className="font-mono text-[10px] font-black uppercase tracking-[0.24em] text-[#C19A6B]">Bikin akses baru</p>
            <h1 className="mt-4 font-clash text-4xl font-black uppercase leading-tight">
              Daftar santai,
              <br />
              masuknya niat.
            </h1>
            <p className="mt-6 max-w-md text-base leading-7 text-white/76">
              Sekali daftar, kamu bisa langsung nulis review, masuk komunitas, dan berhenti jadi penonton pasif yang cuma mantau story orang.
            </p>
          </div>

          <div className="mt-10 border-2 border-white bg-white p-5 text-[#1A0F0A]">
            <p className="font-mono text-[10px] font-black uppercase tracking-[0.22em] text-[#C19A6B]">Catatan kecil biar mulus</p>
            <ul className="mt-3 space-y-2 text-base leading-7">
              <li>Nama wajib isi, biar nggak misterius kebangetan.</li>
              <li>Username harus unik, jangan nebeng identitas orang.</li>
              <li>Email juga harus unik, biar notifikasi nggak nyasar.</li>
              <li>Password minimal 8 karakter, jangan sependek kesabaran.</li>
            </ul>
          </div>
        </section>

        <section className="flex items-center px-6 py-12">
          <div className="w-full border-2 border-[#1A0F0A] bg-white p-6 shadow-[8px_8px_0px_0px_#1A0F0A] md:p-8">
            <div className="mb-8">
              <div className="flex items-start justify-between gap-4">
                <div>
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.22em] text-[#C19A6B]">Racik identitas</p>
                  <h2 className="mt-2 font-clash text-4xl font-black uppercase leading-tight">
                    Biar akunmu cakep
                    <br />
                    dari awal.
                  </h2>
                </div>
                <div className="hidden border-2 border-[#1A0F0A] bg-[#FAF6F0] p-3 md:block">
                  <Sparkles size={18} className="text-[#1A0F0A]" />
                </div>
              </div>
            </div>

            {flash?.error && (
              <div className="mb-4 border-2 border-[#1A0F0A] bg-[#C19A6B] p-4 font-mono text-xs font-bold uppercase tracking-[0.15em] text-[#1A0F0A]">
                {flash.error}
              </div>
            )}

            <form onSubmit={handleSubmit} className="grid gap-5 md:grid-cols-2">
              <div className="md:col-span-2">
                <label className="mb-2 block font-mono text-[10px] font-black uppercase tracking-[0.22em]">
                  Nama panggung
                </label>
                <div className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-white px-4 py-4">
                  <UserRound size={18} />
                  <input
                    type="text"
                    value={data.name}
                    onChange={(event) => setData('name', event.target.value)}
                    className="w-full border-0 bg-transparent p-0 text-base outline-none placeholder:text-[#1A0F0A]/38"
                    placeholder="Contoh: Anak Skena"
                  />
                </div>
                {errors.name && (
                  <p className="mt-2 font-mono text-[11px] font-black uppercase tracking-[0.15em] text-[#C19A6B]">
                    {errors.name}
                  </p>
                )}
              </div>

              <div>
                <label className="mb-2 block font-mono text-[10px] font-black uppercase tracking-[0.22em]">
                  Username
                </label>
                <div className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-white px-4 py-4">
                  <AtSign size={18} />
                  <input
                    type="text"
                    value={data.username}
                    onChange={(event) => setData('username', event.target.value)}
                    className="w-full border-0 bg-transparent p-0 text-base outline-none placeholder:text-[#1A0F0A]/38"
                    placeholder="Misal: anakskena"
                  />
                </div>
                {errors.username && (
                  <p className="mt-2 font-mono text-[11px] font-black uppercase tracking-[0.15em] text-[#C19A6B]">
                    {errors.username}
                  </p>
                )}
              </div>

              <div>
                <label className="mb-2 block font-mono text-[10px] font-black uppercase tracking-[0.22em]">
                  Email
                </label>
                <div className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-white px-4 py-4">
                  <Mail size={18} />
                  <input
                    type="email"
                    value={data.email}
                    onChange={(event) => setData('email', event.target.value)}
                    className="w-full border-0 bg-transparent p-0 text-base outline-none placeholder:text-[#1A0F0A]/38"
                    placeholder="Contoh: user@kopi.com"
                  />
                </div>
                {errors.email && (
                  <p className="mt-2 font-mono text-[11px] font-black uppercase tracking-[0.15em] text-[#C19A6B]">
                    {errors.email}
                  </p>
                )}
              </div>

              <div>
                <label className="mb-2 block font-mono text-[10px] font-black uppercase tracking-[0.22em]">
                  Password
                </label>
                <div className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-white px-4 py-4">
                  <LockKeyhole size={18} />
                  <input
                    type="password"
                    value={data.password}
                    onChange={(event) => setData('password', event.target.value)}
                    className="w-full border-0 bg-transparent p-0 text-base outline-none placeholder:text-[#1A0F0A]/38"
                    placeholder="Minimal 8 karakter, jangan ngasal"
                  />
                </div>
                {errors.password && (
                  <p className="mt-2 font-mono text-[11px] font-black uppercase tracking-[0.15em] text-[#C19A6B]">
                    {errors.password}
                  </p>
                )}
              </div>

              <div>
                <label className="mb-2 block font-mono text-[10px] font-black uppercase tracking-[0.22em]">
                  Ulang password
                </label>
                <div className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-white px-4 py-4">
                  <LockKeyhole size={18} />
                  <input
                    type="password"
                    value={data.password_confirmation}
                    onChange={(event) => setData('password_confirmation', event.target.value)}
                    className="w-full border-0 bg-transparent p-0 text-base outline-none placeholder:text-[#1A0F0A]/38"
                    placeholder="Biar nggak typo pas gas daftar"
                  />
                </div>
              </div>

              <div className="md:col-span-2">
                <button
                  type="submit"
                  disabled={processing}
                  className="w-full border-2 border-[#1A0F0A] bg-[#C19A6B] px-5 py-4 font-mono text-xs font-black uppercase tracking-[0.22em] text-[#1A0F0A] shadow-[4px_4px_0px_0px_#1A0F0A] transition-all duration-300 ease-out hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[2px_2px_0px_0px_#1A0F0A] disabled:cursor-not-allowed disabled:opacity-70"
                >
                  {processing ? 'Lagi ngeracik akun...' : 'Gas Daftar'}
                </button>
              </div>
            </form>

            <div className="mt-8 flex flex-wrap items-center justify-between gap-3 border-t-2 border-[#1A0F0A] pt-5">
              <p className="text-base leading-7 text-[#1A0F0A]/68">
                Sudah punya akses? Tinggal masuk, nggak usah muter dulu.
              </p>
              <Link
                href="/login"
                className="inline-flex items-center justify-center border-2 border-[#1A0F0A] bg-[#FAF6F0] px-4 py-3 font-mono text-xs font-black uppercase tracking-[0.18em] text-[#1A0F0A] shadow-[4px_4px_0px_0px_#1A0F0A] transition-all duration-300 ease-out hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[2px_2px_0px_0px_#1A0F0A]"
              >
                Gas Login
              </Link>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
}
