<!DOCTYPE html>
<html lang="id" class="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @filamentStyles
    <style>
        :root {
            --brand-primary: #6366f1;
            --brand-dark: #3730a3;
        }
        body { margin: 0; background: #f8fafc; }
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }
        @keyframes floatPulse {
            0%, 100% { transform: scale(1) translateY(0); }
            50% { transform: scale(1.05) translateY(-8px); }
        }
        @keyframes fadeSlideUp {
            0% { transform: translateY(28px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideInLeft {
            0% { transform: translateX(-60px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        @keyframes scaleInBounce {
            0% { transform: scale(.85); opacity: 0; }
            60% { transform: scale(1.02); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes shimmerGradient {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .fi-fo-field-wrp input {
            border-radius: 12px !important;
            padding: 12px 16px !important;
            border: 1.5px solid #d1d5db !important;
            transition: all .3s !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .fi-fo-field-wrp input:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12) !important;
        }
        .fi-fo-field-wrp label {
            font-weight: 600 !important;
            color: #44403c !important;
            margin-bottom: 6px !important;
        }
        .fi-checkbox-input {
            border-radius: 5px !important;
            border-color: #d1d5db !important;
        }
        .fi-checkbox-input:checked {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
        }
        button[type="submit"] {
            position: relative !important;
            overflow: hidden !important;
            width: 100% !important;
            padding: 14px 24px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 12px !important;
            cursor: pointer !important;
            box-shadow: 0 4px 16px rgba(99,102,241,.3) !important;
            transition: all .3s !important;
            margin-top: 8px !important;
            font-family: Inter, system-ui, sans-serif !important;
        }
        button[type="submit"]:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 8px 24px rgba(99,102,241,.4) !important;
        }
        button[type="submit"]::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 30%, rgba(255,255,255,.15) 50%, transparent 70%);
            transform: translateX(-100%);
            animation: shimmerGradient 3s ease-in-out infinite;
        }
        .fi-fo-field-wrp {
            margin-bottom: 16px !important;
        }
        .fi-fo-field-wrp .fi-fo-field-wrp-label {
            margin-bottom: 6px !important;
        }
        @media (max-width: 1023px) {
            .login-left { min-height: 240px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body class="fi-body min-h-screen antialiased">
{{ $slot }}
@filamentScripts
</body>
</html>
