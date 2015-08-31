<?php

session_start();

# We require the library
require_once('twitteroauth/twitteroauth.php');


if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {

    # TwitterOAuth instance, with two new parameters we got in twitter_login.php
    $twitteroauth = new TwitterOAuth('DihIjvV8u74SPsOaXDbOWN7rE', '5qtbYY22XGnRJzj7M6gzHYBMztHqnZwYrAKNASuZ9UmKLfkPuJ', $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

    # Let's request the access token
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);

    # Save it in a session var
    $_SESSION['access_token'] = $access_token;

    # Let's get the user's info
    $user_info = $twitteroauth->get('account/verify_credentials');

    # Print user's info
    echo "<pre>";
    print_r($user_info);
    echo "</pre>";

    if (isset($user_info->error)) {
        # Something's wrong, go back to square 1
        header('Location: login.php');
    } else {
        mysql_connect('localhost', 'root', '') or die("Invalid database connection");
        mysql_select_db('sandbox_crud') or die("invalid database selection");

        # Let's find the user by its ID
        $query = mysql_query("SELECT * FROM users_extend WHERE oauth_provider = 'twitter' AND oauth_uid = " . $user_info->id);
        $result = mysql_fetch_array($query);

        # If not, let's add it to the database
        if (empty($result))
        {
            $avatar_url = "{$user_info->profile_image_url_https}";
            $avatar_url = trim(str_replace("_normal", "", $avatar_url));
            $avatar = file_get_contents($avatar_url);
            file_put_contents("assets/"."{$user_info->id}".".jpeg", $avatar);

            $query = mysql_query("INSERT INTO users_extend (oauth_provider, oauth_uid, username, oauth_token, oauth_secret) VALUES ('twitter', {$user_info->id}, '{$user_info->screen_name}', '{$access_token['oauth_token']}', '{$access_token['oauth_token_secret']}')");
            $query = mysql_query("SELECT * FROM users_extend WHERE id = " . mysql_insert_id());
            $result = mysql_fetch_array($query);
        } else {
            # Update the tokens
            $query = mysql_query("UPDATE users_extend SET oauth_token = '{$access_token['oauth_token']}', oauth_secret = '{$access_token['oauth_token_secret']}' WHERE oauth_provider = 'twitter' AND oauth_uid = {$user_info->id}");
        }

        $_SESSION['id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['oauth_uid'] = $result['oauth_uid'];
        $_SESSION['oauth_provider'] = $result['oauth_provider'];
        $_SESSION['oauth_token'] = $result['oauth_token'];
        $_SESSION['oauth_secret'] = $result['oauth_secret'];

        header('Location: home.php');
    }

} else {
    # Something is missing, go back to login process
    header('Location: login.php');
}