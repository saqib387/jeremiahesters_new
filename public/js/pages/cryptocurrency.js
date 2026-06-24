/**
 * Cryptocurrency module for handling cryptocurrency-related functions
 */
const Cryptocurrency = (function() {
    
    /**
     * Initialize the module
     */
    function init() {
        attachEventListeners();
        initCharts();
    }
    
    /**
     * Attach event listeners to DOM elements
     */
    function attachEventListeners() {
        // Buy form amount input
        const amountInput = document.getElementById('amount');
        if (amountInput) {
            amountInput.addEventListener('input', updateTotalPrice);
        }
        
        // Transaction filters
        const filterBtns = document.querySelectorAll('.transaction-filter');
        if (filterBtns.length > 0) {
            filterBtns.forEach(btn => {
                btn.addEventListener('click', filterTransactions);
            });
        }
        
        // Copy wallet address button
        const copyAddressBtn = document.getElementById('copy-wallet-address');
        if (copyAddressBtn) {
            copyAddressBtn.addEventListener('click', copyWalletAddress);
        }
    }
    
    /**
     * Format a number as currency
     * @param {number} value - The value to format
     * @param {number} decimals - Number of decimal places
     * @returns {string} - Formatted currency string
     */
    function formatCurrency(value, decimals = 2) {
        if (!value && value !== 0) return '$0.00';
        return '$' + parseFloat(value).toFixed(decimals);
    }
    
    /**
     * Format a large number with commas
     * @param {number} value - The value to format
     * @returns {string} - Formatted number string
     */
    function formatNumber(value) {
        if (!value && value !== 0) return '0';
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    /**
     * Update the total price in buy/sell form
     */
    function updateTotalPrice() {
        const amountInput = document.getElementById('amount');
        const pricePerToken = document.getElementById('price-per-token');
        const totalPriceElem = document.getElementById('total-price');
        
        if (amountInput && pricePerToken && totalPriceElem) {
            const amount = parseInt(amountInput.value) || 0;
            const price = parseFloat(pricePerToken.value) || 0;
            const totalPrice = amount * price;
            
            totalPriceElem.textContent = formatCurrency(totalPrice);
        }
    }
    
    /**
     * Filter transactions by type
     * @param {Event} e - Click event
     */
    function filterTransactions(e) {
        e.preventDefault();
        
        const filterType = e.target.dataset.filter;
        const transactions = document.querySelectorAll('.transaction-row');
        
        // Update active filter button
        document.querySelectorAll('.transaction-filter').forEach(btn => {
            btn.classList.remove('active');
        });
        e.target.classList.add('active');
        
        // Filter transactions
        transactions.forEach(row => {
            if (filterType === 'all' || row.dataset.type === filterType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    /**
     * Copy wallet address to clipboard
     * @param {Event} e - Click event
     */
    function copyWalletAddress(e) {
        e.preventDefault();
        
        const walletAddress = document.getElementById('wallet-address');
        if (walletAddress) {
            navigator.clipboard.writeText(walletAddress.textContent.trim())
                .then(() => {
                    showToast('Address copied to clipboard!');
                })
                .catch(err => {
                    console.error('Failed to copy address: ', err);
                });
        }
    }
    
    /**
     * Show a toast notification
     * @param {string} message - Message to display
     */
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast show';
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="mr-auto">Notification</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    /**
     * Initialize charts on the page
     */
    function initCharts() {
        const priceChartElem = document.getElementById('price-chart');
        if (priceChartElem && window.Chart) {
            initPriceChart(priceChartElem);
        }
    }
    
    /**
     * Initialize price history chart
     * @param {HTMLElement} canvas - Canvas element for the chart
     */
    function initPriceChart(canvas) {
        // Get chart data from data attributes
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');
        
        // Create the chart
        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Price History',
                    data: values,
                    borderColor: '#e83e8c',
                    backgroundColor: 'rgba(232, 62, 140, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 2,
                    pointBackgroundColor: '#e83e8c'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' $' + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    
    return {
        init: init,
        formatCurrency: formatCurrency,
        formatNumber: formatNumber
    };
})();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    Cryptocurrency.init();
}); 