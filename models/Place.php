<?php

/**
 *  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
 *  `created_on` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
 *  `name` tinytext NOT NULL, 
 *  `description` text NOT NULL,
 *  `status` varchar(10) DEFAULT 'pending' NOT NULL,
 *  `lat` float DEFAULT 0.0 NOT NULL, 
 *  `long` float DEFAULT 0.0 NOT NULL, 
 *  `category` mediumint(9) NOT NULL, 
 *  `image` tinytext, 
 * */
class Place
{

    public $id;
    public $name;
    public $description;
    public $image;
    public $category;
    public $embedCode;
    private $createdOn;
    public $status;
    public $user_id;
    /**
     * Class constructor.
     */
    public function __construct(string $name, string $embedCode, string $description, ?int $id, ?string $status, ?int $category, ?string $createdOn, ?int $image, $user_id)
    {
        if (isset($id)) $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->embedCode = $embedCode;
        $this->user_id = $user_id;
        if (!isset($category)) $this->category = 1;
        $this->category = Category::withId($category);
        if (isset($status)) $this->status = $status;
        else $this->status = "pending";
        if (isset($createdOn)) $this->createdOn = $createdOn;
        else $this->createdOn = date('Y-m-d H:i:s');
        if (isset($image)) $this->image = $image;
    }
    public static function save(Place $place)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        if (isset($place->id)) {
            $sql = "UPDATE $table_name set 
                name = '$place->name',
                status = '$place->status',
                description = '$place->description',
                category = '" . $place->category->id . "',
                embed_code = '$place->embedCode',
                image = '$place->image',
                user_id = '$place->user_id'
                where id = $place->id";
            $wpdb->query($sql);
        } else {
            $sql = "INSERT INTO $table_name(name,status,description,embed_code,category,created_on, image, user_id) values (
            '$place->name',
            '$place->status',
            '$place->description',
            '$place->embedCode',
            '" . $place->category->id . "',
            '$place->createdOn',
            '$place->image',
            '$place->user_id'
        )";
            $wpdb->query($sql);
            $place->id = $wpdb->insert_id;
        }
    }
    public static function withId(int $id): ?Place
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = "SELECT * from $table_name where id = $id";
        $row = $wpdb->get_row($sql, ARRAY_A);
        if ($row)
            return Place::withRow($row);
        else return null;
    }
    public static function makeActive(int $userId)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = "UPDATE $table_name set status='active' where status='pending' and user_id = $userId";
        $wpdb->query($sql, ARRAY_A);
    }
    public static function checkPending(int $userId)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = "SELECT * from $table_name where status = 'pending' and user_id = $userId";
        $row = $wpdb->get_row($sql, ARRAY_A);
        if ($row)
            return $row;
        else return null;
    }
    public static function getAllAsArray($page = 1): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = sprintf("SELECT * from $table_name limit %d,10", ($page - 1) * 10);
        $rows = $wpdb->get_results($sql, ARRAY_A);
        return $rows;
    }
    public static function getAllWithParams($page = 1, $category = 'all', $query = "%"): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = sprintf("SELECT * from $table_name where status='active' %s %s limit %d,10", $category === 'all' ? '' : "and category=$category", $query === '%' ? '' : "and name like '%$query%'", ($page - 1) * 10);
        $rows = $wpdb->get_results($sql, ARRAY_A);
        return $rows;
    }
    public static function getWithCategory($category): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = sprintf("SELECT * from $table_name where status='active' and category=%s", $category);
        $rows = $wpdb->get_results($sql, ARRAY_A);
        return $rows;
    }
    public static function getTotalPagesWithParams($category = 'all', $query = "%"): int
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = sprintf("SELECT count(*) as count from $table_name where status='active' %s %s", $category === 'all' ? '' : "and category=$category", $query === '%' ? '' : "and name like '%$query%'");
        return (intval($wpdb->get_row($sql, ARRAY_A)["count"]) / 10 + 1) ?? 1;
    }
    public static function getTotalCount(): int
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = "SELECT count(*) as count from $table_name";
        return $wpdb->get_row($sql, ARRAY_A)["count"] ?? 0;
    }
    public static function deleteBulk($placeIds)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        if (is_array($placeIds))
            $sql = "DELETE from $table_name where id in (" . implode(",", $placeIds) . ")";
        else
            $sql = "DELETE from $table_name where id  = $placeIds";
        $wpdb->query($sql);
    }

    public static function withRow(array $row): Place
    {
        return new Place($row["name"], $row["embed_code"], $row["description"], $row["id"], $row["status"], $row["category"], $row["created_on"], $row["image"], $row['user_id']);
    }
    public function getCreatedOn(): string
    {
        return $this->createdOn;
    }
}
