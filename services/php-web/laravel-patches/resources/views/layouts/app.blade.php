<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Space Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>
    #map{height:400px; border-radius: 8px; overflow: hidden;}
    
    /* Анимации */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
      from { transform: translateX(-20px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes orbitPulse {
      0%, 100% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    @keyframes trailGlow {
      0%, 100% { opacity: 0.6; }
      50% { opacity: 1; }
    }
    
    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }
    
    .slide-in {
      animation: slideIn 0.4s ease-out;
    }
    
    .card {
      transition: transform 0.3s, box-shadow 0.3s;
      border: none;
      background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }
    
    /* Градиенты для карточек метрик */
    .card-metric-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    
    .card-metric-success {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }
    
    .card-metric-info {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }
    
    .card-metric-warning {
      background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      color: white;
    }
    
    .card-metric-primary .text-muted,
    .card-metric-success .text-muted,
    .card-metric-info .text-muted,
    .card-metric-warning .text-muted {
      color: rgba(255,255,255,0.8) !important;
    }
    
    .table tbody tr {
      transition: background-color 0.2s;
    }
    
    .table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .btn {
      transition: all 0.2s;
    }
    
    .btn:hover {
      transform: scale(1.05);
    }
    
    .navbar-brand {
      font-weight: bold;
      transition: color 0.2s;
    }
    
    .navbar-brand:hover {
      color: #0d6efd !important;
    }
    
    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(0,0,0,.1);
      border-radius: 50%;
      border-top-color: #0d6efd;
      animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    .pulse {
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
    
    /* Стили для траектории МКС */
    .iss-trail {
      stroke: #ff6b6b;
      stroke-width: 4;
      fill: none;
      stroke-linecap: round;
      stroke-linejoin: round;
      filter: drop-shadow(0 0 4px rgba(255, 107, 107, 0.6));
      animation: trailGlow 3s ease-in-out infinite;
    }
    
    .iss-marker {
      animation: orbitPulse 2s ease-in-out infinite;
    }
    
    /* Улучшенные графики */
    .chart-container {
      position: relative;
      height: 110px;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Инициализация tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  </script>
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
<nav class="navbar navbar-expand-lg navbar-dark mb-4 shadow-lg" style="background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 100%); backdrop-filter: blur(10px);">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ route('dashboard') }}" style="font-size: 1.5rem;">
      <i class="bi bi-rocket-takeoff me-2" style="font-size: 1.8rem;"></i>
      Space Spectator
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center {{ request()->routeIs('osdr') ? 'active fw-bold' : '' }}" href="{{ route('osdr') }}">
            <i class="bi bi-database me-2"></i>
            OSDR
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center {{ request()->routeIs('telemetry*') ? 'active fw-bold' : '' }}" href="{{ route('telemetry') }}">
            <i class="bi bi-graph-up me-2"></i>
            Telemetry
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container fade-in" style="background: rgba(255,255,255,0.95); border-radius: 15px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
@yield('content')
</div>
<style>
  .navbar-nav .nav-link {
    transition: all 0.3s;
    border-radius: 8px;
    margin: 0 0.25rem;
    padding: 0.5rem 1rem !important;
  }
  .navbar-nav .nav-link:hover {
    background: rgba(255,255,255,0.1);
    transform: translateY(-2px);
  }
  .navbar-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
