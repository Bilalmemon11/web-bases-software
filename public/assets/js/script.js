// ==================== GLOBAL CURRENCY FORMATTER ====================

/**
 * Format amount in Pakistani currency style
 * @param {number} amount - The amount to format
 * @returns {string} Formatted currency string
 */
function formatPakistaniCurrency(amount) {
    if (!amount || isNaN(amount)) return '';

    const num = parseFloat(amount);
    if (num === 0) return '';

    // Convert to Pakistani numbering system (Lakh/Crore)
    let formatted = '';
    if (num >= 10000000) { // 1 Crore
        formatted = `₨ ${(num / 10000000).toFixed(2)} Crore`;
    } else if (num >= 100000) { // 1 Lakh
        formatted = `₨ ${(num / 100000).toFixed(2)} Lakh`;
    } else if (num >= 1000) { // 1 Thousand
        formatted = `₨ ${(num / 1000).toFixed(2)} Thousand`;
    } else {
        formatted = `₨ ${num.toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    console.log(formatted);
    return formatted;
}

/**
 * Setup currency formatter for any input field
 * @param {HTMLInputElement} input - The input element
 * @param {HTMLElement} displayElement - Element to show formatted value
 */
function setupCurrencyFormatter(input, displayElement) {
    if (!input || !displayElement) return;

    input.addEventListener('input', function () {
        const amount = parseFloat(this.value) || 0;
        displayElement.textContent = formatPakistaniCurrency(amount);
        displayElement.classList.toggle('text-primary', amount > 0);
    });

    // Format on load if there's a value
    if (input.value) {
        const amount = parseFloat(input.value) || 0;
        displayElement.textContent = formatPakistaniCurrency(amount);
        displayElement.classList.toggle('text-primary', amount > 0);
    }
}

function currencyFormat(input, displayElementId) {
    const displayElement = document.getElementById(displayElementId);

    // Format on load if there's a value
    if (input.value) {
        const amount = parseFloat(input.value) || 0;
        displayElement.textContent = formatPakistaniCurrency(amount);
        displayElement.classList.toggle('text-primary', amount > 0);
    }
}
// Auto-calculate profit share based on investment
function calculateProfitShare(investmentAmount, totalInvestment) {
    if (!totalInvestment || totalInvestment === 0) return '';
    const share = (investmentAmount / totalInvestment) * 100;
    return share.toFixed(2);
}
function calculateProfitShareAndDisplay(investmentInputId, displayInvestmentId, totalInvestment) {

    if (!totalInvestment || totalInvestment === 0) return '';
    const investmentInput = document.getElementById(investmentInputId);
    const displayInvestment = document.getElementById(displayInvestmentId);
    if (!investmentInput || !displayInvestment) return '';

    const investmentAmount = parseFloat(investmentInput.value) || 0;
    const share = (investmentAmount / totalInvestment) * 100;
    displayInvestment.value = share.toFixed(2);
}

const toggleClass = (elementId, className) => {
    const element = document.getElementById(elementId);
    element.classList.toggle(className);
};
const toggleClassMulti = (elementIds, className) => {
    elementIds = Array.isArray(elementIds) ? elementIds : [elementIds];
    elementIds.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.toggle(className);
        }
    });
};
const addClass = (elementId, className) => {
    const element = document.getElementById(elementId);
    element.classList.add(className);
};
const removeClass = (elementId, className) => {
    const element = document.getElementById(elementId);
    element.classList.remove(className);
};


// Apply multiple class additions from URL parameters
function applyClassesFromURL() {
    const params = new URLSearchParams(window.location.search);
    const addClassParam = params.get('addclass');
    const removeClassParam = params.get('removeclass');

    if (addClassParam) {
        // Split by commas for multiple element-class pairs
        const pairs = addClassParam.split(',');

        pairs.forEach(pair => {
            const [elementId, classPart] = pair.split('@');
            if (elementId && classPart) {
                const element = document.getElementById(elementId.trim());
                if (element) {
                    // Support multiple classes separated by "+"
                    const classes = classPart.split('+').map(c => c.trim());
                    element.classList.add(...classes);
                }
            }
        });
    }
    if (removeClassParam) {
        // Split by commas for multiple element-class pairs
        const pairs = removeClassParam.split(',');

        pairs.forEach(pair => {
            const [elementId, classPart] = pair.split('@');
            if (elementId && classPart) {
                const element = document.getElementById(elementId.trim());
                if (element) {
                    // Support multiple classes separated by "+"
                    const classes = classPart.split('+').map(c => c.trim());
                    element.classList.remove(...classes);
                }
            }
        });
    }
}

// Execute on page load
document.addEventListener('DOMContentLoaded', applyClassesFromURL);
function setNull(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.value = '';
    }
}