import './bootstrap'
import Chart from 'chart.js/auto';

// Ekspos ke global scope untuk Alpine.js/Filament
window.Chart = Chart;

// Optional: Konfigurasi default
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6b7280';
