// Simulador de Trading Profissional
class TradingSimulator {
    constructor() {
        // Configurações
        this.config = {
            candlePeriod: 500, // 5 segundos por vela
            visibleHistory: 50, // 20 velas visíveis
            simulationSpeed: 0.1, // velocidade 1x
            tickInterval: 5, // tick a cada 200ms
            priceRange: { min: 99.30, max: 99.70 },
            initialPrice: 99.30 // preço inicial no centro do range
        };
        
        // Estado do mercado
        this.market = {
            currentPrice: this.config.initialPrice,
            lastPrice: this.config.initialPrice,
            sessionOpen: this.config.initialPrice,
            sessionHigh: this.config.initialPrice,
            sessionLow: this.config.initialPrice,
            volume: 0,
            trend: 'neutral',
            volatility: 0.02
        };
        
        // Vela atual
        this.currentCandle = {
            open: this.config.initialPrice,
            high: this.config.initialPrice,
            low: this.config.initialPrice,
            close: this.config.initialPrice,
            volume: 0,
            startTime: Date.now(),
            ticks: []
        };
        
        // Histórico de velas
        this.candleHistory = [];
        
        // Estado da simulação
        this.isRunning = false;
        this.isPaused = false;
        this.tickTimer = null;
        this.candleTimer = null;
        
        // Elementos DOM
        this.elements = {};
        
        this.initializeElements();
        this.setupEventListeners();
        this.setupPriceAxis();
        this.setupTimeAxis();
    }
    
    initializeElements() {
        // Elementos essenciais do gráfico (removido currentTick - bolinha do lastprice)
        this.elements = {
            chartArea: document.getElementById('chartArea'),
            priceLabels: document.getElementById('priceLabels'),
            timeLabels: document.getElementById('timeLabels'),
            candlesticksContainer: document.getElementById('candlesticksContainer')
        };
        
        // Verificar se todos os elementos existem
        for (const [key, element] of Object.entries(this.elements)) {
            if (!element) {
                console.warn(`Elemento ${key} não encontrado`);
            }
        }
    }
    
    setupEventListeners() {
        // Como removemos os controles, não precisamos mais dos event listeners
        // Mantemos apenas a funcionalidade básica do simulador
    }
    
    setupPriceAxis() {
        const priceLabels = this.elements.priceLabels;
        const chartHeight = this.elements.chartArea.offsetHeight;
        const priceRange = this.config.priceRange.max - this.config.priceRange.min;
        const stepSize = priceRange / 10;
        
        priceLabels.innerHTML = '';
        
        for (let i = 0; i <= 10; i++) {
            const price = this.config.priceRange.max - (i * stepSize);
            const label = document.createElement('div');
            label.className = 'price-label';
            label.textContent = `$${price.toFixed(2)}`;
            label.style.position = 'absolute';
            label.style.top = `${(i / 10) * 100}%`;
            label.style.transform = 'translateY(-50%)';
            priceLabels.appendChild(label);
        }
    }
    
    setupTimeAxis() {
        const timeLabels = this.elements.timeLabels;
        timeLabels.innerHTML = '';
        
        // Criar labels de tempo baseados no histórico de velas
        const now = Date.now();
        const timeStep = this.config.candlePeriod;
        
        for (let i = 0; i < this.config.visibleHistory; i++) {
            const label = document.createElement('div');
            label.className = 'time-label';
            const timestamp = now - (this.config.visibleHistory - i - 1) * timeStep;
            label.textContent = this.formatTime(timestamp);
            label.style.flex = '1';
            label.style.textAlign = 'center';
            timeLabels.appendChild(label);
        }
    }
    
    updateTimeAxis() {
        if (!this.elements.timeLabels) return;
        
        const timeLabels = this.elements.timeLabels;
        const labels = timeLabels.querySelectorAll('.time-label');
        
        // Atualizar timestamps dos labels existentes
        const now = Date.now();
        const timeStep = this.config.candlePeriod;
        
        labels.forEach((label, i) => {
            const timestamp = now - (this.config.visibleHistory - i - 1) * timeStep;
            label.textContent = this.formatTime(timestamp);
        });
    }
    
    start() {
        if (this.isRunning) return;
        
        this.isRunning = true;
        this.isPaused = false;
        
        this.startTicking();
        this.startCandleTimer();
        
        console.log('Simulador de Trading iniciado');
    }
    
    stop() {
        this.isRunning = false;
        this.clearTimers();
        console.log('Simulador de Trading parado');
    }
    
    togglePause() {
        if (!this.isRunning) {
            this.start();
            return;
        }
        
        this.isPaused = !this.isPaused;
        
        if (this.isPaused) {
            this.clearTimers();
        } else {
            this.startTicking();
            this.startCandleTimer();
        }
    }
    
    reset(fullReset = false) {
        this.stop();
        
        // Reset do mercado
        if (fullReset) {
            // Reset completo - volta ao preço inicial
            this.market.currentPrice = this.config.initialPrice;
            this.market.lastPrice = this.config.initialPrice;
            this.market.sessionOpen = this.config.initialPrice;
            this.market.sessionHigh = this.config.initialPrice;
            this.market.sessionLow = this.config.initialPrice;
            this.oscillationDirection = 1;
            
            // Limpar histórico apenas no reset completo
            this.candleHistory = [];
            
            // Limpar display apenas no reset completo
            this.elements.candlesticksContainer.innerHTML = '';
        } else {
            // Reset parcial - mantém o preço atual e histórico
            this.market.lastPrice = this.market.currentPrice;
            // Mantém sessionOpen, sessionHigh, sessionLow para continuidade
            // Mantém candleHistory para continuidade
        }
        
        this.market.volume = 0;
        
        // Reset da vela atual
        this.currentCandle = {
            open: this.market.currentPrice,
            high: this.market.currentPrice,
            low: this.market.currentPrice,
            close: this.market.currentPrice,
            volume: 0,
            startTime: Date.now(),
            ticks: []
        };
        
        this.updateMarketInfo();
        this.updateCurrentCandleInfo();
        
        console.log('Simulador resetado - fullReset:', fullReset, 'currentPrice:', this.market.currentPrice);
    }
    
    startTicking() {
        const tickInterval = this.config.tickInterval / this.config.simulationSpeed;
        
        this.tickTimer = setInterval(() => {
            this.generateTick();
            this.updateDisplay();
        }, tickInterval);
    }
    
    startCandleTimer() {
        const candleInterval = this.config.candlePeriod / this.config.simulationSpeed;
        
        this.candleTimer = setInterval(() => {
            this.completeCandle();
            this.startNewCandle();
        }, candleInterval);
    }
    
    clearTimers() {
        if (this.tickTimer) {
            clearInterval(this.tickTimer);
            this.tickTimer = null;
        }
        
        if (this.candleTimer) {
            clearInterval(this.candleTimer);
            this.candleTimer = null;
        }
    }
    
    adjustTimers() {
        if (this.isRunning && !this.isPaused) {
            this.clearTimers();
            this.startTicking();
            this.startCandleTimer();
        }
    }
    
    restartCandle() {
        this.completeCandle();
        this.startNewCandle();
        this.adjustTimers();
    }
    
    generateTick() {
        // Movimento oscilatório tick a tick entre 99.76 e 100.24
        
        // Inicializar direção se não existir
        if (!this.oscillationDirection) {
            this.oscillationDirection = 1; // 1 para subir, -1 para descer
        }
        
        // Definir o tamanho do tick (movimento mínimo)
        const tickSize = 0.01; // 1 centavo por tick
        
        // Calcular próximo preço baseado na direção
        let nextPrice = this.market.currentPrice + (this.oscillationDirection * tickSize);
        
        // Verificar se atingiu os limites e inverter direção
        if (nextPrice >= this.config.priceRange.max) {
            nextPrice = this.config.priceRange.max;
            this.oscillationDirection = -1; // inverter para descer
        } else if (nextPrice <= this.config.priceRange.min) {
            nextPrice = this.config.priceRange.min;
            this.oscillationDirection = 1; // inverter para subir
        }
        
        // Adicionar pequeno ruído ocasional (10% de chance)
        if (Math.random() < 0.1) {
            const noise = (Math.random() - 0.5) * 0.02;
            nextPrice += noise;
            
            // Garantir que ainda está dentro dos limites após o ruído
            nextPrice = Math.max(
                this.config.priceRange.min,
                Math.min(this.config.priceRange.max, nextPrice)
            );
        }
        
        // Atualizar preço atual
        this.market.currentPrice = nextPrice;
        
        // Atualizar vela atual
        this.currentCandle.close = this.market.currentPrice;
        this.currentCandle.high = Math.max(this.currentCandle.high, this.market.currentPrice);
        this.currentCandle.low = Math.min(this.currentCandle.low, this.market.currentPrice);
        
        // Simular volume
        const volumeTick = Math.floor(Math.random() * 100 + 50);
        this.currentCandle.volume += volumeTick;
        this.market.volume += volumeTick;
        
        // Adicionar tick ao histórico
        this.currentCandle.ticks.push({
            price: this.market.currentPrice,
            volume: volumeTick,
            timestamp: Date.now()
        });
        
        // Atualizar máximas e mínimas da sessão
        this.market.sessionHigh = Math.max(this.market.sessionHigh, this.market.currentPrice);
        this.market.sessionLow = Math.min(this.market.sessionLow, this.market.currentPrice);
    }
    
    getTrendFactor() {
        switch (this.market.trend) {
            case 'bullish': return 1;
            case 'bearish': return -1;
            default: return 0;
        }
    }
    
    adjustTrend() {
        // Lógica simples de mudança de tendência
        const recentTicks = this.currentCandle.ticks.slice(-10);
        if (recentTicks.length < 5) return;
        
        const priceChanges = recentTicks.map((tick, i) => {
            if (i === 0) return 0;
            return tick.price - recentTicks[i - 1].price;
        }).slice(1);
        
        const avgChange = priceChanges.reduce((a, b) => a + b, 0) / priceChanges.length;
        
        if (avgChange > 0.05) {
            this.market.trend = 'bullish';
        } else if (avgChange < -0.05) {
            this.market.trend = 'bearish';
        } else {
            this.market.trend = 'neutral';
        }
        
        // Ajustar volatilidade
        const volatilityFactor = Math.abs(avgChange) * 10;
        this.market.volatility = Math.max(0.01, Math.min(0.05, volatilityFactor));
    }
    
    completeCandle() {
        if (this.currentCandle.ticks.length === 0) return;
        
        // Adicionar vela ao histórico
        const completedCandle = {
            ...this.currentCandle,
            endTime: Date.now(),
            duration: Date.now() - this.currentCandle.startTime
        };
        
        this.candleHistory.push(completedCandle);
        
        // Manter apenas o histórico visível + buffer
        const maxHistory = this.config.visibleHistory + 10;
        if (this.candleHistory.length > maxHistory) {
            this.candleHistory = this.candleHistory.slice(-maxHistory);
        }
        
        console.log('Vela completada:', completedCandle);
    }
    
    startNewCandle() {
        this.currentCandle = {
            open: this.market.currentPrice,
            high: this.market.currentPrice,
            low: this.market.currentPrice,
            close: this.market.currentPrice,
            volume: 0,
            startTime: Date.now(),
            ticks: []
        };
        
        console.log('Nova vela iniciada com preço:', this.market.currentPrice);
    }
    
    updateDisplay() {
        // Removidas as chamadas para updateLastPriceLine e updateCurrentTick
        this.updateCandlesticksDisplay();
        this.updateTimeAxis();
    }
    
    // Métodos removidos: updateLastPriceLine e updateCurrentTick
    // Estes métodos foram removidos pois a linha amarela e a bolinha do lastprice foram removidas da interface
    
    updateCandlesticksDisplay() {
        const container = this.elements.candlesticksContainer;
        const visibleCandles = this.candleHistory.slice(-this.config.visibleHistory);
        
        // Limpar container
        container.innerHTML = '';
        
        // Adicionar velas históricas
        visibleCandles.forEach((candle, index) => {
            const candleElement = this.createCandleElement(candle, false);
            container.appendChild(candleElement);
        });
        
        // Adicionar vela atual se tiver ticks
        if (this.currentCandle.ticks.length > 0) {
            const currentCandleElement = this.createCandleElement(this.currentCandle, true);
            container.appendChild(currentCandleElement);
        }
    }
    
    createCandleElement(candle, isCurrent = false) {
        const isGreen = candle.close >= candle.open;
        const priceRange = this.config.priceRange.max - this.config.priceRange.min;
        const chartHeight = this.elements.chartArea.offsetHeight - 40; // padding
        
        // Calcular dimensões
        const bodyHeight = Math.max(2, Math.abs(candle.close - candle.open) / priceRange * chartHeight);
        const totalHeight = Math.max(bodyHeight + 10, (candle.high - candle.low) / priceRange * chartHeight);
        
        // Calcular posições das wicks
        const topWickHeight = (candle.high - Math.max(candle.open, candle.close)) / priceRange * chartHeight;
        const bottomWickHeight = (Math.min(candle.open, candle.close) - candle.low) / priceRange * chartHeight;
        
        const candleDiv = document.createElement('div');
        candleDiv.className = `trading-candlestick ${isGreen ? 'bullish' : 'bearish'} ${isCurrent ? 'current' : ''}`;
        candleDiv.style.height = `${totalHeight}px`;
        candleDiv.style.minWidth = '8px';
        candleDiv.style.position = 'relative';
        candleDiv.style.margin = '0 1px';
        
        candleDiv.innerHTML = `
            <div class="wick top" style="
                height: ${Math.max(0, topWickHeight)}px;
                width: 1px;
                background: ${isGreen ? '#22c55e' : '#ef4444'};
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
            "></div>
            <div class="body" style="
                height: ${bodyHeight}px;
                width: 8px;
                background: ${isGreen ? '#22c55e' : '#ef4444'};
                position: absolute;
                top: ${topWickHeight}px;
                left: 50%;
                transform: translateX(-50%);
                border-radius: 1px;
                ${isCurrent ? 'animation: pulse 1s infinite;' : ''}
            "></div>
            <div class="wick bottom" style="
                height: ${Math.max(0, bottomWickHeight)}px;
                width: 1px;
                background: ${isGreen ? '#22c55e' : '#ef4444'};
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
            "></div>
        `;
        
        // Tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'candle-tooltip';
        tooltip.innerHTML = `
            <div class="tooltip-ohlc">
                <div>O: $${candle.open.toFixed(2)}</div>
                <div>H: $${candle.high.toFixed(2)}</div>
                <div>L: $${candle.low.toFixed(2)}</div>
                <div>C: $${candle.close.toFixed(2)}</div>
            </div>
            <div class="tooltip-volume">Vol: ${candle.volume.toLocaleString()}</div>
            <div class="tooltip-time">${this.formatTime(candle.startTime)}</div>
        `;
        
        tooltip.style.cssText = `
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 100;
            border: 1px solid ${isGreen ? '#22c55e' : '#ef4444'};
        `;
        
        candleDiv.appendChild(tooltip);
        
        // Mostrar tooltip no hover
        candleDiv.addEventListener('mouseenter', () => {
            tooltip.style.opacity = '1';
        });
        
        candleDiv.addEventListener('mouseleave', () => {
            tooltip.style.opacity = '0';
        });
        
        return candleDiv;
    }
    
    // Método removido: updateCandleProgress
    // Este método foi removido pois os elementos de progresso da vela foram removidos da interface
    
    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}

// Adicionar estilos CSS para animações
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .trading-candlestick {
        transition: transform 0.2s ease;
    }
    
    .trading-candlestick:hover {
        transform: scale(1.1);
    }
    
    .trading-candlestick.current {
        filter: brightness(1.2);
    }
`;
document.head.appendChild(style);