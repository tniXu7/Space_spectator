@extends('layouts.app')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <h2 class="mb-3">CSV: {{ $filename }}</h2>
    
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
          <table class="table table-striped table-bordered table-sm">
            <thead class="table-dark sticky-top">
              <tr>
                @foreach($headers as $header)
                  <th>{{ $header }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($rows as $row)
                <tr class="fade-in">
                  @foreach($headers as $header)
                    <td>
                      @if($header === 'recorded_at_timestamp')
                        {{ \Carbon\Carbon::createFromTimestamp($row[$header])->format('Y-m-d H:i:s') }}
                      @elseif($header === 'is_active')
                        <span class="badge {{ $row[$header] === 'ИСТИНА' ? 'bg-success' : 'bg-secondary' }}">
                          {{ $row[$header] }}
                        </span>
                      @elseif(is_numeric($row[$header]) && strpos($row[$header], '.') !== false)
                        {{ number_format((float)$row[$header], 2) }}
                      @else
                        {{ $row[$header] }}
                      @endif
                    </td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="mt-3">
      <a href="{{ route('telemetry.csv.list') }}" class="btn btn-outline-secondary">Назад к списку</a>
    </div>
  </div>
</div>
@endsection

