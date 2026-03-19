<div
    class="divide-y divide-gray-100 dark:divide-white/10 rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
    @foreach ($variables as $variable => $label)
        <div class="flex items-center justify-between gap-4 px-4 py-2.5 bg-white dark:bg-gray-900">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</span>
            <div style="display:flex;align-items:center;gap:8px;">
                <code
                    class="font-mono text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-500/10 px-2 py-0.5 rounded">{{ $variable }}</code>
                <button
                    type="button"
                    title="Copiar variável"
                    data-var="{{ $variable }}"
                    onclick="
                        var text = this.dataset.var;
                        var s = this.querySelectorAll('svg');
                        var btn = this;
                        var success = function() {
                            s[0].style.display = 'none';
                            s[1].style.display = 'block';
                            setTimeout(function() {
                                s[0].style.display = 'block';
                                s[1].style.display = 'none';
                            }, 1500);
                        };
                        if (navigator.clipboard && window.isSecureContext) {
                            navigator.clipboard.writeText(text).then(success);
                        } else {
                            var el = document.createElement('textarea');
                            el.value = text;
                            el.style.position = 'absolute';
                            el.style.left = '-9999px';
                            document.body.appendChild(el);
                            el.select();
                            document.execCommand('copy');
                            document.body.removeChild(el);
                            success();
                        }
                    "
                    style="display:flex;align-items:center;background:none;border:none;padding:2px;cursor:pointer;color:#9ca3af;border-radius:4px;"
                    onmouseover="this.style.color='#6366f1'"
                    onmouseout="this.style.color='#9ca3af'"
                >
                    <svg style="width:16px;height:16px;display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-4 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <svg style="width:16px;height:16px;display:none;color:#22c55e;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </div>
    @endforeach
</div>
