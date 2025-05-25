<?php
require_once __DIR__ . '/../utils/sanitise_utils.php';

if (! function_exists('handleWebsiteSummary')) {
    function handleWebsiteSummary($data, $db)
    {
        $query1 = "SELECT
                     (SELECT COUNT(store_id) FROM STORES) AS total_stores,
                     (SELECT COUNT(id) FROM PRODUCTS) AS total_books,
                     (SELECT COUNT(*) FROM USERS WHERE user_type = 'regular') AS total_regular_users,
                     (SELECT COUNT(*) FROM REVIEWS) AS total_reviews,
                     (SELECT COUNT(*) FROM RATINGS) AS total_ratings,
                     (SELECT COUNT(*) FROM STORE_INFO) AS total_prices,
                     (SELECT ROUND(MIN(price), 2) FROM STORE_INFO WHERE price > 0) AS cheapest_price,
                     (SELECT ROUND(MAX(price), 2) FROM STORE_INFO WHERE price > 0) AS most_expensive_price,
                     (SELECT ROUND(AVG(rating), 2) FROM RATINGS) AS average_rating,
                     (SELECT ROUND(AVG(price), 2) FROM STORE_INFO WHERE price > 0) AS average_price,
                     (SELECT COUNT(*) FROM CATEGORIES) AS total_categories";

        $stmt1 = $db->prepare($query1);
        if (! $stmt1) {
            error_log("Database prepare statement failed (website summary - query 1): " . $db->error);
            apiResponse(false, null, 'An internal error occurred (q1p). Please try again later.', 500);
        }

        if (! $stmt1->execute()) {
            error_log("Database execute failed (website summary - query 1): " . $stmt1->error);
            $stmt1->close();
            apiResponse(false, null, 'An internal error occurred (q1e). Please try again later.', 500);
        }

        $result1 = $stmt1->get_result();
        $stats   = $result1->fetch_assoc();
        $stmt1->close();

        $responseData = [
            'total_stores'         => isset($stats['total_stores']) ? (int) $stats['total_stores'] : 0,
            'total_books'          => isset($stats['total_books']) ? (int) $stats['total_books'] : 0,
            'total_regular_users'  => isset($stats['total_regular_users']) ? (int) $stats['total_regular_users'] : 0,
            'total_reviews'        => isset($stats['total_reviews']) ? (int) $stats['total_reviews'] : 0,
            'total_ratings'        => isset($stats['total_ratings']) ? (int) $stats['total_ratings'] : 0,
            'total_prices'         => isset($stats['total_prices']) ? (int) $stats['total_prices'] : 0,
            'cheapest_price'       => $stats['cheapest_price'] !== null ? number_format((float) $stats['cheapest_price'], 2) : null,
            'most_expensive_price' => $stats['most_expensive_price'] !== null ? number_format((float) $stats['most_expensive_price'], 2) : null,
            'average_rating'       => $stats['average_rating'] !== null ? number_format((float) $stats['average_rating'], 2) : null,
            'average_price'        => $stats['average_price'] !== null ? number_format((float) $stats['average_price'], 2) : null,
            'total_categories'     => isset($stats['total_categories']) ? (int) $stats['total_categories'] : 0,
        ];

//         $query2 = " SELECT
//     S.store_id,
//     S.name AS store_name,
//     (
//         SELECT ROUND(AVG(SI.price), 2)
//         FROM STORE_INFO SI
//         WHERE SI.store_id = S.store_id AND SI.price > 0
//     ) AS average_store_price,
//     (
//         SELECT ROUND(AVG(SI.rating), 2)
//         FROM STORE_INFO SI
//         WHERE SI.store_id = S.store_id
//     ) AS average_store_rating
// FROM
//     STORES S
// ORDER BY
//     S.store_id;
// ";
//old code above of prev not optimised sql query

        $query2 = "SELECT
                        S.store_id,
                        S.name AS store_name,
                        ROUND(AVG(SI.price), 2) AS average_store_price,
                        ROUND(AVG(SI.rating), 2) AS average_store_rating
                   FROM
                        STORES S
                   LEFT JOIN
                        STORE_INFO SI ON S.store_id = SI.store_id
                   GROUP BY
                        S.store_id, S.name
                   ORDER BY
                        S.store_id;";

        $stmt2 = $db->prepare($query2);
        if (! $stmt2) {
            error_log("Database prepare statement failed (website summary - query 2): " . $db->error);
            $responseData['store_specific_averages'] = [];
        } else {
            if (! $stmt2->execute()) {
                error_log("Database execute failed (website summary - query 2): " . $stmt2->error);
                $responseData['store_specific_averages'] = [];
            } else {
                $result2               = $stmt2->get_result();
                $storeSpecificAverages = [];
                while ($row = $result2->fetch_assoc()) {
                    $storeSpecificAverages[] = [
                        'store_id'             => (int) $row['store_id'],
                        'store_name'           => $row['store_name'],
                        'average_store_price'  => $row['average_store_price'] !== null ? number_format((float) $row['average_store_price'], 2) : null,
                        'average_store_rating' => $row['average_store_rating'] !== null ? number_format((float) $row['average_store_rating'], 2) : null,
                    ];
                }
                $responseData['store_specific_averages'] = $storeSpecificAverages;
            }
            $stmt2->close();
        }

        apiResponse(true, $responseData, null, 200);
    }
}
