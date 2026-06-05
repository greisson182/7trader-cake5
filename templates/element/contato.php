<section id="contato" class="content-section light">
    <div class="container">
        <h1 class="section-title">Contato</h1>
        <p class="section-subtitle">Clique no botão e fale com a equipe pelo WhatsApp para receber detalhes, valores e disponibilidade.</p>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card trading-card border-0">
                    <div class="card-body p-4">
                        <?php
                        $whatsNumber = '5541992752998';
                        $whatsText = rawurlencode('Olá! Quero informações sobre o curso e as mentorias (grupo e particular).');
                        $whatsUrl = "https://wa.me/{$whatsNumber}?text={$whatsText}";
                        ?>
                        <a class="btn btn-success w-100" href="<?= h($whatsUrl) ?>" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp me-2"></i>Chamar no WhatsApp
                        </a>
                        <div class="text-muted small mt-3">
                            Atendimento via WhatsApp. Responderemos o mais rápido possível.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="modern-card h-100">
                    <h2 class="h4 mb-3">Atendimento</h2>
                    <p class="text-muted mb-0">Você recebe retorno com orientação do melhor formato: curso, mentoria em grupo ou mentoria particular.</p>
                </div>
            </div>
        </div>
    </div>
</section>
