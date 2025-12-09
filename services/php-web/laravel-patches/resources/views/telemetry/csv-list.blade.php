@extends('layouts.app')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <h2 class="mb-3">CSV Files</h2>
    
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Имя файла</th>
                <th>Размер</th>
                <th>Изменен</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody>
              @forelse($files as $file)
                <tr class="fade-in">
                  <td>{{ $file['name'] }}</td>
                  <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                  <td>{{ $file['modified'] }}</td>
                  <td>
                    <a href="{{ route('telemetry.csv.view', ['filename' => $file['name']]) }}" class="btn btn-sm btn-primary">Просмотр</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Нет CSV файлов</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="mt-3">
      <a href="{{ route('telemetry') }}" class="btn btn-outline-secondary">Назад к телеметрии</a>
    </div>
  </div>
</div>
@endsection

