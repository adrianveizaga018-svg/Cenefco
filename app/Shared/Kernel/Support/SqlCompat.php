<?php

namespace App\Shared\Kernel\Support;

use Illuminate\Support\Facades\DB;

final class SqlCompat
{
    public static function isPgsql(): bool
    {
        return DB::connection()->getDriverName() === 'pgsql';
    }

    public static function isSqlite(): bool
    {
        return DB::connection()->getDriverName() === 'sqlite';
    }

    /**
     * Cross-DB CONCAT: supports MySQL, PostgreSQL, and SQLite.
     * Pass column references or literal strings. Literals must be quoted inside the call.
     * Example: SqlCompat::concat("COALESCE(vend.nombre,'')", "' '", "COALESCE(vend.apellido,'')")
     */
    public static function concat(string ...$parts): string
    {
        if (self::isSqlite()) {
            return implode(' || ', $parts);
        }
        return 'CONCAT(' . implode(', ', $parts) . ')';
    }

    public static function trimConcat(string ...$parts): string
    {
        return 'TRIM(' . self::concat(...$parts) . ')';
    }

    public static function dateFormat(string $column, string $mysqlFormat): string
    {
        $map = ['%Y-%m' => 'YYYY-MM', '%Y' => 'YYYY'];

        return self::isPgsql()
            ? "TO_CHAR({$column}, '{$map[$mysqlFormat]}')"
            : "DATE_FORMAT({$column}, '{$mysqlFormat}')";
    }

    public static function castUnsignedInt(string $column): string
    {
        if (self::isSqlite() || self::isPgsql()) {
            return "CAST({$column} AS INTEGER)";
        }
        return "CAST({$column} AS UNSIGNED)";
    }

    public static function castDecimal(string $column, string $precision = '12,2'): string
    {
        return "CAST({$column} AS DECIMAL({$precision}))";
    }

    public static function year(string $column): string
    {
        if (self::isSqlite()) return "CAST(strftime('%Y', {$column}) AS INTEGER)";
        return self::isPgsql() ? "CAST(EXTRACT(YEAR FROM {$column}) AS INTEGER)" : "YEAR({$column})";
    }

    public static function hour(string $column): string
    {
        if (self::isSqlite()) return "CAST(strftime('%H', {$column}) AS INTEGER)";
        return self::isPgsql() ? "CAST(EXTRACT(HOUR FROM {$column}) AS INTEGER)" : "HOUR({$column})";
    }

    /**
     * Cross-DB equivalente a CONCAT_WS(' ', NULLIF(a,''), NULLIF(b,''), ...).
     * En SQLite usa || con NULLIF. En MySQL/PgSQL usa CONCAT_WS.
     */
    public static function concatWsNulls(string $separator, string ...$columns): string
    {
        if (self::isSqlite()) {
            // SQLite: TRIM(col1 || ' ' || col2 ...) — filtra nulos con NULLIF
            $parts = array_map(fn($c) => "COALESCE(NULLIF({$c},''), '')", $columns);
            return 'TRIM(' . implode(" || '{$separator}' || ", $parts) . ')';
        }
        $nullified = array_map(fn($c) => "NULLIF({$c},'')", $columns);
        return "TRIM(CONCAT_WS('{$separator}', " . implode(', ', $nullified) . "))";
    }
}
