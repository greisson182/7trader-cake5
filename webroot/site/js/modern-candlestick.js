// Modern Candlestick Chart with Trading Indicators
class ModernCandlestickChart {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.candlesticks = [];
        this.currentPrice = 100;
        this.lastClosePrice = 100; // Preço de fechamento do último candlestick
        this.priceHistory = [];
        this.trend = 'neutral';
        this.volatility = 0.3;
        
        this.init();
    }
    
    init() {
        if (!this.container) return;
        
        this.setupPriceGrid();
        this.startAnimation();
        this.addTradingIndicators();
    }
    
    setupPriceGrid() {
        const priceGrid = this.container.querySelector('.price-grid');
        if (!priceGrid) return;
        
        const prices = [110, 105, 100, 95, 90];
        const priceLines = priceGrid.querySelectorAll('.price-line');
        
        priceLines.forEach((line, index) => {
            if (prices[index]) {
                line.setAttribute('data-price', `$${prices[index]}`);
            }
        });
    }
    
    addTradingIndicators() {
        // Adicionar funcionalidade aos indicadores de venda/compra
        const sellZone = this.container.querySelector('.sell-zone');
        const buyZone = this.container.querySelector('.buy-zone');
        
        if (sellZone) {
            sellZone.addEventListener('click', () => {
                this.triggerSellSignal();
            });
        }
        
        if (buyZone) {
            buyZone.addEventListener('click', () => {
                this.triggerBuySignal();
            });
        }
    }
    
    triggerSellSignal() {
        const sellZone = this.container.querySelector('.sell-zone .zone-label');
        if (sellZone) {
            sellZone.style.animation = 'none';
            sellZone.offsetHeight; // Trigger reflow
            sellZone.style.animation = 'glow-red 0.5s ease-in-out 3';
            
            // Simular tendência de baixa
            this.trend = 'bearish';
            this.volatility = 0.5;
            
            // Mostrar notificação
            this.showNotification('Sinal de VENDA ativado!', 'sell');
        }
    }
    
    triggerBuySignal() {
        const buyZone = this.container.querySelector('.buy-zone .zone-label');
        if (buyZone) {
            buyZone.style.animation = 'none';
            buyZone.offsetHeight; // Trigger reflow
            buyZone.style.animation = 'glow-green 0.5s ease-in-out 3';
            
            // Simular tendência de alta
            this.trend = 'bullish';
            this.volatility = 0.5;
            
            // Mostrar notificação
            this.showNotification('Sinal de COMPRA ativado!', 'buy');
        }
    }
    
    showNotification(message, type) {
        // Criar notificação temporária
        const notification = document.createElement('div');
        notification.className = `trading-notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 600;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            background: ${type === 'sell' ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #22c55e, #16a34a)'};
            border: 1px solid ${type === 'sell' ? 'rgba(239, 68, 68, 0.3)' : 'rgba(34, 197, 94, 0.3)'};
            box-shadow: 0 10px 30px ${type === 'sell' ? 'rgba(239, 68, 68, 0.3)' : 'rgba(34, 197, 94, 0.3)'};
        `;
        
        document.body.appendChild(notification);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    startAnimation() {
        const candlesticksRow = this.container.querySelector('.candlesticks-row');
        if (!candlesticksRow) return;
        
        // Animar candlesticks existentes
        this.animateExistingCandlesticks();
        
        // Adicionar novos candlesticks periodicamente
        setInterval(() => {
            this.addNewCandlestick();
        }, 2500);
    }
    
    animateExistingCandlesticks() {
        const candlesticks = this.container.querySelectorAll('.modern-candlestick');
        
        candlesticks.forEach((candlestick, index) => {
            setTimeout(() => {
                candlestick.style.animation = 'candlestickGrowModern 1.5s ease-out';
            }, index * 200);
        });
    }
    
    addNewCandlestick() {
        const candlesticksRow = this.container.querySelector('.candlesticks-row');
        if (!candlesticksRow) return;
        
        // Remover o primeiro candlestick se houver muitos
        const existingCandlesticks = candlesticksRow.querySelectorAll('.modern-candlestick');
        if (existingCandlesticks.length >= 8) {
            existingCandlesticks[0].style.animation = 'slideOutLeft 0.8s ease-in';
            setTimeout(() => {
                if (existingCandlesticks[0].parentNode) {
                    existingCandlesticks[0].parentNode.removeChild(existingCandlesticks[0]);
                }
            }, 800);
        }
        
        // Gerar dados OHLC realistas
        const ohlcData = this.generateRealisticOHLC();
        
        // Atualizar preço atual para o fechamento
        this.currentPrice = ohlcData.close;
        this.lastClosePrice = ohlcData.close;
        this.priceHistory.push(ohlcData);
        
        // Determinar se é verde ou vermelho
        const isGreen = ohlcData.close >= ohlcData.open;
        
        // Calcular altura do corpo baseada na diferença open-close
        const bodyHeight = Math.max(8, Math.abs(ohlcData.close - ohlcData.open) * 200);
        const totalHeight = Math.max(25, (ohlcData.high - ohlcData.low) * 200);
        
        // Criar novo candlestick com dados OHLC
        const newCandlestick = document.createElement('div');
        newCandlestick.className = `modern-candlestick ${isGreen ? 'green' : 'red'}`;
        newCandlestick.style.height = `${totalHeight}px`;
        newCandlestick.style.opacity = '0';
        newCandlestick.style.transform = 'translateX(30px) scale(0.8)';
        
        // Armazenar dados OHLC
        newCandlestick.dataset.open = ohlcData.open.toFixed(4);
        newCandlestick.dataset.high = ohlcData.high.toFixed(4);
        newCandlestick.dataset.low = ohlcData.low.toFixed(4);
        newCandlestick.dataset.close = ohlcData.close.toFixed(4);
        newCandlestick.dataset.volume = ohlcData.volume.toFixed(0);
        newCandlestick.dataset.timestamp = Date.now();
        
        // Calcular posições das wicks
        const topWickHeight = (ohlcData.high - Math.max(ohlcData.open, ohlcData.close)) * 200;
        const bottomWickHeight = (Math.min(ohlcData.open, ohlcData.close) - ohlcData.low) * 200;
        
        newCandlestick.innerHTML = `
            <div class="candlestick-wick top" style="height: ${Math.max(0, topWickHeight)}px"></div>
            <div class="candlestick-body" style="height: ${bodyHeight}px; ${isGreen ? 'bottom: ' + bottomWickHeight + 'px' : 'top: ' + topWickHeight + 'px'}"></div>
            <div class="candlestick-wick bottom" style="height: ${Math.max(0, bottomWickHeight)}px"></div>
            <div class="price-tooltip">
                <div class="ohlc-data">
                    <div class="price-line">O: $${ohlcData.open.toFixed(2)}</div>
                    <div class="price-line">H: $${ohlcData.high.toFixed(2)}</div>
                    <div class="price-line">L: $${ohlcData.low.toFixed(2)}</div>
                    <div class="price-line">C: $${ohlcData.close.toFixed(2)}</div>
                </div>
                <div class="price-change ${isGreen ? 'positive' : 'negative'}">
                    ${isGreen ? '+' : ''}${((ohlcData.close - ohlcData.open) / ohlcData.open * 100).toFixed(2)}%
                </div>
                <div class="volume-info">Vol: ${ohlcData.volume}</div>
            </div>
        `;
        
        // Adicionar ao container
        candlesticksRow.appendChild(newCandlestick);
        
        // Animação de entrada natural
        setTimeout(() => {
            newCandlestick.style.transition = 'all 1.2s cubic-bezier(0.4, 0, 0.2, 1)';
            newCandlestick.style.opacity = '1';
            newCandlestick.style.transform = 'translateX(0) scale(1)';
            
            // Efeito de crescimento do corpo
            const body = newCandlestick.querySelector('.candlestick-body');
            body.style.transform = 'scaleY(0)';
            body.style.transformOrigin = isGreen ? 'bottom' : 'top';
            
            setTimeout(() => {
                body.style.transition = 'transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                body.style.transform = 'scaleY(1)';
            }, 200);
            
            // Efeito de brilho para movimentos significativos
            if (Math.abs(ohlcData.close - ohlcData.open) / ohlcData.open > 0.02) {
                newCandlestick.classList.add('significant-move');
                setTimeout(() => {
                    newCandlestick.classList.remove('significant-move');
                }, 2000);
            }
        }, 100);
        
        // Atualizar informações do gráfico
        this.updateChartInfo();
        
        // Simular reação do mercado
        this.simulateMarketReaction(ohlcData.close - ohlcData.open, isGreen);
    }
    
    generateRealisticOHLC() {
        // O próximo candlestick sempre abre no fechamento do anterior
        const open = this.lastClosePrice;
        
        // Gerar movimento baseado na tendência
        let priceMovement = this.generatePriceMovement();
        
        // Calcular fechamento
        const close = open + priceMovement;
        
        // Gerar high e low realistas
        const volatilityRange = Math.abs(priceMovement) + (Math.random() * 0.5 + 0.2);
        
        // High sempre é o maior entre open, close + alguma extensão
        const high = Math.max(open, close) + (Math.random() * volatilityRange * 0.7);
        
        // Low sempre é o menor entre open, close - alguma extensão
        const low = Math.min(open, close) - (Math.random() * volatilityRange * 0.7);
        
        // Volume baseado na volatilidade
        const volume = Math.floor(Math.abs(priceMovement) * 15000 + Math.random() * 8000 + 2000);
        
        return {
            open: open,
            high: high,
            low: low,
            close: close,
            volume: volume
        };
    }
    
    generatePriceMovement() {
        // Analisar movimentos recentes para reversão natural
        const recentMoves = this.priceHistory.slice(-3);
        let baseMovement = (Math.random() - 0.5) * this.volatility;
        
        // Lógica de reversão após movimentos consecutivos
        if (recentMoves.length >= 2) {
            const lastMove = recentMoves[recentMoves.length - 1];
            const secondLastMove = recentMoves[recentMoves.length - 2];
            
            if (lastMove && secondLastMove) {
                const lastDirection = lastMove.close > lastMove.open ? 1 : -1;
                const secondLastDirection = secondLastMove.close > secondLastMove.open ? 1 : -1;
                
                // Se os últimos 2 movimentos foram na mesma direção, aumentar chance de reversão
                if (lastDirection === secondLastDirection) {
                    const reversalChance = 0.6;
                    if (Math.random() < reversalChance) {
                        baseMovement = -lastDirection * (Math.random() * 0.8 + 0.3);
                    }
                }
            }
        }
        
        // Aplicar tendência de mercado
        switch(this.trend) {
            case 'bullish':
                baseMovement += Math.random() * 0.4 + 0.1;
                break;
            case 'bearish':
                baseMovement -= Math.random() * 0.4 + 0.1;
                break;
            case 'volatile':
                baseMovement *= 2;
                break;
        }
        
        // Adicionar ruído natural
        baseMovement += (Math.random() - 0.5) * 0.15;
        
        return baseMovement;
    }
    
    simulateMarketReaction(priceChange, isGreen) {
        // Simular reação visual do mercado
        if (Math.abs(priceChange) > 0.5) {
            // Movimento significativo - destacar zona correspondente
            const targetZone = isGreen ? 
                this.container.querySelector('.buy-zone .zone-label') :
                this.container.querySelector('.sell-zone .zone-label');
                
            if (targetZone) {
                targetZone.style.animation = 'none';
                targetZone.offsetHeight;
                targetZone.style.animation = `${isGreen ? 'glow-green' : 'glow-red'} 0.6s ease-in-out 2`;
            }
        }
        
        // Atualizar volatilidade baseada no movimento
        if (Math.abs(priceChange) > 0.8) {
            this.volatility = Math.min(1.0, this.volatility * 1.15);
        } else {
            this.volatility = Math.max(0.2, this.volatility * 0.95);
        }
    }
    
    updateChartInfo() {
        const trendInfo = this.container.querySelector('.trend-info small');
        const marketStats = this.container.querySelector('.market-stats small');
        
        if (trendInfo) {
            const trendText = this.trend === 'bullish' ? 'Tendência de Alta Confirmada' : 
                             this.trend === 'bearish' ? 'Tendência de Baixa Detectada' : 
                             'Mercado Lateral';
            trendInfo.textContent = trendText;
        }
        
        if (marketStats) {
            marketStats.textContent = `Preço: $${this.currentPrice.toFixed(2)} | Vol: ${(this.volatility * 100).toFixed(1)}%`;
        }
    }
}

// Adicionar estilos para animações
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.8); }
    }
`;
document.head.appendChild(style);

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Procurar por gráficos de candlestick modernos na página
    const modernCharts = document.querySelectorAll('.modern-candlestick-chart');
    
    modernCharts.forEach((chart, index) => {
        // Dar um ID único se não tiver
        if (!chart.id) {
            chart.id = `modern-candlestick-${index}`;
        }
        
        // Inicializar o gráfico
        new ModernCandlestickChart(chart.id);
    });
});