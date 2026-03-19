@php
    use App\Models\Contratante;
    use App\Models\Contratado;
    use App\Models\ObjetoContrato;
    $contratanteVars = Contratante::availableVariableLabels();
    $contratadoVars = Contratado::availableVariableLabels();
    $objetoVars = ObjetoContrato::availableVariableLabels();
    $allVariables = array_merge($contratanteVars, $contratadoVars, $objetoVars);
    $initialCounts = array_fill_keys(array_keys($allVariables), 0);
@endphp

<div x-data="{
    open: false,
    savedRange: null,
    sectionContratante: true,
    sectionContratado: false,
    sectionObjeto: false,
    counts: {{ \Illuminate\Support\Js::from($initialCounts) }},
    saveEditorSelection() {
        const sel = window.getSelection();
        if (sel && sel.rangeCount > 0) {
            this.savedRange = sel.getRangeAt(0).cloneRange();
        }
    },
    insertVariable(text) {
        this.open = false;
        const editor = document.querySelector('.tiptap[contenteditable]');
        if (!editor) { return; }
        editor.focus();
        if (this.savedRange) {
            const sel = window.getSelection();
            if (sel) {
                sel.removeAllRanges();
                sel.addRange(this.savedRange);
            }
        }
        document.execCommand('insertText', false, text);
    },
    updateCounts() {
        const el = document.querySelector('.tiptap[contenteditable]');
        const text = el ? (el.innerText || el.textContent || '') : '';
        @foreach($allVariables as $variable => $label)
        this.counts[{{ \Illuminate\Support\Js::from($variable) }}] = text.split({{ \Illuminate\Support\Js::from($variable) }}).length - 1;
        @endforeach
    }
}" x-init="updateCounts();
setInterval(() => updateCounts(), 1000);" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;">

    {{-- Painel de variáveis --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform translateY(8px)"
        x-transition:enter-end="opacity-100 transform translateY(0)" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.outside="open = false"
        style="position:absolute;bottom:4.5rem;right:0;width:22rem;border-radius:0.75rem;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.12);">

        {{-- Cabeçalho geral --}}
        <div style="padding:0.75rem 1rem;border-bottom:1px solid rgba(255,255,255,0.08);background:#1f2937;">
            <p style="margin:0;font-size:0.85rem;font-weight:600;color:#f9fafb;">Variáveis Disponíveis</p>
            <p style="margin:0.25rem 0 0;font-size:0.75rem;color:#9ca3af;">Clique em copiar para fechar e inserir no
                contrato</p>
        </div>

        <div style="max-height:28rem;overflow-y:auto;background:#111827;">

            {{-- Seção Contratante --}}
            <div>
                <button type="button" @click="sectionContratante = !sectionContratante"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:0.6rem 1rem;background:#1a2535;border:none;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.08);">
                    <span style="font-size:0.8rem;font-weight:600;color:#f59e0b;">Variáveis de Contratante</span>
                    <svg x-show="!sectionContratante" style="width:14px;height:14px;color:#9ca3af;flex-shrink:0;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                    <svg x-show="sectionContratante" style="width:14px;height:14px;color:#9ca3af;flex-shrink:0;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
                <div x-show="sectionContratante" x-transition>
                    @foreach ($contratanteVars as $variable => $label)
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.6rem 1rem;border-bottom:1px solid rgba(255,255,255,0.06);">
                            <span
                                style="font-size:0.8rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $label }}</span>
                            <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                                <span x-show="counts[{{ \Illuminate\Support\Js::from($variable) }}] > 0"
                                    x-text="counts[{{ \Illuminate\Support\Js::from($variable) }}]"
                                    title="Número de vezes que esta variável aparece no contrato"
                                    style="font-size:0.7rem;font-weight:700;color:#f59e0b;background:rgba(245,158,11,0.15);padding:0.1rem 0.45rem;border-radius:999px;min-width:1.4rem;text-align:center;line-height:1.6;"></span>
                                <button type="button" title="Inserir variável" data-variable="{{ $variable }}"
                                    @click.stop="insertVariable($el.dataset.variable)"
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

            {{-- Seção Contratado --}}
            <div>
                <button type="button" @click="sectionContratado = !sectionContratado"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:0.6rem 1rem;background:#1a2535;border:none;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.08);border-top:1px solid rgba(255,255,255,0.08);">
                    <span style="font-size:0.8rem;font-weight:600;color:#60a5fa;">Variáveis de Contratado</span>
                    <svg x-show="!sectionContratado" style="width:14px;height:14px;color:#9ca3af;flex-shrink:0;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                    <svg x-show="sectionContratado" style="width:14px;height:14px;color:#9ca3af;flex-shrink:0;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
                <div x-show="sectionContratado" x-transition>
                    @foreach ($contratadoVars as $variable => $label)
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.6rem 1rem;border-bottom:1px solid rgba(255,255,255,0.06);">
                            <span
                                style="font-size:0.8rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $label }}</span>
                            <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                                <span x-show="counts[{{ \Illuminate\Support\Js::from($variable) }}] > 0"
                                    x-text="counts[{{ \Illuminate\Support\Js::from($variable) }}]"
                                    title="Número de vezes que esta variável aparece no contrato"
                                    style="font-size:0.7rem;font-weight:700;color:#60a5fa;background:rgba(96,165,250,0.15);padding:0.1rem 0.45rem;border-radius:999px;min-width:1.4rem;text-align:center;line-height:1.6;"></span>
                                <button type="button" title="Inserir variável" data-variable="{{ $variable }}"
                                    @click.stop="insertVariable($el.dataset.variable)"
                                    style="display:flex;align-items:center;justify-content:center;background:none;border:none;padding:6px;cursor:pointer;color:#6b7280;border-radius:4px;min-width:32px;min-height:32px;"
                                    onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#6b7280'">
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

            {{-- Seção Objeto de Contrato --}}
            <div>
                <button type="button" @click="sectionObjeto = !sectionObjeto"
                    style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:0.6rem 1rem;background:#1a2535;border:none;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.08);border-top:1px solid rgba(255,255,255,0.08);">
                    <span style="font-size:0.8rem;font-weight:600;color:#34d399;">Variáveis do Objeto de
                        Contrato</span>
                    <svg x-show="!sectionObjeto" style="width:14px;height:14px;color:#9ca3af;flex-shrink:0;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                    <svg x-show="sectionObjeto" style="width:14px;height:14px;color:#9ca3af;flex-shrink:0;"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                    </svg>
                </button>
                <div x-show="sectionObjeto" x-transition>
                    @foreach ($objetoVars as $variable => $label)
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0.6rem 1rem;border-bottom:1px solid rgba(255,255,255,0.06);">
                            <span
                                style="font-size:0.8rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $label }}</span>
                            <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                                <span x-show="counts[{{ \Illuminate\Support\Js::from($variable) }}] > 0"
                                    x-text="counts[{{ \Illuminate\Support\Js::from($variable) }}]"
                                    title="Número de vezes que esta variável aparece no contrato"
                                    style="font-size:0.7rem;font-weight:700;color:#34d399;background:rgba(52,211,153,0.15);padding:0.1rem 0.45rem;border-radius:999px;min-width:1.4rem;text-align:center;line-height:1.6;"></span>
                                <button type="button" title="Inserir variável" data-variable="{{ $variable }}"
                                    @click.stop="insertVariable($el.dataset.variable)"
                                    style="display:flex;align-items:center;justify-content:center;background:none;border:none;padding:6px;cursor:pointer;color:#6b7280;border-radius:4px;min-width:32px;min-height:32px;"
                                    onmouseover="this.style.color='#34d399'" onmouseout="this.style.color='#6b7280'">
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

        </div>
    </div>

    {{-- Botão circular flutuante --}}
    <button type="button" @click="if (!open) { saveEditorSelection(); } open = !open" title="Variáveis disponíveis"
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
