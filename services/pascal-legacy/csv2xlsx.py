import csv
import sys
from pathlib import Path
from openpyxl import Workbook
from openpyxl.styles import Font, Alignment
from datetime import datetime


def convert(csv_path: Path):
    xlsx_path = csv_path.with_suffix(".xlsx")
    wb = Workbook()
    ws = wb.active
    ws.title = "Telemetry Data"
    
    with csv_path.open("r", encoding="utf-8") as f:
        reader = csv.reader(f)
        header = next(reader)
        ws.append(header)
        
        # Форматируем заголовок
        for cell in ws[1]:
            cell.font = Font(bold=True)
            cell.alignment = Alignment(horizontal="center")
        
        # Обрабатываем данные с правильными типами
        for row in reader:
            processed_row = []
            for i, value in enumerate(row):
                if i == 0 and header[i] == 'recorded_at_timestamp':
                    # Конвертируем timestamp в дату/время
                    try:
                        timestamp = int(value)
                        dt = datetime.fromtimestamp(timestamp)
                        processed_row.append(dt)
                    except:
                        processed_row.append(value)
                elif header[i] in ['voltage', 'temp']:
                    # Числовые значения
                    try:
                        processed_row.append(float(value))
                    except:
                        processed_row.append(value)
                elif header[i] == 'is_active':
                    # Логические значения (ИСТИНА/ЛОЖЬ)
                    processed_row.append(value)
                else:
                    # Строки
                    processed_row.append(value)
            ws.append(processed_row)
    
    # Автоподбор ширины столбцов
    for column in ws.columns:
        max_length = 0
        column_letter = column[0].column_letter
        for cell in column:
            try:
                if len(str(cell.value)) > max_length:
                    max_length = len(str(cell.value))
            except:
                pass
        adjusted_width = min(max_length + 2, 50)
        ws.column_dimensions[column_letter].width = adjusted_width
    
    wb.save(xlsx_path)
    return xlsx_path


def main():
    if len(sys.argv) < 2:
        print("usage: csv2xlsx <csvfile>", file=sys.stderr)
        sys.exit(1)
    path = Path(sys.argv[1]).resolve()
    out = convert(path)
    print(out)


if __name__ == "__main__":
    main()

