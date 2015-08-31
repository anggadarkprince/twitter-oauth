<?php session_start(); ?>
<h2>Hello <?=(!empty($_SESSION['username']) ? '@' . $_SESSION['username'] : 'Guest'); ?></h2>

Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolor ea impedit possimus quam tempora,
voluptate voluptatum? Aliquid asperiores at consequatur error fuga, ipsa, labore molestiae placeat praesentium,
sit sunt voluptatem?

<p><a href="logout.php">Logout</a></p>