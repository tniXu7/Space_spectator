@extends('layouts.app')

@section('content')
<div class="container pb-5">
  {{-- верхние карточки с метриками --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-0 h-100 card-metric-primary">
        <div class="card-body text-center">
          <div class="mb-2">
            <i class="bi bi-speedometer2" style="font-size: 2rem;"></i>
          </div>
          <div class="small mb-1">Скорость МКС</div>
          <div class="fs-3 fw-bold">
            {{ isset(($iss['payload'] ?? [])['velocity']) ? number_format($iss['payload']['velocity'],0,'',' ') : '—' }}
            <small class="fs-6">км/ч</small>
          </div>
          <div class="small mt-1" data-bs-toggle="tooltip" title="Текущая скорость Международной космической станции">
            <i class="bi bi-info-circle"></i> Текущая скорость
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-0 h-100 card-metric-success">
        <div class="card-body text-center">
          <div class="mb-2">
            <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
          </div>
          <div class="small mb-1">Высота МКС</div>
          <div class="fs-3 fw-bold">
            {{ isset(($iss['payload'] ?? [])['altitude']) ? number_format($iss['payload']['altitude'],0,'',' ') : '—' }}
            <small class="fs-6">км</small>
          </div>
          <div class="small mt-1" data-bs-toggle="tooltip" title="Высота орбиты над поверхностью Земли">
            <i class="bi bi-info-circle"></i> Высота орбиты
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-0 h-100 card-metric-info">
        <div class="card-body text-center">
          <div class="mb-2">
            <i class="bi bi-image" style="font-size: 2rem;"></i>
          </div>
          <div class="small mb-1">JWST изображений</div>
          <div class="fs-3 fw-bold">
            <span id="jwstCount">—</span>
          </div>
          <div class="small mt-1" data-bs-toggle="tooltip" title="Количество загруженных изображений с телескопа JWST">
            <i class="bi bi-info-circle"></i> В галерее
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm border-0 h-100 card-metric-warning">
        <div class="card-body text-center">
          <div class="mb-2">
            <i class="bi bi-stars" style="font-size: 2rem;"></i>
          </div>
          <div class="small mb-1">Астрономических событий</div>
          <div class="fs-3 fw-bold">
            <span id="astroCount">—</span>
          </div>
          <div class="small mt-1" data-bs-toggle="tooltip" title="Количество найденных астрономических событий">
            <i class="bi bi-info-circle"></i> Найдено
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- левая колонка: Информация о JWST --}}
    <div class="col-lg-7">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="me-2 text-info" viewBox="0 0 16 16">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
            </svg>
            JWST — информация
          </h5>
          <div class="text-muted">
            <p class="mb-2">
              <strong>James Webb Space Telescope</strong> — космический телескоп для наблюдения в инфракрасном диапазоне.
            </p>
            <p class="mb-0 small">
              Используйте фильтры ниже для поиска изображений по инструменту, программе или суффиксу. 
              Основная галерея находится внизу страницы.
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- правая колонка: карта МКС --}}
    <div class="col-lg-5">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title m-0 d-flex align-items-center">
              <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
              МКС — траектория движения
            </h5>
            <div class="d-flex gap-2">
              <button id="toggleTrail" class="btn btn-sm btn-outline-primary" title="Показать/скрыть траекторию">
                <i class="bi bi-diagram-3"></i>
              </button>
              <button id="centerMap" class="btn btn-sm btn-outline-secondary" title="Центрировать карту">
                <i class="bi bi-crosshair"></i>
              </button>
            </div>
          </div>
          <div class="mb-2">
            <div class="d-flex justify-content-between align-items-center small text-muted">
              <span data-bs-toggle="tooltip" title="Карта обновляется каждые 15 секунд">
                <i class="bi bi-info-circle"></i> Траектория за последние 24 часа
              </span>
              <span id="issStatus" class="badge bg-success pulse">
                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Активно
              </span>
            </div>
          </div>
          <div id="map" class="rounded mb-3 border shadow-sm"></div>
          <div class="row g-2">
            <div class="col-6">
              <div class="small text-muted text-center mb-1">
                <i class="bi bi-speedometer2 me-1"></i>Скорость
              </div>
              <div class="chart-container">
                <canvas id="issSpeedChart"></canvas>
              </div>
            </div>
            <div class="col-6">
              <div class="small text-muted text-center mb-1">
                <i class="bi bi-arrow-up-circle me-1"></i>Высота
              </div>
              <div class="chart-container">
                <canvas id="issAltChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- НИЖНЯЯ ПОЛОСА: НОВАЯ ГАЛЕРЕЯ JWST --}}
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title m-0 d-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="me-2 text-info" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
              </svg>
              JWST — последние изображения
            </h5>
            <form id="jwstFilter" class="row g-2 align-items-center">
              <div class="col-auto">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Выберите источник изображений">Источник</label>
                <select class="form-select form-select-sm" name="source" id="srcSel">
                  <option value="jpg" selected>Все JPG</option>
                  <option value="suffix">По суффиксу</option>
                  <option value="program">По программе</option>
                </select>
              </div>
              <div class="col-auto">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Введите суффикс файла (например: _cal, _thumb)" style="display:none" id="suffixLabel">Суффикс</label>
                <input type="text" class="form-control form-control-sm" name="suffix" id="suffixInp" placeholder="_cal / _thumb" style="width:140px;display:none">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Введите ID программы (например: 2734)" style="display:none" id="progLabel">Программа</label>
                <input type="text" class="form-control form-control-sm" name="program" id="progInp" placeholder="2734" style="width:110px;display:none">
              </div>
              <div class="col-auto">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Выберите инструмент телескопа">Инструмент</label>
                <select class="form-select form-select-sm" name="instrument" style="width:130px">
                  <option value="">Любой инструмент</option>
                  <option>NIRCam</option><option>MIRI</option><option>NIRISS</option><option>NIRSpec</option><option>FGS</option>
                </select>
              </div>
              <div class="col-auto">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Количество изображений на странице">Количество</label>
                <select class="form-select form-select-sm" name="perPage" style="width:90px">
                  <option>12</option><option selected>24</option><option>36</option><option>48</option>
                </select>
              </div>
              <div class="col-auto">
                <button class="btn btn-sm btn-primary" type="submit">
                  <i class="bi bi-search me-1"></i>Показать
                </button>
              </div>
            </form>
          </div>

          <style>
            .jwst-slider{position:relative}
            .jwst-track{
              display:flex; gap:.75rem; overflow:auto; scroll-snap-type:x mandatory; padding:.25rem;
            }
            .jwst-item{flex:0 0 180px; scroll-snap-align:start}
            .jwst-item img{width:100%; height:180px; object-fit:cover; border-radius:.5rem}
            .jwst-cap{font-size:.85rem; margin-top:.25rem}
            .jwst-nav{position:absolute; top:40%; transform:translateY(-50%); z-index:2}
            .jwst-prev{left:-.25rem} .jwst-next{right:-.25rem}
          </style>

          <div class="jwst-slider">
            <button class="btn btn-light border jwst-nav jwst-prev" type="button" aria-label="Prev">‹</button>
            <div id="jwstTrack" class="jwst-track border rounded"></div>
            <button class="btn btn-light border jwst-nav jwst-next" type="button" aria-label="Next">›</button>
          </div>

          <div id="jwstInfo" class="small text-muted mt-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function () {
  // ====== УЛУЧШЕННАЯ КАРТА И ГРАФИКИ МКС ======
  if (typeof L !== 'undefined' && typeof Chart !== 'undefined') {
    const last = @json(($iss['payload'] ?? []));
    let lat0 = Number(last.latitude || 0), lon0 = Number(last.longitude || 0);
    
    // Инициализация карты с улучшенными настройками
    const map = L.map('map', { 
      attributionControl: false,
      zoomControl: true,
      scrollWheelZoom: true
    }).setView([lat0||0, lon0||0], lat0 ? 3 : 2);
    
    // Темная карта для лучшей видимости траектории
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap contributors',
      noWrap: true,
      maxZoom: 18
    }).addTo(map);
    
    // Создаем кастомную иконку для МКС
    const issIcon = L.divIcon({
      className: 'iss-marker',
      html: '<div style="background: #ff6b6b; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(255,107,107,0.8);"></div>',
      iconSize: [20, 20],
      iconAnchor: [10, 10]
    });
    
    // Траектория с улучшенным стилем
    const trail = L.polyline([], {
      weight: 4,
      color: '#ff6b6b',
      opacity: 0.8,
      smoothFactor: 1
    }).addTo(map);
    
    // Маркер МКС
    const marker = L.marker([lat0||0, lon0||0], { 
      icon: issIcon,
      title: 'Международная космическая станция'
    }).addTo(map);
    
    // Попап с информацией
    marker.bindPopup(`
      <div class="text-center">
        <h6 class="mb-2">🚀 МКС</h6>
        <div class="small">
          <div><strong>Широта:</strong> ${lat0.toFixed(4)}°</div>
          <div><strong>Долгота:</strong> ${lon0.toFixed(4)}°</div>
          <div><strong>Скорость:</strong> ${(last.velocity || 0).toFixed(0)} км/ч</div>
          <div><strong>Высота:</strong> ${(last.altitude || 0).toFixed(0)} км</div>
        </div>
      </div>
    `);
    
    let trailVisible = true;
    let animationIndex = 0;
    
    // Кнопки управления
    document.getElementById('toggleTrail')?.addEventListener('click', () => {
      trailVisible = !trailVisible;
      if (trailVisible) {
        map.addLayer(trail);
        document.getElementById('toggleTrail').innerHTML = '<i class="bi bi-diagram-3"></i>';
      } else {
        map.removeLayer(trail);
        document.getElementById('toggleTrail').innerHTML = '<i class="bi bi-diagram-3-fill"></i>';
      }
    });
    
    document.getElementById('centerMap')?.addEventListener('click', () => {
      if (marker.getLatLng().lat && marker.getLatLng().lng) {
        map.setView(marker.getLatLng(), 4, { animate: true, duration: 1 });
      }
    });

    // Улучшенные графики
    const speedChart = new Chart(document.getElementById('issSpeedChart'), {
      type: 'line',
      data: { 
        labels: [], 
        datasets: [{ 
          label: 'Скорость (км/ч)', 
          data: [],
          borderColor: '#667eea',
          backgroundColor: 'rgba(102, 126, 234, 0.1)',
          tension: 0.4,
          fill: true,
          pointRadius: 2,
          pointHoverRadius: 4
        }] 
      },
      options: { 
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { 
          x: { display: false },
          y: { 
            display: true,
            ticks: { font: { size: 10 } }
          }
        },
        animation: { duration: 750 }
      }
    });
    
    const altChart = new Chart(document.getElementById('issAltChart'), {
      type: 'line',
      data: { 
        labels: [], 
        datasets: [{ 
          label: 'Высота (км)', 
          data: [],
          borderColor: '#f5576c',
          backgroundColor: 'rgba(245, 87, 108, 0.1)',
          tension: 0.4,
          fill: true,
          pointRadius: 2,
          pointHoverRadius: 4
        }] 
      },
      options: { 
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { 
          x: { display: false },
          y: { 
            display: true,
            ticks: { font: { size: 10 } }
          }
        },
        animation: { duration: 750 }
      }
    });

    // Функция анимации движения маркера
    function animateMarker(pts) {
      if (!pts || pts.length === 0) return;
      
      const currentPos = marker.getLatLng();
      const targetPos = L.latLng(pts[pts.length - 1]);
      
      if (currentPos.lat !== targetPos.lat || currentPos.lng !== targetPos.lng) {
        // Плавное перемещение маркера
        const steps = 20;
        let step = 0;
        const latStep = (targetPos.lat - currentPos.lat) / steps;
        const lngStep = (targetPos.lng - currentPos.lng) / steps;
        
        const animate = setInterval(() => {
          step++;
          const newLat = currentPos.lat + (latStep * step);
          const newLng = currentPos.lng + (lngStep * step);
          marker.setLatLng([newLat, newLng]);
          
          if (step >= steps) {
            clearInterval(animate);
            marker.setLatLng(targetPos);
          }
        }, 30);
      }
    }

    async function loadTrend() {
      try {
        const r = await fetch('/api/iss/trend?limit=240');
        const js = await r.json();
        const pts = Array.isArray(js.points) ? js.points.map(p => [p.lat, p.lon]) : [];
        
        if (pts.length) {
          // Обновляем траекторию
          trail.setLatLngs(pts);
          
          // Анимируем маркер
          animateMarker(pts);
          
          // Обновляем попап
          const lastPoint = js.points[js.points.length - 1];
          marker.setPopupContent(`
            <div class="text-center">
              <h6 class="mb-2">🚀 МКС</h6>
              <div class="small">
                <div><strong>Широта:</strong> ${lastPoint.lat.toFixed(4)}°</div>
                <div><strong>Долгота:</strong> ${lastPoint.lon.toFixed(4)}°</div>
                <div><strong>Скорость:</strong> ${lastPoint.velocity.toFixed(0)} км/ч</div>
                <div><strong>Высота:</strong> ${lastPoint.altitude.toFixed(0)} км</div>
                <div class="mt-2 text-muted"><small>${new Date(lastPoint.at).toLocaleString('ru-RU')}</small></div>
              </div>
            </div>
          `);
          
          // Обновляем графики
          const t = js.points.map(p => new Date(p.at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' }));
          speedChart.data.labels = t;
          speedChart.data.datasets[0].data = js.points.map(p => p.velocity);
          speedChart.update('none'); // Без анимации для плавности
          
          altChart.data.labels = t;
          altChart.data.datasets[0].data = js.points.map(p => p.altitude);
          altChart.update('none');
          
          // Обновляем статус
          document.getElementById('issStatus').innerHTML = '<i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Активно';
        }
      } catch(e) {
        console.error('Ошибка загрузки данных МКС:', e);
        document.getElementById('issStatus').innerHTML = '<i class="bi bi-circle-fill text-warning" style="font-size: 0.5rem;"></i> Ошибка';
      }
    }
    
    // Загружаем данные сразу и затем каждые 15 секунд
    loadTrend();
    setInterval(loadTrend, 15000);
  }

  // ====== JWST ГАЛЕРЕЯ ======
  const track = document.getElementById('jwstTrack');
  const info  = document.getElementById('jwstInfo');
  const form  = document.getElementById('jwstFilter');
  const srcSel = document.getElementById('srcSel');
  const sfxInp = document.getElementById('suffixInp');
  const progInp= document.getElementById('progInp');

  function toggleInputs(){
    const isSuffix = srcSel.value==='suffix';
    const isProgram = srcSel.value==='program';
    sfxInp.style.display = isSuffix ? '' : 'none';
    document.getElementById('suffixLabel').style.display = isSuffix ? '' : 'none';
    progInp.style.display = isProgram ? '' : 'none';
    document.getElementById('progLabel').style.display = isProgram ? '' : 'none';
  }
  srcSel.addEventListener('change', toggleInputs); toggleInputs();

  async function loadFeed(qs){
    track.innerHTML = '<div class="p-3 text-muted">Загрузка…</div>';
    info.textContent= '';
    try{
      const url = '/api/jwst/feed?'+new URLSearchParams(qs).toString();
      const r = await fetch(url);
      const js = await r.json();
      track.innerHTML = '';
      (js.items||[]).forEach(it=>{
        const fig = document.createElement('figure');
        fig.className = 'jwst-item m-0';
        fig.innerHTML = `
          <a href="${it.link||it.url}" target="_blank" rel="noreferrer">
            <img loading="lazy" src="${it.url}" alt="JWST">
          </a>
          <figcaption class="jwst-cap">${(it.caption||'').replaceAll('<','&lt;')}</figcaption>`;
        track.appendChild(fig);
      });
      info.textContent = `Источник: ${js.source} · Показано ${js.count||0}`;
      // Обновляем счетчик в карточке
      document.getElementById('jwstCount').textContent = js.count || 0;
    }catch(e){
      track.innerHTML = '<div class="p-3 text-danger">Ошибка загрузки</div>';
      document.getElementById('jwstCount').textContent = '—';
    }
  }

  form.addEventListener('submit', function(ev){
    ev.preventDefault();
    const fd = new FormData(form);
    const q = Object.fromEntries(fd.entries());
    loadFeed(q);
  });

  // навигация
  document.querySelector('.jwst-prev').addEventListener('click', ()=> track.scrollBy({left:-600, behavior:'smooth'}));
  document.querySelector('.jwst-next').addEventListener('click', ()=> track.scrollBy({left: 600, behavior:'smooth'}));

  // стартовые данные
  loadFeed({source:'jpg', perPage:24});
});
</script>
@endsection

    <!-- ASTRO — события -->
    <div class="col-12 order-first mt-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title m-0 d-flex align-items-center">
              <i class="bi bi-stars me-2 text-warning"></i>
              Астрономические события
            </h5>
            <form id="astroForm" class="row g-2 align-items-center">
              <div class="col-auto">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Широта от -90 до 90">Широта</label>
                <input type="number" step="0.0001" class="form-control form-control-sm" name="lat" value="55.7558" placeholder="55.7558">
              </div>
              <div class="col-auto">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Долгота от -180 до 180">Долгота</label>
                <input type="number" step="0.0001" class="form-control form-control-sm" name="lon" value="37.6176" placeholder="37.6176">
              </div>
              <div class="col-auto">
                <label class="form-label small mb-0" data-bs-toggle="tooltip" title="Количество дней вперед (1-30)">Дней</label>
                <input type="number" min="1" max="30" class="form-control form-control-sm" name="days" value="7" style="width:90px">
              </div>
              <div class="col-auto d-flex align-items-end">
                <button class="btn btn-sm btn-primary" type="submit">
                  <i class="bi bi-search me-1"></i>Показать
                </button>
              </div>
            </form>
          </div>

          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr><th>#</th><th>Тело</th><th>Событие</th><th>Когда (UTC)</th><th>Дополнительно</th></tr>
              </thead>
              <tbody id="astroBody">
                <tr><td colspan="5" class="text-muted">нет данных</td></tr>
              </tbody>
            </table>
          </div>

          <details class="mt-2">
            <summary>Полный JSON</summary>
            <pre id="astroRaw" class="bg-light rounded p-2 small m-0" style="white-space:pre-wrap"></pre>
          </details>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('astroForm');
        const body = document.getElementById('astroBody');
        const raw  = document.getElementById('astroRaw');

        function normalize(node){
          const name = node.name || node.body || node.object || node.target || '';
          const type = node.type || node.event_type || node.category || node.kind || '';
          const when = node.time || node.date || node.occursAt || node.peak || node.instant || '';
          const extra = node.magnitude || node.mag || node.altitude || node.note || '';
          return {name, type, when, extra};
        }

        function collect(root){
          const rows = [];
          (function dfs(x){
            if (!x || typeof x !== 'object') return;
            if (Array.isArray(x)) { x.forEach(dfs); return; }
            if ((x.type || x.event_type || x.category) && (x.name || x.body || x.object || x.target)) {
              rows.push(normalize(x));
            }
            Object.values(x).forEach(dfs);
          })(root);
          return rows;
        }

        async function load(q){
          body.innerHTML = '<tr><td colspan="5" class="text-muted">Загрузка…</td></tr>';
          const url = '/api/astro/events?' + new URLSearchParams(q).toString();
          try{
            const r  = await fetch(url);
            const js = await r.json();
            raw.textContent = JSON.stringify(js, null, 2);

            const rows = collect(js);
            if (!rows.length) {
              body.innerHTML = '<tr><td colspan="5" class="text-muted">события не найдены</td></tr>';
              return;
            }
            const rowsHtml = rows.slice(0,200).map((r,i)=>`
              <tr>
                <td>${i+1}</td>
                <td>${r.name || '—'}</td>
                <td>${r.type || '—'}</td>
                <td><code>${r.when || '—'}</code></td>
                <td>${r.extra || ''}</td>
              </tr>
            `).join('');
            body.innerHTML = rowsHtml;
            // Обновляем счетчик в карточке
            document.getElementById('astroCount').textContent = rows.length;
          }catch(e){
            body.innerHTML = '<tr><td colspan="5" class="text-danger">ошибка загрузки</td></tr>';
            document.getElementById('astroCount').textContent = '—';
          }
        }

        form.addEventListener('submit', ev=>{
          ev.preventDefault();
          const q = Object.fromEntries(new FormData(form).entries());
          load(q);
        });

        // автозагрузка
        load({lat: form.lat.value, lon: form.lon.value, days: form.days.value});
      });
    </script>


{{-- CMS блок удален - дубликат кода --}}
