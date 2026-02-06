<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $fname;
    public $lname;
    public $email;
    public $phone_number;
    public $role;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name}
            (fname, lname, email, phone_number, role, password)
            VALUES (:fname, :lname, :email, :phone_number, :role, :password)";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":fname", $this->fname);
        $stmt->bindParam(":lname", $this->lname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":password", $password_hash);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function phoneExists() {
        $query = "SELECT id FROM {$this->table_name}
                  WHERE phone_number = :phone_number LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function login($identifier, $password) {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE email = :id OR phone_number = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $identifier);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])) {
                $this->id           = $row['id'];
                $this->fname        = $row['fname'];
                $this->lname        = $row['lname'];
                $this->email        = $row['email'];
                $this->phone_number = $row['phone_number'];
                $this->role         = $row['role'];
                return true;
            }
        }
        return false;
    }
}
