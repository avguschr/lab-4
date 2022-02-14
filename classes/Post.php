<?php
require_once '../config/db.php';

class Post
{
    public $id = null;
    public $title = null;
    public $body = null;
    public $published = null;
    public $img = null;
    public $autor = null;
    public $additiona_photo = null;
    public $chapter = null;

    public function __construct($data=array()) {
        if (isset($data['id'])) $this->id = (int) $data['id'];
        if (isset($data['title'])) $this->title = $data['title'];
        if (isset($data['body'])) $this->body = $data['body'];
        if (isset($data['published'])) $this->published = $data['published'];
        if (isset($data['img'])) $this->img = $data['img'];
        if (isset($data['autor'])) $this->autor = $data['autor'];
        if (isset($data['additional_photo'])) $this->additiona_photo = $data['additional_photo'];
        if (isset($data['chapter'])) $this->chapter = $data['chapter'];
    }

    public function storeFormValues($params) {
        $this->__construct($params);
        if (isset($params['published'])) {
            $published = explode('-', $params['published']);

            if (count($published) == 3) {
                list($y, $m, $d) = $published;
                $this->published = mktime(0, 0, 0, $m, $d, $y);
            }
        }
    }

    public static function getById($id) {
        $sql = 'SELECT *, UNIX_TIMESTAMP(published) AS published FROM post WHERE id = :id';
        $st = $conn->prepare($sql);
        $st->bindValue(':id', $id, PDO:PARAM_INT);
        $st->execute();
        $row = $st->fetch();
        if ($row) return new Post($row);
    }

    public static function getList($numRows=1000000, $order='published DESC') {
        $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(published) AS published FROM post ORDER BY ' . mysql_escape_string($order) . '";
        $st = $conn->prepare($sql);
        $st->bindValue(':numRows', $numRows, PDO::PARAM_INT);
        $st->execute();
        $list = array();

        while ($row = $st->fetch()) {
            $post = new Post($row);
            $list[] = $post;
        }

        $sql = 'SELECT FOUND_ROWS() AS totalRows';
        $totalRows = $conn->query($sql)->fetch();

        return (array('res' => $list, 'totalRows' => $totalRows[0]));
    }

    public function insert() {
        if (!is_null($this->id)) trigger_error("Post::insert(): Attempt to insert an Post object that already has its ID property set (to $this->id).", E_USER_ERROR);
        
        $sql = "INSERT INTO post (title, body, published, img, autor, additional_photo, chapter) VALUES (FROM_UNIXTIME(:published), :title, :body, :img, :autor, :additional_photo, :chapter)";
        $st = $conn->prepare($sql);
        $st->bindValue(":published", $this->published, PDO::PARAM_INT);
        $st->bindValue(":title", $this->title, PDO::PARAM_STR);
        $st->bindValue(":body", $this->body, PDO::PARAM_STR);
        $st->bindValue(":img", $this->img, PDO::PARAM_STR);
        $st->bindValue(":autor", $this->autor, PDO::PARAM_STR);
        $st->bindValue(":additional_photo", $this->additional_photo, PDO::PARAM_STR);
        $st->bindValue(":chapter", $this->chapter, PDO::PARAM_STR);
        $st->execute();
        $this->id = $conn->lastInsertId();
    }

    public function update() {
        if (is_null($this->id)) trigger_error("Post::update(): Attempt to update an Post object that does not have its ID property set.", E_USER_ERROR);

        $sql = "UPDATE post SET published=FROM_UNIXTIME(:published), title=:title, body=:body, img=:img, autor=:autor, :additional_photo=additional_photo, :chapter=chapter WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":published", $this->published, PDO::PARAM_INT);
        $st->bindValue(":title", $this->title, PDO::PARAM_STR);
        $st->bindValue(":body", $this->body, PDO::PARAM_STR);
        $st->bindValue(":img", $this->img, PDO::PARAM_STR);
        $st->bindValue(":autor", $this->autor, PDO::PARAM_STR);
        $st->bindValue(":additional_photo", $this->additional_photo, PDO::PARAM_STR);
        $st->bindValue(":chapter", $this->chapter, PDO::PARAM_STR);
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
        $st->execute();
    }

    public function delete() {
        if (is_null( $this->id)) trigger_error( "Post::delete(): Attempt to delete an Post object that does not have its ID property set.", E_USER_ERROR);

        $st = $conn->prepare("DELETE FROM post WHERE id = :id LIMIT 1");
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
        $st->execute();
    }        
}