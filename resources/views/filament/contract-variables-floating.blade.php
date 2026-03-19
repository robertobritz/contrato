@php
    use App\Models\Client;
    $variables = Client::availableVariableLabels();
@endphp

<script>
    function contratoCopyVar(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).catch(function() {
                contratoCopyLegacy(text);
            });
            return;
        }
        contratoCopyLegacy(text);
    }

    function contratoCopyLegacy(text) {
        var el = document.createElement('input');
        el.setAttribute('type', 'text');
        el.setAttribute('value', text);
        el.setAttribute('readonly', '');
        el.style.cssText = 'position:fixed;top:0;left:0;width:2px;height:2px;opacity:0.01;z-index:99999;';
        document.body.appendChild(el);
        el.focus({
            preventScroll: true
        });
        if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
            var r = document.createRange();
            r.selectNodeContents(el);
            var s = window.getSelection();
            if (s) {
                s.removeAllRanges();
                s.addRange(r);
            }
            el.setSelectionRange(0, 999999);
        } else {
            el.select();
            el.setSelectionRange(0, 999999);
        }
        try {
            document.execCommand('copy');
        } catch (e) {}
        document.body.removeChild(el);
    }
</script>

<div x-data="{ open: false }" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;">

    {{-- Painel de variáveis --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform translateY(8px)"
        x-transition:enter-end="opacity-100 transform translateY(0)" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.outside="open = false"
        style="position:absolute;bottom:4.5rem;right:0;width:22rem;border-radius:0.75rem;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.12);">
        {{-- Cabeçalho --}}
        <div style="padding:0.75rem 1rem;border-bottom:1px solid rgba(255,255,255,0.08);background:#1f2937;">
            <p style="margin:0;font-size:0.85rem;font-weight:600;color:#f9fafb;">Variáveis de Cliente Disponíveis</p>
            <p style="margin:0.25rem 0 0;font-size:0.75rem;color:#9ca3af;">Clique em copiar para fechar e inserir no
                contrato</p>
        </div>
        {{-- Lista --}}
        <div style="max-height:22rem;overflow-y:auto;background:#111827;">
            @foreach ($variables as $variable => $label)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.6rem 1rem;border-bottom:1px solid rgba(255,255,255,0.06);">
                    <span
                        style="font-size:0.8rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $label }}</span>
                    <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                        <code
                            style="font-family:monospace;font-size:0.78rem;font-weight:600;color:#f59e0b;background:rgba(245,158,11,0.1);padding:0.15rem 0.4rem;border-radius:4px;">{{ $variable }}</code>
                        <button type="button" title="Copiar variável" data-variable="{{ $variable }}"
                            @click.stop="open = false; contratoCopyVar($el.dataset.variable)"
                            style="display:flex;align-items:center;justify-content:center;background:none;border:none;padding:6px;cursor:pointer;color:#6b7280;border-radius:4px;min-width:32px;min-height:32px;"
                            onmouseover="this.style.color='#f59e0b'" onmouseout="this.style.color='#6b7280'">
                            <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-4 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Botão circular flutuante --}}
    <button type="button" @click="open = !open" title="Variáveis disponíveis"
        style="width:3.5rem;height:3.5rem;border-radius:50%;background:#f59e0b;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(245,158,11,0.5);transition:background 0.2s,transform 0.2s;"
        onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">
        <svg x-show="!open" style="width:22px;height:22px;color:#fff;" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
        </svg>
        <svg x-show="open" style="width:22px;height:22px;color:#fff;" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
