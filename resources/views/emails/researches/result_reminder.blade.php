@php
    $title = $research->title ?? $research->judul ?? 'Penelitian';
    $author = $research->author ?? optional($research->user)->name ?? 'Peneliti';
    $startDate = optional($research->start_date)->format('d M Y');
    $endDate = optional($research->end_date)->format('d M Y');
    $uploadLink = route('researches.results.edit', $research->id);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Permintaan Unggah Hasil Penelitian</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p>Halo {{ $author }},</p>

    <p>Kami mencatat bahwa periode penelitian berikut telah selesai:</p>
    <ul>
        <li><strong>Judul:</strong> {{ $title }}</li>
        <li><strong>Periode:</strong> {{ $startDate ?: '-' }} s/d {{ $endDate ?: '-' }}</li>
        <li><strong>Institusi:</strong> {{ optional(optional($research->user)->institution)->name ?? optional($research->institution)->name ?? '-' }}</li>
    </ul>

    <p>Mohon unggah hasil penelitian melalui tautan berikut:</p>
    <p><a href="{{ $uploadLink }}" style="color: #ea580c; font-weight: bold;">{{ $uploadLink }}</a></p>

    <p>Apabila membutuhkan bantuan, silakan balas email ini.</p>

    <p>Terima kasih.</p>
    <p>{{ config('app.name', 'Portal Riset Bappeda') }}</p>
</body>
</html>
