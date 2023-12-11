<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

class Orders_ctrl
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    public function import_orders($req = null)
    {
        if (isset($_POST['action'],$_FILES)) {
            $this->save_imported_orders($req = new stdClass);
            exit;
        }
        $context = (object) array(
            'page' => 'allorders/create.php',
            'data' => (object) array(
                'req' => obj($req),
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    function save_imported_orders($req = null)
    {
        $req = (object) $_POST;
        $req->sheet = (object) $_FILES ?? null;
        $rules = [
            'action' => 'required|string',
            'sheet' => 'required|file',
        ];
        $pass = validateData(data: arr($req), rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn(return:true));
            exit;
        } else {
            $db = new Dbobjects;
            if (isset($req->sheet->sheet)) {
                $file = $req->sheet->sheet;
                // myprint($file);
                $spreadsheet = IOFactory::load($file['tmp_name']);

                $sheet = $spreadsheet->getActiveSheet();

                // Prepare the SQL statement
                $sql = "INSERT INTO manual_orders (created_at, name, email, phone, address, order_item, quantity, amount) VALUES (:created_at, :name, :email, :phone, :address, :order_item, :quantity, :amount)";
                // Loop through each row and insert data into the database
                for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                    $created_at = $sheet->getCell('A' . $row)->getValue();
                    $created_at = $this->convertToValidDateFormat($created_at);
                    $email = $sheet->getCell('C' . $row)->getValue();
                    $old_sql = "select id from manual_orders where email='$email' and created_at = '$created_at'";
                    $exists = $db->showOne($old_sql);
                    if (!$exists) {
                        $params = [
                            ':created_at' => $created_at,
                            ':name' => $sheet->getCell('B' . $row)->getValue(),
                            ':email' => $email,
                            ':phone' => $sheet->getCell('D' . $row)->getValue(),
                            ':address' => $sheet->getCell('E' . $row)->getValue(),
                            ':order_item' => $sheet->getCell('F' . $row)->getValue(),
                            ':quantity' => $sheet->getCell('G' . $row)->getValue(),
                            ':amount' => $sheet->getCell('H' . $row)->getValue(),
                        ];
                    } else {
                        msg_set("Old data found");
                    }
                    try {
                        if (isset($params)) {
                            $stmt = $db->pdo->prepare($sql);
                            $stmt->execute($params);
                        } else {
                            msg_set("Bypass duplicate entry");
                        }
                    } catch (PDOException $e) {
                        echo "Error inserting data for row $row: " . $e->getMessage() . "\n";
                    }
                }
                echo js_alert(msg_ssn(return:true));
                exit;
            }
            exit;
        }
    }
    function convertToValidDateFormat($inputDate)
    {
        // Remove extra spaces and trim the input
        $cleanedDate = trim(preg_replace('/\s+/', ' ', $inputDate));

        // Define possible date formats
        $dateFormats = [
            'm/d/y, h:i A',
            'm/d/y, h:iA',
            'm/d/y, h A',
            'm/d/y, hA',
        ];

        // Try to parse the date using each format
        foreach ($dateFormats as $format) {
            $dateTime = DateTime::createFromFormat($format, $cleanedDate);
            if ($dateTime !== false) {
                // Return the date in a specific format (adjust as needed)
                return $dateTime->format('Y-m-d H:i:s');
            }
        }

        // Return null if the date couldn't be parsed
        return null;
    }
    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
}
