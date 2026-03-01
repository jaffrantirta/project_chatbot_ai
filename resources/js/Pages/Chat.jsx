import { useState, useRef, useEffect, useCallback } from 'react';
import axios from 'axios';
import ReactMarkdown from 'react-markdown';
import remarkGfm from 'remark-gfm';

// ── Icons ─────────────────────────────────────────────────────────────────────

const SendIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-5 h-5">
        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
    </svg>
);

const BotIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-4 h-4">
        <path d="M16.5 7.5h-9v9h9v-9z" />
        <path fillRule="evenodd" d="M8.25 2.25A.75.75 0 019 3v.75h2.25V3a.75.75 0 011.5 0v.75H15V3a.75.75 0 011.5 0v.75h.75a3 3 0 013 3v.75H21A.75.75 0 0121 9h-.75v2.25H21a.75.75 0 010 1.5h-.75V15H21a.75.75 0 010 1.5h-.75v.75a3 3 0 01-3 3h-.75V21a.75.75 0 01-1.5 0v-.75h-2.25V21a.75.75 0 01-1.5 0v-.75H9V21a.75.75 0 01-1.5 0v-.75h-.75a3 3 0 01-3-3v-.75H3A.75.75 0 013 15h.75v-2.25H3a.75.75 0 010-1.5h.75V9H3a.75.75 0 010-1.5h.75v-.75a3 3 0 013-3h.75V3a.75.75 0 01.75-.75zM6 6.75A.75.75 0 016.75 6h10.5a.75.75 0 01.75.75v10.5a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75V6.75z" clipRule="evenodd" />
    </svg>
);

const UserIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="w-4 h-4">
        <path fillRule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clipRule="evenodd" />
    </svg>
);

// ── Typing indicator ───────────────────────────────────────────────────────────

function TypingDots() {
    return (
        <div className="flex gap-1 items-center py-1">
            {[0, 1, 2].map(i => (
                <span
                    key={i}
                    className="w-2 h-2 rounded-full bg-gray-400 animate-bounce"
                    style={{ animationDelay: `${i * 0.15}s` }}
                />
            ))}
        </div>
    );
}

// ── Message bubble ─────────────────────────────────────────────────────────────

function MarkdownContent({ content }) {
    return (
        <ReactMarkdown
            remarkPlugins={[remarkGfm]}
            components={{
                p:      ({ children }) => <p className="mb-2 last:mb-0">{children}</p>,
                strong: ({ children }) => <strong className="font-semibold">{children}</strong>,
                em:     ({ children }) => <em className="italic">{children}</em>,
                ul:     ({ children }) => <ul className="list-disc list-inside mb-2 space-y-0.5">{children}</ul>,
                ol:     ({ children }) => <ol className="list-decimal list-inside mb-2 space-y-0.5">{children}</ol>,
                li:     ({ children }) => <li className="leading-relaxed">{children}</li>,
                h1:     ({ children }) => <h1 className="text-base font-bold mb-1 mt-2">{children}</h1>,
                h2:     ({ children }) => <h2 className="text-sm font-bold mb-1 mt-2">{children}</h2>,
                h3:     ({ children }) => <h3 className="text-sm font-semibold mb-1 mt-2">{children}</h3>,
                code:   ({ inline, children }) => inline
                    ? <code className="bg-gray-100 text-emerald-700 px-1 py-0.5 rounded text-xs font-mono">{children}</code>
                    : <code className="block bg-gray-100 text-gray-800 p-2 rounded-lg text-xs font-mono overflow-x-auto my-2 whitespace-pre">{children}</code>,
                pre:    ({ children }) => <>{children}</>,
                blockquote: ({ children }) => <blockquote className="border-l-2 border-emerald-300 pl-3 text-gray-600 italic my-2">{children}</blockquote>,
                a:      ({ href, children }) => <a href={href} target="_blank" rel="noreferrer" className="text-emerald-600 underline underline-offset-2 hover:text-emerald-800">{children}</a>,
                hr:     () => <hr className="border-gray-200 my-2" />,
                table:  ({ children }) => <div className="overflow-x-auto my-2"><table className="text-xs border-collapse w-full">{children}</table></div>,
                th:     ({ children }) => <th className="border border-gray-200 bg-gray-50 px-2 py-1 text-left font-semibold">{children}</th>,
                td:     ({ children }) => <td className="border border-gray-200 px-2 py-1">{children}</td>,
            }}
        >
            {content}
        </ReactMarkdown>
    );
}

function Message({ message }) {
    const isUser = message.role === 'user';
    return (
        <div className={`flex items-end gap-2 ${isUser ? 'justify-end' : 'justify-start'}`}>
            {!isUser && (
                <div className="shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-600">
                    <BotIcon />
                </div>
            )}
            <div className="max-w-[78%]">
                <div className={`px-4 py-2.5 text-sm leading-relaxed shadow-sm rounded-2xl ${
                    isUser
                        ? 'bg-emerald-600 text-white rounded-br-sm'
                        : 'bg-white text-gray-800 border border-gray-100 rounded-bl-sm'
                }`}>
                    {isUser ? (
                        message.content.split('\n').map((line, i, arr) => (
                            <span key={i}>{line}{i < arr.length - 1 && <br />}</span>
                        ))
                    ) : (
                        <MarkdownContent content={message.content} />
                    )}
                </div>
                <p className={`mt-1 text-[10px] text-gray-400 ${isUser ? 'text-right pr-1' : 'text-left pl-1'}`}>
                    {message.created_at}
                    {message.tokens && <span> · {message.tokens} tok</span>}
                </p>
            </div>
            {isUser && (
                <div className="shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 text-gray-500">
                    <UserIcon />
                </div>
            )}
        </div>
    );
}

// ── Main page ──────────────────────────────────────────────────────────────────

export default function Chat({ session, messages: initialMessages }) {
    const [messages, setMessages]   = useState(initialMessages ?? []);
    const [input, setInput]         = useState('');
    const [sending, setSending]     = useState(false);
    const [error, setError]         = useState('');
    const [tokensUsed, setTokensUsed] = useState(session.tokens_used ?? 0);

    const bottomRef   = useRef(null);
    const textareaRef = useRef(null);
    const isClosed    = session.status === 'closed';

    // Auto-scroll on new messages
    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, sending]);

    // Auto-grow textarea
    const autoGrow = useCallback(() => {
        const el = textareaRef.current;
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 140) + 'px';
    }, []);

    const send = useCallback(async () => {
        const text = input.trim();
        if (!text || sending || isClosed) return;

        const optimistic = {
            id: Date.now(),
            role: 'user',
            content: text,
            created_at: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        };

        setMessages(prev => [...prev, optimistic]);
        setInput('');
        setSending(true);
        setError('');

        if (textareaRef.current) textareaRef.current.style.height = 'auto';

        try {
            const { data } = await axios.post(
                `/chat/${session.token}/send`,
                { message: text },
                { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content } }
            );
            setMessages(prev => [...prev, data.message]);
            setTokensUsed(data.tokens_used ?? tokensUsed);
        } catch (err) {
            setError(err.response?.data?.error ?? 'Gagal mengirim pesan. Coba lagi.');
            // Remove optimistic message on failure
            setMessages(prev => prev.filter(m => m.id !== optimistic.id));
        } finally {
            setSending(false);
            textareaRef.current?.focus();
        }
    }, [input, sending, isClosed, session.token, tokensUsed]);

    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send();
        }
    };

    return (
        <div className="flex flex-col h-screen bg-gray-50">

            {/* ── Header ───────────────────────────────────────────────── */}
            <header className="shrink-0 bg-white border-b border-gray-200 shadow-sm">
                <div className="max-w-3xl mx-auto px-4 py-3 flex items-center gap-3">
                    <div className="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500 text-white shadow">
                        <BotIcon />
                    </div>
                    <div className="flex-1 min-w-0">
                        <h1 className="text-sm font-semibold text-gray-900 truncate">
                            {session.title}
                        </h1>
                        <div className="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
                            <span className="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{session.model}</span>
                            {session.farm && <span>🏠 {session.farm}</span>}
                            {session.chicken && <span>🐔 {session.chicken}</span>}
                        </div>
                    </div>
                    <div className="shrink-0 flex items-center gap-2">
                        <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${
                            isClosed
                                ? 'bg-gray-100 text-gray-500'
                                : 'bg-emerald-100 text-emerald-700'
                        }`}>
                            <span className={`w-1.5 h-1.5 rounded-full ${isClosed ? 'bg-gray-400' : 'bg-emerald-500'}`} />
                            {isClosed ? 'Ditutup' : 'Aktif'}
                        </span>
                        <span className="text-xs text-gray-400 hidden sm:block">
                            {tokensUsed.toLocaleString()} tok
                        </span>
                    </div>
                </div>
            </header>

            {/* ── Closed banner ─────────────────────────────────────────── */}
            {isClosed && (
                <div className="shrink-0 bg-amber-50 border-b border-amber-200 px-4 py-2.5 text-sm text-amber-800 text-center">
                    ⚠️ Sesi ini telah <strong>ditutup</strong> dan tidak menerima pesan baru.
                </div>
            )}

            {/* ── Messages ──────────────────────────────────────────────── */}
            <main className="flex-1 overflow-y-auto">
                <div className="max-w-3xl mx-auto px-4 py-6 space-y-4">

                    {messages.length === 0 && !sending && (
                        <div className="flex flex-col items-center justify-center py-24 text-center text-gray-400 select-none">
                            <div className="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4 text-emerald-500">
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" strokeWidth={1.5} viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                            </div>
                            <p className="font-medium text-gray-500">Mulai percakapan</p>
                            <p className="text-xs mt-1">Tanyakan apa saja tentang kesehatan ayam Anda</p>
                        </div>
                    )}

                    {messages.map(msg => (
                        <Message key={msg.id} message={msg} />
                    ))}

                    {sending && (
                        <div className="flex items-end gap-2 justify-start">
                            <div className="shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-600">
                                <BotIcon />
                            </div>
                            <div className="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                                <TypingDots />
                            </div>
                        </div>
                    )}

                    <div ref={bottomRef} />
                </div>
            </main>

            {/* ── Error ─────────────────────────────────────────────────── */}
            {error && (
                <div className="shrink-0 max-w-3xl mx-auto w-full px-4 pb-2">
                    <div className="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-xs px-3 py-2 rounded-lg">
                        <svg className="w-4 h-4 shrink-0" fill="none" stroke="currentColor" strokeWidth={2} viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        {error}
                        <button onClick={() => setError('')} className="ml-auto text-red-400 hover:text-red-600">✕</button>
                    </div>
                </div>
            )}

            {/* ── Input area ────────────────────────────────────────────── */}
            <footer className="shrink-0 bg-white border-t border-gray-200">
                <div className="max-w-3xl mx-auto px-4 py-3">
                    <div className={`flex items-end gap-2 rounded-xl border bg-white px-3 py-2 transition-shadow ${
                        isClosed ? 'opacity-60' : 'focus-within:ring-2 focus-within:ring-emerald-500 focus-within:border-emerald-500'
                    } border-gray-300`}>
                        <textarea
                            ref={textareaRef}
                            value={input}
                            onChange={e => { setInput(e.target.value); autoGrow(); }}
                            onKeyDown={handleKeyDown}
                            disabled={isClosed || sending}
                            placeholder={isClosed ? 'Sesi telah ditutup.' : 'Tanya tentang kesehatan ayam… (Enter kirim · Shift+Enter baris baru)'}
                            rows={1}
                            className="flex-1 resize-none bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none disabled:cursor-not-allowed"
                            style={{ maxHeight: '140px', overflowY: 'auto' }}
                        />
                        <button
                            onClick={send}
                            disabled={!input.trim() || sending || isClosed}
                            className="shrink-0 flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-600 text-white transition hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed active:scale-95"
                            title="Kirim (Enter)"
                        >
                            {sending ? (
                                <svg className="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            ) : (
                                <SendIcon />
                            )}
                        </button>
                    </div>
                    <p className="mt-1.5 text-center text-[10px] text-gray-400">
                        AI dapat membuat kesalahan · Verifikasi saran medis dengan dokter hewan
                    </p>
                </div>
            </footer>

        </div>
    );
}
