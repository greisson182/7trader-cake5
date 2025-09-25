/**
 * Automatic Value Color System
 * Aplica cores condicionais para spans com valores positivos/negativos
 */

document.addEventListener('DOMContentLoaded', function() {
    applyValueColors();
});

function applyValueColors() {
    // Seleciona todos os spans que podem conter valores numéricos
    const spans = document.querySelectorAll('span');
    
    spans.forEach(span => {
        const text = span.textContent.trim();
        
        // Verifica se o span contém um valor monetário ou percentual
        if (isNumericValue(text)) {
            const numericValue = extractNumericValue(text);
            
            if (numericValue > 0) {
                span.classList.add('value-positive');
                span.classList.remove('value-negative');
            } else if (numericValue < 0) {
                span.classList.add('value-negative');
                span.classList.remove('value-positive');
            }
        }
    });
}

function isNumericValue(text) {
    // Padrões para identificar valores numéricos
    const patterns = [
        /^\$?-?\d+\.?\d*$/,           // $123.45, -123.45, 123
        /^\$?-?\d{1,3}(,\d{3})*\.?\d*$/, // $1,234.56, -1,234
        /^-?\d+\.?\d*%$/,            // 12.5%, -5%
        /^-?\d+\.?\d*$/              // 123.45, -123
    ];
    
    return patterns.some(pattern => pattern.test(text));
}

function extractNumericValue(text) {
    // Remove símbolos e extrai o valor numérico
    const cleanText = text.replace(/[\$,%]/g, '');
    return parseFloat(cleanText) || 0;
}

// Função para aplicar cores manualmente a elementos específicos
function setValueColor(element, value) {
    if (value > 0) {
        element.classList.add('value-positive');
        element.classList.remove('value-negative');
    } else if (value < 0) {
        element.classList.add('value-negative');
        element.classList.remove('value-positive');
    }
}

// Exporta funções para uso global
window.ValueColors = {
    apply: applyValueColors,
    setColor: setValueColor,
    isNumeric: isNumericValue,
    extract: extractNumericValue
};