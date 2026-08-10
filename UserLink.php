<?php
session_start();
require('./include/config.php');
class User1 extends Dbconfig {
	protected $db_driver;
    protected $hostName;
    protected $userName;
    protected $password;
	protected $dbName;
	protected $Site_url;
	protected $cookies = []; // Mảng để lưu trữ cookie
	protected $Sessions = []; // Mảng để lưu trữ cookie
	private $userTable = 'user';
	private $dbConnect = false;
	private $dbConnect1 = false;
    public function __construct(){
		
	if (empty($this->hostName) || empty($this->userName) || empty($this->password) || empty($this->dbName)) {
        $database = new dbConfig();   
        $this->db_driver = "mysql";		
        $this->hostName = $database->serverName;
        $this->userName = $database->userName;
        $this->password = $database->password;
        $this->dbName = $database->dbName;
		 $this->Site_url = $database->Site_url;
    }	
        if(!$this->dbConnect){ 		
			/*$database = new dbConfig();            
            $this -> hostName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;*/			
            $conn = new mysqli($this->hostName, $this->userName, $this->password, $this->dbName);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            } else{
                $this->dbConnect = $conn;
            }
        }
	if(!$this->dbConnect1){ 		
			/*$database = new dbConfig();            
            $this -> hostName = $database -> serverName;
            $this -> userName = $database -> userName;
            $this -> password = $database ->password;
			$this -> dbName = $database -> dbName;		*/	
		$dboptions = array(
			PDO::ATTR_PERSISTENT => FALSE,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		);
		try {
		 $this->dbConnect1 = new PDO($this->db_driver . ':host=' . $this -> hostName . ';dbname=' . $this -> dbName, $this -> userName, $this -> password, $dboptions);
		} catch (Exception $ex) {
		  echo $ex->getMessage();
		  die;
		}
    }
 }
   public function createCookieName($id) {
        return "ma_".$id; // Tạo tên cookie
    }
	
    // Hàm thiết lập cookie
    public function setCookieById($id, $value) {
        if (!empty($value)) { // Kiểm tra nếu giá trị không rỗng
            $cookie_name = $this->createCookieName($id); // Gọi hàm tạo tên cookie
            setcookie($cookie_name, $value, time() + (86400 * 30), "/"); // Cookie tồn tại trong 30 ngày
            
            // Lưu trữ vào mảng cookies
            $this->cookies[$id] = $value; // Lưu giá trị cookie theo ID
        } else {
            echo "Giá trị cookie không được để trống.";
        }
    }

    // Hàm lấy giá trị cookie từ ID
    public function getCookieValueById($id) {
        $cookie_name = $this->createCookieName($id); // Tạo tên cookie từ ID
        if (isset($_COOKIE[$cookie_name])) {
            return $_COOKIE[$cookie_name]; // Trả về giá trị của cookie
        } else {
            // Kiểm tra trong mảng cookies nếu không tìm thấy
            return isset($this->cookies[$id]) ? $this->cookies[$id] : null; // Nếu không có trong cookie, trả về từ mảng
        }
    }
	 public function createSessionName($id) {
        return "ma_".$id; // Tạo tên cookie
    }

    // Hàm thiết lập cookie
    public function setSessionById($id, $value) {
        if (!empty($value)) { // Kiểm tra nếu giá trị không rỗng
            $Session_name = $this->createSessionName($id); // Gọi hàm tạo tên cookie
            //setcookie($cookie_name, $value, time() + (86400 * 30), "/"); // Cookie tồn tại trong 30 ngày
            $_SESSION[$Session_name]=$value;
            // Lưu trữ vào mảng cookies
            $this->Sessions[$id] = $value; // Lưu giá trị cookie theo ID
        } else {
            echo "Giá trị Sessions không được để trống.";
        }
    }

    // Hàm lấy giá trị cookie từ ID
    public function getSessionValueById($id) {
       $Session_name = $this->createSessionName($id);// Tạo tên cookie từ ID
        if (isset( $_SESSION[$Session_name])) {
            return  $_SESSION[$Session_name]; // Trả về giá trị của cookie
        } else {
            // Kiểm tra trong mảng cookies nếu không tìm thấy
            return isset($this->Sessions[$id]) ? $this->Sessions[$id] : null; // Nếu không có trong cookie, trả về từ mảng
        }
    }
   

    // Hàm để lấy tất cả các cookie đã lưu
    public function getAllCookies() {
        return $this->cookies; // Trả về toàn bộ mảng cookies
    }
public function getData($sqlQuery) {
    $result = mysqli_query($this->dbConnect, $sqlQuery);
    if (!$result) {
        return false; // Trả về false nếu truy vấn thất bại
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC); // Trả về tất cả các hàng trong một mảng
}

public function addNoiDung($data)
{
    try {

        $pdo = null;

        if (isset($this->dbConnect1) && $this->dbConnect1 instanceof PDO) {
            $pdo = $this->dbConnect1;
        }
        elseif (isset($this->conn) && $this->conn instanceof PDO) {
            $pdo = $this->conn;
        }
        else {
            return array(
                'success' => false,
                'message' => 'Không có kết nối DB (PDO) trong class'
            );
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO tbl_noidung
                (
                    id_chuong,
                    ten_noidung,
                    type,
                    placeholder,
                    Namefunction,
                    Folder,
                    Linkvideo,
                    image,
                    active
                )
                VALUES
                (
                    :id_chuong,
                    :ten_noidung,
                    :type,
                    :placeholder,
                    :Namefunction,
                    :Folder,
                    :Linkvideo,
                    :image,
                    :active
                )";

        $stmt = $pdo->prepare($sql);

        $executed = $stmt->execute(array(

            ':id_chuong'    => $data['id_chuong'],
            ':ten_noidung'  => $data['ten_noidung'],
            ':type'         => $data['type'],
            ':placeholder'  => $data['placeholder'],
            ':Namefunction' => $data['Namefunction'],
            ':Folder'       => $data['Folder'],
            ':Linkvideo'    => $data['Linkvideo'],

            ':image'        => isset($data['image'])
                                ? $data['image']
                                : '',

            ':active'       => $data['active']

        ));

        if ($executed) {

            return array(
                'success'  => true,
                'message'  => 'Thêm nội dung thành công',
                'insertId' => $pdo->lastInsertId(),
                'rowCount' => $stmt->rowCount()
            );

        } else {

            return array(
                'success' => false,
                'message' => 'Lệnh INSERT không thực thi được'
            );

        }

    } catch (PDOException $e) {

        @file_put_contents(
            __DIR__ . '/debug_addNoiDung.log',
            date('c') . " - PDOException: " . $e->getMessage() . PHP_EOL,
            FILE_APPEND
        );

        return array(
            'success' => false,
            'message' => 'Lỗi DB: ' . $e->getMessage()
        );

    } catch (Exception $e) {

        return array(
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        );

    }
}
```php
public function Editusers(
    $id,
    $Id_Pm,
    $Id_pmw1,
    $Hoten,
    $Email,
    $Phone,
    $Password,
    $gender,
    $designation,
    $image,
    $Date,
    $Solancap,
    $Solandung,
    $status,
    $Xoa
) {
    try {

        /*
         * Lấy PDO
         */
        $pdo = null;

        if (
            isset($this->dbConnect1) &&
            $this->dbConnect1 instanceof PDO
        ) {
            $pdo = $this->dbConnect1;
        }
        elseif (
            isset($this->conn) &&
            $this->conn instanceof PDO
        ) {
            $pdo = $this->conn;
        }
        else {
            return array(
                'success' => false,
                'message' => 'Không có kết nối DB'
            );
        }

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );


        /*
         * Cập nhật users
         *
         * KHÔNG cập nhật Serial_computer
         */
        $sql = "
            UPDATE users
            SET
                Id_Pm = :Id_Pm,
                Id_pmw1 = :Id_pmw1,
                Hoten = :Hoten,
                Email = :Email,
                Phone = :Phone,
                Password = :Password,
                gender = :gender,
                designation = :designation,
                image = :image,
                Date = :Date,
                Solancap = :Solancap,
                Solandung = :Solandung,
                status = :status,
                Xoa = :Xoa
            WHERE id = :id
        ";


        $stmt = $pdo->prepare($sql);


        $executed = $stmt->execute(array(

            ':Id_Pm'       => $Id_Pm,
            ':Id_pmw1'     => $Id_pmw1,
            ':Hoten'       => $Hoten,
            ':Email'       => $Email,
            ':Phone'       => $Phone,
            ':Password'    => $Password,
            ':gender'      => $gender,
            ':designation' => $designation,
            ':image'       => $image,
            ':Date'        => $Date,
            ':Solancap'    => $Solancap,
            ':Solandung'   => $Solandung,
            ':status'      => $status,
            ':Xoa'         => $Xoa,
            ':id'          => $id

        ));


        if ($executed) {

            return array(
                'success' => true,
                'message' => 'Cập nhật users thành công',
                'rowCount' => $stmt->rowCount()
            );

        }
        else {

            return array(
                'success' => false,
                'message' => 'Update thất bại'
            );

        }

    }
    catch (PDOException $e) {

        return array(
            'success' => false,
            'message' => $e->getMessage()
        );

    }
    catch (Exception $e) {

        return array(
            'success' => false,
            'message' => $e->getMessage()
        );

    }
}
```

public function getAllHocSinh()
{
    try {
        $pdo = null;

        if (isset($this->dbConnect1) && $this->dbConnect1 instanceof PDO) {
            $pdo = $this->dbConnect1;
        } elseif (isset($this->conn) && $this->conn instanceof PDO) {
            $pdo = $this->conn;
        } else {
            return array('success' => false, 'message' => 'Không có kết nối DB', 'data' => array());
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT Id_hocsinh, Hovaten, Lop FROM hocsinh ORDER BY Lop, Hovaten";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return array(
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        );

    } catch (Exception $e) {
        return array('success' => false, 'message' => $e->getMessage(), 'data' => array());
    }
}
// Lấy toàn bộ danh sách nội dung
public function getAllNoiDung()
{
    try {
        $pdo = null;

        if (isset($this->dbConnect1) && $this->dbConnect1 instanceof PDO) {
            $pdo = $this->dbConnect1;
        } elseif (isset($this->conn) && $this->conn instanceof PDO) {
            $pdo = $this->conn;
        } else {
            return array('success' => false, 'message' => 'Không có kết nối DB', 'data' => array());
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT id, id_chuong, ten_noidung, type, Folder, active
                FROM tbl_noidung
                WHERE active = 1
                ORDER BY id_chuong, id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return array('success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC));

    } catch (Exception $e) {
        return array('success' => false, 'message' => $e->getMessage(), 'data' => array());
    }
}

// Gán 1 nội dung cho 1 học sinh (insert vào tbl_hocsinh_noidung)
public function addHocSinhNoiDung($id_hocsinh, $id_noidung)
{
    try {
        $pdo = null;

        if (isset($this->dbConnect1) && $this->dbConnect1 instanceof PDO) {
            $pdo = $this->dbConnect1;
        } elseif (isset($this->conn) && $this->conn instanceof PDO) {
            $pdo = $this->conn;
        } else {
            return array('success' => false, 'message' => 'Không có kết nối DB (PDO) trong class');
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Kiểm tra đã gán nội dung này cho học sinh này chưa, tránh trùng
        $checkSql = "SELECT id FROM tbl_hocsinh_noidung WHERE id_hocsinh = :id_hocsinh AND id_noidung = :id_noidung";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(array(
            ':id_hocsinh' => $id_hocsinh,
            ':id_noidung' => $id_noidung
        ));

        if ($checkStmt->fetch()) {
            return array('success' => false, 'message' => 'Học sinh này đã được gán nội dung này rồi');
        }

        $sql = "INSERT INTO tbl_hocsinh_noidung (id_hocsinh, id_noidung, active, ngaytao)
                VALUES (:id_hocsinh, :id_noidung, 1, NOW())";

        $stmt = $pdo->prepare($sql);

        $executed = $stmt->execute(array(
            ':id_hocsinh' => $id_hocsinh,
            ':id_noidung' => $id_noidung
        ));

        if ($executed) {
            return array(
                'success'  => true,
                'message'  => 'Gán nội dung thành công',
                'insertId' => $pdo->lastInsertId()
            );
        } else {
            return array('success' => false, 'message' => 'Lệnh INSERT không thực thi được');
        }

    } catch (PDOException $e) {

        @file_put_contents(
            __DIR__ . '/debug_addHocSinhNoiDung.log',
            date('c') . " - PDOException: " . $e->getMessage() . PHP_EOL,
            FILE_APPEND
        );

        return array('success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage());

    } catch (Exception $e) {
        return array('success' => false, 'message' => 'Lỗi: ' . $e->getMessage());
    }
}
public function getData1($userTable, $includeSelf = false) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM " . $userTable;

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        $sql .= " WHERE Id_user != '" . $_SESSION['userid'] . "'";
    }

    $result = $this->dbConnect->query($sql);

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Query failed: " . $this->dbConnect->error);
    }

    // Khởi tạo mảng kết quả
    $links = [];

    // Lấy dữ liệu từ kết quả truy vấn
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $links[] = $row; // Thêm từng hàng vào mảng
        }
    }

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $links;
}
public function getData9()
{
    try {
        $pdo = null;

        if (isset($this->dbConnect1) && $this->dbConnect1 instanceof PDO) {
            $pdo = $this->dbConnect1;
        } elseif (isset($this->conn) && $this->conn instanceof PDO) {
            $pdo = $this->conn;
        } else {
            return array('success' => false, 'message' => 'Không có kết nối DB (PDO) trong class');
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT
                    hsnd.id,
                    hsnd.id_hocsinh,
                    hsnd.id_noidung,
                    hsnd.active,
                    hsnd.ngaytao,
                    hs.Hovaten,
                    hs.Lop,
                    nd.ten_noidung
                FROM tbl_hocsinh_noidung hsnd
                INNER JOIN hocsinh hs
                    ON hsnd.id_hocsinh = hs.Id_hocsinh
                INNER JOIN tbl_noidung nd
                    ON hsnd.id_noidung = nd.id
                ORDER BY hsnd.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return array(
            'success' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data'    => $stmt->fetchAll(PDO::FETCH_ASSOC)
        );

    } catch (PDOException $e) {
        @file_put_contents(__DIR__ . '/debug_getData9.log', date('c') . " - PDOException: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return array('success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage());
    } catch (Exception $e) {
        @file_put_contents(__DIR__ . '/debug_getData9.log', date('c') . " - Exception: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return array('success' => false, 'message' => 'Lỗi: ' . $e->getMessage());
    }
}
public function updateQuestion($Id, $Id_Categories, $Namequestion, $Images, $Question, $Type, $answer1, $answer2, $answer3, $answer4, $Correct_answer) {
    try {
        // First check if the question exists
        $checkSql = "SELECT Id FROM question WHERE Id = :Id";
        $checkStmt = $this->dbConnect1->prepare($checkSql);
        $checkStmt->bindValue(":Id", $Id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        // If no rows returned, question doesn't exist
        if ($checkStmt->rowCount() === 0) {
            return [
                'success' => false,
                'message' => 'Question not found'
            ];
        }
        
        // Prepare the update SQL statement
        $sql = "UPDATE question 
                SET Id_Categories = :Id_Categories, 
                    Namequestion = :Namequestion, 
                    Images = :Images,
                    Question = :Question,
                    Type = :Type,
                    answer1 = :answer1,
                    answer2 = :answer2,
                    answer3 = :answer3,
                    answer4 = :answer4,
                    Correct_answer = :Correct_answer
                WHERE Id = :Id";
        
        // Prepare statement
        $stmt = $this->dbConnect1->prepare($sql);
        
        // Bind parameters
        $stmt->bindValue(":Id_Categories", $Id_Categories, PDO::PARAM_INT);
        $stmt->bindValue(":Namequestion", $Namequestion, PDO::PARAM_STR);
        $stmt->bindValue(":Images", $Images, PDO::PARAM_STR);
        $stmt->bindValue(":Question", $Question, PDO::PARAM_STR);
        $stmt->bindValue(":Type", $Type, PDO::PARAM_STR);
        $stmt->bindValue(":answer1", $answer1, PDO::PARAM_STR);
        $stmt->bindValue(":answer2", $answer2, PDO::PARAM_STR);
        $stmt->bindValue(":answer3", $answer3, PDO::PARAM_STR);
        $stmt->bindValue(":answer4", $answer4, PDO::PARAM_STR);
        $stmt->bindValue(":Correct_answer", $Correct_answer, PDO::PARAM_STR);
        $stmt->bindValue(":Id", $Id, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        
        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Question updated successfully',
                'rowCount' => $stmt->rowCount()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No changes were made to the question'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
public function AddExcise($id_bttoan, $id_user, $id_khoi, $id_hocky, $id_chuong, $tenbaitap, $hinh, $ngaygiao, $ngaynop)
{
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Câu lệnh SQL chèn vào bảng baitaptoan
    $sql = "INSERT INTO `baitaptoan` 
            (`id_bttoan`, `id_user`, `id_khoi`, `id_hocky`, `id_chuong`, `Tenbaitap`, `Hinh`, `Ngaygiao`, `Ngaynop`)
            VALUES (:id_bttoan, :id_user, :id_khoi, :id_hocky, :id_chuong, :tenbaitap, :hinh, :ngaygiao, :ngaynop)";

    try {
        $stmt = $this->dbConnect1->prepare($sql);

        // Gán giá trị cho các tham số
        $stmt->bindValue(":id_bttoan", $id_bttoan, PDO::PARAM_INT);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);
        $stmt->bindValue(":id_khoi", $id_khoi, PDO::PARAM_INT);
        $stmt->bindValue(":id_hocky", $id_hocky, PDO::PARAM_INT);
        $stmt->bindValue(":id_chuong", $id_chuong, PDO::PARAM_INT);
        $stmt->bindValue(":tenbaitap", $tenbaitap, PDO::PARAM_STR);
        $stmt->bindValue(":hinh", $hinh, PDO::PARAM_STR);
        $stmt->bindValue(":ngaygiao", $ngaygiao);
        $stmt->bindValue(":ngaynop", $ngaynop);

        // Thực thi câu lệnh
        $stmt->execute();

        // Trả kết quả
        $message = "Insert successfully";
        $rowCount = $stmt->rowCount();
        $debugSql = str_replace(
            [":id_bttoan", ":id_user", ":id_khoi", ":id_hocky", ":id_chuong", ":tenbaitap", ":hinh", ":ngaygiao", ":ngaynop"],
            [$id_bttoan, $id_user, $id_khoi, $id_hocky, $id_chuong, $tenbaitap, $hinh, $ngaygiao, $ngaynop],
            $sql
        );

    } catch (Exception $e) {
        $message = "Insert failed: " . $e->getMessage();
        $rowCount = 0;
        $debugSql = $sql;
    }

    // Xuất kết quả dạng JSON
    echo json_encode([
        'message' => $message,
        'rowCount' => $rowCount,
        'debugSql' => $debugSql
    ]);
    return;
}

public function updateCourse($Id_noidung, $Id_chuong, $Tennoidung, $Linkvideo, $Namefunction)
{
    try {
        // Tìm PDO instance: $dbConnect1 hoặc $conn
        $pdo = null;
        if (isset($this->dbConnect1) && $this->dbConnect1 instanceof PDO) {
            $pdo = $this->dbConnect1;
        } elseif (isset($this->conn) && $this->conn instanceof PDO) {
            $pdo = $this->conn;
        } else {
            return ['success' => false, 'message' => 'Không có kết nối DB (PDO) trong class'];
        }

        // Bật chế độ exceptions để bắt lỗi rõ ràng
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE noidungkhoahoc
                SET Id_chuong = :Id_chuong,
                    Tennoidung = :Tennoidung,
                    Linkvideo = :Linkvideo,
                    Namefunction = :Namefunction
                WHERE Id_noidung = :Id_noidung";

        $stmt = $pdo->prepare($sql);

        $executed = $stmt->execute([
            ':Id_chuong'    => $Id_chuong,
            ':Tennoidung'   => $Tennoidung,
            ':Linkvideo'    => $Linkvideo,
            ':Namefunction' => $Namefunction,
            ':Id_noidung'   => $Id_noidung
        ]);

        // Nếu execute trả về true -> coi là thành công (MySQL có thể trả rowCount = 0 nếu dữ liệu giống cũ)
        if ($executed) {
            return [
                'success'  => true,
                'message'  => 'Cập nhật thành công (execute ok)',
                'rowCount' => $stmt->rowCount()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Lệnh UPDATE không thực thi (execute returned false)'
            ];
        }
    } catch (PDOException $e) {
        // Ghi log debug (tuỳ môi trường bạn có thể đổi đường dẫn)
        @file_put_contents(__DIR__ . '/debug_updateCourse.log', date('c') . " - PDOException: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return ['success' => false, 'message' => 'Lỗi DB: ' . $e->getMessage()];
    } catch (Exception $e) {
        @file_put_contents(__DIR__ . '/debug_updateCourse.log', date('c') . " - Exception: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
    }
}
public function updateHocSinhNoiDung($id, $id_hocsinh, $id_noidung, $active)
{
    try {

        // Tìm PDO instance: $dbConnect1 hoặc $conn
        $pdo = null;

        if (isset($this->dbConnect1) && $this->dbConnect1 instanceof PDO) {
            $pdo = $this->dbConnect1;
        } elseif (isset($this->conn) && $this->conn instanceof PDO) {
            $pdo = $this->conn;
        } else {
            return array(
                'success' => false,
                'message' => 'Không có kết nối DB (PDO) trong class'
            );
        }

        // Bật chế độ exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE tbl_hocsinh_noidung
                SET
                    id_hocsinh = :id_hocsinh,
                    id_noidung = :id_noidung,
                    active = :active
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $executed = $stmt->execute(array(
            ':id_hocsinh' => $id_hocsinh,
            ':id_noidung' => $id_noidung,
            ':active' => $active,
            ':id' => $id
        ));

        // Execute thành công
        if ($executed) {

            return array(
                'success'  => true,
                'message'  => 'Cập nhật thành công (execute ok)',
                'rowCount' => $stmt->rowCount()
            );

        } else {

            return array(
                'success' => false,
                'message' => 'Lệnh UPDATE không thực thi (execute returned false)'
            );

        }

    } catch (PDOException $e) {

        @file_put_contents(
            __DIR__ . '/debug_updateHocSinhNoiDung.log',
            date('c') . " - PDOException: " . $e->getMessage() . PHP_EOL,
            FILE_APPEND
        );

        return array(
            'success' => false,
            'message' => 'Lỗi DB: ' . $e->getMessage()
        );

    } catch (Exception $e) {

        @file_put_contents(
            __DIR__ . '/debug_updateHocSinhNoiDung.log',
            date('c') . " - Exception: " . $e->getMessage() . PHP_EOL,
            FILE_APPEND
        );

        return array(
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        );

    }
}
public function Editquestion($Id_qs, $Id_cs, $Quenstion, $Image) {

    try {

        /*
        lấy PDO
        */
        $pdo = null;


        if(

            isset($this->dbConnect1)

            &&

            $this->dbConnect1 instanceof PDO

        ){

            $pdo =
            $this->dbConnect1;

        }

        elseif(

            isset($this->conn)

            &&

            $this->conn instanceof PDO

        ){

            $pdo =
            $this->conn;

        }

        else{

            return array(

                'success'=>false,

                'message'=>

                'Không có kết nối DB'

            );

        }




        $pdo->setAttribute(

            PDO::ATTR_ERRMODE,

            PDO::ERRMODE_EXCEPTION

        );





        $sql = "

            UPDATE quenstion_practice

            SET

                Id_cs = :Id_cs,

                Quenstion = :Quenstion,

                Image = :Image

            WHERE

                Id_qs = :Id_qs

        ";





        $stmt =

        $pdo->prepare(

            $sql

        );





        $executed =

        $stmt->execute(

            array(

                ':Id_cs'=>$Id_cs,

                ':Quenstion'=>$Quenstion,

                ':Image'=>$Image,

                ':Id_qs'=>$Id_qs

            )

        );






        if(

            $executed

        ){

            return array(

                'success'=>true,

                'message'=>

                'Cập nhật thành công',

                'rowCount'=>

                $stmt->rowCount()

            );

        }

        else{

            return array(

                'success'=>false,

                'message'=>

                'Update thất bại'

            );

        }




    }

    catch(PDOException $e){

        return array(

            'success'=>false,

            'message'=>

            $e->getMessage()

        );

    }


    catch(Exception $e){

        return array(

            'success'=>false,

            'message'=>

            $e->getMessage()

        );

    }

}
public function updateStudent(
    $Id_hocsinh,
    $Id_khoi,
    $Hovaten,
    $Lop,
    $Ngaysinh,
    $Diachi,
    $Dienthoai,
    $Hotencha,
    $Dienthoai1,
    $Hotenme
) {
    try {
        // Kiểm tra học sinh có tồn tại không
        $checkSql = "SELECT Id_hocsinh FROM hocsinh WHERE Id_hocsinh = :Id_hocsinh";
        $checkStmt = $this->dbConnect1->prepare($checkSql);
        $checkStmt->bindValue(":Id_hocsinh", $Id_hocsinh, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            return [
                'success' => false,
                'message' => 'Học sinh không tồn tại'
            ];
        }

        // Câu lệnh cập nhật
        $sql = "UPDATE hocsinh
                SET Id_khoi     = :Id_khoi,
                    Hovaten     = :Hovaten,
                    Lop         = :Lop,
                    Ngaysinh    = :Ngaysinh,
                    Diachi      = :Diachi,
                    Dienthoai   = :Dienthoai,
                    Hotencha    = :Hotencha,
                    Dienthoai1  = :Dienthoai1,
                    Hotenme     = :Hotenme
                WHERE Id_hocsinh = :Id_hocsinh";

        $stmt = $this->dbConnect1->prepare($sql);

        // Gán giá trị
        $stmt->bindValue(":Id_khoi", $Id_khoi, PDO::PARAM_INT);
        $stmt->bindValue(":Hovaten", $Hovaten, PDO::PARAM_STR);
        $stmt->bindValue(":Lop", $Lop, PDO::PARAM_STR);
        $stmt->bindValue(":Ngaysinh", $Ngaysinh, PDO::PARAM_STR);
        $stmt->bindValue(":Diachi", $Diachi, PDO::PARAM_STR);
        $stmt->bindValue(":Dienthoai", $Dienthoai, PDO::PARAM_STR);
        $stmt->bindValue(":Hotencha", $Hotencha, PDO::PARAM_STR);
        $stmt->bindValue(":Dienthoai1", $Dienthoai1, PDO::PARAM_STR);
        $stmt->bindValue(":Hotenme", $Hotenme, PDO::PARAM_STR);
        $stmt->bindValue(":Id_hocsinh", $Id_hocsinh, PDO::PARAM_INT);

        // Thực thi câu lệnh
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Cập nhật học sinh thành công',
                'rowCount' => $stmt->rowCount()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không có thay đổi nào được thực hiện'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()
        ];
    }
}
/**
 * Delete a single question from the database
 * 
 * @param int $Id The ID of the question to delete
 * @return array Result information including success status and message
 */
public function deleteQuestion($Id) {
    try {
        // Prepare the delete SQL statement
        $sql = "DELETE FROM question WHERE Id = :Id";
        
        // Prepare statement
        $stmt = $this->dbConnect1->prepare($sql);
        
        // Bind parameters
        $stmt->bindValue(":Id", $Id, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        
        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Question deleted successfully',
                'rowCount' => $stmt->rowCount()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Question not found or could not be deleted'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}

/**
 * Delete multiple questions from the database
 * 
 * @param array $ids Array of question IDs to delete
 * @return array Result information including success status and message
 */
public function deleteMultipleQuestions($ids) {
    try {
        // Create placeholders for the IN clause
        $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
        
        // Prepare the delete SQL statement
        $sql = "DELETE FROM question WHERE Id IN ($placeholders)";
        
        // Prepare statement
        $stmt = $this->dbConnect1->prepare($sql);
        
        // Execute with the array of IDs
        $stmt->execute($ids);
        
        // Check if any rows were affected
        $deletedCount = $stmt->rowCount();
        if ($deletedCount > 0) {
            return [
                'success' => true,
                'message' => "$deletedCount questions deleted successfully",
                'rowCount' => $deletedCount
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No questions were deleted'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}
/**
 * Delete multiple links from the database
 * 
 * @param array $idlinks Array of link IDs to delete
 * @return array Result information including success status and message
 */
/*public function deleteLinks($idlinks) {
    try {
        // Validate input
        if (empty($idlinks) || !is_array($idlinks)) {
            return [
                'success' => false,
                'message' => 'No links specified for deletion'
            ];
        }
        
        // Create placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($idlinks), '?'));
        
        // Prepare the delete SQL statement
        $sql = "DELETE FROM link WHERE idlink IN ($placeholders)";
        
        // Prepare statement
        $stmt = $this->dbConnect1->prepare($sql);
        
        // Bind each ID as a parameter
        foreach ($idlinks as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        
        // Execute the query
        $stmt->execute();
        
        // Check how many rows were affected
        $affectedRows = $stmt->rowCount();
        
        if ($affectedRows > 0) {
            return [
                'success' => true,
                'message' => "$affectedRows link(s) deleted successfully",
                'rowCount' => $affectedRows
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No links were deleted'
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }
}*/
public function getdata3($userTable, $id = null) {
    // Xây dựng câu lệnh SQL
    if ($id !== null) {
        // Nếu có id, thêm điều kiện WHERE
        $sql = "SELECT * FROM " . $userTable . " WHERE idlink = :id";
    } else {
        // Nếu không có id, chọn tất cả
        $sql = "SELECT * FROM " . $userTable;
    }

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($sql);
    
    // Liên kết giá trị nếu có id
    if ($id !== null) {
        $stmt->bindValue(":id", $id, PDO::PARAM_STR); // Sử dụng PDO::PARAM_STR nếu id là chuỗi
    }

    // Thực thi câu lệnh
    $stmt->execute();

    // Kiểm tra lỗi truy vấn
    if ($stmt->errorCode() !== '00000') {
        die("Query failed: " . implode(", ", $stmt->errorInfo()));
    }

    // Khởi tạo mảng kết quả
    $links = [];

    // Lấy dữ liệu từ kết quả truy vấn
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $links[] = $row; // Thêm từng hàng vào mảng
    }

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $links;
}
public function getData5($userTable, $includeSelf = false) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT dp.* 
            FROM detailproduct dp 
            JOIN user u ON dp.user_id = u.Id_user 
            WHERE u.Money > 0 
            AND u.Date_begin < CURDATE() 
            AND CURDATE() < u.Date_end";

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        $sql .= " AND u.Id_user != '" . $_SESSION['userid'] . "'";
    }

    $result = $this->dbConnect->query($sql);

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Query failed: " . $this->dbConnect->error);
    }

    // Khởi tạo mảng kết quả
    $links = [];

    // Lấy dữ liệu từ kết quả truy vấn
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $links[] = $row; // Thêm từng hàng vào mảng
        }
    }

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $links;
}
public function getData4($userTable, $includeSelf = false) {
    // Bước 1: Truy vấn để lấy danh sách user thỏa mãn điều kiện
	  // Thiết lập mã hóa cho kết nối
    $this->dbConnect->set_charset("utf8");
	$currentDate = date('Y-m-d');
    $sqlUsers = "SELECT id 
             FROM $userTable
             WHERE Amount > 0 
             AND Date_begin < '$currentDate'
             AND '$currentDate' < Date_end";
    //echo $sqlUsers;
    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
 
    $resultUsers = $this->dbConnect->query($sqlUsers);

    // Kiểm tra lỗi truy vấn
    if (!$resultUsers) {
        die("Query failed: " . $this->dbConnect->error);
    }

    // Khởi tạo mảng chứa các sản phẩm
    $products = [];

    // Nếu có người dùng thỏa mãn điều kiện
    if ($resultUsers->num_rows > 0) {
        while ($user = $resultUsers->fetch_assoc()) {
            $userId = $user['id'];
			//echo $userId;

            // Bước 2: Truy vấn để lấy sản phẩm cho từng người dùng
            $sqlProducts = "SELECT * 
                            FROM detailproduct  
                            WHERE Id = '" . $userId . "'";
           // echo $sqlProducts;
            $resultProducts = $this->dbConnect->query($sqlProducts);
            
            // Kiểm tra lỗi truy vấn
            if (!$resultProducts) {
                die("Query failed: " . $this->dbConnect->error);
            }

            // Bước 3: Lấy dữ liệu từ kết quả truy vấn
            if ($resultProducts->num_rows > 0) {
                while ($product = $resultProducts->fetch_assoc()) {
                    $products[] = $product; // Thêm từng sản phẩm vào mảng
                }
            }
        }
    }

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $products;
}
public function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
 public function loginStatus (){
		if(empty($_SESSION["userid"])) {
			header("Location: login.php");
		}
	}	
   	public function login(){		
		$errorMessage = '';
		if(!empty($_POST["login"]) && $_POST["loginId"]!=''&& $_POST["loginPass"]!='') {	
			$loginId = $_POST['loginId'];
			$password = $_POST['loginPass'];
			if(isset($_COOKIE["loginPass"]) && $_COOKIE["loginPass"] == $password) {
				$password = $_COOKIE["loginPass"];
			} else {
				$password = md5($password);
			}	
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$loginId."' AND password='".$password."' AND status = 'active'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValidLogin = mysqli_num_rows($resultSet);	
			if($isValidLogin){
				if(!empty($_POST["remember"]) && $_POST["remember"] != '') {
					setcookie ("loginId", $loginId, time()+ (10 * 365 * 24 * 60 * 60));  
					setcookie ("loginPass",	$password,	time()+ (10 * 365 * 24 * 60 * 60));
				} else {
					$_COOKIE['loginId' ]='';
					$_COOKIE['loginPass'] = '';
				}
				$userDetails = mysqli_fetch_assoc($resultSet);
				$_SESSION["userid"] = $userDetails['id'];
				$_SESSION["email"] = $userDetails['email'];
				$_SESSION["Partner"] = $userDetails['Partner'];
				$_SESSION["type"] = $userDetails['type'];//administrator
				$_SESSION["name"] = $userDetails['first_name']." ".$userDetails['last_name'];
				header("location: index.php"); 		
			} else {		
				$errorMessage = "Invalid login!";		 
			}
		} else if(!empty($_POST["loginId"])){
			$errorMessage = "Enter Both user and password!";	
		}
		return $errorMessage; 		
	}
public function adminLoginStatus (){
		if(empty($_SESSION["adminUserid"])) {
			header("Location: index.php");
		}
	}
	
public function getLinks($userTable) {
    $sql = "SELECT Tenwebsite, Link, View, Id FROM " . $userTable . " WHERE Id_user != '" . $_SESSION['userid'] . "' AND Partner != '1'";
    $result = $this->dbConnect->query($sql);

    if (!$result) {
        die("Query failed: " . $this->conn->error); // Kiểm tra lỗi truy vấn
    }

    $links = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $links[] = $row;
        }
    }

    return $links;
}
public function getwebsite($userTable) {
    $sql = "SELECT Tenwebsite, Link, View, Id FROM " . $userTable . " WHERE Id_user = '" . $_SESSION['userid'] . "'";
    $result = $this->dbConnect->query($sql);

    if (!$result) {
        die("Query failed: " . $this->conn->error); // Kiểm tra lỗi truy vấn
    }

    $links = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $links[] = $row;
        }
    }

    return $links;
}
public function updateWebsiteCount($userId, $websiteId) {
    if ($userId && $websiteId) {
        // Bước 1: Đếm số lượng website trong bảng linkwebsite với Id cụ thể
        $countQuery = "SELECT COUNT(*) as total FROM linkwebsite WHERE Id = ?";
        
        // Chuẩn bị câu lệnh
        $stmtCount = $this->dbConnect1->prepare($countQuery);
        $stmtCount->bind_param("i", $websiteId);

        // Thực thi câu lệnh
        if ($stmtCount->execute()) {
            $result = $stmtCount->get_result();
            $row = $result->fetch_assoc();
            $totalWebsites = $row['total'];
        } else {
            return false; // Đếm không thành công
        }

        // Bước 2: Cập nhật số lượng website vào bảng user
        $updateQuery = "UPDATE user SET number_web = ? WHERE Id = ?";
        
        // Chuẩn bị câu lệnh
        $stmtUpdate = $this->dbConnect1->prepare($updateQuery);
        $stmtUpdate->bind_param("ii", $totalWebsites, $userId);

        // Thực thi câu lệnh
        if ($stmtUpdate->execute()) {
            return true; // Cập nhật thành công
        } else {
            return false; // Cập nhật thất bại
        }
    }
    return false; // Nếu không có userId hoặc websiteId
}
public function updateView($id, $tableName) {
    if ($id) {
		  if (is_null($id) || is_null($tableName))
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
        // Truy vấn SQL để cập nhật lượt view
        $updateQuery = "UPDATE " . $tableName . " SET View = View + 1 WHERE Id = :id";
        
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($updateQuery);
		$stmt->bindValue(":id", $id);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            return true; // Cập nhật thành công
        } else {
            return false; // Cập nhật thất bại
        }
    }
    return false; // Nếu không có ID
}
public function updatePartner($id, $tableName,$Partner) {
    if ($id) {
		  if (is_null($id) || is_null($tableName))
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
        
        // Truy vấn SQL để cập nhật lượt view
        $updateQuery = "UPDATE " . $tableName . " SET Partner= :Partner WHERE Id = :id";
        
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($updateQuery);
		$stmt->bindValue(":id", $id);
		$stmt->bindValue(":Partner", $Partner);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
             $success = "success";
			$message="Good! You are an SSC partner.!";
        } else {
              $success = "false";
			$message="Sorry! You Register SSC partner have not been successed";
        }
    }
     // Trả về thông báo dưới dạng JSON
    echo json_encode(['success' => $success, 'message' => $message]);
    return; // Đảm bảo không có gì khác được in ra
}

	public function adminLogin(){		
		$errorMessage = '';
		if(!empty($_POST["login"]) && $_POST["email"]!=''&& $_POST["password"]!='') {	
			$email = $_POST['email'];
			$password = $_POST['password'];
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$email."' AND password='".md5($password)."' AND status = 'active' AND type = 'administrator'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValidLogin = mysqli_num_rows($resultSet);	
			if($isValidLogin){
				$userDetails = mysqli_fetch_assoc($resultSet);
				$_SESSION["adminUserid"] = $userDetails['id'];
				$_SESSION["admin"] = $userDetails['first_name']." ".$userDetails['last_name'];
				header("location: dashboard.php"); 		
			} else {		
				$errorMessage = "Invalid login!";		 
			}
		} else if(!empty($_POST["login"])){
			$errorMessage = "Enter Both user and password!";	
		}
		return $errorMessage; 		
	}

public function checkEmailExists($email, $id_form) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Xây dựng câu lệnh SQL
    $sql = "SELECT COUNT(*) AS count FROM user WHERE email = :email_id AND Id_form = :id_form";

    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($sql);

        // Liên kết giá trị với tham số
        $stmt->bindValue(":email_id", $email);
        $stmt->bindValue(":id_form", $id_form);
        
        // Thực thi câu lệnh
        $stmt->execute();
        
        // Lấy kết quả
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra số lượng email tồn tại
        if ($result['count'] > 0) {
            return ['msg' => "Email already exists", 'msgType' => "warning"];
        } else {
            return ['msg' => "Email is available", 'msgType' => "success"];
        }
    } catch (Exception $e) {
        // Xử lý lỗi nếu có
        return ['msg' => "Query failed: " . $e->getMessage(), 'msgType' => "error"];
    }
}
	public function register(){		
		$message = '';
		if(!empty($_POST["register"]) && $_POST["email"] !='') {
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE email='".$_POST["email"]."'";
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$isUserExist = mysqli_num_rows($result);
			if($isUserExist) {
				$message = "User already exist with this email address.";
			} else {			
				$authtoken = $this->getAuthtoken($_POST["email"]);
				$insertQuery = "INSERT INTO ".$this->userTable."(first_name, last_name, email, password, authtoken) 
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".md5($_POST["passwd"])."', '".$authtoken."')";
				$userSaved = mysqli_query($this->dbConnect, $insertQuery);
				if($userSaved) {				
					$link = "<a href='http://webdamn.com/demo/user-management-system/verify.php?authtoken=".$authtoken."'>Verify Email</a>";			
					$toEmail = $_POST["email"];
					$subject = "Verify email to complete registration";
					$msg = "Hi there, click on this ".$link." to verify email to complete registration.";
					$msg = wordwrap($msg,70);
					$headers = "From: info@webdamn.com";
					if(mail($toEmail, $subject, $msg, $headers)) {
						$message = "Verification email send to your email address. Please check email and verify to complete registration.";
					}
				} else {
					$message = "User register request failed.";
				}
			}
		}
		return $message;
	}
//registerPartner
public function registerPartner() {
    $message = '';
    $success = false;
    
    if (!empty($_POST["email"])) {
        require_once "phpmailer/class.phpmailer.php";

        // Lấy dữ liệu từ POSTis_null($emailfriend)|| is_null($message)
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);
        $phone_mobile = trim($_POST["phone"]);
        $email = trim($_POST["email"]);
		$emailfriend= trim($_POST["emailfriend"]);
		$message=trim($_POST["message"]);//
		$partner=trim($_POST["partner"]);
        $password = md5(trim($_POST["password"])); // Chú ý: Nên dùng bcrypt thay cho md5
        $id_form = 2;
        if (is_null($id_form) || is_null($first_name) || is_null($last_name) || is_null($phone_mobile) || is_null($email) || is_null($password)|| is_null($emailfriend)|| is_null($message)) {
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
}
        // Gọi hàm checkEmailExists
        $emailCheckResult = $this->checkEmailExists($email, $id_form);

        if ($emailCheckResult['msgType'] === "warning") {
            $message = $emailCheckResult['msg'];
        } else {
            $authtoken = $this->getAuthtoken($email);
            $insertResult = $this->insertUserPartner($id_form, $first_name, $last_name, $phone_mobile, $email, $password,$authtoken,$emailfriend,$message,$partner);

            if (isset($insertResult['rowCount']) && $insertResult['rowCount'] > 0) {
                $lastID = $insertResult['lastInsertId'];
                //$message = "User inserted successfully with ID: " . $lastID . ". Verification email sent.";
                // Gọi hàm sendEmail
				$emailResult =$this->sendEmailVerification($this->Site_url,$last_name, $email, $lastID, $id_form,$authtoken);
				   // Gọi hàm sendEmail
				$emailfriendResult =$this->sendfriendEmail($this->Site_url,$last_name, $emailfriend, $lastID, $id_form,$authtoken,$message);
				// Kiểm tra và hiển thị thông điệp
				if ($emailResult['msgType']=== "success") {
					  $success = "success";
					  $message="An email has been sent for verification. Please check your email to vertify";
				}
				else   { $success =  "Failed";;
					  $message="You enter email again";}
			if ($emailfriendResult['msgType']=== "success") {
					  $success = "success";
					  $message="Website offered gift and message to your friend";
				}
				else   { $success =  "Failed";;
					  $message="You enter email again";}
              
            } else {
                $message = "Failed to insert user.";
                if (isset($insertResult['error'])) {
                    $message .= " Error: " . $insertResult['error'];
                }
            }
        }
    } else {
        $message = "Please fill in all required fields.";
    }

    // Trả về thông báo dưới dạng JSON
    echo json_encode(['success' => $success, 'message' => $message]);
    return; // Đảm bảo không có gì khác được in ra
}

public function register1() {
    $message = '';
    $success = false;
    
    if (!empty($_POST["email"])) {
        require_once "phpmailer/class.phpmailer.php";

        // Lấy dữ liệu từ POSTis_null($emailfriend)|| is_null($message)
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);
        $phone_mobile = trim($_POST["phone"]);
        $email = trim($_POST["email"]);
		$emailfriend= trim($_POST["emailfriend"]);
		$message=trim($_POST["message"]);
        $password = md5(trim($_POST["password"])); // Chú ý: Nên dùng bcrypt thay cho md5
        $id_form = 2;
        if (is_null($id_form) || is_null($first_name) || is_null($last_name) || is_null($phone_mobile) || is_null($email) || is_null($password)|| is_null($emailfriend)|| is_null($message)) {
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
}
        // Gọi hàm checkEmailExists
        $emailCheckResult = $this->checkEmailExists($email, $id_form);

        if ($emailCheckResult['msgType'] === "warning") {
            $message = $emailCheckResult['msg'];
        } else {
            $authtoken = $this->getAuthtoken($email);
            $insertResult = $this->insertUser($id_form, $first_name, $last_name, $phone_mobile, $email, $password,$authtoken,$emailfriend,$message);

            if (isset($insertResult['rowCount']) && $insertResult['rowCount'] > 0) {
                $lastID = $insertResult['lastInsertId'];
                //$message = "User inserted successfully with ID: " . $lastID . ". Verification email sent.";
                // Gọi hàm sendEmail
				$emailResult =$this->sendEmailVerification($this->Site_url,$last_name, $email, $lastID, $id_form,$authtoken);
				   // Gọi hàm sendEmail
				$emailfriendResult =$this->sendfriendEmail($this->Site_url,$last_name, $emailfriend, $lastID, $id_form,$authtoken,$message);
				// Kiểm tra và hiển thị thông điệp
				if ($emailResult['msgType']=== "success") {
					  $success = "success";
					  $message="An email has been sent for verification. Please check your email to vertify";
				}
				else   { $success =  "Failed";;
					  $message="You enter email again";}
			if ($emailfriendResult['msgType']=== "success") {
					  $success = "success";
					  $message="Website offered gift and message to your friend";
				}
				else   { $success =  "Failed";;
					  $message="You enter email again";}
              
            } else {
                $message = "Failed to insert user.";
                if (isset($insertResult['error'])) {
                    $message .= " Error: " . $insertResult['error'];
                }
            }
        }
    } else {
        $message = "Please fill in all required fields.";
    }

    // Trả về thông báo dưới dạng JSON
    echo json_encode(['success' => $success, 'message' => $message]);
    return; // Đảm bảo không có gì khác được in ra
}
	public function getAuthtoken($email) {
		$code = md5(889966);
		$authtoken = $code."".md5($email);
		return $authtoken;
	}	
public function verifyRegister(){
		$verifyStatus = 0;
		if(!empty($_GET["authtoken"]) && $_GET["authtoken"] != '') {			
			$sqlQuery = "SELECT * FROM ".$this->userTable." 
				WHERE authtoken='".$_GET["authtoken"]."'";
			$resultSet = mysqli_query($this->dbConnect, $sqlQuery);
			$isValid = mysqli_num_rows($resultSet);	
			if($isValid){
				$userDetails = mysqli_fetch_assoc($resultSet);
				$authtoken = $this->getAuthtoken($userDetails['email']);
				if($authtoken == $_GET["authtoken"]) {					
					$updateQuery = "UPDATE ".$this->userTable." SET status = 'active'
						WHERE id='".$userDetails['id']."'";
					$isUpdated = mysqli_query($this->dbConnect, $updateQuery);					
					if($isUpdated) {
						$verifyStatus = 1;
					}
				}
			}
		}
		return $verifyStatus;
	}
public function userDetails () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
			WHERE id ='".$_SESSION["userid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$userDetails = mysqli_fetch_assoc($result);
		return $userDetails;
	}
public function editAccount () {
		$message = '';
		$updatePassword = '';
		if(!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] != $_POST["cpasswd"]) {
			$message = "Confirm passwords do not match.";
		} else if(!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] == $_POST["cpasswd"]) {
			$updatePassword = ", password='".md5($_POST["passwd"])."' ";
		}		
		$updateQuery = "UPDATE ".$this->userTable." 
			SET first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."' $updatePassword
			WHERE id ='".$_SESSION["userid"]."'";
		$isUpdated = mysqli_query($this->dbConnect, $updateQuery);	
		if($isUpdated) {
			$_SESSION["name"] = $_POST['firstname']." ".$_POST['lastname'];
			$message = "Account details saved.";
		}
		return $message;
	}
public function resetPassword(){
		$message = '';
		if($_POST['email'] == '') {
			$message = "Please enter username or email to proceed with password reset";			
		} else {
			$sqlQuery = "
				SELECT email 
				FROM ".$this->userTable." 
				WHERE email='".$_POST['email']."'";			
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$numRows = mysqli_num_rows($result);
			if($numRows) {			
				$user = mysqli_fetch_assoc($result);
				$authtoken = $this->getAuthtoken($user['email']);
				$link="<a href='https://www.webdamn.com/demo/user-management-system/reset_password.php?authtoken=".$authtoken."'>Reset Password</a>";				
				$toEmail = $user['email'];
				$subject = "Reset your password on examplesite.com";
				$msg = "Hi there, click on this ".$link." to reset your password.";
				$msg = wordwrap($msg,70);
				$headers = "From: info@webdamn.com";
				if(mail($toEmail, $subject, $msg, $headers)) {
					$message =  "Password reset link send. Please check your mailbox to reset password.";
				}				
			} else {
				$message = "No account exist with entered email address.";
			}
		}
		return $message;
	}
public function savePassword(){
		$message = '';
		if($_POST['password'] != $_POST['cpassword']) {
			$message = "Password does not match the confirm password.";
		} else if($_POST['authtoken']) {
			$sqlQuery = "
				SELECT email, authtoken 
				FROM ".$this->userTable." 
				WHERE authtoken='".$_POST['authtoken']."'";			
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$numRows = mysqli_num_rows($result);
			if($numRows) {				
				$userDetails = mysqli_fetch_assoc($result);
				$authtoken = $this->getAuthtoken($userDetails['email']);
				if($authtoken == $_POST['authtoken']) {
					$sqlUpdate = "
						UPDATE ".$this->userTable." 
						SET password='".md5($_POST['password'])."'
						WHERE email='".$userDetails['email']."' AND authtoken='".$authtoken."'";	
					$isUpdated = mysqli_query($this->dbConnect, $sqlUpdate);	
					if($isUpdated) {
						$message = "Password saved successfully. Please <a href='login.php'>Login</a> to access account.";
					}
				} else {
					$message = "Invalid password change request.";
				}
			} else {
				$message = "Invalid password change request.";
			}	
		}
		return $message;
	}
	public function getUserList1() {
    // Xây dựng câu truy vấn để lấy danh sách người dùng
    $sqlQuery = "SELECT Id, Id_user, Tenwebsite, Link, View, Number_web FROM " . $this->userTable . " WHERE Id_user != '" . $_SESSION['userid'] . "' ";

    // Thêm điều kiện sắp xếp
    if (!empty($_POST["order"])) {
        $sqlQuery .= 'ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST['order']['0']['dir'] . ' ';
    } else {
        $sqlQuery .= 'ORDER BY Id DESC ';
    }

    // Thêm điều kiện giới hạn
    if ($_POST["length"] != -1) {
        $sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }

    // Thực hiện truy vấn
    $result = mysqli_query($this->dbConnect, $sqlQuery);

    // Truy vấn để đếm số lượng bản ghi
    $sqlQuery1 = "SELECT Id FROM " . $this->userTable . " WHERE Id_user != '" . $_SESSION['userid'] . "' ";
    $result1 = mysqli_query($this->dbConnect, $sqlQuery1);
    $numRows = mysqli_num_rows($result1);

    $userData = array();	
    while ($users = mysqli_fetch_assoc($result)) {
        $userRows = array();
        $userRows[] = $users['Id'];
        $userRows[] = $users['Id_user'];
        $userRows[] = $users['Tenwebsite'];
        $userRows[] = $users['Link'];
        $userRows[] = $users['View'];
        $userRows[] = $users['Number_web'];
        $userRows[] = '<button type="button" name="update" id="' . $users["Id"] . '" class="btn btn-warning btn-xs update">Update</button>';
        $userRows[] = '<button type="button" name="delete" id="' . $users["Id"] . '" class="btn btn-danger btn-xs delete">Delete</button>';
        $userData[] = $userRows;
    }

    // Tạo mảng đầu ra
    $output = array(
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $numRows,
        "recordsFiltered" => $numRows,
        "data" => $userData
    );

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode($output);
}
	public function getUserList(){		
		$sqlQuery = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['adminUserid']."' ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		
		$sqlQuery1 = "SELECT * FROM ".$this->userTable." WHERE id !='".$_SESSION['adminUserid']."' ";
		$result1 = mysqli_query($this->dbConnect, $sqlQuery1);
		$numRows = mysqli_num_rows($result1);
		
		$userData = array();	
		while( $users = mysqli_fetch_assoc($result) ) {		
			$userRows = array();
			$status = '';
			if($users['status'] == 'active')	{
				$status = '<span class="label label-success">Active</span>';
			} else if($users['status'] == 'pending') {
				$status = '<span class="label label-warning">Inactive</span>';
			} else if($users['status'] == 'deleted') {
				$status = '<span class="label label-danger">Deleted</span>';
			}
			$userRows[] = $users['id'];
			$userRows[] = ucfirst($users['first_name']." ".$users['last_name']);
			$userRows[] = $users['gender'];			
			$userRows[] = $users['email'];	
			$userRows[] = $users['mobile'];	
			$userRows[] = $users['type'];
			$userRows[] = $status;						
			$userRows[] = '<button type="button" name="update" id="'.$users["id"].'" class="btn btn-warning btn-xs update">Update</button>';
			$userRows[] = '<button type="button" name="delete" id="'.$users["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$userData[] = $userRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$userData
		);
		echo json_encode($output);
	}
public function deleteUser(){
		if($_POST["userid"]) {
			$sqlUpdate = "
				UPDATE ".$this->userTable." SET status = 'deleted'
				WHERE id = '".$_POST["userid"]."'";		
			mysqli_query($this->dbConnect, $sqlUpdate);		
		}
	}
	public function getUser(){
		$sqlQuery = "
			SELECT * FROM ".$this->userTable." 
			WHERE id = '".$_POST["userid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
function activateUser($id, $id_form, $authtoken) {
    // Kiểm tra kết nối cơ sở dữ liệu
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Câu lệnh SQL để cập nhật trạng thái người dùng
    $sql = "UPDATE `user` SET `status` = 'active' WHERE `id` = :id AND `Id_form` = :id_form AND `authtoken` = :authtoken";

    try {
        // Chuẩn bị câu lệnh SQL
        $stmt = $this->dbConnect1->prepare($sql);
        // Gán giá trị cho các tham số
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":id_form", $id_form);
        $stmt->bindValue(":authtoken", $authtoken);
        
        // Thực hiện câu lệnh
        $stmt->execute();

        // Kiểm tra xem có bản ghi nào bị ảnh hưởng không
        if ($stmt->rowCount() > 0) {
            // Lấy thông tin người dùng (giả định bạn đã có hàm để lấy thông tin)
            $userInfo = $this->getUserInfo($id, $id_form); // Hàm này cần được định nghĩa để lấy thông tin người dùng
            
            if ($userInfo) {
                // Lấy thông tin người dùng
                $first_name = $userInfo["first_name"];
                $last_name = $userInfo["last_name"];
                $department = $userInfo["department"];
                $phone_mobile = $userInfo["phone_mobile"];
                $email = $userInfo["email"];
                $birthdate = $userInfo["birthdate"];
                $description = $userInfo["description"];

                // Gửi email thông báo
                $emailchinh = "tinhocsucsong@gmail.com";
                require_once 'formdangkycrm1.php';
                require_once "phpmailer/class.phpmailer.php";

                // Gửi email thông báo (giả định có hàm sendEmail)
                $this->sendEmail($email, "Account Activated", "Your account has been activated.");

                // Trả về thông điệp thành công
                return [
                    'msg' => "Your account has been activated.",
                    'msgType' => "success"
                ];
            } else {
                return ['msg' => "User information could not be retrieved.", 'msgType' => "error"];
            }
        } else {
            return ['msg' => "No user found with the provided details.", 'msgType' => "error"];
        }
    } catch (Exception $e) {
        return [
            'msg' => "Error: " . $e->getMessage(),
            'msgType' => "error"
        ];
    }
}

// Giả định có hàm để lấy thông tin người dùng
private function getUserInfo($id, $id_form) {
    // Câu lệnh SQL để lấy thông tin người dùng
    $sql = "SELECT * FROM `user` WHERE `id` = :id AND `id_form` = :id_form";
    
    $stmt = $this->dbConnect1->prepare($sql);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":id_form", $id_form);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC); // Hoặc fetchAll tùy vào trường hợp
}
public function updateUser() {
		if($_POST['userid']) {	
			$updateQuery = "UPDATE ".$this->userTable." 
			SET first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."', status = '".$_POST["status"]."', type = '".$_POST['user_type']."'
			WHERE id ='".$_POST["userid"]."'";
			$isUpdated = mysqli_query($this->dbConnect, $updateQuery);		
		}	
	}
public function updateLink1($id, $tableName, $tenWebsite, $link, $view) {
    if ($id) {
        // Truyền các tham số đã được xác thực vào câu lệnh SQL
        $updateQuery = "UPDATE " . $tableName . " 
        SET Tenwebsite = '" . mysqli_real_escape_string($this->dbConnect, $tenWebsite) . "',
            Link = '" . mysqli_real_escape_string($this->dbConnect, $link) . "',
            View = " . intval($view) . " 
        WHERE Id = " . intval($id);

        $isUpdated = mysqli_query($this->dbConnect, $updateQuery);

        if ($isUpdated) {
            return true; // Cập nhật thành công
        } else {
            return false; // Cập nhật thất bại
        }
    }
    return false; // Nếu không có ID
}
	public function saveAdminPassword(){
		$message = '';
		if($_POST['password'] && $_POST['password'] != $_POST['cpassword']) {
			$message = "Password does not match the confirm password.";
		} else {			
			$sqlUpdate = "
				UPDATE ".$this->userTable." 
				SET password='".md5($_POST['password'])."'
				WHERE id='".$_SESSION['adminUserid']."' AND type='administrator'";	
			$isUpdated = mysqli_query($this->dbConnect, $sqlUpdate);	
			if($isUpdated) {
				$message = "Password saved successfully.";
			}				
		}
		return $message;
	}
	public function adminDetails () {
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
			WHERE id ='".$_SESSION["adminUserid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$userDetails = mysqli_fetch_assoc($result);
		return $userDetails;
	}

	public function addUser () {
		if($_POST["email"]) {
			$authtoken = $this->getAuthtoken($_POST['email']);
			$insertQuery = "INSERT INTO ".$this->userTable."(first_name, last_name, email, gender, password, mobile, designation, type, status, authtoken) 
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".$_POST["gender"]."', '".md5($_POST["password"])."', '".$_POST["mobile"]."', '".$_POST["designation"]."', '".$_POST['user_type']."', 'active', '".$authtoken."')";
			$userSaved = mysqli_query($this->dbConnect, $insertQuery);
		}
	}
public function totalUsers ($status) {
		$query = '';
		if($status) {
			$query = " AND status = '".$status."'";
		}
		$sqlQuery = "SELECT * FROM ".$this->userTable." 
		WHERE id !='".$_SESSION["adminUserid"]."' $query";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
  public function addWebsite1($userId, $website, $Tenwebsite) {
        if (!$userId) {
            return json_encode(['success' => false, 'message' => 'User ID không hợp lệ.']);
        }

        // Bước 1: Kiểm tra số lượng website hiện tại của người dùng
        $countQuery = "SELECT COUNT(*) as total FROM linkwebsite WHERE Id_user = ?";
        
        // Chuẩn bị câu lệnh
        $stmtCount = $this->dbConnect1->prepare($countQuery);
        $stmtCount->bind_param("i", $userId);

        // Thực thi câu lệnh
        if (!$stmtCount->execute()) {
            return json_encode(['success' => false, 'message' => 'Đếm không thành công.']);
        }
        
        $result = $stmtCount->get_result();
        $row = $result->fetch_assoc();
        $currentCount = $row['total'];

        // Bước 2: Lấy Limit_website từ bảng user
        /*$limitQuery = "SELECT Limit_website FROM user WHERE id = ?";
        $stmtLimit = $this->dbConnect1->prepare($limitQuery);
        $stmtLimit->bind_param("i", $userId);
          $debugSql = str_replace(
            [":id"],
            [$userId],$limitQuery);
        if (!$stmtLimit->execute()) {
            return json_encode(['success' => false, 'message' => 'Lấy limit không thành công.']);
        }

        $result = $stmtLimit->get_result();
        $row = $result->fetch_assoc();
        $limitWebsite = $row['Limit_website'];

        // Bước 3: Kiểm tra xem có thể thêm website không
        if ($currentCount < $limitWebsite) {
            // Bước 4: Thêm website vào bảng linkwebsite
            $insertQuery = "INSERT INTO linkwebsite (Id_user, Link, Tenwebsite) VALUES (?, ?, ?)";
            $stmtInsert = $this->dbConnect1->prepare($insertQuery);
            $stmtInsert->bind_param("iss", $userId, $website, $Tenwebsite);
*/
            // Thực thi câu lệnh
            if ($stmtInsert->execute()) {
                  // Trả về số lượng hàng bị ảnh hưởng và ID của bản ghi
        return [
            'message'=> 'Insert Succcess',
			 'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi
        ];
            } else {
				 return [
         'message'=> 'Insert Succcess',
            'error' => $e->getMessage(), // Thêm thông tin lỗi nếu cần
			 'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi lỗi
        ];
                //return json_encode(['success' => false, 'message' => 'Thêm không thành công.']);
            }
        /*} else {
					 return [
         'message'=> 'Da dat toi gioi han so luong',
            'error' => $e->getMessage(), // Thêm thông tin lỗi nếu cần
			 'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi lỗi
        ];
         
            //return json_encode(['success' => false, 'message' => 'Đã đạt giới hạn số lượng website.']);
        }*/
    }
public function CountWeb($userId, $table) {
    $debugSql = ''; // Khởi tạo biến debugSql

    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Bước 1: Kiểm tra số lượng website hiện tại của người dùng
    $countQuery = "SELECT COUNT(*) as total FROM " . $table . " WHERE Id_user = :Id_user";

    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($countQuery);

        // Liên kết giá trị với tham số
        $stmt->bindValue(":Id_user", $userId);
        
        // Tạo câu lệnh SQL cho debug
        $debugSql = str_replace(":Id_user", $userId, $countQuery);
        
        // Thực thi câu lệnh
        if ($stmt->execute()) {
            // Lấy kết quả
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $success = "success";
            $total = $result['total']; // Số lượng website hiện tại
			$_SESSION['total']=$total;
        } else {
            $success = "false";		
            $total = 0;
			$_SESSION['total']=$total;	
        }
    } catch (Exception $e) {
        // Xử lý lỗi nếu có
        $success = "false";
        $error = $e->getMessage();
        $total = 0;
    }
    
    // Trả về kết quả dưới dạng JSON
   // echo json_encode(['success' => $success, 'total' => $total, 'debugSql' => $debugSql]);
    //echo json_encode(['success' => $success,'total' => $total]);
    return;
}
public function getdata2($userTable) {
    $sql = "SELECT * FROM " . $userTable . " WHERE id = '" . $_SESSION['userid'] . "'";
    $result = $this->dbConnect->query($sql);
    $debugSql = str_replace(  [":Id_user"],
        [$_SESSION['userid']], $sql);
   // Kiểm tra xem $links có dữ liệu không
if (!empty($links)) {
        die("Query failed: " . $this->conn->error); // Kiểm tra lỗi truy vấn
		$success="false";	
		 $error =$e->getMessage();
		 $debugSql = $debugSql;
		 $total=0;
		  echo json_encode(['success' => $success,'total' => $total]);
    }

    $links = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $links[] = $row;
        }
    }

    return $links;
}
   
public function addWebsite($userId, $website, $Tenwebsite,$Partner) {
    $message = '';
    $success = false;
    $debugSql = ''; // Khởi tạo biến debugSql

    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    if (is_null($userId) || is_null($website) || is_null($Tenwebsite)) {
        return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
    }

    $user = new User();
    $countrow = $user->CountWeb($userId, "linkwebsite");
    $currentCount=$_SESSION['total'];
	//$currentCount = $countrow['total'];
   /*if ($countrow['success'] === "success") {
        $currentCount = $countrow['total']; // Sửa lại đây
		$success = $currentCount;
		/*$success = "success";
		$message =$countrow['message'];
		$debugSql=$countrow['debugSql'];*/
  /*} else  {
		$currentCount = 0;
		//$success = "false";
		//$success = "success";
		//$message =$countrow['message'];
		//$debugSql=$countrow['debugSql'];
		}*/
     $countlimit=[];
    $links = $user->getData2("user");
	$limitWebsite;
   if (!empty($links)) {
	   
       // Giả sử Limit_website là một trường trong mảng trả về
    // Duyệt qua từng phần tử trong mảng
    foreach ($links as $link) {
        // Kiểm tra xem trường Limit_website có tồn tại không
        if (isset($link['Limit_website'])) {
            $limitWebsite = $link['Limit_website'];
            $success = "success";
            $message = "Limit_website found";
			//$debugSql = $links[debugSql];
        } else {
           $success = "false";
            $message = "Limit_website not found";
			$debugSql = $links[debugSql];
        }
    }
		
    } else {
        // Xử lý trường hợp không có dữ liệu
        return ['success' => "false", 'message' => "No data found."];
    }
	$View=0;
    //$currentCount=0;
    if ($currentCount < $limitWebsite) {
        // SQL query to insert a new user
        $sql = "INSERT INTO `linkwebsite` (`Id_user`, `Link`, `Tenwebsite`,`Partner`,`View`) VALUES (:userId, :Link, :Tenwebsite,:Partner,:View)";
        try {
            $stmt = $this->dbConnect1->prepare($sql);
            $stmt->bindValue(":userId", $userId);
            $stmt->bindValue(":Link", $website);
            $stmt->bindValue(":Tenwebsite", $Tenwebsite);//
			$stmt->bindValue(":Partner", $Partner);//$View=0;
			$stmt->bindValue(":View", $View);

            // In ra câu lệnh SQL với các tham số đã được bind
            $debugSql = str_replace(
                [":userId", ":Link", ":Tenwebsite",":Partner"],
                [$userId, $website, $Tenwebsite,$Partner],
                $sql
            );

            // Thực thi câu lệnh
            if ($stmt->execute()) {
                // Đếm số bản ghi bị ảnh hưởng
                $affectedRows = $stmt->rowCount(); // Sửa lại đây
                $success = "success";
                $message = "Website added";
				$debugSql = $debugSql;
                /*return [
                    'success' => $success,
                    'message' => $message,
                    'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi
                ];*/
            } else {
                $success = "false";
                $message = "Error: " . $stmt->errorInfo()[2]; // Lấy thông báo lỗi
				$debugSql = $debugSql;
               /* return [
                    'success' => $success,
                    'message' => $message,
                    'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi
                ];*/
            }
        } catch (Exception $e) {
            // Nếu có lỗi, trả về 0 cho rowCount và thông tin lỗi
			$success = "false";
            $message = "Error: " . $e->getMessage(); // Lấy thông báo lỗi
			$debugSql = $debugSql;
            /*return [
                'rowCount' => 0,
                'error' => $e->getMessage(), // Thêm thông tin lỗi nếu cần
                'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi lỗi
            ];*/
        }
    } else {
        $success = $limitWebsite;
        $message = "The number of websites is limited. Upgrade to Premium now to add more websites.";
        /*return [
            'success' => $success,
            'message' => $message
        ];*/
   }

    // Trả về thông báo dưới dạng JSON
	echo json_encode(['success' => $success,'Number your Web'=>$currentCount,'LimitWebsite' =>$limitWebsite, 'message' => $message]);
    //echo json_encode(['success' => $success,'Number your Web'=>$currentCount,'LimitWebsite' =>$limitWebsite, 'message' => $message]);
    return;
}//insertcategory
public function insertcategory($category) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // SQL query to insert a new record
    $sql = "INSERT INTO `danhmucsanpham` (`Tendm`) VALUES (:Tendm)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
        
        $stmt->bindValue(":Tendm", $category);
        $debugSql = str_replace(
                [":Tendm"],
                [$category],
                $sql
            );

        // Execute the statement
        $stmt->execute();
        
        // Trả về kết quả
            $message  ="Insert successfully";
            $rowCount =$stmt->rowCount();
			$debugSql = $debugSql;
			  //return json_encode(['message' => "Insert successfully", 'rowCount' => $stmt->rowCount(), 'debugSql' => $debugSql]);
       
    } catch (Exception $e) {
        // Nếu có lỗi, trả về thông tin lỗi
        $message  ="Insert successfully";
            $rowCount =$stmt->rowCount();
			$debugSql = $debugSql;
			 //return json_encode(['message' => "Insert successfully", 'rowCount' => $stmt->rowCount(), 'debugSql' => $debugSql]);
    }
	  // Trả về thông báo dưới dạng JSON
	echo json_encode(['message' => $message,'rowCount' => $rowCount,'debugSql' => $debugSql]);
    //echo json_encode(['success' => $success,'Number your Web'=>$currentCount,'LimitWebsite' =>$limitWebsite, 'message' => $message]);
    return;
}
public function insertlink($id_dm,$idweb,$url,$tenfile) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // SQL query to insert a new record
    $sql = "INSERT INTO `link` (`idweb`, `id_dm`, `url`, `tenfile`) VALUES (:idweb, :id_dm, :url, :tenfile)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
        
        $stmt->bindValue(":idweb", $idweb);
        $stmt->bindValue(":id_dm", $id_dm);        
        $stmt->bindValue(":url", $url);      
        $stmt->bindValue(":tenfile", $tenfile);  // Chèn tên hình ảnh đã đổi   
        $debugSql = str_replace(
                [":idweb", ":id_dm", ":url",":tenfile"],
                [$idweb, $id_dm, $url,$tenfile],
                $sql
            );

        // Execute the statement
        $stmt->execute();
        
        // Trả về kết quả
            $message  ="Insert successfully";
            $rowCount =$stmt->rowCount();
			$debugSql = $debugSql;
			  //return json_encode(['message' => "Insert successfully", 'rowCount' => $stmt->rowCount(), 'debugSql' => $debugSql]);
       
    } catch (Exception $e) {
        // Nếu có lỗi, trả về thông tin lỗi
        $message  ="Insert successfully";
            $rowCount =$stmt->rowCount();
			$debugSql = $debugSql;
			 //return json_encode(['message' => "Insert successfully", 'rowCount' => $stmt->rowCount(), 'debugSql' => $debugSql]);
    }
	  // Trả về thông báo dưới dạng JSON
	echo json_encode(['message' => $message,'rowCount' => $rowCount,'debugSql' => $debugSql]);
    //echo json_encode(['success' => $success,'Number your Web'=>$currentCount,'LimitWebsite' =>$limitWebsite, 'message' => $message]);
    return;
}
function insertUserPartner($id_form,$first_name, $last_name, $phone_mobile, $email,$password,$authtoken,$emailfriend,$message,$Partner) {
		 if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
	if (is_null($id_form) || is_null($first_name) || is_null($last_name) || is_null($phone_mobile) || is_null($email) || is_null($password)|| is_null($emailfriend)|| is_null($message)) {
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
}
    $authtoken = $this->getAuthtoken($email);
		$Limit_website=2;	
    // SQL query to insert a new user
    $sql = "INSERT INTO `user` (`Id_form`,`first_name`,`last_name`, `phone_mobile`, `email`,`authtoken`,`password`,`emailfriend`,`message`,`Limit_website`,`Partner`) VALUES (:id_form,:first_name, :last_name, :phone_mobile, :email,:authtoken,:password,:emailfriend,:message,:Limit_website,:Partner)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
		//$stmtLimit->bind_param("i", $userId);
        $stmt->bindValue(":id_form", $id_form);
        $stmt->bindValue(":first_name", $first_name);
        $stmt->bindValue(":last_name", $last_name);
        $stmt->bindValue(":phone_mobile", $phone_mobile);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":authtoken", $authtoken);
        $stmt->bindValue(":password", $password);
		$stmt->bindValue(":emailfriend", $emailfriend);
		$stmt->bindValue(":message", $message);
		$stmt->bindValue(":Limit_website", $Limit_website);//
		$stmt->bindValue(":Partner", $Partner);
        
		 // In ra câu lệnh SQL với các tham số đã được bind
        $debugSql = str_replace(
            [":id_form", ":first_name", ":last_name", ":phone_mobile", ":email", ":authtoken", ":password",":emailfriend",":message",":Limit_website",":Partner"],
            [$id_form, $first_name, $last_name, $phone_mobile, $email, $authtoken, $password,$emailfriend,$message,$Limit_website,$Partner],
            $sql
        );

        // In ra câu lệnh SQL
        //echo "SQL: " . $debugSql; // Tạm thời in ra câu lệnh SQL

		
        // Execute the statement
        $stmt->execute();
        
        // Lấy ID của bản ghi vừa chèn
        //$lastId = $this->dbConnect->lastInsertId();
        
        // Trả về số lượng hàng bị ảnh hưởng và ID của bản ghi
        return [
            'rowCount' => $stmt->rowCount(),
             'lastInsertId' => $this->dbConnect1->lastInsertId(),
			 'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi
        ];
    } catch (Exception $e) {
        // Nếu có lỗi, trả về 0 cho rowCount và null cho lastInsertId
        return [
            'rowCount' => 0,
            'lastInsertId' => null,
            'error' => $e->getMessage(), // Thêm thông tin lỗi nếu cần
			 'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi lỗi
        ];
    }
}
public function getLink1($userTable, $id) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM " . $userTable . " WHERE idlink = :id";

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($sql);
    
    // Liên kết giá trị
    $stmt->bindValue(":id", $id, PDO::PARAM_STR); // Sử dụng PDO::PARAM_STR nếu id là chuỗi
    $stmt->execute();

    // Kiểm tra lỗi truy vấn
    if ($stmt->errorCode() !== '00000') {
        die("Query failed: " . implode(", ", $stmt->errorInfo()));
    }

    // Khởi tạo mảng kết quả
    $links = [];

    // Lấy dữ liệu từ kết quả truy vấn
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $links[] = $row; // Thêm từng hàng vào mảng
    }

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $links;
}

public function getCode($Id, $userTable, $includeSelf = false) {
    // Khởi tạo câu lệnh SQL
    $sql = "SELECT * FROM " . $userTable;

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        $sql .= " WHERE Id = :Id"; // Thêm dấu "=" vào điều kiện
    }

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($sql);
    
    // Nếu có điều kiện, liên kết giá trị
    if (!$includeSelf) {
        $stmt->bindValue(":Id", $Id, PDO::PARAM_INT); // Sử dụng PDO::PARAM_INT nếu Id là kiểu integer
    }

    // Thực thi câu lệnh
    $stmt->execute();

    // Kiểm tra lỗi truy vấn
    if ($stmt->errorCode() !== '00000') {
        die("Query failed: " . implode(", ", $stmt->errorInfo()));
    }

    // Khởi tạo mảng kết quả
    $links = [];
    
    // Lấy dữ liệu từ kết quả truy vấn
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $links[] = $row; // Thêm từng hàng vào mảng
    }

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $links;
}
public function updateCode($Id, $tableName, $ma) {
    // Kiểm tra điều kiện đầu vào
    if (is_null($Id) || is_null($tableName) || is_null($ma)) {
        return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
    }

    // Truy vấn SQL để cập nhật
    $updateQuery = "UPDATE " . $tableName . " SET Code = :Code WHERE Id = :id";

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($updateQuery);
    $stmt->bindValue(":Code", $ma);
    $stmt->bindValue(":id", $Id); // Bind giá trị ID

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        return true; // Cập nhật thành công
    } else {
        return false; // Cập nhật thất bại
    }
}

public function checkId($Id) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Xây dựng câu lệnh SQL
    $sql = "SELECT COUNT(*) AS count FROM view WHERE Id = :Id";

    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($sql);
        $stmt->bindValue(":Id", $Id);
        
        // Thực thi câu lệnh
        $stmt->execute();
        
        // Lấy kết quả
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra số lượng ID tồn tại
        return $result['count'] > 0
            ? ['msg' => "Id already exists", 'msgType' => "warning"]
            : ['msg' => "Id is available", 'msgType' => "success"];
    } catch (Exception $e) {
        // Xử lý lỗi nếu có
        return ['msg' => "Query failed: " . $e->getMessage(), 'msgType' => "error"];
    }
}

public function insertCode($Id, $ma) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    if (is_null($Id) || is_null($ma)) {
        return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
    }

    // Gọi hàm checkId
    $checkId = $this->checkId($Id);
    if ($checkId['msgType'] === "warning") {
        $tableName = 'view'; // Tên bảng bạn muốn cập nhật
        // Gọi hàm updateCode
        $isUpdated = $this->updateCode($Id, $tableName, $ma);
        return ['msg' => "Update successfully", 'msgType' => "success"];
    } else {
        // SQL query to insert a new record
        $sql = "INSERT INTO `view` (`Id`, `Code`) VALUES (:Id, :Code)"; 
        try {
            $stmt = $this->dbConnect1->prepare($sql);
            $stmt->bindValue(":Id", $Id);
            $stmt->bindValue(":Code", $ma);        

            // In ra câu lệnh SQL với các tham số đã được bind
            $debugSql = str_replace(
                [":Id", ":Code"],
                [$Id, $ma],
                $sql
            );

            // Execute the statement
            $stmt->execute();
            
            // Trả về kết quả
            return [
                'message'  => "Insert successfully",
                'rowCount' => $stmt->rowCount(),
                'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi
            ];
        } catch (Exception $e) {
            // Nếu có lỗi, trả về thông tin lỗi
            return [
                'message'  => "Insert failed",
                'rowCount' => 0,
                'error' => $e->getMessage(),
                'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi lỗi
            ];
        }
    }
}

	function insertUser($id_form,$first_name, $last_name, $phone_mobile, $email,$password,$authtoken,$emailfriend,$message) {
		 if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
	if (is_null($id_form) || is_null($first_name) || is_null($last_name) || is_null($phone_mobile) || is_null($email) || is_null($password)|| is_null($emailfriend)|| is_null($message)) {
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
}
    $authtoken = $this->getAuthtoken($email);
		$Limit_website=2;	
    // SQL query to insert a new user
    $sql = "INSERT INTO `user` (`Id_form`,`first_name`,`last_name`, `phone_mobile`, `email`,`authtoken`,`password`,`emailfriend`,`message`,`Limit_website`) VALUES (:id_form,:first_name, :last_name, :phone_mobile, :email,:authtoken,:password,:emailfriend,:message,:Limit_website)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
		//$stmtLimit->bind_param("i", $userId);
        $stmt->bindValue(":id_form", $id_form);
        $stmt->bindValue(":first_name", $first_name);
        $stmt->bindValue(":last_name", $last_name);
        $stmt->bindValue(":phone_mobile", $phone_mobile);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":authtoken", $authtoken);
        $stmt->bindValue(":password", $password);
		$stmt->bindValue(":emailfriend", $emailfriend);
		$stmt->bindValue(":message", $message);
		$stmt->bindValue(":Limit_website", $Limit_website);
        
		 // In ra câu lệnh SQL với các tham số đã được bind
        $debugSql = str_replace(
            [":id_form", ":first_name", ":last_name", ":phone_mobile", ":email", ":authtoken", ":password",":emailfriend",":message",":Limit_website"],
            [$id_form, $first_name, $last_name, $phone_mobile, $email, $authtoken, $password,$emailfriend,$message,$Limit_website],
            $sql
        );

        // In ra câu lệnh SQL
        //echo "SQL: " . $debugSql; // Tạm thời in ra câu lệnh SQL

		
        // Execute the statement
        $stmt->execute();
        
        // Lấy ID của bản ghi vừa chèn
        //$lastId = $this->dbConnect->lastInsertId();
        
        // Trả về số lượng hàng bị ảnh hưởng và ID của bản ghi
        return [
            'rowCount' => $stmt->rowCount(),
             'lastInsertId' => $this->dbConnect1->lastInsertId(),
			 'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi
        ];
    } catch (Exception $e) {
        // Nếu có lỗi, trả về 0 cho rowCount và null cho lastInsertId
        return [
            'rowCount' => 0,
            'lastInsertId' => null,
            'error' => $e->getMessage(), // Thêm thông tin lỗi nếu cần
			 'debugSql' => $debugSql // Thêm câu lệnh SQL vào phản hồi lỗi
        ];
    }
}
	
function sendEmailVerification($Site_url,$last_name, $email, $lastID, $id_form,$authtoken) {
    $message = '<html><head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>Activation email for the SSC website traffic view increase software.</title>
                </head>
                <body>';
    $message .= '<h1>Hello ' . htmlspecialchars($last_name) . '! You have registered for the software</h1>';
   $message .= '<p><a href="' . $Site_url. 'activate.php?id=' . base64_encode($lastID) . '&id_danh_muc=' . base64_encode($id_form) . '&authtoken=' . $authtoken . '">You click this link to Activation software you registered </a></p>'; $message .= "</body></html>";

    // Tạo đối tượng PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl"; 
        $mail->Host = "smtp.mailgun.org"; 
        $mail->Port = 465; 
        $mail->Username = 'ssc@bizcrmweb.com';
        $mail->Password = '5f17a7d2a8bb33e4556be469e9fb0c79-24e2ac64-3787027b';
        
        $mail->SetFrom('ssc@bizcrmweb.com', 'daykembatnha.top');
        $mail->AddAddress($email);
        $mail->Subject = "Activation email for the SSC website traffic view increase software.";
        $mail->MsgHTML($message);

        // Gửi email
        $mail->send();
        return ["msg" => "An email has been sent for verification.", "msgType" => "success"];
    } catch (Exception $ex) {
        return ["msg" => $ex->getMessage(), "msgType" => "warning"];
    }
}
//Gui friend
function sendfriendEmail($Site_url,$last_name, $email, $lastID, $id_form,$authtoken,$message1) {
    $message = '<html><head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>Offer you software to increase traffic views for the SSC website.</title>
                </head>
                <body>';
    $message .= '<h1>Your closer ' . htmlspecialchars($last_name) . 'offer You software to increase traffic views for the SSC website. You Click</h1><h2>'.$message1.'</h2>';
   $message .= '<p><a href="' . $Site_url.'">You click this link to receive your gift from your friend</a></p>'; $message .= "</body></html>";

    // Tạo đối tượng PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl"; 
        $mail->Host = "smtp.mailgun.org"; 
        $mail->Port = 465; 
        $mail->Username = 'ssc@bizcrmweb.com';
        $mail->Password = '5f17a7d2a8bb33e4556be469e9fb0c79-24e2ac64-3787027b';
        
        $mail->SetFrom('ssc@bizcrmweb.com', 'daykembatnha.top');
        $mail->AddAddress($email);
        $mail->Subject = $last_name." Offer you software to increase traffic views for the SSC website";
        $mail->MsgHTML($message);

        // Gửi email
        $mail->send();
        return ["msg" => "An email has been sent for verification.", "msgType" => "success"];
    } catch (Exception $ex) {
        return ["msg" => $ex->getMessage(), "msgType" => "warning"];
    }
}

// Sử dụng hàm
}
?>