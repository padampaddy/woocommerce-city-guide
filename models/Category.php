<?php

/**
 *  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
 *  `created_on` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
 *  `name` tinytext NOT NULL, 
 * */
class Category
{

    public $id;
    public $name;
    public $parent;
    public $image;
    private $createdOn;

    /**
     * Class constructor.
     */
    public function __construct(string $name, ?int $id, ?string $createdOn, ?string $image, ?int $parent)
    {
        if (isset($id)) $this->id = $id;
        $this->name = $name;
        if ($createdOn) $this->createdOn = $createdOn;
        else $this->createdOn = date('Y-m-d H:i:s');
        if ($image) $this->image = $image;
        if ($parent) $this->parent = $parent;
    }
    public static function save(Category $category)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "categories";
        if (isset($category->id)) {
            $sql = "UPDATE $table_name set 
                name = '$category->name',
                image = '$category->image',
                parent = '$category->parent'
                where id = $category->id";
            $wpdb->query($sql);
        } else {
            $sql = "INSERT INTO $table_name(name,created_on,image,parent) values (
            '$category->name',
            '$category->createdOn',
            '$category->image',
            '$category->parent'
        )";
            $wpdb->query($sql);
            $category->id = $wpdb->insert_id;
        }
    }
    public static function withId(int $id): ?Category
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "categories";
        $sql = "SELECT * from $table_name where id = $id";
        $row = $wpdb->get_row($sql, ARRAY_A);
        if ($row)
            return Category::withRow($row);
        else return null;
    }
    public static function getAllAsArray(?int $parent): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "categories";
        if (isset($parent))
            $sql = "SELECT * from $table_name where parent=$parent";
        else
            $sql = "SELECT * from $table_name;";
        $rows = $wpdb->get_results($sql, ARRAY_A);
        return $rows;
    }
    public static function getAllParentCategory(): array{
        global $wpdb;
        $table_name = $wpdb->prefix . "categories";
        $sql = "SELECT * from $table_name where parent=0 or parent is null;";
        $rows = $wpdb->get_results($sql, ARRAY_A);
        return $rows;
    }
    public static function deleteBulk($categoryIds)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "categories";
        if (is_array($categoryIds)) {
            $categoryIds = array_diff($categoryIds, [1]);
            $sql = "DELETE from $table_name where id in (" . implode(",", $categoryIds) . ")";
            if (sizeof($categoryIds) == 0) return;
        } else if ($categoryIds == 1)
            return;
        else
            $sql = "DELETE from $table_name where id  = $categoryIds";
        $wpdb->query($sql);
    }
    public static function withRow(array $row): Category
    {
        return new Category($row["name"], $row["id"], $row["created_on"], $row["image"], $row["parent"]);
    }
    public function getCreatedOn(): string
    {
        return $this->createdOn;
    }
}
