@extends('layouts.app')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <h2 class="mb-3">Telemetry Data</h2>
    
    <!-- Фильтры и поиск -->
    <div class="card shadow-sm mb-4 slide-in">
      <div class="card-body">
        <form method="GET" action="{{ route('telemetry') }}" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Поиск</label>
            <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Ключевые слова...">
          </div>
          <div class="col-md-2">
            <label class="form-label">Дата от</label>
            <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Дата до</label>
            <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Столбец</label>
            <select class="form-select" name="sort">
              <option value="recorded_at" {{ $sortColumn === 'recorded_at' ? 'selected' : '' }}>Дата</option>
              <option value="voltage" {{ $sortColumn === 'voltage' ? 'selected' : '' }}>Напряжение</option>
              <option value="temp" {{ $sortColumn === 'temp' ? 'selected' : '' }}>Температура</option>
              <option value="source_file" {{ $sortColumn === 'source_file' ? 'selected' : '' }}>Файл</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Направление</label>
            <select class="form-select" name="direction">
              <option value="asc" {{ $sortDirection === 'asc' ? 'selected' : '' }}>По возрастанию</option>
              <option value="desc" {{ $sortDirection === 'desc' ? 'selected' : '' }}>По убыванию</option>
            </select>
          </div>
          <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Применить</button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Таблица данных -->
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Дата записи</th>
                <th>Напряжение</th>
                <th>Температура</th>
                <th>Исходный файл</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items as $item)
                <tr class="fade-in">
                  <td>{{ $item->id }}</td>
                  <td>{{ \Carbon\Carbon::parse($item->recorded_at)->format('Y-m-d H:i:s') }}</td>
                  <td>{{ number_format($item->voltage, 2) }}V</td>
                  <td>{{ number_format($item->temp, 2) }}°C</td>
                  <td><small>{{ $item->source_file }}</small></td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">Нет данных</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <!-- Пагинация -->
        <div class="mt-3">
          {{ $items->links() }}
        </div>
      </div>
    </div>
    
    <div class="mt-3">
      <a href="{{ route('telemetry.csv.list') }}" class="btn btn-outline-secondary">Просмотр CSV файлов</a>
    </div>
  </div>
</div>
@endsection

