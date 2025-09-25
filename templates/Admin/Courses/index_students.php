<?php
$title = 'Cursos Disponíveis - 7 Trader';
$description = 'Explore nossa biblioteca de cursos de trading e análise técnica';
?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); min-height: 100vh;">
    <div class="container">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <i class="fas fa-graduation-cap text-success me-3"></i>
                    Cursos de Trading
                </h1>
                <p class="lead text-light opacity-75">
                    Aprenda com nossos especialistas e domine o mercado financeiro
                </p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-dark border-secondary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <select class="form-select bg-dark text-white border-secondary" id="filterCategory">
                                    <option value="">Todas as Categorias</option>
                                    <option value="Análise Técnica">Análise Técnica</option>
                                    <option value="Análise Fundamentalista">Análise Fundamentalista</option>
                                    <option value="Day Trade">Day Trade</option>
                                    <option value="Swing Trade">Swing Trade</option>
                                    <option value="Psicologia">Psicologia</option>
                                    <option value="Gestão de Risco">Gestão de Risco</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select bg-dark text-white border-secondary" id="filterDifficulty">
                                    <option value="">Todas as Dificuldades</option>
                                    <option value="Iniciante">Iniciante</option>
                                    <option value="Intermediário">Intermediário</option>
                                    <option value="Avançado">Avançado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select bg-dark text-white border-secondary" id="filterPrice">
                                    <option value="">Todos os Preços</option>
                                    <option value="free">Gratuitos</option>
                                    <option value="paid">Pagos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-dark text-white border-secondary" 
                                           placeholder="Buscar cursos..." id="searchInput">
                                    <button class="btn btn-success" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Cursos -->
        <div class="row" id="coursesContainer">
            <?php if (empty($courses)): ?>
                <div class="col-12">
                    <div class="card bg-dark border-secondary text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-white">Nenhum curso disponível</h4>
                            <p class="text-muted">Em breve teremos novos cursos para você!</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <?php
                    $isEnrolled = isset($enrollments[$course['id']]);
                    $enrollment = $enrollments[$course['id']] ?? null;
                    $isCompleted = $enrollment && $enrollment['completed_at'];
                    
                    // Calcular duração formatada
                    $totalMinutes = round($course['total_duration'] / 60);
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
                    
                    // Badge de dificuldade
                    $difficultyColors = [
                        'Iniciante' => 'success',
                        'Intermediário' => 'warning',
                        'Avançado' => 'danger'
                    ];
                    $difficultyColor = $difficultyColors[$course['difficulty']] ?? 'secondary';
                    ?>
                    
                    <div class="col-lg-4 col-md-6 mb-4 course-card" 
                         data-category="<?= htmlspecialchars($course['category']) ?>"
                         data-difficulty="<?= htmlspecialchars($course['difficulty']) ?>"
                         data-price="<?= $course['is_free'] ? 'free' : 'paid' ?>"
                         data-title="<?= htmlspecialchars(strtolower($course['title'])) ?>">
                        
                        <div class="card bg-dark border-secondary h-100 course-hover-effect">
                            <!-- Thumbnail -->
                            <div class="position-relative">
                                <?php if ($course['thumbnail']): ?>
                                    <img src="<?= htmlspecialchars($course['thumbnail']) ?>" 
                                         class="card-img-top" style="height: 200px; object-fit: cover;" 
                                         alt="<?= htmlspecialchars($course['title']) ?>">
                                <?php else: ?>
                                    <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                                         style="height: 200px; background: linear-gradient(45deg, #28a745, #20c997);">
                                        <i class="fas fa-play-circle fa-3x text-white opacity-75"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Badges -->
                                <div class="position-absolute top-0 start-0 p-2">
                                    <?php if ($course['is_free']): ?>
                                        <span class="badge bg-success">GRATUITO</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">R$ <?= number_format($course['price'], 2, ',', '.') ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="position-absolute top-0 end-0 p-2">
                                    <span class="badge bg-<?= $difficultyColor ?>"><?= htmlspecialchars($course['difficulty']) ?></span>
                                </div>
                                
                                <?php if ($isEnrolled): ?>
                                    <div class="position-absolute bottom-0 start-0 p-2">
                                        <?php if ($isCompleted): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>CONCLUÍDO
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info">
                                                <i class="fas fa-play me-1"></i>EM ANDAMENTO
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-white mb-2"><?= htmlspecialchars($course['title']) ?></h5>
                                
                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    <?= htmlspecialchars(substr($course['description'], 0, 120)) ?>
                                    <?= strlen($course['description']) > 120 ? '...' : '' ?>
                                </p>
                                
                                <!-- Informações do Curso -->
                                <div class="course-info mb-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Vídeos</small>
                                            <strong class="text-success"><?= $course['video_count'] ?></strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Duração</small>
                                            <strong class="text-success"><?= $durationText ?></strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Categoria</small>
                                            <strong class="text-success"><?= htmlspecialchars($course['category']) ?></strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($course['instructor']): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Instrutor:</small>
                                        <div class="text-white">
                                            <i class="fas fa-user-tie me-1"></i>
                                            <?= htmlspecialchars($course['instructor']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Ações -->
                                <div class="mt-auto">
                                    <?php if ($isEnrolled): ?>
                                        <a href="/admin/courses/view-students/<?= $course['id'] ?>" 
                                           class="btn btn-success w-100">
                                            <i class="fas fa-play me-2"></i>Continuar Curso
                                        </a>
                                    <?php else: ?>
                                        <div class="d-grid gap-2">
                                            <a href="/admin/courses/view-students/<?= $course['id'] ?>" 
                                               class="btn btn-outline-success">
                                                <i class="fas fa-eye me-2"></i>Ver Detalhes
                                            </a>
                                            <?php if ($course['is_free']): ?>
                                                <a href="/admin/courses/enroll/<?= $course['id'] ?>" 
                                                   class="btn btn-success">
                                                    <i class="fas fa-graduation-cap me-2"></i>Inscrever-se Grátis
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.course-hover-effect {
    transition: all 0.3s ease;
    border: 1px solid #444;
}

.course-hover-effect:hover {
    transform: translateY(-5px);
    border-color: #28a745;
    box-shadow: 0 10px 25px rgba(40, 167, 69, 0.2);
}

.course-info {
    border-top: 1px solid #444;
    border-bottom: 1px solid #444;
    padding: 15px 0;
}

.bg-gradient {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterCategory = document.getElementById('filterCategory');
    const filterDifficulty = document.getElementById('filterDifficulty');
    const filterPrice = document.getElementById('filterPrice');
    const searchInput = document.getElementById('searchInput');
    const coursesContainer = document.getElementById('coursesContainer');
    
    function filterCourses() {
        const categoryFilter = filterCategory.value.toLowerCase();
        const difficultyFilter = filterDifficulty.value.toLowerCase();
        const priceFilter = filterPrice.value;
        const searchFilter = searchInput.value.toLowerCase();
        
        const courseCards = document.querySelectorAll('.course-card');
        let visibleCount = 0;
        
        courseCards.forEach(card => {
            const category = card.dataset.category.toLowerCase();
            const difficulty = card.dataset.difficulty.toLowerCase();
            const price = card.dataset.price;
            const title = card.dataset.title;
            
            const matchesCategory = !categoryFilter || category.includes(categoryFilter);
            const matchesDifficulty = !difficultyFilter || difficulty.includes(difficultyFilter);
            const matchesPrice = !priceFilter || price === priceFilter;
            const matchesSearch = !searchFilter || title.includes(searchFilter);
            
            if (matchesCategory && matchesDifficulty && matchesPrice && matchesSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Mostrar mensagem se nenhum curso for encontrado
        let noResultsMsg = document.getElementById('noResultsMessage');
        if (visibleCount === 0 && courseCards.length > 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.id = 'noResultsMessage';
                noResultsMsg.className = 'col-12';
                noResultsMsg.innerHTML = `
                    <div class="card bg-dark border-secondary text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4 class="text-white">Nenhum curso encontrado</h4>
                            <p class="text-muted">Tente ajustar os filtros para encontrar o que procura.</p>
                        </div>
                    </div>
                `;
                coursesContainer.appendChild(noResultsMsg);
            }
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }
    
    // Event listeners
    filterCategory.addEventListener('change', filterCourses);
    filterDifficulty.addEventListener('change', filterCourses);
    filterPrice.addEventListener('change', filterCourses);
    searchInput.addEventListener('input', filterCourses);
});
</script>