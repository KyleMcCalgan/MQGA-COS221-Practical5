<?php
$book_ids = [
    2, 3, 4, 5, 7, 8, 10, 12, 13, 14, 15, 16, 17, 19, 20, 21, 22, 23, 24, 25, 26,
    32, 33, 34, 35, 36, 37, 40, 41, 42, 45, 46, 48, 50, 52, 53, 54, 55, 57, 59, 60, 
    61, 63, 64, 65, 66, 67, 73, 75, 76, 77, 78, 79, 80, 83, 86, 89, 104, 107, 109, 
    112, 113, 114, 117, 118, 119, 125, 126, 129, 130, 131, 132, 133, 134, 135, 136, 
    137, 138, 139, 140, 141, 142, 144, 147, 148, 151, 152, 154, 155, 156, 157, 158, 
    159, 160, 161, 162, 164, 166, 168, 169, 172, 173, 174, 175, 176, 178, 181, 182, 
    183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 195, 196, 197, 198, 
    199, 201, 202, 205, 206, 207, 208, 209, 210, 211, 212, 213, 215, 216, 217, 218, 
    219, 220, 221, 222, 224, 225, 226, 229, 230, 231, 233, 234, 235, 236, 237, 239, 
    240, 241, 242, 243, 244, 245, 246, 247, 248, 249, 250, 253, 254, 259, 261, 263, 
    264, 266, 268, 269, 270, 272, 274, 275, 277, 278, 280, 281, 284, 285, 286, 287, 
    292, 294, 296, 297, 299, 300, 301, 302, 303, 304, 306, 308, 324, 325, 326, 327, 
    328, 329, 330, 331, 332, 334, 335, 336, 337, 338, 339, 340, 342, 343, 344, 345, 
    346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 362, 
    363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 374, 375, 377, 378, 380, 381, 
    382, 384, 385, 386, 387, 388, 389, 391, 392, 393, 394, 396, 397, 399, 408, 409, 
    410, 415, 422, 425, 429, 430, 431, 433, 435, 437, 439, 440, 441, 442, 443, 444, 
    445, 448, 449, 450, 451, 452, 455, 456, 459, 460, 461, 463, 464, 466, 467, 468,
    473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 
    489, 491, 492, 493, 494, 495, 496, 497, 498, 499, 505, 506, 507, 508
];
$user_ids = [77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87];
$reviews = [
    ['text' => 'Really disappointing read, didn’t live up to expectations.', 'stars' => 1],
    ['text' => 'Struggled to get through it, not engaging at all.', 'stars' => 1],
    ['text' => 'Poorly written and hard to follow.', 'stars' => 1],
    ['text' => 'Felt like a waste of time, wouldn’t recommend.', 'stars' => 1],
    ['text' => 'Boring and predictable, couldn’t finish it.', 'stars' => 1],
    ['text' => 'Not worth the price, very underwhelming.', 'stars' => 1],
    ['text' => 'Confusing plot with unlikable characters.', 'stars' => 1],
    ['text' => 'Expected more, but it fell flat.', 'stars' => 1],
    ['text' => 'Lackluster and forgettable story.', 'stars' => 1],
    ['text' => 'Didn’t connect with it, quite dull.', 'stars' => 1],
    ['text' => 'It was okay, but nothing special.', 'stars' => 2],
    ['text' => 'Had some good moments, but overall meh.', 'stars' => 2],
    ['text' => 'Mediocre, could’ve been better.', 'stars' => 2],
    ['text' => 'Not terrible, but not great either.', 'stars' => 2],
    ['text' => 'Some parts were fine, others dragged.', 'stars' => 2],
    ['text' => 'Average read, didn’t stand out.', 'stars' => 2],
    ['text' => 'A bit slow, didn’t fully grab me.', 'stars' => 2],
    ['text' => 'Okay for a quick read, but forgettable.', 'stars' => 2],
    ['text' => 'Had potential, but didn’t deliver.', 'stars' => 2],
    ['text' => 'Mixed feelings, not my favorite.', 'stars' => 2],
    ['text' => 'Decent read, worth checking out.', 'stars' => 3],
    ['text' => 'Enjoyed it, but not a standout.', 'stars' => 3],
    ['text' => 'Good enough, no major complaints.', 'stars' => 3],
    ['text' => 'Solid, but didn’t blow me away.', 'stars' => 3],
    ['text' => 'Fairly engaging, a nice read.', 'stars' => 3],
    ['text' => 'Middle-of-the-road, not bad.', 'stars' => 3],
    ['text' => 'Interesting in parts, overall fine.', 'stars' => 3],
    ['text' => 'A good time, but not memorable.', 'stars' => 3],
    ['text' => 'Balanced read, some highs and lows.', 'stars' => 3],
    ['text' => 'Pretty good, but room for improvement.', 'stars' => 3],
    ['text' => 'Really enjoyed this, highly recommend!', 'stars' => 4],
    ['text' => 'Great read, kept me hooked.', 'stars' => 4],
    ['text' => 'Well-written and engaging story.', 'stars' => 4],
    ['text' => 'Loved the pacing and characters.', 'stars' => 4],
    ['text' => 'Very entertaining, solid pick.', 'stars' => 4],
    ['text' => 'Impressive, didn’t want it to end.', 'stars' => 4],
    ['text' => 'Captivating, a great experience.', 'stars' => 4],
    ['text' => 'Strong story, worth your time.', 'stars' => 4],
    ['text' => 'Really good, almost perfect.', 'stars' => 4],
    ['text' => 'Fun and gripping, loved it.', 'stars' => 4],
    ['text' => 'Absolutely fantastic, a must-read!', 'stars' => 5],
    ['text' => 'Couldn’t put it down, phenomenal!', 'stars' => 5],
    ['text' => 'One of the best I’ve ever read.', 'stars' => 5],
    ['text' => 'Masterpiece, absolutely loved it.', 'stars' => 5],
    ['text' => 'Perfect from start to finish.', 'stars' => 5],
    ['text' => 'Incredible story, highly recommend.', 'stars' => 5],
    ['text' => 'Unforgettable, a true gem.', 'stars' => 5],
    ['text' => 'Brilliant, exceeded all expectations.', 'stars' => 5],
    ['text' => 'A stunning read, pure perfection.', 'stars' => 5],
    ['text' => 'Loved every page, simply amazing.', 'stars' => 5]
];
if (count($book_ids) !== 330) {
    die("Error: Expected 330 book IDs, got " . count($book_ids));
}
if (count($user_ids) !== 11) {
    die("Error: Expected 11 user IDs, got " . count($user_ids));
}
if (count($reviews) !== 50) {
    die("Error: Expected 50 reviews, got " . count($reviews));
}
$sql = "";
$review_rows = [];
$rating_rows = [];
foreach ($user_ids as $user_id) {
    $selected_book_ids = array_rand(array_flip($book_ids), 100);
    foreach ($selected_book_ids as $book_id) {
        $review_index = array_rand($reviews);
        $review_text = $reviews[$review_index]['text'];
        $rating = $reviews[$review_index]['stars'];
        $review_text = addslashes($review_text);
        $review_rows[] = "($book_id, $user_id, '$review_text')";
        $rating_rows[] = "($book_id, $user_id, $rating)";
    }
}
if (count($review_rows) !== 1100 || count($rating_rows) !== 1100) {
    die("Error: Expected 1100 rows, got " . count($review_rows) . " reviews, " . count($rating_rows) . " ratings");
}
$sql .= "INSERT INTO `REVIEWS` (`book_id`, `user_id`, `review`) VALUES\n";
$sql .= implode(",\n", $review_rows) . ";\n\n";

$sql .= "INSERT INTO `RATINGS` (`book_id`, `user_id`, `rating`) VALUES\n";
$sql .= implode(",\n", $rating_rows) . ";";

file_put_contents('reviews_and_ratings.sql', $sql);
echo "SQL file generated: reviews_and_ratings.sql with 1100 reviews and 1100 ratings\n";
?>