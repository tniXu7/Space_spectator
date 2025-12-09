@extends('layouts.app')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <h2 class="mb-3">NASA OSDR</h2>
    <div class="small text-muted mb-3">Источник {{ $src }}</div>

    <!-- Фильтры и поиск -->
    <div class="card shadow-sm mb-4 slide-in">
      <div class="card-body">
        <form method="GET" action="{{ route('osdr') }}" class="row g-3" id="osdrFilterForm">
          <div class="col-md-4">
            <label class="form-label">Поиск по ключевым словам</label>
            <input type="text" class="form-control" name="search" value="{{ request()->query('search') }}" placeholder="dataset_id, title...">
          </div>
          <div class="col-md-2">
            <label class="form-label">Столбец сортировки</label>
            <select class="form-select" name="sort">
              <option value="inserted_at" {{ request()->query('sort') === 'inserted_at' ? 'selected' : '' }}>Дата вставки</option>
              <option value="updated_at" {{ request()->query('sort') === 'updated_at' ? 'selected' : '' }}>Дата обновления</option>
              <option value="dataset_id" {{ request()->query('sort') === 'dataset_id' ? 'selected' : '' }}>Dataset ID</option>
              <option value="title" {{ request()->query('sort') === 'title' ? 'selected' : '' }}>Название</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Направление</label>
            <select class="form-select" name="direction">
              <option value="desc" {{ request()->query('direction') === 'desc' ? 'selected' : '' }}>По убыванию</option>
              <option value="asc" {{ request()->query('direction') === 'asc' ? 'selected' : '' }}>По возрастанию</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Лимит</label>
            <input type="number" class="form-control" name="limit" value="{{ request()->query('limit', '20') }}" min="1" max="100">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Применить</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Таблица данных -->
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>dataset_id</th>
                <th>title</th>
                <th>REST_URL</th>
                <th>updated_at</th>
                <th>inserted_at</th>
                <th>raw</th>
              </tr>
            </thead>
            <tbody>
            @forelse($items as $row)
              <tr class="fade-in">
                <td>{{ $row['id'] }}</td>
                <td>{{ $row['dataset_id'] ?? '—' }}</td>
                <td style="max-width:420px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                  {{ $row['title'] ?? '—' }}
                </td>
                <td>
                  @if(!empty($row['rest_url']))
                    <a href="{{ $row['rest_url'] }}" target="_blank" rel="noopener">открыть</a>
                  @else — @endif
                </td>
                <td>{{ $row['updated_at'] ?? '—' }}</td>
                <td>{{ $row['inserted_at'] ?? '—' }}</td>
                <td>
                  <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">JSON</button>
                </td>
              </tr>
              <tr class="collapse" id="raw-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                <td colspan="7">
                  <pre class="mb-0" style="max-height:260px;overflow:auto">{{ json_encode($row['raw'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted">нет данных</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
