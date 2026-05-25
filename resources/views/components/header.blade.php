<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bridge' }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @if (!empty($css))
        <link rel="stylesheet" href="{{ asset($css) }}">
    @endif

    @if (!empty($accent_color))
        <style>
            :root {
                --accent-color: {{ $accent_color }};
            }
        </style>
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>