import React, { useState } from 'react';
import { X, MessageCircle, Instagram, Phone, Gamepad2, Check } from 'lucide-react';

export default function PublicProfileModal({ user, isOpen, onClose, onDmClick }) {
  const [discordCopied, setDiscordCopied] = useState(false);
  if (!isOpen || !user) return null;

  const getAvatarUrl = (u) => {
    if (u?.profile_picture?.startsWith('http')) return u.profile_picture;
    if (u?.profile_picture) return `/uploads/profile_pictures/${u.profile_picture}`;
    const label = encodeURIComponent(u?.name || u?.username || 'Anak Skena');
    return `https://ui-avatars.com/api/?name=${label}&background=1A0F0A&color=FAF6F0&bold=true`;
  };

  const btnPrimary =
    'magnetic flex-1 inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-4 py-3 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] transition-all duration-300 hover:bg-[#1A0F0A] hover:text-[#FAF6F0]';
  const btnSocial =
    'inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-white p-3 hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors';

  return (
    <div
      className="fixed inset-0 z-[200] flex items-center justify-center bg-[#1A0F0A]/80 backdrop-blur-sm p-4 animate-in fade-in duration-200"
      onClick={onClose}
    >
      <div
        className="w-full max-w-md bg-[#FAF6F0] border-2 border-[#1A0F0A] shadow-[8px_8px_0px_0px_#C19A6B] overflow-hidden animate-in zoom-in-95 slide-in-from-bottom-4 duration-300"
        onClick={(e) => e.stopPropagation()}
      >
          {/* Header Cover Area */}
          <div className="h-24 bg-[#1A0F0A] relative flex justify-end p-4">
            <button onClick={onClose} className="text-[#FAF6F0] hover:text-[#C19A6B] transition-colors bg-black/20 p-1 rounded-full backdrop-blur-sm h-fit">
              <X size={20} />
            </button>
            <div className="absolute -bottom-12 left-6 border-4 border-[#FAF6F0] bg-white rounded-none">
              <img src={getAvatarUrl(user)} alt={user.name} className="w-24 h-24 object-cover" />
            </div>
          </div>

          {/* Profile Info */}
          <div className="pt-16 pb-6 px-6">
            <div className="mb-6">
              <h2 className="font-clash text-2xl font-black uppercase leading-tight">{user.name || user.username}</h2>
              <p className="font-mono text-sm text-[#1A0F0A]/60">@{user.username || (user.name || 'user').toLowerCase().replace(/\s+/g, '')}</p>
            </div>

            {user.bio && (
              <div className="mb-6 border-l-2 border-[#C19A6B] pl-4">
                <p className="text-sm italic text-[#1A0F0A]/80">"{user.bio}"</p>
              </div>
            )}

            <div className="space-y-4">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B] border-b-2 border-[#1A0F0A]/10 pb-2">Koneksi Skena</p>
              
              <div className="flex gap-3">
                <button onClick={() => { onClose(); onDmClick(user); }} className={btnPrimary}>
                  <MessageCircle size={16} /> Kirim DM
                </button>
              </div>

              <div className="grid grid-cols-3 gap-3 pt-2">
                {user.instagram ? (
                  <a href={`https://instagram.com/${user.instagram.replace('@', '')}`} target="_blank" rel="noreferrer" className={btnSocial} title="Instagram">
                    <Instagram size={20} />
                  </a>
                ) : (
                  <div className={`${btnSocial} opacity-50 cursor-not-allowed`} title="Belum link IG"><Instagram size={20} /></div>
                )}
                
                {user.whatsapp ? (
                  <a href={`https://wa.me/${user.whatsapp.replace(/^0/, '62').replace(/\D/g, '')}`} target="_blank" rel="noreferrer" className={btnSocial} title="WhatsApp">
                    <Phone size={20} />
                  </a>
                ) : (
                  <div className={`${btnSocial} opacity-50 cursor-not-allowed`} title="Belum link WA"><Phone size={20} /></div>
                )}
                
                {user.discord ? (
                  <button
                    className={discordCopied ? `${btnSocial} bg-green-50 text-green-700 border-green-400` : btnSocial}
                    onClick={() => {
                      navigator.clipboard.writeText(user.discord).then(() => {
                        setDiscordCopied(true);
                        setTimeout(() => setDiscordCopied(false), 2000);
                      }).catch(() => {
                        // fallback for browsers without clipboard API
                        const ta = document.createElement('textarea');
                        ta.value = user.discord;
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                        setDiscordCopied(true);
                        setTimeout(() => setDiscordCopied(false), 2000);
                      });
                    }}
                    title={discordCopied ? 'Discord ID tersalin!' : `Salin Discord: ${user.discord}`}
                  >
                    {discordCopied ? <Check size={20} className="text-green-600" /> : <Gamepad2 size={20} />}
                  </button>
                ) : (
                  <div className={`${btnSocial} opacity-50 cursor-not-allowed`} title="Belum link Discord"><Gamepad2 size={20} /></div>
                )}
              </div>
            </div>
          </div>
      </div>
    </div>
  );
}
