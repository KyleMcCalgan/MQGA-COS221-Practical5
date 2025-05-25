 SET profiling = 1;
 SELECT
    S.store_id,
    S.name AS store_name,
    (
        SELECT ROUND(AVG(SI.price), 2)
        FROM STORE_INFO SI
        WHERE SI.store_id = S.store_id AND SI.price > 0
    ) AS average_store_price,
    (
        SELECT ROUND(AVG(SI.rating), 2)
        FROM STORE_INFO SI
        WHERE SI.store_id = S.store_id
    ) AS average_store_rating
FROM
    STORES S
ORDER BY
    S.store_id;


 SELECT
    S.store_id,
    S.name AS store_name,
    ROUND(AVG(SI.price), 2) AS average_store_price,
    ROUND(AVG(SI.rating), 2) AS average_store_rating
FROM
    STORES S
JOIN
    STORE_INFO SI ON S.store_id = SI.store_id
GROUP BY
    S.store_id, S.name
ORDER BY
    S.store_id;

SET profiling=0;
SHOW PROFILES;

EXPLAIN SELECT
    S.store_id,
    S.name AS store_name,
    (
        SELECT ROUND(AVG(SI.price), 2)
        FROM STORE_INFO SI
        WHERE SI.store_id = S.store_id AND SI.price > 0
    ) AS average_store_price,
    (
        SELECT ROUND(AVG(SI.rating), 2)
        FROM STORE_INFO SI
        WHERE SI.store_id = S.store_id
    ) AS average_store_rating
FROM
    STORES S
ORDER BY
    S.store_id;


EXPLAIN SELECT
    S.store_id,
    S.name AS store_name,
    ROUND(AVG(SI.price), 2) AS average_store_price,
    ROUND(AVG(SI.rating), 2) AS average_store_rating
FROM
    STORES S
JOIN
    STORE_INFO SI ON S.store_id = SI.store_id
GROUP BY
    S.store_id, S.name
ORDER BY
    S.store_id;



