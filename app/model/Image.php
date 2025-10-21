<?php
class Image {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function saveImage($data, $mimeType) {
        $hash = hash('sha256', $data);

        $stmt_check = $this->conn->prepare("SELECT id FROM images WHERE hash = ?");
        $stmt_check->bind_param("s", $hash);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['id'];
        } else {
            $stmt_img = $this->conn->prepare("INSERT INTO images (image, mime_type, hash) VALUES (?, ?, ?)");
            $empty = "";
            $stmt_img->bind_param("bss", $empty, $mimeType, $hash);
            $stmt_img->send_long_data(0, $data);
            $stmt_img->execute();

            return $this->conn->insert_id;
        }
    }

}
?>
