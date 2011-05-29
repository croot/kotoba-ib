<?php
$postdata = array('name' => 'KotobaHighloadBot',
                  'subject' => 'Test subject',
                  'text' => 'Test message',
                  'file' => "@1.jpg",
                  'password' => 'testpassword',
                  'goto' => 'b');
$threads_count = 100;
$replies_conut = 100;
$posts_failed = 0;
$posts_total = $threads_count * $replies_conut + 100;
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
for ($t = 0; $t < $threads_count; $t++) {
    $postdata['board'] = '3';
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_URL, 'http://sorc.dyndns-home.com/kotoba/create_thread.php');
    $postdata['t'] = curl_exec($ch);
    if (!ctype_digit($postdata['t'])) {
        $posts_failed += $replies_conut;
        echo "Thread creation failed. Failed/Total posts: {$posts_failed}/{$posts_total} " . (($posts_failed * 100) / $posts_total) . "%\n";
        echo "{$postdata['t']}\n";
        continue;
    } else if ($postdata['t'] == 0) {
        $posts_failed += $replies_conut;
        echo "Suddenly thread is 0. Failed/Total posts: {$posts_failed}/{$posts_total} " . (($posts_failed * 100) / $posts_total) . "%\n";
        continue;
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_URL, 'http://sorc.dyndns-home.com/kotoba/reply.php');
    for ($r = 0; $r < $replies_conut; $r++) {
        $post_id = curl_exec($ch);
        if (!ctype_digit($post_id)) {
            $posts_failed++;
            echo "Post failed. Failed/Total: {$posts_failed}/{$posts_total} " . (($posts_failed * 100) / $posts_total) . "%\n";
            if ($post_id == '') {
                echo "Ops. Something goes wrong. Query does not return post id.\n";
            } else {
                echo "$post_id\n";
            }
        }
    }
}
curl_close($ch);
?>
