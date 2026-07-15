/**
 * Theme Management System
 * Handles light/dark mode switching with localStorage persistence
 */

class ThemeManager {
    constructor() {
        this.themeToggle = null;
        this.currentTheme = this.getStoredTheme() || this.getSystemTheme();
        
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupTheme();
            });
        } else {
            this.setupTheme();
        }
    }

    setupTheme() {
        const themeToggle = document.getElementById('theme-toggle');
        const themeLabel = document.querySelector('.theme-toggle');
        
        if (themeToggle && themeLabel) {
            this.themeToggle = themeToggle;
            
            // Set initial theme
            const storedTheme = this.getStoredTheme();
            const systemTheme = this.getSystemTheme();
            const initialTheme = storedTheme || systemTheme;
            
            this.setTheme(initialTheme);
            
            // Add single event listener to the label (covers all clicks)
            themeLabel.addEventListener('click', (e) => {
                // Prevent default label behavior to avoid double triggering
                e.preventDefault();
                console.log('Clique no botão de tema detectado, alternando...');
                this.toggleTheme();

            });
            
            console.log('Theme toggle initialized successfully');
        } else {
            console.error('Theme toggle elements not found');
        }
    }

    getSystemTheme() {
        // Tema fixo em dark - a preferência de cor do sistema operacional é ignorada.
        return 'dark';
    }

    getStoredTheme() {
        return localStorage.getItem('theme');
    }

    setTheme(theme) {
        this.currentTheme = theme;
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        // Update meta theme-color for mobile browsers
        this.updateMetaThemeColor(theme);
        
        // Force update toggle state
        this.updateToggleState();
        
        console.log(`Theme set to: ${theme}`);
        console.log(`data-theme attribute: ${document.documentElement.getAttribute('data-theme')}`);
    }

    updateMetaThemeColor(theme) {
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            const color = theme === 'light' ? '#ffffff' : '#0a0a0a';
            metaThemeColor.setAttribute('content', color);
        }
    }

    updateToggleState() {
        if (this.themeToggle) {
            this.themeToggle.checked = this.currentTheme === 'light';
        }
    }

    toggleTheme() {
        const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        console.log(`Toggling from ${this.currentTheme} to ${newTheme}`);
        this.setTheme(newTheme);
        
        // Add smooth transition effect
        this.addTransitionEffect();
    }

    addTransitionEffect() {
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        
        // Remove transition after animation completes
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }
}

// Initialize theme manager
let themeManager;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        themeManager = new ThemeManager();
    });
} else {
    themeManager = new ThemeManager();
}

// Tema fixo em dark: mudanças de preferência de cor do sistema operacional são ignoradas de propósito.