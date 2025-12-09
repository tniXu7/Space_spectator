program LegacyCSV;

{$mode objfpc}{$H+}

uses
  SysUtils, DateUtils, Process, Unix, StrUtils;

function GetEnvDef(const name, def: string): string;
var v: string;
begin
  v := GetEnvironmentVariable(name);
  if v = '' then Exit(def) else Exit(v);
end;

function RandFloat(minV, maxV: Double): Double;
begin
  Result := minV + Random * (maxV - minV);
end;

function RandBool(): Boolean;
begin
  Result := Random(2) = 1;
end;

function TimestampToUnix(ts: TDateTime): Int64;
begin
  Result := DateTimeToUnix(ts);
end;

procedure GenerateAndCopy();
var
  outDir, fn, fullpath, pghost, pgport, pguser, pgpass, pgdb, copyCmd, xlsxPath: string;
  f: TextFile;
  ts: string;
  timestamp: Int64;
  voltage, temp: Double;
  isActive: Boolean;
begin
  outDir := GetEnvDef('CSV_OUT_DIR', '/data/csv');
  ts := FormatDateTime('yyyymmdd_hhnnss', Now);
  fn := 'telemetry_' + ts + '.csv';
  fullpath := IncludeTrailingPathDelimiter(outDir) + fn;

  // Генерируем данные с правильными типами
  timestamp := TimestampToUnix(Now);
  voltage := RandFloat(3.2, 12.6);
  temp := RandFloat(-50.0, 80.0);
  isActive := RandBool();

  // write CSV с правильными типами: timestamp, ИСТИНА/ЛОЖЬ, числа, строки
  AssignFile(f, fullpath);
  Rewrite(f);
  Writeln(f, 'recorded_at_timestamp,voltage,temp,is_active,source_file,description');
  Writeln(f, IntToStr(timestamp) + ',' +
             FormatFloat('0.00', voltage) + ',' +
             FormatFloat('0.00', temp) + ',' +
             IfThen(isActive, 'ИСТИНА', 'ЛОЖЬ') + ',' +
             fn + ',' +
             'Telemetry data from legacy system');
  CloseFile(f);

  // COPY into Postgres - конвертируем timestamp в TIMESTAMPTZ
  pghost := GetEnvDef('PGHOST', 'db');
  pgport := GetEnvDef('PGPORT', '5432');
  pguser := GetEnvDef('PGUSER', 'monouser');
  pgpass := GetEnvDef('PGPASSWORD', 'monopass');
  pgdb   := GetEnvDef('PGDATABASE', 'monolith');

  SetEnvironmentVariable('PGPASSWORD', pgpass);
  
  // Используем временную таблицу для конвертации timestamp в TIMESTAMPTZ
  copyCmd := 'psql "host=' + pghost + ' port=' + pgport + ' user=' + pguser + ' dbname=' + pgdb + '" ' +
             '-c "CREATE TEMP TABLE IF NOT EXISTS temp_telemetry_import(recorded_at_timestamp BIGINT, voltage NUMERIC, temp NUMERIC, is_active TEXT, source_file TEXT, description TEXT);"';
  fpSystem(copyCmd);
  
  copyCmd := 'psql "host=' + pghost + ' port=' + pgport + ' user=' + pguser + ' dbname=' + pgdb + '" ' +
             '-c "\copy temp_telemetry_import FROM ''' + fullpath + ''' WITH (FORMAT csv, HEADER true)"';
  fpSystem(copyCmd);
  
  copyCmd := 'psql "host=' + pghost + ' port=' + pgport + ' user=' + pguser + ' dbname=' + pgdb + '" ' +
             '-c "INSERT INTO telemetry_legacy(recorded_at, voltage, temp, source_file) SELECT to_timestamp(recorded_at_timestamp), voltage, temp, source_file FROM temp_telemetry_import ON CONFLICT DO NOTHING"';
  fpSystem(copyCmd);
  
  copyCmd := 'psql "host=' + pghost + ' port=' + pgport + ' user=' + pguser + ' dbname=' + pgdb + '" ' +
             '-c "DROP TABLE IF EXISTS temp_telemetry_import"';
  fpSystem(copyCmd);

  // Генерируем XLSX файл
  xlsxPath := ChangeFileExt(fullpath, '.xlsx');
  fpSystem('python3 /opt/legacy/csv2xlsx.py "' + fullpath + '"');
end;

var period: Integer;
begin
  Randomize;
  period := StrToIntDef(GetEnvDef('GEN_PERIOD_SEC', '300'), 300);
  while True do
  begin
    try
      GenerateAndCopy();
    except
      on E: Exception do
        WriteLn('Legacy error: ', E.Message);
    end;
    Sleep(period * 1000);
  end;
end.
