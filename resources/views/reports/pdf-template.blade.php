<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { size: A4; margin: 20mm; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #333; max-width: 210mm; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #2563eb; padding-bottom: 15px; }
        .header h1 { color: #2563eb; margin: 0; font-size: 24pt; }
        .header p { margin: 5px 0; color: #666; }
        .info-box { background: #f3f4f6; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .info-box h3 { margin: 0 0 10px 0; color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #2563eb; color: white; padding: 10px; text-align: left; font-weight: bold; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        .summary { background: #eff6ff; padding: 15px; margin: 20px 0; border-left: 4px solid #2563eb; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 2px solid #e5e7eb; color: #666; font-size: 9pt; }
        .stat { display: inline-block; margin: 10px 20px; }
        .stat-label { color: #666; font-size: 9pt; }
        .stat-value { font-size: 14pt; font-weight: bold; color: #2563eb; }
        .print-btn { position: fixed; top: 20px; right: 20px; padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .print-btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Print / Save as PDF</button>
    
    <div class="header">
        <h1>BSS Investment Group</h1>
        <p>{{ $title }}</p>
        <p>Generated: {{ date('F d, Y - h:i A') }}</p>
    </div>

    @yield('content')

    <div class="footer">
        <p>BSS Investment Group System | Confidential Report</p>
        <p>Page generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
