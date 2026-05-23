@extends('layouts.app')

@section('content')
<section class="hub-container grid gap-8 py-8 md:py-10 lg:grid-cols-[1fr_420px]">
    <div class="min-w-0">
        <h1 class="text-3xl font-black text-white">Проверка ПК</h1>
        <p class="mt-2 text-slate-400">Введите конфигурацию и выберите игру для сравнения с минимальными и рекомендуемыми требованиями.</p>
        @if($configs->count())
            <div class="mt-5 rounded-lg border border-cyan-400/20 bg-cyan-400/10 p-4 text-sm text-cyan-50">
                У вас уже есть сохранённая конфигурация. На странице игры кнопка “Проверить ПК” будет использовать её сразу, без перехода к созданию новой.
            </div>
        @endif
        <div class="mt-5 rounded-lg border border-cyan-400/20 bg-cyan-400/10 p-4 text-sm text-cyan-50">
            Браузер не может точно узнать всё железо компьютера из-за безопасности. Автоопределение заполнит только примерные данные: ОС, потоки CPU, RAM и GPU, если браузер отдаёт эти поля.
        </div>
        <form method="POST" action="{{ route('pc.compare') }}" class="hub-panel mt-8 grid gap-4 p-4 md:grid-cols-2 md:p-5" id="pc-check-form">@csrf
            <input id="pc-title" name="title" class="hub-input md:col-span-2" value="Мой ПК" placeholder="Название сборки">
            <input id="pc-cpu" name="cpu" class="hub-input" placeholder="CPU, например Intel Core i5-10400">
            <input id="pc-gpu" name="gpu" class="hub-input" placeholder="GPU, например RTX 3060">
            <input id="pc-ram" name="ram" type="number" class="hub-input" placeholder="RAM GB">
            <input id="pc-storage" name="storage" type="number" class="hub-input" placeholder="Свободное место GB">
            <input id="pc-os" name="os" class="hub-input" placeholder="OS, например Windows 11">
            <select name="game_id" class="hub-input">
                @foreach($games as $game)<option value="{{ $game->id }}">{{ $game->title }}</option>@endforeach
            </select>
            <textarea id="pc-notes" name="notes" class="hub-input md:col-span-2" placeholder="Заметки"></textarea>
            <div class="grid gap-3 md:col-span-2 md:grid-cols-2">
                <button type="button" class="hub-btn-secondary" id="detect-pc">Определить автоматически</button>
                <button class="hub-btn" formaction="{{ route('pc.compare') }}">Проверить совместимость</button>
                <button class="hub-btn-secondary md:col-span-2" formaction="{{ route('pc.store') }}">Сохранить конфигурацию ПК</button>
            </div>
            <p id="detect-pc-status" class="hidden rounded-md border border-white/10 bg-white/5 p-3 text-sm text-slate-300 md:col-span-2"></p>
        </form>
        @if($errors->any())
            <div class="mt-4 rounded-md border border-red-400/30 bg-red-500/10 p-4 text-red-100">{{ $errors->first() }}</div>
        @endif
    </div>
    <aside class="min-w-0 space-y-6">
        @if($result)
            <div class="hub-panel p-5">
                <h2 class="text-xl font-black text-white">{{ $result['game'] }}</h2>
                <p class="mt-2 text-cyan-200">{{ $result['conclusion'] }}</p>
                <div class="mt-4 space-y-3">
                    @foreach($result['details'] as $detail)
                        <div class="rounded-lg bg-white/5 p-3 text-sm">
                            <div class="flex justify-between"><b>{{ $detail['label'] }}</b><span class="{{ $detail['min_pass'] ? 'text-cyan-200' : 'text-red-300' }}">{{ $detail['value'] }}</span></div>
                            <p class="mt-1 text-slate-400">Мин: {{ $detail['minimum'] }} | Рек: {{ $detail['recommended'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="hub-panel p-5">
            <h2 class="text-xl font-black text-white">Сохранённые ПК</h2>
            <div class="mt-4 space-y-3">
                @forelse($configs as $config)
                    <div class="rounded-lg bg-white/5 p-3 text-sm">
                        <b>{{ $config->title }}</b>
                        <p class="mt-1 text-slate-400">{{ $config->cpu }} / {{ $config->gpu }} / {{ $config->ram }} GB RAM</p>
                        <form method="POST" action="{{ route('pc.destroy', $config) }}" class="mt-3" onsubmit="return confirm('Удалить конфигурацию ПК? После этого можно будет создать новую.');">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-200 hover:bg-red-500/20">Удалить конфигурацию ПК</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Сохранённых конфигураций пока нет.</p>
                @endforelse
            </div>
        </div>
    </aside>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('detect-pc');
        if (!button) return;

        const fields = {
            cpu: document.getElementById('pc-cpu'),
            gpu: document.getElementById('pc-gpu'),
            ram: document.getElementById('pc-ram'),
            storage: document.getElementById('pc-storage'),
            os: document.getElementById('pc-os'),
            notes: document.getElementById('pc-notes'),
        };
        const status = document.getElementById('detect-pc-status');

        function detectOS() {
            const platform = navigator.userAgentData?.platform || navigator.platform || navigator.userAgent || '';
            const value = platform.toLowerCase();

            if (value.includes('win')) return 'Windows 11';
            if (value.includes('mac')) return 'macOS';
            if (value.includes('linux')) return 'Linux';
            if (value.includes('android')) return 'Android';

            return platform || 'Не удалось определить ОС';
        }

        function detectGPU() {
            try {
                const canvas = document.createElement('canvas');
                const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
                if (!gl) return '';

                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                if (!debugInfo) return '';

                return gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) || '';
            } catch (error) {
                return '';
            }
        }

        button.addEventListener('click', async function () {
            const cores = navigator.hardwareConcurrency || '';
            const memory = navigator.deviceMemory || '';
            const gpu = detectGPU();
            const os = detectOS();

            fields.cpu.value = cores ? `CPU, ${cores} потоков` : fields.cpu.value;
            fields.gpu.value = gpu || fields.gpu.value;
            fields.ram.value = memory ? Math.max(2, Number(memory)) : fields.ram.value;
            fields.storage.value = fields.storage.value || 100;
            fields.os.value = os;
            fields.notes.value = [
                'Автоопределение браузером: данные приблизительные.',
                cores ? `CPU threads: ${cores}` : null,
                memory ? `RAM estimate: ${memory} GB` : null,
                gpu ? `GPU renderer: ${gpu}` : null,
            ].filter(Boolean).join("\n");

            status.classList.remove('hidden');
            status.textContent = 'Готово: сайт заполнил данные, которые браузер разрешил определить. Проверьте поля вручную перед сравнением.';
        });
    });
</script>
@endpush
@endsection
