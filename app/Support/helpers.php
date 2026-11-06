<?php

use Illuminate\Support\Carbon;

if (! function_exists('formato_moneda')) {
    /**
     * Formatea un valor monetario en pesos colombianos.
     */
    function formato_moneda(float|int|string|null $valor): string
    {
        return '$ '.number_format((float) $valor, 0, ',', '.');
    }
}

if (! function_exists('formato_fecha')) {
    /**
     * Formatea una fecha como dd/mm/aaaa.
     */
    function formato_fecha(mixed $fecha): string
    {
        return $fecha === null ? '—' : Carbon::parse($fecha)->format('d/m/Y');
    }
}

if (! function_exists('formato_fecha_hora')) {
    /**
     * Formatea una fecha con hora como dd/mm/aaaa hh:mm.
     */
    function formato_fecha_hora(mixed $fecha): string
    {
        return $fecha === null ? '—' : Carbon::parse($fecha)->format('d/m/Y H:i');
    }
}

if (! function_exists('formato_fecha_larga')) {
    /**
     * Formatea una fecha en lenguaje natural: "5 de noviembre de 2026".
     */
    function formato_fecha_larga(mixed $fecha): string
    {
        return $fecha === null
            ? '—'
            : Carbon::parse($fecha)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    }
}

if (! function_exists('pluralizar')) {
    /**
     * Devuelve "1 producto" o "3 productos" según la cantidad.
     */
    function pluralizar(int $cantidad, string $singular, string $plural): string
    {
        return $cantidad.' '.($cantidad === 1 ? $singular : $plural);
    }
}

if (! function_exists('iniciales')) {
    /**
     * Devuelve las iniciales de un nombre para el avatar.
     */
    function iniciales(?string $nombre): string
    {
        $partes = preg_split('/\s+/', trim((string) $nombre)) ?: [];
        $partes = array_values(array_filter($partes));

        if ($partes === []) {
            return 'SG';
        }

        $iniciales = array_map(
            static fn (string $parte): string => mb_strtoupper(mb_substr($parte, 0, 1)),
            array_slice($partes, 0, 2)
        );

        return implode('', $iniciales);
    }
}
