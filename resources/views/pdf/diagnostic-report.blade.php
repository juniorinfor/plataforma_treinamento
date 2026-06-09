<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório — {{ $tool->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.5;
        }

        /* ── PAGE SETUP ────────────────── */
        @page {
            margin: 0;
            size: A4 portrait;
        }

        .page {
            width: 100%;
            min-height: 297mm;
            position: relative;
        }

        .page-break {
            page-break-after: always;
        }

        /* ── COVER PAGE ─────────────────── */
        .cover-header {
            background-color: {{ $accentColor }};
            padding: 48pt 40pt 36pt;
            color: white;
        }

        .cover-logo-row {
            margin-bottom: 40pt;
        }

        .cover-logo-text {
            font-size: 18pt;
            font-weight: bold;
            color: white;
            letter-spacing: 1px;
        }

        .cover-company {
            font-size: 10pt;
            color: rgba(255,255,255,0.75);
            margin-top: 4pt;
        }

        .cover-tool-badge {
            display: inline-block;
            background-color: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            color: white;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4pt 10pt;
            border-radius: 20pt;
            margin-bottom: 12pt;
        }

        .cover-title {
            font-size: 26pt;
            font-weight: bold;
            color: white;
            line-height: 1.2;
            margin-bottom: 8pt;
        }

        .cover-subtitle {
            font-size: 11pt;
            color: rgba(255,255,255,0.8);
        }

        .cover-body {
            padding: 40pt;
            background: #fff;
        }

        .score-hero-table {
            width: 100%;
            margin-bottom: 32pt;
        }

        .score-circle {
            width: 120pt;
            height: 120pt;
            border-radius: 60pt;
            background-color: {{ $scoreColor }};
            text-align: center;
            padding-top: 28pt;
        }

        .score-number {
            font-size: 38pt;
            font-weight: bold;
            color: white;
            line-height: 1;
        }

        .score-of {
            font-size: 10pt;
            color: rgba(255,255,255,0.85);
        }

        .score-details td {
            padding-left: 24pt;
            vertical-align: middle;
        }

        .score-label {
            font-size: 22pt;
            font-weight: bold;
            color: {{ $scoreColor }};
            display: block;
        }

        .score-tool {
            font-size: 12pt;
            color: #6b7280;
            display: block;
            margin-top: 4pt;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24pt;
        }

        .meta-table td {
            padding: 10pt 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 9.5pt;
        }

        .meta-label {
            color: #9ca3af;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 40%;
        }

        .meta-value {
            color: #111827;
            font-weight: 600;
        }

        .confidential {
            text-align: center;
            font-size: 8pt;
            color: #d1d5db;
            border-top: 1px solid #f3f4f6;
            padding-top: 16pt;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── SECTION PAGES ──────────────── */
        .section-header {
            background-color: {{ $accentColor }};
            padding: 24pt 40pt 20pt;
            color: white;
        }

        .section-number {
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.7);
            margin-bottom: 4pt;
        }

        .section-title {
            font-size: 18pt;
            font-weight: bold;
            color: white;
        }

        .section-body {
            padding: 32pt 40pt 40pt;
            background: #fff;
        }

        /* ── SCORE BARS ─────────────────── */
        .index-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8pt;
        }

        .index-table tr {
            page-break-inside: avoid;
        }

        .index-row td {
            padding: 8pt 0;
            border-bottom: 1px solid #f9fafb;
        }

        .index-code {
            font-size: 8pt;
            font-weight: bold;
            padding: 3pt 7pt;
            border-radius: 4pt;
            text-transform: uppercase;
            width: 40pt;
            text-align: center;
        }

        .index-name {
            font-size: 10pt;
            color: #374151;
            padding-left: 10pt;
            width: 35%;
        }

        .bar-cell {
            width: 40%;
            padding: 0 12pt;
            vertical-align: middle;
        }

        .bar-track {
            background-color: #f3f4f6;
            border-radius: 6pt;
            height: 8pt;
            width: 100%;
        }

        .bar-fill {
            border-radius: 6pt;
            height: 8pt;
        }

        .index-score {
            font-size: 14pt;
            font-weight: bold;
            width: 30pt;
            text-align: right;
        }

        .index-label {
            font-size: 8pt;
            font-weight: bold;
            width: 60pt;
            padding-left: 8pt;
        }

        /* ── GLOBAL SUMMARY BOX ─────────── */
        .global-box {
            background-color: {{ $accentColor }}12;
            border: 2px solid {{ $accentColor }}30;
            border-radius: 8pt;
            padding: 16pt 20pt;
            margin-top: 16pt;
        }

        .global-box-label {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: {{ $accentColor }};
            margin-bottom: 4pt;
        }

        .global-box-score {
            font-size: 28pt;
            font-weight: bold;
            color: {{ $scoreColor }};
            line-height: 1;
        }

        .global-box-text {
            font-size: 11pt;
            font-weight: bold;
            color: {{ $scoreColor }};
            margin-left: 8pt;
        }

        /* ── CONSULTANT ANALYSIS ────────── */
        .archetype-badge {
            display: inline-block;
            background-color: {{ $accentColor }}15;
            border: 1.5pt solid {{ $accentColor }}40;
            color: {{ $accentColor }};
            font-size: 9pt;
            font-weight: bold;
            padding: 5pt 14pt;
            border-radius: 20pt;
            margin-bottom: 20pt;
        }

        .analysis-text {
            font-size: 10pt;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 8pt;
        }

        .subsection-title {
            font-size: 10pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1.5pt solid {{ $accentColor }};
            padding-bottom: 5pt;
            margin-top: 20pt;
            margin-bottom: 12pt;
        }

        /* ── HIGHLIGHTS ─────────────────── */
        .highlight-item {
            margin-bottom: 8pt;
            padding-left: 20pt;
            position: relative;
        }

        .highlight-bullet {
            position: absolute;
            left: 0;
            top: 2pt;
            width: 12pt;
            height: 12pt;
            border-radius: 6pt;
            background-color: {{ $accentColor }};
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            color: white;
            line-height: 12pt;
        }

        .highlight-text {
            font-size: 10pt;
            color: #374151;
        }

        /* ── SWOT ──────────────────────── */
        .swot-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6pt;
        }

        .swot-cell {
            width: 50%;
            padding: 12pt;
            border-radius: 6pt;
            vertical-align: top;
        }

        .swot-header {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .swot-item {
            font-size: 9pt;
            color: #374151;
            margin-bottom: 5pt;
            padding-left: 12pt;
            position: relative;
        }

        .swot-dot {
            position: absolute;
            left: 0;
            top: 5pt;
            width: 5pt;
            height: 5pt;
            border-radius: 3pt;
        }

        /* ── PAGE FOOTER ─────────────────── */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10pt 40pt;
            border-top: 1pt solid #e5e7eb;
            font-size: 7.5pt;
            color: #9ca3af;
        }

        .footer-table {
            width: 100%;
        }

        .footer-left { text-align: left; }
        .footer-center { text-align: center; }
        .footer-right { text-align: right; }
    </style>
</head>
<body>

{{-- ════════════════════════════════════════ --}}
{{-- PÁGINA 1 — CAPA                         --}}
{{-- ════════════════════════════════════════ --}}
<div class="page page-break">

    {{-- Cabeçalho colorido --}}
    <div class="cover-header">
        <div class="cover-logo-row">
            <div class="cover-logo-text">Executive Map</div>
            @if($company)
            <div class="cover-company">{{ $company->name }}</div>
            @endif
        </div>

        @if($tool->code)
        <div class="cover-tool-badge">{{ $tool->code }}</div>
        @endif

        <div class="cover-title">{{ $tool->name }}</div>
        <div class="cover-subtitle">Relatório de Diagnóstico Organizacional</div>
    </div>

    {{-- Corpo da capa --}}
    <div class="cover-body">

        {{-- Score Hero --}}
        <table class="score-hero-table">
            <tr>
                <td style="width: 130pt; vertical-align: middle;">
                    <div class="score-circle">
                        <div class="score-number">{{ number_format($score, 0) }}</div>
                        <div class="score-of">de 100</div>
                    </div>
                </td>
                <td class="score-details">
                    <span class="score-label">{{ $assessment->global_label }}</span>
                    <span class="score-tool">{{ $tool->name }}</span>
                    @if($tool->description)
                    <p style="font-size:9pt; color:#6b7280; margin-top:8pt; max-width:280pt; line-height:1.5;">
                        {{ Str::limit($tool->description, 180) }}
                    </p>
                    @endif
                </td>
            </tr>
        </table>

        {{-- Metadados --}}
        <table class="meta-table">
            <tr>
                <td class="meta-label">Colaborador</td>
                <td class="meta-value">{{ $user?->name ?? '—' }}</td>
            </tr>
            @if($company)
            <tr>
                <td class="meta-label">Empresa</td>
                <td class="meta-value">{{ $company->name }}</td>
            </tr>
            @endif
            <tr>
                <td class="meta-label">Data de conclusão</td>
                <td class="meta-value">{{ $assessment->completed_at?->format('d \d\e F \d\e Y') ?? '—' }}</td>
            </tr>
            @if($assessment->results->count() > 0)
            <tr>
                <td class="meta-label">Índices avaliados</td>
                <td class="meta-value">{{ $assessment->results->count() }} índices</td>
            </tr>
            @endif
            @if($hasReport && $report?->published_at)
            <tr>
                <td class="meta-label">Análise publicada em</td>
                <td class="meta-value">{{ $report->published_at->format('d/m/Y') }}</td>
            </tr>
            @endif
        </table>

        <div class="confidential">
            Documento confidencial &mdash; Executive Map Platform &mdash; {{ now()->format('Y') }}
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════ --}}
{{-- PÁGINA 2 — SCORES POR ÍNDICE            --}}
{{-- ════════════════════════════════════════ --}}
<div class="page {{ $hasReport ? 'page-break' : '' }}">

    <div class="section-header">
        <div class="section-number">Seção 01</div>
        <div class="section-title">Análise por Índice</div>
    </div>

    <div class="section-body">

        @if(count($results) > 0)
        <table class="index-table">
            <tbody>
                @foreach($results as $r)
                <tr class="index-row">
                    {{-- Código --}}
                    <td style="width: 50pt; padding: 8pt 0; border-bottom: 1pt solid #f3f4f6; vertical-align: middle;">
                        <span class="index-code"
                              style="background-color: {{ $r['color'] }}18; color: {{ $r['color'] }};">
                            {{ $r['code'] ?: strtoupper(substr($r['name'], 0, 3)) }}
                        </span>
                    </td>

                    {{-- Nome --}}
                    <td class="index-name" style="border-bottom: 1pt solid #f3f4f6; vertical-align: middle;">
                        {{ $r['name'] }}
                    </td>

                    {{-- Barra --}}
                    <td class="bar-cell" style="border-bottom: 1pt solid #f3f4f6; vertical-align: middle;">
                        <div class="bar-track">
                            <div class="bar-fill"
                                 style="width: {{ $r['bar_width'] }}%; background-color: {{ $r['color'] }};"></div>
                        </div>
                    </td>

                    {{-- Score --}}
                    <td class="index-score"
                        style="color: {{ $r['score_color'] }}; border-bottom: 1pt solid #f3f4f6; vertical-align: middle;">
                        {{ number_format($r['score'], 0) }}
                    </td>

                    {{-- Label --}}
                    <td class="index-label"
                        style="color: {{ $r['score_color'] }}; border-bottom: 1pt solid #f3f4f6; vertical-align: middle;">
                        {{ $r['label'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Score Global --}}
        <div class="global-box">
            <table style="width: 100%;">
                <tr>
                    <td style="vertical-align: middle; width: 50%;">
                        <div class="global-box-label">Score Global AS SCORE®</div>
                        <table>
                            <tr>
                                <td class="global-box-score">{{ number_format($score, 0) }}</td>
                                <td class="global-box-text">{{ $assessment->global_label }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align: middle; width: 50%; padding-left: 20pt;">
                        <p style="font-size: 9pt; color: #6b7280; line-height: 1.5;">
                            @switch($assessment->global_label)
                                @case('Excelente')
                                    Zona de excelência. A organização demonstra práticas consolidadas e serve de referência.
                                    @break
                                @case('Saudável')
                                    Ambiente saudável com boas práticas consolidadas. Potencial para atingir excelência.
                                    @break
                                @case('Atenção')
                                    Há pontos de melhoria claros que requerem atenção estratégica.
                                    @break
                                @case('Crítico')
                                    Situação crítica. Ação imediata e plano estruturado são recomendados.
                                    @break
                                @default
                                    Vulnerabilidade alta. Plano de ação urgente é necessário.
                            @endswitch
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════ --}}
{{-- PÁGINA 3 — ANÁLISE DO CONSULTOR         --}}
{{-- (apenas se o relatório estiver publicado) --}}
{{-- ════════════════════════════════════════ --}}
@if($hasReport)
<div class="page">

    <div class="section-header">
        <div class="section-number">Seção 02</div>
        <div class="section-title">Análise do Consultor</div>
    </div>

    <div class="section-body">

        {{-- Arquétipo --}}
        @if($report->archetype)
        <div class="archetype-badge">{{ $report->archetype }}</div>
        @endif

        {{-- Análise qualitativa --}}
        @if($report->content)
        <div class="subsection-title">Análise Qualitativa</div>
        @foreach(explode("\n\n", $report->content) as $para)
        @if(trim($para))
        <p class="analysis-text">{{ trim($para) }}</p>
        @endif
        @endforeach
        @endif

        {{-- Destaques --}}
        @if(!empty($report->highlights) && array_filter($report->highlights))
        <div class="subsection-title">Destaques Principais</div>
        @foreach($report->highlights as $i => $hl)
        @if(trim($hl))
        <div class="highlight-item">
            <div class="highlight-bullet">{{ $i + 1 }}</div>
            <div class="highlight-text">{{ trim($hl) }}</div>
        </div>
        @endif
        @endforeach
        @endif

        {{-- SWOT --}}
        @if(!empty($report->swot))
        @php
            $swotItems = [
                'strengths'     => ['Forças',        '#10B981', '#f0fdf4'],
                'weaknesses'    => ['Fraquezas',     '#EF4444', '#fef2f2'],
                'opportunities' => ['Oportunidades', '#3B82F6', '#eff6ff'],
                'threats'       => ['Ameaças',       '#F59E0B', '#fffbeb'],
            ];
        @endphp
        <div class="subsection-title">Análise SWOT</div>
        <table class="swot-table">
            <tr>
                @foreach(array_slice($swotItems, 0, 2, true) as $key => [$label, $color, $bg])
                @if(!empty($report->swot[$key]) && array_filter((array)$report->swot[$key]))
                <td class="swot-cell" style="background-color: {{ $bg }}; border: 1pt solid {{ $color }}25;">
                    <div class="swot-header" style="color: {{ $color }};">{{ $label }}</div>
                    @foreach((array)$report->swot[$key] as $item)
                    @if(trim($item))
                    <div class="swot-item" style="position: relative;">
                        <span style="position: absolute; left: 0; top: 5pt; width: 5pt; height: 5pt;
                                     border-radius: 3pt; background: {{ $color }}; display: block;"></span>
                        {{ trim($item) }}
                    </div>
                    @endif
                    @endforeach
                </td>
                @else
                <td class="swot-cell"></td>
                @endif
                @endforeach
            </tr>
            <tr>
                @foreach(array_slice($swotItems, 2, 2, true) as $key => [$label, $color, $bg])
                @if(!empty($report->swot[$key]) && array_filter((array)$report->swot[$key]))
                <td class="swot-cell" style="background-color: {{ $bg }}; border: 1pt solid {{ $color }}25;">
                    <div class="swot-header" style="color: {{ $color }};">{{ $label }}</div>
                    @foreach((array)$report->swot[$key] as $item)
                    @if(trim($item))
                    <div class="swot-item" style="position: relative;">
                        <span style="position: absolute; left: 0; top: 5pt; width: 5pt; height: 5pt;
                                     border-radius: 3pt; background: {{ $color }}; display: block;"></span>
                        {{ trim($item) }}
                    </div>
                    @endif
                    @endforeach
                </td>
                @else
                <td class="swot-cell"></td>
                @endif
                @endforeach
            </tr>
        </table>
        @endif
    </div>
</div>
@endif


{{-- ── Footer (aparece em todas as páginas) ── --}}
<div class="page-footer">
    <table class="footer-table">
        <tr>
            <td class="footer-left">Executive Map Platform &bull; {{ $tool->name }}</td>
            <td class="footer-center">{{ $user?->name }}</td>
            <td class="footer-right">Documento Confidencial</td>
        </tr>
    </table>
</div>

</body>
</html>
