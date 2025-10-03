<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;

class OrderSummaryService
{
    static function generate()
    {
        DB::transaction(function () {
            DB::statement('
                INSERT INTO client_order_summary (
                    client_id,
                    name,
                    number_of_orders,
                    total_price_of_orders,
                    most_recent_order_date,
                    recency,
                    frequency,
                    monetary
                )
                SELECT
                    s.client_id,
                    s.name,
                    s.number_of_orders,
                    s.total_price_of_orders,
                    s.most_recent_order_date,
                    NTILE(5) OVER (ORDER BY s.most_recent_order_date ASC) AS recency,
                    NTILE(5) OVER (ORDER BY s.number_of_orders DESC) AS frequency,
                    NTILE(5) OVER (ORDER BY s.total_price_of_orders DESC) AS monetary
                FROM (
                    SELECT
                        c.id AS client_id,
                        c.name,
                        COUNT(o.id) AS number_of_orders,
                        COALESCE(SUM(o.price), 0) AS total_price_of_orders,
                        MAX(o.created_at) AS most_recent_order_date
                    FROM clients c
                    LEFT JOIN orders o ON o.client_id = c.id
                    GROUP BY c.id, c.name
                ) s
                ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    number_of_orders = VALUES(number_of_orders),
                    total_price_of_orders = VALUES(total_price_of_orders),
                    most_recent_order_date = VALUES(most_recent_order_date),
                    recency = VALUES(recency),
                    frequency = VALUES(frequency),
                    monetary = VALUES(monetary)
            ');
        });
    }
}
