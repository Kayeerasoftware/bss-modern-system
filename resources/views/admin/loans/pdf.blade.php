<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Loan Application - {{ $loan->loan_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #10b981; margin: 0; font-size: 28px; }
        .header p { color: #666; margin: 5px 0; }
        .section { margin-bottom: 25px; }
        .section-title { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 15px; font-size: 16px; font-weight: bold; margin-bottom: 15px; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; font-weight: bold; padding: 8px; width: 40%; background: #f3f4f6; border: 1px solid #e5e7eb; }
        .info-value { display: table-cell; padding: 8px; border: 1px solid #e5e7eb; }
        .financial-box { background: #ecfdf5; border: 2px solid #10b981; padding: 15px; margin: 10px 0; text-align: center; }
        .financial-box .label { font-size: 12px; color: #666; }
        .financial-box .value { font-size: 24px; font-weight: bold; color: #10b981; }
        .settings-grid { display: table; width: 100%; font-size: 11px; }
        .settings-row { display: table-row; }
        .settings-cell { display: table-cell; padding: 6px; border: 1px solid #e5e7eb; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; font-size: 11px; color: #666; }
        .signature-section { margin-top: 50px; }
        .signature-box { display: inline-block; width: 45%; text-align: center; }
        .signature-line { border-top: 2px solid #333; margin-top: 60px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BSS INVESTMENT GROUP</h1>
        <p>Business Support System</p>
        <p style="font-size: 20px; font-weight: bold; color: #10b981; margin-top: 15px;">LOAN APPLICATION FORM</p>
    </div>

    <!-- Loan Information -->
    <div class="section">
        <div class="section-title">LOAN INFORMATION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Loan ID</div>
                <div class="info-value">{{ $loan->loan_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Application Date</div>
                <div class="info-value">{{ $loan->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value" style="text-transform: uppercase; font-weight: bold; color: {{ $loan->status == 'approved' ? '#10b981' : ($loan->status == 'rejected' ? '#ef4444' : '#f59e0b') }};">{{ $loan->status }}</div>
            </div>
        </div>
    </div>

    <!-- Member Information -->
    <div class="section">
        <div class="section-title">APPLICANT INFORMATION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Member ID</div>
                <div class="info-value">{{ $loan->member->member_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Full Name</div>
                <div class="info-value">{{ $loan->member->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $loan->member->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $loan->member->phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Member Savings</div>
                <div class="info-value">UGX {{ number_format($loan->member->savings ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Loan Details -->
    <div class="section">
        <div class="section-title">LOAN DETAILS</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Loan Amount</div>
                <div class="info-value" style="font-size: 18px; font-weight: bold; color: #10b981;">UGX {{ number_format($loan->amount, 2) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Interest Rate</div>
                <div class="info-value">{{ $loan->interest_rate ?? 10 }}%</div>
            </div>
            <div class="info-row">
                <div class="info-label">Repayment Period</div>
                <div class="info-value">{{ $loan->repayment_months }} Months</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Interest</div>
                <div class="info-value">UGX {{ number_format($loan->interest, 2) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Processing Fee</div>
                <div class="info-value">UGX {{ number_format($loan->processing_fee ?? 0, 2) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Repayment</div>
                <div class="info-value" style="font-size: 18px; font-weight: bold; color: #059669;">UGX {{ number_format($loan->amount + $loan->interest, 2) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Monthly Payment</div>
                <div class="info-value" style="font-size: 16px; font-weight: bold;">UGX {{ number_format($loan->monthly_payment, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Purpose -->
    <div class="section">
        <div class="section-title">LOAN PURPOSE</div>
        <div style="padding: 15px; border: 1px solid #e5e7eb; background: #f9fafb;">
            {{ $loan->purpose }}
        </div>
    </div>

    @if($loan->applicant_comment)
    <div class="section">
        <div class="section-title">APPLICANT COMMENT</div>
        <div style="padding: 15px; border: 1px solid #e5e7eb; background: #fef3c7;">
            {{ $loan->applicant_comment }}
        </div>
    </div>
    @endif

    @if($loan->guarantor_1_name)
    <div class="section">
        <div class="section-title">GUARANTOR INFORMATION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Guarantor 1 Name</div>
                <div class="info-value">{{ $loan->guarantor_1_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Guarantor 1 Phone</div>
                <div class="info-value">{{ $loan->guarantor_1_phone }}</div>
            </div>
            @if($loan->guarantor_2_name)
            <div class="info-row">
                <div class="info-label">Guarantor 2 Name</div>
                <div class="info-value">{{ $loan->guarantor_2_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Guarantor 2 Phone</div>
                <div class="info-value">{{ $loan->guarantor_2_phone }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Organizational Loan Rules -->
    <div class="section">
        <div class="section-title">ORGANISATIONAL LOAN RULES, TERMS AND CONDITIONS (AT TIME OF APPLICATION)</div>
        <div class="settings-grid">
            <div class="settings-row">
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Min Interest Rate</div>
                <div class="settings-cell">{{ $loan->settings_min_interest_rate ?? 'N/A' }}%</div>
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Max Interest Rate</div>
                <div class="settings-cell">{{ $loan->settings_max_interest_rate ?? 'N/A' }}%</div>
            </div>
            <div class="settings-row">
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Min Loan Amount</div>
                <div class="settings-cell">UGX {{ number_format($loan->settings_min_loan_amount ?? 0) }}</div>
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Max Loan Amount</div>
                <div class="settings-cell">UGX {{ number_format($loan->settings_max_loan_amount ?? 0) }}</div>
            </div>
            <div class="settings-row">
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Min Repayment Months</div>
                <div class="settings-cell">{{ $loan->settings_min_repayment_months ?? 'N/A' }}</div>
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Max Repayment Months</div>
                <div class="settings-cell">{{ $loan->settings_max_repayment_months ?? 'N/A' }}</div>
            </div>
            <div class="settings-row">
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Processing Fee</div>
                <div class="settings-cell">{{ $loan->settings_processing_fee_percentage ?? 'N/A' }}%</div>
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Late Payment Penalty</div>
                <div class="settings-cell">{{ $loan->settings_late_payment_penalty ?? 'N/A' }}%</div>
            </div>
            <div class="settings-row">
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Grace Period</div>
                <div class="settings-cell">{{ $loan->settings_grace_period_days ?? 'N/A' }} Days</div>
                <div class="settings-cell" style="font-weight: bold; background: #f3f4f6;">Max Loan-to-Savings Ratio</div>
                <div class="settings-cell">{{ $loan->settings_max_loan_to_savings_ratio ?? 'N/A' }}%</div>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>Applicant Signature</strong><br>
                Date: _________________
            </div>
        </div>
        <div class="signature-box" style="float: right;">
            <div class="signature-line">
                <strong>Loan Officer Signature</strong><br>
                Date: _________________
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>BSS Investment Group</strong> | Business Support System</p>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        <p style="font-size: 10px; margin-top: 10px;">This is a computer-generated document. No signature is required for validity.</p>
    </div>
</body>
</html>
