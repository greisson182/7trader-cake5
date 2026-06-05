<section id="contato" class="content-section light">
    <div class="container">
        <h1 class="section-title">Contato</h1>
        <p class="section-subtitle">Envie sua mensagem para receber detalhes, valores e disponibilidade.</p>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card trading-card border-0">
                    <div class="card-body p-4">
                        <form id="contactForm" action="/envia_contato" method="post">
                            <input type="text" name="full_name" value="" class="d-none" tabindex="-1" autocomplete="off">

                            <div class="mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" class="form-control" name="contactsName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="contactsEmail" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefone/WhatsApp</label>
                                <input type="text" class="form-control" name="contactsPhone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mensagem</label>
                                <textarea class="form-control" name="contactsMassage" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Enviar mensagem</button>
                        </form>
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
