// Site Public JavaScript

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('a[href^="#"]');

    links.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Navbar scroll effect
window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    }
});

// Animation on scroll
function animateOnScroll() {
    const elements = document.querySelectorAll('.animate-on-scroll');

    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;

        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('animated');
        }
    });
}

window.addEventListener('scroll', animateOnScroll);
document.addEventListener('DOMContentLoaded', animateOnScroll);

// Counter animation
function animateCounters() {
    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    });
}

// Trigger counter animation when stats section is visible
const statsSection = document.querySelector('.stats-section');
if (statsSection) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    });

    observer.observe(statsSection);
}

// Contact form handling
const contactForm = document.querySelector('#contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Get form data
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        // Basic validation
        if (!data.name || !data.email || !data.message) {
            showAlert('Por favor, preencha todos os campos obrigatórios.', 'danger');
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            showAlert('Por favor, insira um email válido.', 'danger');
            return;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Enviando...';
        submitBtn.disabled = true;

        // Simulate form submission (replace with actual endpoint)
        setTimeout(() => {
            showAlert('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');
            this.reset();
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }, 2000);
    });
}

// Alert system
function showAlert(message, type = 'info') {
    const alertContainer = document.querySelector('.alert-container') || createAlertContainer();

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    alertContainer.appendChild(alert);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.classList.remove('show');
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 150);
        }
    }, 5000);
}

function createAlertContainer() {
    const container = document.createElement('div');
    container.className = 'alert-container position-fixed';
    container.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    document.body.appendChild(container);
    return container;
}

// Mobile menu toggle
const navbarToggler = document.querySelector('.navbar-toggler');
const navbarCollapse = document.querySelector('.navbar-collapse');

if (navbarToggler && navbarCollapse) {
    navbarToggler.addEventListener('click', function () {
        navbarCollapse.classList.toggle('show');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function (e) {
        if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
            navbarCollapse.classList.remove('show');
        }
    });
}

// Lazy loading for images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', lazyLoadImages);

// Back to top button
function createBackToTopButton() {
    const button = document.createElement('button');
    button.innerHTML = '<i class="fas fa-arrow-up"></i>';
    button.className = 'btn btn-primary back-to-top';
    button.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: none;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    `;

    button.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    document.body.appendChild(button);

    // Show/hide button based on scroll position
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            button.style.display = 'block';
        } else {
            button.style.display = 'none';
        }
    });
}

// Initialize back to top button
document.addEventListener('DOMContentLoaded', createBackToTopButton);

// Feature cards hover effect
document.addEventListener('DOMContentLoaded', function () {
    const featureCards = document.querySelectorAll('.feature-card');

    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Export functions for global use
window.SiteJS = {
    showAlert,
    animateCounters,
    lazyLoadImages
};
// Simulador de Mercado Realista
class MarketSimulator {
    constructor() {
        this.currentTrend = 'neutral'; // 'bullish', 'bearish', 'neutral', 'volatile'
        this.trendDuration = 0;
        this.maxTrendDuration = 15; // segundos
        this.price = 100; // preço base
        this.priceHistory = [];
        this.volatility = 0.5;

        this.updateTrend();
    }

    updateTrend() {
        const trends = ['bullish', 'bearish', 'neutral', 'volatile'];
        const weights = [0.3, 0.3, 0.2, 0.2]; // probabilidades

        let random = Math.random();
        let cumulativeWeight = 0;

        for (let i = 0; i < trends.length; i++) {
            cumulativeWeight += weights[i];
            if (random <= cumulativeWeight) {
                this.currentTrend = trends[i];
                break;
            }
        }

        this.trendDuration = 0;
        this.maxTrendDuration = Math.random() * 20 + 10; // 10-30 segundos

        // Ajustar volatilidade baseada na tendência
        switch (this.currentTrend) {
            case 'volatile':
                this.volatility = Math.random() * 0.8 + 0.7; // 0.7-1.5
                break;
            case 'bullish':
            case 'bearish':
                this.volatility = Math.random() * 0.4 + 0.3; // 0.3-0.7
                break;
            default:
                this.volatility = Math.random() * 0.3 + 0.2; // 0.2-0.5
        }
    }

    getMarketData() {
        this.trendDuration++;

        if (this.trendDuration >= this.maxTrendDuration) {
            this.updateTrend();
        }

        // Calcular mudança de preço baseada na tendência
        let priceChange = 0;
        const baseChange = (Math.random() - 0.5) * this.volatility;

        switch (this.currentTrend) {
            case 'bullish':
                priceChange = baseChange + (Math.random() * 0.3 + 0.1);
                break;
            case 'bearish':
                priceChange = baseChange - (Math.random() * 0.3 + 0.1);
                break;
            case 'volatile':
                priceChange = (Math.random() - 0.5) * this.volatility * 2;
                break;
            default: // neutral
                priceChange = baseChange * 0.5;
        }

        this.price += priceChange;
        this.priceHistory.push(this.price);

        // Manter histórico limitado
        if (this.priceHistory.length > 50) {
            this.priceHistory.shift();
        }

        return {
            trend: this.currentTrend,
            price: this.price,
            change: priceChange,
            volatility: this.volatility,
            isGreen: priceChange >= 0
        };
    }
}

// Instância do simulador
const marketSim = new MarketSimulator();


// Função para criar candlesticks estáticos
function createStaticFooterCandlesticks() {
    const container = document.getElementById('footerCandlesticks');
    if (!container) return;

    // Criar candlesticks estáticos para decoração
    const candlestickPositions = [15, 25, 35, 45, 55, 65, 75, 85, 95, 105];
    const candlestickTypes = [true, false, true, true, false, true, false, true, false, true]; // true = green, false = red
    const candlestickHeights = [20, 25, 35, 50, 22, 28, 24, 26, 28, 30];

    candlestickPositions.forEach((position, index) => {
        const candlestick = document.createElement('div');
        const isGreen = candlestickTypes[index];
        const height = candlestickHeights[index];

        candlestick.className = `footer-candlestick ${isGreen ? 'green' : 'red'} static`;
        candlestick.style.left = position + '%';
        candlestick.style.height = height + 'px';
        candlestick.style.position = 'absolute';
        candlestick.style.bottom = '0';
        candlestick.style.animation = 'none'; // Remove todas as animações
        candlestick.style.opacity = '0.5'; // Deixa mais sutil como decoração

        container.appendChild(candlestick);
    });
}

// Iniciar candlesticks estáticos quando a página carregar
document.addEventListener('DOMContentLoaded', function () {
    // Criar candlesticks estáticos apenas uma vez
    createStaticFooterCandlesticks();

    // Debug: mostrar tendência atual no console (remover em produção)
    setInterval(() => {
        const data = marketSim.getMarketData();
        console.log(`Tendência: ${data.trend}, Preço: ${data.price.toFixed(2)}, Volatilidade: ${data.volatility.toFixed(2)}`);
    }, 5000);
});