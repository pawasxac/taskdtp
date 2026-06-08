import React, { useEffect, useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft, CheckCircle2, LockKeyhole, Mail, Sparkles } from 'lucide-react';

export default function Login() {
  const { flash } = usePage().props;
  const { data, setData, post, processing, errors, reset } = useForm({
    login: '',
    password: '',
  });
  const [toast, setToast] = useState(null);

  useEffect(() => {
    if (flash?.success) {
      setToast({ type: 'success', message: flash.success });
    }

    if (flash?.error) {
      setToast({ type: 'error', message: flash.error });
    }
  }, [flash]);

  useEffect(() => {
    if (!toast) {
      return undefined;
    }

    const timer = window.setTimeout(() => setToast(null), 3200);
    return () => window.clearTimeout(timer);
  }, [toast]);

  const handleSubmit = (event) => {
    event.preventDefault();

    post('/login', {
      onStart: () => setToast({ type: 'success', message: 'Sip, identitas lagi dicek. Jangan kabur dulu.' }),
      onFinish: () => reset('password'),
    });
  };

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Gas Login" />

      {toast && (
        <div className="fixed right-6 top-6 z-50 w-full max-w-sm">
          <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A] transition-all duration-300 ease-out">
            <div className="flex items-start gap-3">
              <div className="relative mt-0.5">
                <span className="absolute inset-0 animate-ping rounded-full bg-[#C19A6B]/50" />
                <CheckCircle2 size={18} className="relative z-10 text-[#1A0F0A]" />
              </div>
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.22em] text-[#C19A6B]">
                  Notif Akses
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

          <div className="mt-12 space-y-5">
            <p className="font-mono text-[10px] font-black uppercase tracking-[0.24em] text-[#C19A6B]">
              Pintu Masuk Roastery Skena
            </p>
            <h1 className="font-clash text-4xl font-black uppercase leading-tight">
              Gas login,
              <br />
              jangan cuma ngintip.
            </h1>
            <p className="max-w-md text-base leading-7 text-white/76">
              Begitu masuk, kamu bisa nulis review, nimbrung komunitas, dan lanjut ngobrol tanpa mode tamu yang setengah hati.
            </p>
          </div>

          <div className="mt-10 grid gap-4">
            <div className="border-2 border-white bg-white p-5 text-[#1A0F0A]">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.22em] text-[#C19A6B]">
                Akun tester admin
              </p>
              <p className="mt-3 text-base leading-7">Email: admin@kopi.com</p>
              <p className="text-base leading-7">Password: password</p>
            </div>

            <div className="border-2 border-white bg-transparent p-5">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.22em] text-[#C19A6B]">
                Akun tester user
              </p>
              <p className="mt-3 text-base leading-7">Email: user@kopi.com</p>
              <p className="text-base leading-7">Password: password</p>
            </div>
          </div>
        </section>

        <section className="flex items-center px-6 py-12">
          <div className="w-full border-2 border-[#1A0F0A] bg-white p-6 shadow-[8px_8px_0px_0px_#1A0F0A] transition-all duration-300 ease-out md:p-8">
            <div className="mb-8 flex items-start justify-between gap-4">
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.22em] text-[#C19A6B]">
                  Validasi Identitas
                </p>
                <h2 className="mt-2 font-clash text-4xl font-black uppercase leading-tight">
                  Biar tongkronganmu
                  <br />
                  makin sah.
                </h2>
              </div>
              <div className="hidden border-2 border-[#1A0F0A] bg-[#FAF6F0] p-3 md:block">
                <Sparkles size={18} className="text-[#1A0F0A]" />
              </div>
            </div>

            <form onSubmit={handleSubmit} className="space-y-5">
              <div>
                <label className="mb-2 block font-mono text-[10px] font-black uppercase tracking-[0.22em]">
                  Email atau username
                </label>
                <div className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-white px-4 py-4">
                  <Mail size={18} />
                  <input
                    type="text"
                    value={data.login}
                    onChange={(event) => setData('login', event.target.value)}
                    className="w-full border-0 bg-transparent p-0 text-base outline-none placeholder:text-[#1A0F0A]/38"
                    placeholder="Contoh: admin@kopi.com atau anakskena"
                  />
                </div>
                {errors.login && (
                  <p className="mt-2 font-mono text-[11px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">
                    {errors.login}
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
                    placeholder="Yang bener ya, jangan ngadi-ngadi"
                  />
                </div>
                {errors.password && (
                  <p className="mt-2 font-mono text-[11px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">
                    {errors.password}
                  </p>
                )}
              </div>

              <button
                type="submit"
                disabled={processing}
                className="inline-flex w-full items-center justify-center gap-3 border-2 border-[#1A0F0A] bg-[#C19A6B] px-5 py-4 font-mono text-xs font-black uppercase tracking-[0.22em] text-[#1A0F0A] shadow-[4px_4px_0px_0px_#1A0F0A] transition-all duration-300 ease-out hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[2px_2px_0px_0px_#1A0F0A] disabled:cursor-not-allowed disabled:opacity-70"
              >
                {processing ? (
                  <>
                    <span className="inline-flex h-4 w-4 animate-pulse rounded-full border-2 border-[#1A0F0A]" />
                    Lagi ngecek akses
                  </>
                ) : (
                  'Gas Login'
                )}
              </button>
            </form>

            <div className="mt-8 flex flex-wrap items-center justify-between gap-4 border-t-2 border-[#1A0F0A] pt-5">
              <p className="text-base leading-7 text-[#1A0F0A]/68">
                Belum punya akun? Tenang, nggak perlu panik dulu.
              </p>
              <Link
                href="/register"
                className="inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#FAF6F0] px-4 py-3 font-mono text-xs font-black uppercase tracking-[0.18em] text-[#1A0F0A] shadow-[4px_4px_0px_0px_#1A0F0A] transition-all duration-300 ease-out hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[2px_2px_0px_0px_#1A0F0A]"
              >
                Daftar Dulu
              </Link>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
}
