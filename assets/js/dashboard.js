/**
 * Custom JavaScript for PHP CRUD Admin Dashboard
 * Author: Admin CRUD System
 * Version: 1.0.0
 */

// Global Variables
const AdminDashboard = {
  version: "1.0.0",
  debug: false,

  // Initialize the dashboard
  init: function () {
    this.setupEventListeners();
    this.initializeComponents();
    this.checkBrowserCompatibility();
    console.log("🚀 Admin Dashboard initialized successfully!");
  },

  // Setup event listeners
  setupEventListeners: function () {
    // Form validations
    this.setupFormValidation();

    // Table interactions
    this.setupTableInteractions();

    // Modal handlers
    this.setupModalHandlers();

    // Search functionality
    this.setupSearchHandlers();
  },

  // Initialize components
  initializeComponents: function () {
    // Initialize tooltips
    this.initTooltips();

    // Initialize alerts auto-hide
    this.initAutoHideAlerts();

    // Initialize loading states
    this.initLoadingStates();
  },

  // Form validation setup
  setupFormValidation: function () {
    const forms = document.querySelectorAll("form");
    forms.forEach((form) => {
      form.addEventListener("submit", function (e) {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add("was-validated");
      });
    });
  },

  // Table interactions
  setupTableInteractions: function () {
    // Row hover effects
    const tableRows = document.querySelectorAll("table tbody tr");
    tableRows.forEach((row) => {
      row.addEventListener("mouseenter", function () {
        this.style.transform = "scale(1.01)";
        this.style.transition = "transform 0.2s ease";
      });

      row.addEventListener("mouseleave", function () {
        this.style.transform = "scale(1)";
      });
    });

    // Sortable table headers
    const sortableHeaders = document.querySelectorAll("th[data-sortable]");
    sortableHeaders.forEach((header) => {
      header.style.cursor = "pointer";
      header.addEventListener("click", function () {
        AdminDashboard.sortTable(this);
      });
    });
  },

  // Modal handlers
  setupModalHandlers: function () {
    // Confirmation modals
    const deleteButtons = document.querySelectorAll("[data-confirm]");
    deleteButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        const message = this.getAttribute("data-confirm") || "Are you sure?";
        if (!confirm(message)) {
          e.preventDefault();
        }
      });
    });
  },

  // Search handlers
  setupSearchHandlers: function () {
    const searchInputs = document.querySelectorAll("[data-search]");
    searchInputs.forEach((input) => {
      input.addEventListener("input", function () {
        AdminDashboard.performSearch(this);
      });
    });
  },

  // Initialize tooltips
  initTooltips: function () {
    const tooltipElements = document.querySelectorAll(
      '[data-bs-toggle="tooltip"]'
    );
    tooltipElements.forEach((element) => {
      new bootstrap.Tooltip(element);
    });
  },

  // Auto-hide alerts
  initAutoHideAlerts: function () {
    const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");
    alerts.forEach((alert) => {
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 5000);
    });
  },

  // Loading states
  initLoadingStates: function () {
    const loadingButtons = document.querySelectorAll(".btn[data-loading]");
    loadingButtons.forEach((button) => {
      button.addEventListener("click", function () {
        AdminDashboard.showButtonLoading(this);
      });
    });
  },

  // Show button loading state
  showButtonLoading: function (button) {
    const originalText = button.innerHTML;
    button.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
    button.disabled = true;

    // Reset after 3 seconds (adjust as needed)
    setTimeout(() => {
      button.innerHTML = originalText;
      button.disabled = false;
    }, 3000);
  },

  // Perform search
  performSearch: function (input) {
    const searchTerm = input.value.toLowerCase();
    const targetTable = document.querySelector(
      input.getAttribute("data-search")
    );

    if (targetTable) {
      const rows = targetTable.querySelectorAll("tbody tr");
      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? "" : "none";
      });
    }
  },

  // Sort table
  sortTable: function (header) {
    const table = header.closest("table");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const columnIndex = Array.from(header.parentNode.children).indexOf(header);
    const isAscending = header.classList.contains("sort-asc");

    // Remove existing sort classes
    header.parentNode.querySelectorAll("th").forEach((th) => {
      th.classList.remove("sort-asc", "sort-desc");
    });

    // Add new sort class
    header.classList.add(isAscending ? "sort-desc" : "sort-asc");

    // Sort rows
    rows.sort((a, b) => {
      const aText = a.cells[columnIndex].textContent.trim();
      const bText = b.cells[columnIndex].textContent.trim();

      // Check if numeric
      const aNum = parseFloat(aText);
      const bNum = parseFloat(bText);

      if (!isNaN(aNum) && !isNaN(bNum)) {
        return isAscending ? bNum - aNum : aNum - bNum;
      } else {
        return isAscending
          ? bText.localeCompare(aText)
          : aText.localeCompare(bText);
      }
    });

    // Re-append sorted rows
    rows.forEach((row) => tbody.appendChild(row));
  },

  // Check browser compatibility
  checkBrowserCompatibility: function () {
    const isIE =
      navigator.userAgent.indexOf("MSIE") !== -1 ||
      navigator.appVersion.indexOf("Trident/") > -1;
    if (isIE) {
      alert(
        "⚠️ This application works best in modern browsers. Please consider upgrading."
      );
    }
  },

  // Utility functions
  utils: {
    // Show notification
    showNotification: function (message, type = "info") {
      const alertDiv = document.createElement("div");
      alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
      alertDiv.style.cssText =
        "top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
      alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

      document.body.appendChild(alertDiv);

      // Auto remove after 5 seconds
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
    },

    // Format currency
    formatCurrency: function (amount) {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
      }).format(amount);
    },

    // Format date
    formatDate: function (date) {
      return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      }).format(new Date(date));
    },

    // Debounce function
    debounce: function (func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    },
  },
};

// Chart utilities
const ChartUtils = {
  // Default colors
  colors: {
    primary: "#667eea",
    secondary: "#764ba2",
    success: "#28a745",
    warning: "#ffc107",
    danger: "#dc3545",
    info: "#17a2b8",
  },

  // Create pie chart
  createPieChart: function (ctx, data, options = {}) {
    return new Chart(ctx, {
      type: "pie",
      data: data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
        ...options,
      },
    });
  },

  // Create bar chart
  createBarChart: function (ctx, data, options = {}) {
    return new Chart(ctx, {
      type: "bar",
      data: data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
        ...options,
      },
    });
  },

  // Create line chart
  createLineChart: function (ctx, data, options = {}) {
    return new Chart(ctx, {
      type: "line",
      data: data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        tension: 0.1,
        ...options,
      },
    });
  },
};

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  AdminDashboard.init();
});

// Export for global use
window.AdminDashboard = AdminDashboard;
window.ChartUtils = ChartUtils;
