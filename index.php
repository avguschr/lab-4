<?php

require('./config/congig.php');
require('./config/db.php');

$action = isset($_GET['action']) ? $_GET['action'] : "";

switch ($action) {
      case 'archive':
        archive();
        break;
      case 'viewPost':
        viewPost();
        break;
      default:
        homepage();
}

function archive() {
    $results = array();
    $data = Post::getList();
    $results['posts'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $results['pageTitle'] = 'post Archive';
    require(TEMPLATE_PATH . '/archive.php');
}

function viewPost() {
    if (!isset(($_GET["postId"]) || !$_GET["postId"])) {
        homepage();
        return;
    }
    
    $results = array();
    $results['post'] = Post::getById((int) $_GET['postId']);
    $result['pageName'] = $results['post']->title;
    require(TEMPLATE_PATH . '/viewPost.php');
}