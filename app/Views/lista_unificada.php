<?= $this->include('partials/wl-layout-open') ?>

<style>
.wl-hub-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: .55rem;
}

.wl-hub-tab {
    border: 1px solid #dbe5f1;
    background: #f8fafc;
    color: #334155;
    border-radius: 999px;
    padding: .4rem .9rem;
    font-size: .84rem;
    font-weight: 600;
    transition: all .2s ease;
}

.wl-hub-tab:hover {
    border-color: #93c5fd;
    color: #1d4ed8;
}

.wl-hub-tab.is-active {
    background: #dbeafe;
    border-color: #93c5fd;
    color: #1d4ed8;
}

.wl-hub-frame-wrap {
    background: #f8fbff;
    border-top: 1px solid #e2e8f0;
}

.wl-hub-frame-stack {
    width: 100%;
    height: 100%;
}

.wl-hub-frame {
    display: block;
    width: 100%;
    height: calc(100vh - 245px);
    min-height: 640px;
    border: 0;
    background: #f8fbff;
}

@media (max-width: 992px) {
    .wl-hub-frame {
        height: calc(100vh - 285px);
        min-height: 560px;
    }
}
</style>

<div class="card wl-card">
    <div class="card-header border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="card-title mb-0"><?= esc($titulo ?? 'Painel de Tarefas') ?></h5>
        <div class="wl-hub-tabs" id="hub_tabs">
            <?php foreach (($abas ?? []) as $abaKey => $abaData) { ?>
                <button type="button" class="wl-hub-tab<?= (($aba_inicial ?? '') === $abaKey) ? ' is-active' : '' ?>" data-tab="<?= esc($abaKey) ?>">
                    <?= esc($abaData['label'] ?? $abaKey) ?>
                </button>
            <?php } ?>
        </div>
    </div>

    <div class="card-body p-0 wl-hub-frame-wrap">
        <div id="hub_frames" class="wl-hub-frame-stack"></div>
    </div>
</div>

<?= $this->include('partials/wl-layout-close') ?>
<?= $this->include('partials/wl-scripts') ?>

<script>
(function () {
    var abas = <?= json_encode($abas ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var abaInicial = '<?= esc($aba_inicial ?? '') ?>';
    var framesWrap = document.getElementById('hub_frames');
    var tabs = document.querySelectorAll('#hub_tabs [data-tab]');
    var frames = {};

    function criarFrame(tabKey, ativo) {
        if (!abas[tabKey] || !framesWrap) {
            return null;
        }

        if (frames[tabKey]) {
            return frames[tabKey];
        }

        var frame = document.createElement('iframe');
        frame.className = 'wl-hub-frame';
        frame.title = 'Painel de Tarefas - ' + tabKey;
        frame.dataset.tab = tabKey;
        frame.style.display = ativo ? 'block' : 'none';
        frame.src = abas[tabKey].url;
        framesWrap.appendChild(frame);
        frames[tabKey] = frame;
        return frame;
    }

    function ativarAba(tabKey) {
        if (!abas[tabKey] || !framesWrap) {
            return;
        }

        tabs.forEach(function (btn) {
            if (btn.dataset.tab === tabKey) {
                btn.classList.add('is-active');
            } else {
                btn.classList.remove('is-active');
            }
        });

        var frameAtivo = criarFrame(tabKey, true);
        if (!frameAtivo) {
            return;
        }

        Object.keys(frames).forEach(function (key) {
            frames[key].style.display = key === tabKey ? 'block' : 'none';
        });
    }

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            ativarAba(btn.dataset.tab);
        });
    });

    if (!abas[abaInicial]) {
        var firstKey = Object.keys(abas)[0];
        abaInicial = firstKey ? firstKey : '';
    }

    if (abaInicial) {
        ativarAba(abaInicial);
    }

    ['lista_tarefas', 'lista_tarefas_adm'].forEach(function (tabKey) {
        if (tabKey !== abaInicial && abas[tabKey]) {
            criarFrame(tabKey, false);
        }
    });
})();
</script>

<?= $this->include('partials/wl-layout-end') ?>
