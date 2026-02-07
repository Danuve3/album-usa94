<div class="mx-auto max-w-md" x-data="redeemConfetti()" x-on:code-redeemed.window="launch()" x-on:code-error.window="shake()">
    <canvas x-ref="confettiCanvas" class="pointer-events-none fixed inset-0 z-[9999]" style="display:none"></canvas>
    <div x-ref="card" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-5 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/40">
                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Canjear código</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Introduce un código promocional para recibir sobres</p>
        </div>

        <form wire:submit="redeem" class="space-y-4">
            <div>
                <input
                    type="text"
                    wire:model="code"
                    placeholder="Escribe tu código aquí..."
                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 text-center text-lg font-mono tracking-widest uppercase placeholder:text-sm placeholder:font-sans placeholder:normal-case placeholder:tracking-normal text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-emerald-500 dark:focus:bg-gray-700"
                    autocomplete="off"
                >
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-800"
            >
                <span wire:loading.remove>Canjear</span>
                <span wire:loading class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Canjeando...
                </span>
            </button>
        </form>

        @if ($successMessage)
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $successMessage }}
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $errorMessage }}
                </div>
            </div>
        @endif
    </div>

    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 50%, 90% { transform: translateX(-4px); }
            30%, 70% { transform: translateX(4px); }
        }
        .animate-shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>

    <script>
        function redeemConfetti() {
            return {
                shake() {
                    const card = this.$refs.card;
                    card.classList.remove('animate-shake');
                    void card.offsetWidth;
                    card.classList.add('animate-shake');
                    card.addEventListener('animationend', () => card.classList.remove('animate-shake'), { once: true });
                },
                launch() {
                    const canvas = this.$refs.confettiCanvas;
                    canvas.style.display = 'block';
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                    const ctx = canvas.getContext('2d');

                    const colors = ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
                    const pieces = [];
                    const count = 120;

                    for (let i = 0; i < count; i++) {
                        pieces.push({
                            x: canvas.width * 0.5 + (Math.random() - 0.5) * canvas.width * 0.4,
                            y: canvas.height * 0.4,
                            vx: (Math.random() - 0.5) * 16,
                            vy: -(Math.random() * 14 + 4),
                            w: Math.random() * 8 + 4,
                            h: Math.random() * 6 + 3,
                            color: colors[Math.floor(Math.random() * colors.length)],
                            rotation: Math.random() * 360,
                            rotSpeed: (Math.random() - 0.5) * 12,
                            gravity: 0.25 + Math.random() * 0.15,
                            opacity: 1,
                        });
                    }

                    let frame;
                    const animate = () => {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        let alive = false;

                        pieces.forEach(p => {
                            p.vy += p.gravity;
                            p.x += p.vx;
                            p.y += p.vy;
                            p.rotation += p.rotSpeed;
                            p.vx *= 0.99;

                            if (p.y > canvas.height - 40) {
                                p.opacity -= 0.02;
                            }

                            if (p.opacity <= 0) return;
                            alive = true;

                            ctx.save();
                            ctx.globalAlpha = Math.max(0, p.opacity);
                            ctx.translate(p.x, p.y);
                            ctx.rotate((p.rotation * Math.PI) / 180);
                            ctx.fillStyle = p.color;
                            ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                            ctx.restore();
                        });

                        if (alive) {
                            frame = requestAnimationFrame(animate);
                        } else {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            canvas.style.display = 'none';
                        }
                    };

                    frame = requestAnimationFrame(animate);
                }
            }
        }
    </script>
</div>
