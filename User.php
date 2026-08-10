<?php
session_start();
require('./include/config.php');
class User extends Dbconfig {
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

/*public function getvideo($userTable,$Id_noidung,$includeSelf = false) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
	   // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM ".$userTable." WHERE 	Id_noidung = :Id_noidung";

	// Sửa dấu "=" thành ":phone"
    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($sql);

        // Liên kết giá trị với tham số
        $stmt->bindValue(":Id_noidung", $Id_noidung);
        // Thực thi câu lệnh
        $stmt->execute();
      
        // Lấy dữ liệu từ kết quả truy vấn
    $Content = [];

    // Sử dụng fetchAll() nếu bạn muốn lấy tất cả kết quả
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $Content[] = [
			'Linkvideo' => $row['Linkvideo'],
            'Tennoidung' => $row['Tennoidung']
        ];
    }

    // Trả về mảng kết quả
    return [
        "success" => !empty($Content), // true nếu có kết quả
        "Content" => $Content // Gửi dữ liệu
    ];

        // Kiểm tra xem có kết quả hay không
      /*  if ($result) { // Kiểm tra $result trực tiếp
            return ['data' => $result, 'success' => "true"]; // Trả về thông tin học sinh
        } else {
            return ['data' => "Not Student", 'success' => "false"];
        }*/
  /*  } catch (Exception $e) {
        // Xử lý lỗi nếu có
        return ['message' => "Query failed: " . $e->getMessage(), "success" => "error"];
    }
}*/
public function getData6($userTable, $includeSelf = false) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT Id_noidung, COUNT(*) as total FROM " . $userTable;

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        $sql .= " WHERE Id_user = '" . $_SESSION['userid'] . "'";
    }

    // Nhóm theo Id_noidung
    $sql .= " GROUP BY Id_noidung";

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
public function getData5($userTable, $includeSelf = false) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM " . $userTable;

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        $sql .= " WHERE Id_user = '" . $_SESSION['userid'] . "'";
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
public function getscore($userTable, $includeSelf = false) {
    // Thiết lập mã hóa UTF-8
    $this->dbConnect->set_charset("utf8");

    // Khởi tạo câu lệnh SQL
    $sql = "SELECT eq.*, q.Namequestion AS Namequestion FROM " . $this->dbConnect->real_escape_string($userTable) . " eq 
            LEFT JOIN question q ON eq.Id_question = q.Id"; // Giả sử Id_question là khóa ngoại trong bảng exam_questions

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        // Sử dụng Prepared Statements để tránh SQL Injection
        $sql .= " WHERE eq.Id_user = ?";
    }

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect->prepare($sql);
    if (!$includeSelf) {
        // Liên kết tham số
        $stmt->bind_param("i", $_SESSION['userid']); // Giả sử Id_user là kiểu số nguyên
    }

    // Thực thi câu lệnh
    $stmt->execute();

    // Lấy kết quả
    $result = $stmt->get_result();

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Query failed: " . $stmt->error);
    }

    // Khởi tạo mảng kết quả
    $links = [];

    // Lấy dữ liệu từ kết quả truy vấn
    while ($row = $result->fetch_assoc()) {
        $links[] = $row; // Thêm từng hàng vào mảng
    }

    // Đóng câu lệnh
    $stmt->close();

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $links;
}
public function getcategory($userTable) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM " . $userTable;

    $this->dbConnect->set_charset("utf8");
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

public function getquestion1($userTable,$Id_Categories,$includeSelf = false) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
	   // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM ".$userTable." WHERE Id_Categories = :Id_Categories";

	// Sửa dấu "=" thành ":phone"
    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($sql);

        // Liên kết giá trị với tham số
        $stmt->bindValue(":Id_Categories", $Id_Categories);
        // Thực thi câu lệnh
        $stmt->execute();
      
        // Lấy dữ liệu từ kết quả truy vấn
    $question = [];

    // Sử dụng fetchAll() nếu bạn muốn lấy tất cả kết quả
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $question[] = [
			'Id' => $row['Id'],
			'Images' => $row['Images'],
            'Id_Categories' => $row['Id_Categories'],
            'Namequestion' => $row['Namequestion'],
			'Question' => $row['Question'],
			'Type' => $row['Type'],
			'answer1' => $row['answer1'],
			'answer2' => $row['answer2'],
			'answer3' => $row['answer3'],
			'answer4' => $row['answer4'],
			'Correct_answer' => $row['Correct_answer']

        ];
    }

    // Trả về mảng kết quả
    return [
        "success" => !empty($question), // true nếu có kết quả
        "question" => $question // Gửi dữ liệu
    ];

        // Kiểm tra xem có kết quả hay không
      /*  if ($result) { // Kiểm tra $result trực tiếp
            return ['data' => $result, 'success' => "true"]; // Trả về thông tin học sinh
        } else {
            return ['data' => "Not Student", 'success' => "false"];
        }*/
    } catch (Exception $e) {
        // Xử lý lỗi nếu có
        return ['message' => "Query failed: " . $e->getMessage(), "success" => "error"];
    }
}
public function getquestion($userTable,$Id_Categories,$includeSelf = false) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM " . $userTable;

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        $sql .= " WHERE Id_Categories = '" . $Id_Categories . "'";
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
//getData1
public function getvideo($userTable,$Id_noidung) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM " . $userTable;

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
   
        $sql .= " WHERE Id_noidung = '" .$Id_noidung. "'";
   

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
/*
public function getChuongData($Id_hocsinh) {
    // Bước 1: Lấy Id_khoi của học sinh từ bảng hocsinh
    $sqlKhoi = "SELECT Id_khoi FROM hocsinh WHERE Id_hocsinh = '" . $Id_hocsinh . "'";
    $resultKhoi = $this->dbConnect->query($sqlKhoi);

    if (!$resultKhoi || $resultKhoi->num_rows == 0) {
        die("Query failed: " . $this->dbConnect->error);
    }

    $rowKhoi = $resultKhoi->fetch_assoc();
    $idKhoi = $rowKhoi['Id_khoi'];

    // Bước 2: Lấy Tenchuong và Tennoidung theo Id_khoi
    $sql = "SELECT c.Tenchuong,c.Id_chuong, n.Tennoidung 
            FROM chuong c 
            JOIN noidungkhoahoc n ON c.Id_chuong = n.Id_chuong 
            WHERE c.Id_khoi = '" . $idKhoi . "'";

    $result = $this->dbConnect->query($sql);

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Query failed: " . $this->dbConnect->error);
    }

    // Khởi tạo mảng kết quả
    $Chapters = [];

    // Lấy dữ liệu từ kết quả truy vấn
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $Chapters[] = $row; // Thêm từng hàng vào mảng
        }
    }

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $Chapters;
}*/
public function getChuongData($Id_hocsinh) {
    // Bước 1: Lấy Id_khoi của học sinh từ bảng hocsinh
    $sqlKhoi = "SELECT Id_khoi FROM hocsinh WHERE Id_hocsinh = ?";
    $stmtKhoi = $this->dbConnect->prepare($sqlKhoi);
    $stmtKhoi->bind_param("s", $Id_hocsinh); // Giả định Id_hocsinh là chuỗi
    $stmtKhoi->execute();
    $resultKhoi = $stmtKhoi->get_result();

    if (!$resultKhoi || $resultKhoi->num_rows == 0) {
        return [
            "success" => false,
            "message" => "Query failed: " . $this->dbConnect->error
        ];
    }

    $rowKhoi = $resultKhoi->fetch_assoc();
    $idKhoi = $rowKhoi['Id_khoi'];

    // Bước 2: Lấy Tenchuong và Tennoidung theo Id_khoi
    $sql = "SELECT c.Tenchuong, c.Id_chuong, n.Tennoidung 
            FROM chuong c 
            JOIN noidungkhoahoc n ON c.Id_chuong = n.Id_chuong 
            WHERE c.Id_khoi = ?";
    
    $stmt = $this->dbConnect->prepare($sql);
    $stmt->bind_param("s", $idKhoi); // Giả định Id_khoi là chuỗi
    $stmt->execute();
    $result = $stmt->get_result();

    // Khởi tạo mảng kết quả
    $Chapters = [];

    // Lấy dữ liệu từ kết quả truy vấn
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Tạo mảng mới cho mỗi hàng với các trường cụ thể
            $Chapters[] = [
                'Tenchuong' => $row['Tenchuong'],
                'Tennoidung' => $row['Tennoidung'],
                'Id_chuong' => $row['Id_chuong'] // Nếu cần Id_chuong
            ];
        }
    }

    // Trả về mảng kết quả dưới dạng JSON
    return [
        "success" => true,
        "data" => $Chapters // Gửi dữ liệu
    ];
}
public function getData3($userTable, $includeSelf = false) {
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
   public function login()
{
    $errorMessage = '';

    if (!empty($_POST["login"]) && 
        isset($_POST["loginId"]) && $_POST["loginId"] != '' &&
        isset($_POST["loginPass"]) && $_POST["loginPass"] != '') {

        $loginId = trim($_POST['loginId']);
        $passwordInput = $_POST['loginPass'];

        /*
         * Nếu cookie đã chứa MD5 password thì dùng luôn.
         * Nếu không thì MD5 password người dùng nhập.
         */
        if (isset($_COOKIE["loginPass"]) && 
            $_COOKIE["loginPass"] != '' && 
            $_COOKIE["loginPass"] == md5($passwordInput)) {

            $password = $_COOKIE["loginPass"];

        } else {
            $password = md5($passwordInput);
        }

        /*
         * Lấy toàn bộ thông tin user
         */
        $sqlQuery = "SELECT * 
                     FROM users 
                     WHERE Email = ?
                     AND Password = ?
                     AND status = '1'
                     LIMIT 1";

        $stmt = mysqli_prepare($this->dbConnect, $sqlQuery);

        if (!$stmt) {
            return "Database error!";
        }

        mysqli_stmt_bind_param($stmt, "ss", $loginId, $password);
        mysqli_stmt_execute($stmt);

        $resultSet = mysqli_stmt_get_result($stmt);

        if ($resultSet && mysqli_num_rows($resultSet) > 0) {

            /*
             * Lấy toàn bộ record
             */
            $userDetails = mysqli_fetch_assoc($resultSet);

            /*
             * =========================
             * LƯU THÔNG TIN VÀO SESSION
             * =========================
             */

            $_SESSION["userid"] = $userDetails["id"];

            $_SESSION["Id_Pm"] = $userDetails["Id_Pm"];
            $_SESSION["Id_pmw1"] = $userDetails["Id_pmw1"];

            $_SESSION["Hoten"] = $userDetails["Hoten"];
            $_SESSION["Email"] = $userDetails["Email"];
            $_SESSION["Phone"] = $userDetails["Phone"];

            $_SESSION["gender"] = $userDetails["gender"];
            $_SESSION["designation"] = $userDetails["designation"];
            $_SESSION["image"] = $userDetails["image"];

            $_SESSION["Date"] = $userDetails["Date"];

            $_SESSION["Solancap"] = $userDetails["Solancap"];
            $_SESSION["Solandung"] = $userDetails["Solandung"];

            $_SESSION["status"] = $userDetails["status"];

            $_SESSION["Serial_computer"] = $userDetails["Serial_computer"];

            $_SESSION["Xoa"] = $userDetails["Xoa"];


            /*
             * Password không nên lưu vào SESSION
             */


            /*
             * =========================
             * LƯU COOKIE NẾU CHỌN GHI NHỚ
             * =========================
             */

            if (!empty($_POST["remember"])) {

                setcookie(
                    "loginId",
                    $loginId,
                    time() + (10 * 365 * 24 * 60 * 60),
                    "/"
                );

                setcookie(
                    "loginPass",
                    $password,
                    time() + (10 * 365 * 24 * 60 * 60),
                    "/"
                );

            } else {

                setcookie(
                    "loginId",
                    "",
                    time() - 3600,
                    "/"
                );

                setcookie(
                    "loginPass",
                    "",
                    time() - 3600,
                    "/"
                );
            }


            /*
             * =========================
             * TRẢ VỀ RECORD
             * =========================
             *
             * Nếu code phía dưới cần sử dụng
             * thông tin user thì có thể dùng:
             *
             * $userDetails["Hoten"]
             * $userDetails["Email"]
             * $userDetails["Id_Pm"]
             * ...
             */

            $_SESSION["userDetails"] = $userDetails;


            /*
             * Chuyển về trang chính
             */
            header("Location: index.php");
            exit;

        } else {

            $errorMessage = "Email hoặc mật khẩu không đúng!";
        }

        mysqli_stmt_close($stmt);

    } else if (!empty($_POST["loginId"])) {

        $errorMessage = "Vui lòng nhập đầy đủ Email và mật khẩu!";
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
public function updateDate($id, $tableName,$Date_begin,$Date_end) {
    if ($id) {
		  if (is_null($id) || is_null($tableName))
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
        // Truy vấn SQL để cập nhật lượt view
        $updateQuery = "UPDATE " . $tableName . " SET Date_begin =:Date_begin, Date_end =:Date_end  WHERE Id = :id";
        
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($updateQuery);
		$stmt->bindValue(":id", $id);
		$stmt->bindValue(":Date_begin", $Date_begin);
		$stmt->bindValue(":Date_end", $Date_end);

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
function insertstudent($Hovaten,$Dienthoai,$Lop,$Ngaysinh,$Diachi) {
		 if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
	if (is_null($Hovaten) || is_null($Dienthoai)) {
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
}
    // SQL query to insert a new user Address:Address,Birthday:Birthday
    $sql = "INSERT INTO `hocsinh` (`Hovaten`,`Dienthoai`,`Lop`,`Ngaysinh`,`Diachi`) VALUES (:Hovaten,:Dienthoai,:Lop,:Ngaysinh,:Diachi)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
		//$stmtLimit->bind_param("i", $userId);
        $stmt->bindValue(":Hovaten", $Hovaten);
        $stmt->bindValue(":Dienthoai", $Dienthoai);
		$stmt->bindValue(":Lop", $Lop);
		$stmt->bindValue(":Ngaysinh", $Ngaysinh);
		$stmt->bindValue(":Diachi",$Diachi);
            
		 // In ra câu lệnh SQL với các tham số đã được bind
        $debugSql = str_replace(
            [":Hovaten", ":Dienthoai",":Lop",":Ngaysinh",":Diachi"],
            [$Hovaten, $Dienthoai,$Lop,$Ngaysinh,$Diachi],
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
public function getChuongDatatam2($Id_hocsinh) {
    // Bước 1: Lấy Id_khoi của học sinh từ bảng hocsinh
    $sqlKhoi = "SELECT Id_khoi FROM hocsinh WHERE Id_hocsinh = ?";
    $stmtKhoi = $this->dbConnect->prepare($sqlKhoi);
    $stmtKhoi->bind_param("s", $Id_hocsinh); // Giả định Id_hocsinh là chuỗi
    $stmtKhoi->execute();
    $resultKhoi = $stmtKhoi->get_result();

    if (!$resultKhoi || $resultKhoi->num_rows == 0) {
        return [
            "success" => false,
            "message" => "Query failed: " . $this->dbConnect->error
        ];
    }

    $rowKhoi = $resultKhoi->fetch_assoc();
    $idKhoi = $rowKhoi['Id_khoi'];

    // Bước 2: Lấy Tenchuong và Tennoidung theo Id_khoi
    $sql = "SELECT c.Tenchuong, c.Id_chuong, n.Tennoidung 
            FROM chuong c 
            JOIN noidungkhoahoc n ON c.Id_chuong = n.Id_chuong 
            WHERE c.Id_khoi = ?";
    
    $stmt = $this->dbConnect->prepare($sql);
    $stmt->bind_param("s", $idKhoi); // Giả định Id_khoi là chuỗi
    $stmt->execute();
    $result = $stmt->get_result();

    // Khởi tạo mảng kết quả
    $Chapters = [];

    // Lấy dữ liệu từ kết quả truy vấn
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Tạo mảng mới cho mỗi hàng với các trường cụ thể
            $Chapters[] = [
                'Tenchuong' => $row['Tenchuong'],
                'Tennoidung' => $row['Tennoidung'],
                'Id_chuong' => $row['Id_chuong'] // Nếu cần Id_chuong
            ];
        }
    }

    // Trả về mảng kết quả dưới dạng JSON
    return [
        "success" => true,
        "data" => $Chapters // Gửi dữ liệu
    ];
}

public function Checkphone1($phone) {
	if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
    // Bước 2: Lấy Hovaten và Id_hocsinh theo Dienthoai
    $sql = "SELECT Hovaten, Id_hocsinh FROM hocsinh WHERE Dienthoai = ?"; 

    $stmt = $this->dbConnect1->prepare($sql);

    // Liên kết giá trị với tham số
    $stmt->bindValue("s", $phone);
    
    // Thực thi câu lệnh
    $stmt->execute();
	$result = $stmt->get_result();

    
    // Lấy dữ liệu từ kết quả truy vấn
    $Hocsinh = [];
     if ($result->num_rows > 0) {
    // Sử dụng fetchAll() nếu bạn muốn lấy tất cả kết quả
     while ($row = $result->fetch_assoc()) {
        $Hocsinh[] = [
            'Id_hocsinh' => $row['Id_hocsinh'],
            'Hovaten' => $row['Hovaten']
        ];
    }
	}

    // Trả về mảng kết quả
    return [
        "success" => !empty($Hocsinh), // true nếu có kết quả
        "message" => $Hocsinh // Gửi dữ liệu
    ];
}

public function Checkphone($phone) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Xây dựng câu lệnh SQL
    $sql = "SELECT Hovaten, Id_hocsinh FROM hocsinh WHERE Dienthoai = :phone"; // Sửa dấu "=" thành ":phone"
    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($sql);

        // Liên kết giá trị với tham số
        $stmt->bindValue(":phone", $phone);
        // Thực thi câu lệnh
        $stmt->execute();
      
        // Lấy dữ liệu từ kết quả truy vấn
    $Hocsinh = [];

    // Sử dụng fetchAll() nếu bạn muốn lấy tất cả kết quả
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $Hocsinh[] = [
            'Id_hocsinh' => $row['Id_hocsinh'],
            'Hovaten' => $row['Hovaten']
        ];
    }

    // Trả về mảng kết quả
    return [
        "success" => !empty($Hocsinh), // true nếu có kết quả
        "message" => $Hocsinh // Gửi dữ liệu
    ];

        // Kiểm tra xem có kết quả hay không
      /*  if ($result) { // Kiểm tra $result trực tiếp
            return ['data' => $result, 'success' => "true"]; // Trả về thông tin học sinh
        } else {
            return ['data' => "Not Student", 'success' => "false"];
        }*/
    } catch (Exception $e) {
        // Xử lý lỗi nếu có
        return ['message' => "Query failed: " . $e->getMessage(), "success" => "error"];
    }
}
public function Checkinfo($Hovaten,$Ngaysinh,$Dienthoai) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Xây dựng câu lệnh SQL
    $sql = "SELECT COUNT(*) AS count FROM hocsinh WHERE Hovaten = :Hovaten AND Ngaysinh = :Ngaysinh AND  Dienthoai = :Dienthoai";

    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($sql);

        // Liên kết giá trị với tham số
        $stmt->bindValue(":Hovaten", $Hovaten);
		  $stmt->bindValue(":Ngaysinh", $Ngaysinh);
		    $stmt->bindValue(":Dienthoai", $Dienthoai);
        
        // Thực thi câu lệnh
        $stmt->execute();
        
        // Lấy kết quả
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra số lượng email tồn tại
        if ($result['count'] > 0) {
            return ['msg' => "Student already exists", 'msgType' => "warning"];
        } else {
            return ['msg' => "Student is available", 'msgType' => "success"];
        }
    } catch (Exception $e) {
        // Xử lý lỗi nếu có
        return ['msg' => "Query failed: " . $e->getMessage(), 'msgType' => "error"];
    }
}

public function register2() {
    $message = '';
    $success = false;
    
    if (!empty($_POST["email"])) {
        require_once "phpmailer/class.phpmailer.php";

        // Lấy dữ liệu từ POSTis_null($emailfriend)|| is_null($message)
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);//class_name Address:Address,Birthday:Birthday
		$class_name = trim($_POST["class_name"]);
		$Address = trim($_POST["Address"]);
		$Birthday = trim($_POST["Birthday"]);
		$Hovaten=$first_name.$last_name;
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
         
		      // Gọi hàm checkEmailExists
        $StudentCheckResult = $this->Checkinfo($Hovaten,$Birthday,$phone_mobile);
        $results = [];
		$results['messages'] = [];
        if ( $StudentCheckResult['msgType'] === "warning") {
            $message =  $StudentCheckResult['msg'];
        } else {
			 $insertResult1 = $this->insertstudent($Hovaten,$phone_mobile,$class_name,$Birthday,$Address);
			   if (isset($insertResult1['rowCount']) && $insertResult1['rowCount'] > 0) {
				     $lastID1 = $insertResult1['lastInsertId'];
					   $results['status'] = "success";
					    $results['messages'][] = "Student information saved successfully.";
			   }
			     $insertResult = $this->insertUser($id_form, $first_name, $last_name, $phone_mobile, $email, $password,$authtoken,$emailfriend,$message,$lastID1);
            
            if (isset($insertResult['rowCount']) && $insertResult['rowCount'] > 0) {
				  $results['status'] = "success";
                $lastID = $insertResult['lastInsertId'];
				 $results['messages'][] = "User information saved successfully.";
                //$message = "User inserted successfully with ID: " . $lastID . ". Verification email sent.";
                // Gọi hàm sendEmail
				$emailResult =$this->sendEmailVerification($this->Site_url,$last_name, $email, $lastID, $id_form,$authtoken);
				   // Gọi hàm sendEmail
				$emailfriendResult =$this->sendfriendEmail($this->Site_url,$last_name, $emailfriend, $lastID, $id_form,$authtoken,$message);
				// Kiểm tra và hiển thị thông điệp
				if ($emailResult['msgType']=== "success") {
					 $results['status'] = "success";
					  $results['messages'][] = "An email has been sent for verification. Please check your email to vertify.";
				}
				else   {
				       $results['status'] = "Failed";
					   $results['messages'][] = "You enter email again";
					  
					  }
					  
			if ($emailfriendResult['msgType']=== "success") {
					   $results['status'] = "success";
					   $results['messages'][] = "Website offered gift and message to your friend";
				}
				else   { 
						$results['status'] = "Failed";
					   $results['messages'][] = "You enter email again";
					 }
              
            } else {
				$results['status'] = "Failed";
				$results['messages'][] = "Failed to insert user.";
                if (isset($insertResult['error'])) {
					$results['messages'][] = " Error: " . $insertResult['error'];
                }
            }
		}
			 
        }
    } else {
        $message = "Please fill in all required fields.";
    }

    // Trả về thông báo dưới dạng JSON
   // echo json_encode(['success' => $success, 'message' => $message]);
    //return; // Đảm bảo không có gì khác được in ra
	// Trả về thông báo dưới dạng JSON
echo json_encode($results);
return; // Đảm bảo không có gì khác được in ra
}
public function register1() {
    $message = '';
    $success = false;
    
    if (!empty($_POST["email"])) {
        require_once "phpmailer/class.phpmailer.php";

        // Lấy dữ liệu từ POSTis_null($emailfriend)|| is_null($message)
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);//class_name Address:Address,Birthday:Birthday
		$class_name = trim($_POST["class_name"]);
		$Address = trim($_POST["Address"]);
		$Birthday = trim($_POST["Birthday"]);
		$Hovaten=$first_name.$last_name;
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
         
		      // Gọi hàm checkEmailExists
        $StudentCheckResult = $this->Checkinfo($Hovaten,$Birthday,$phone_mobile);

        if ( $StudentCheckResult['msgType'] === "warning") {
            $message =  $StudentCheckResult['msg'];
        } else {
			 $insertResult1 = $this->insertstudent($Hovaten,$phone_mobile,$class_name,$Birthday,$Address);
			   if (isset($insertResult1['rowCount']) && $insertResult1['rowCount'] > 0) {
				     $lastID1 = $insertResult1['lastInsertId'];
					   $success = "success";
					    $message="Student inserted successfully";
			   }
			     $insertResult = $this->insertUser($id_form, $first_name, $last_name, $phone_mobile, $email, $password,$authtoken,$emailfriend,$message,$lastID1);
            
            if (isset($insertResult['rowCount']) && $insertResult['rowCount'] > 0) {
				  $success = "success";
                $lastID = $insertResult['lastInsertId'];
				 $message="Ưser inserted successfully";
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
public function updateLink($id, $tableName, $tenWebsite, $link, $view) {
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
}
//Addquestion
public function Addexam($Code,$Id_user,$Id_Categories,$Namequestion,$answer1,$answer2,$answer3,$answer4,$Correctanswer,$Type,$time,$Date,$Score){
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

if (!empty($answer1)) {
    $answer1 = $answer1;
} else {
    $answer1 = null;
}

if (!empty($answer2)) {
    $answer2 = $answer2;
} else {
    $answer2 = null;
}

if (!empty($answer3)) {
    $answer3 = $answer3;
} else {
    $answer3 = null;
}

if (!empty($answer4)) {
    $answer4 = $answer4;
} else {
    $answer4 = null;
}
   
    // SQL query to insert a new record
    $sql = "INSERT INTO `exam_question`(`Code`,`Id_user`,`Id_Categories`,`Id_question`,`Type`,`answer1`,`answer2`,`answer3`,`answer4`,`Correct_answer`,`Time`,`Date`,`Score`) VALUES (  :Code, :Id_user, :Id_Categories, :Namequestion, :type, :answer1, :answer2, :answer3, :answer4, :Correct_answer, :Time,:Date,:Score)"; 
    try {   
        $stmt = $this->dbConnect1->prepare($sql);
		$stmt->bindValue(":Code", $Code);
		$stmt->bindValue(":Id_user", $Id_user);
        $stmt->bindValue(":Id_Categories", $Id_Categories);
		$stmt->bindValue(":Namequestion", $Namequestion);
        $stmt->bindValue(":answer1", $answer1);
		$stmt->bindValue(":answer2", $answer2); // Chèn NULL cho answer2
        $stmt->bindValue(":answer3", $answer3); // Chèn NULL cho answer3
		$stmt->bindValue(":answer4", $answer4);
		$stmt->bindValue(":Correct_answer", $Correctanswer);
		$stmt->bindValue(":type", $Type);
		$stmt->bindValue(":Time", $time);
		$stmt->bindValue(":Date", $Date);
		$stmt->bindValue(":Score", $Score);
		
        $debugSql = str_replace(
                [":Id_user",":Score",":Id_Categories",":Namequestion",":answer1",":answer2",":answer3",":answer4",":Correct_answer",":type",":Time"],
                [$Id_user,$Score,$Id_Categories,$Namequestion,$answer1,$answer2,$answer3,$answer4,$Correctanswer,$Type,$time],
                $sql
            );

        // Execute the statement
        // Execute the statement
    if ($stmt->execute()) {
        // If successful
         $message  ="Insert successfully";
            $rowCount =$stmt->rowCount();
			$debugSql = $debugSql;
    } else {
        // If failed
        $message = "Insert failed";
        $rowCount = 0;
		$debugSql = $debugSql;
    }
        
        // Trả về kết quả
         
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

public function Addquestion($Id_Categories,$Namequestion,$Image,$answer1,$answer2,$answer3,$answer4,$Correctanswer,$Type,$time){
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
    
   
    // SQL query to insert a new record
    $sql = "INSERT INTO `question` (`Id_Categories`,`Namequestion`,`type`,`answer1`,`answer2`,`answer3`,`answer4`,`Correct_answer`,`created_at`,`Images`) VALUES (:Id_Categories,:Namequestion,:type,:answer1,:answer2,:answer3,:answer4,:Correct_answer,:created_at,:Image)"; 
    try {   
        $stmt = $this->dbConnect1->prepare($sql);
        $stmt->bindValue(":Id_Categories", $Id_Categories);
		$stmt->bindValue(":Namequestion", $Namequestion);
        $stmt->bindValue(":answer1", $answer1);
		$stmt->bindValue(":answer2", $answer2);
		$stmt->bindValue(":answer3", $answer3);
		$stmt->bindValue(":answer4", $answer4);
		$stmt->bindValue(":Correct_answer", $Correctanswer);
		$stmt->bindValue(":type", $Type);
		$stmt->bindValue(":created_at", $time);
		$stmt->bindValue(":Image", $Image);
        $debugSql = str_replace(
                [":Id_Categories",":Namequestion",":type",":created_at"],
                [$Id_Categories,$Namequestion,$Type,$time],
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
public function Addcategory($Namecategory) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

   
    // SQL query to insert a new record
    $sql = "INSERT INTO `categories` (`Category_name`) VALUES (:Namecategory)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
        
        $stmt->bindValue(":Namecategory", $Namecategory);
        $debugSql = str_replace(
                [":Namecategory"],
                [$Namecategory],
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
public function insertKeyword($Id_product, $Id, $keyword, $uploadFile, $View,$Namewebsite) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

   
    // SQL query to insert a new record
    $sql = "INSERT INTO `detailproduct` (`Id_product`, `Id`, `NameDeltail`, `Picture`, `View`,`Namewebsite`) VALUES (:Id_product, :Id, :NameDeltail, :Picture, :View,:Namewebsite)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
        
        $stmt->bindValue(":Id_product", $Id_product);
        $stmt->bindValue(":Id", $Id);        
        $stmt->bindValue(":NameDeltail", $keyword);      
        $stmt->bindValue(":Picture", $uploadFile);  // Chèn tên hình ảnh đã đổi
        $stmt->bindValue(":View", $View);
		$stmt->bindValue(":Namewebsite", $Namewebsite);		
        $debugSql = str_replace(
                [":Id_product", ":Id", ":NameDeltail",":Picture",":View"],
                [$Id_product, $Id, $keyword,$uploadFile,$View],
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
/*public function getData1($userTable, $includeSelf = false) {
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
}*/
public function getAllStudentsWithScores2() {
    // Xây dựng câu lệnh SQL để lấy thông tin học sinh và tổng điểm
   $sql = "SELECT h.Id_hocsinh, h.Hovaten, h.Lop, SUM(d.Diem) AS total_score 
        FROM hocsinh AS h 
        LEFT JOIN diem_daiso AS d ON h.Id_hocsinh = d.Id_hocsinh 
        GROUP BY h.Id_hocsinh 
        ORDER BY total_score ASC";

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($sql);

    // Thực hiện truy vấn
    if (!$stmt->execute()) {
        return [
            "success" => false,
            "message" => "Query failed: " . implode(", ", $stmt->errorInfo())
        ];
    }

    // Khởi tạo mảng kết quả
    $students = [];
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($result)) {
        foreach ($result as $row) {
            $students[] = [
                'Id_hocsinh' => $row['Id_hocsinh'],
                'Hovaten' => $row['Hovaten'],
                'Lop' => $row['Lop'],
              'TotalScore' => isset($row['total_score']) ? $row['total_score'] : 0, // Đảm bảo tổng điểm không null
            ];
        }
    }

    // Trả về danh sách học sinh dưới dạng JSON
    return [
        "success" => true,
        "students" => $students
    ];
}
public function getAllStudentsWithScores() {
    // Xây dựng câu lệnh SQL để lấy thông tin học sinh và tổng điểm
    $sql = "SELECT h.Id_hocsinh, h.Hovaten, h.Lop, SUM(d.Diem) AS total_score 
            FROM hocsinh AS h 
            LEFT JOIN diem_daiso AS d ON h.Id_hocsinh = d.Id_hocsinh 
            GROUP BY h.Id_hocsinh";

    // Thực hiện truy vấn
    $result = $this->dbConnect->query($sql);

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die(json_encode(["success" => false, "message" => "Query failed: " . $this->dbConnect->error]));
    }

    // Khởi tạo mảng kết quả
    $students = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'Id_hocsinh' => $row['Id_hocsinh'],
                'Hovaten' => $row['Hovaten'],
                'Lop' => $row['Lop'],
                'TotalScore' => $row['total_score'] ? $row['total_score'] : 0 // Đảm bảo tổng điểm không null
            ];
        }
    }

    // Trả về danh sách học sinh dưới dạng JSON
    echo json_encode(["success" => true, "students" => $students]);
}
/*public function getAllStudentsWithScores1() {
    // Xây dựng câu lệnh SQL để lấy thông tin học sinh và tổng điểm
     $sql = "SELECT h.Id_hocsinh, h.Hovaten, h.Lop, SUM(d.Diem) AS total_score 
            FROM hocsinh AS h 
            LEFT JOIN diem_daiso AS d ON h.Id_hocsinh = d.Id_hocsinh 
            GROUP BY h.Id_hocsinh";

    // Thực hiện truy vấn
    $result = $this->dbConnect->query($sql);

    // Kiểm tra lỗi truy vấn
    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Query failed: " . $this->dbConnect->error);
    }

    // Lưu trữ kết quả
   //$students = [];
    /*if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'Id_hocsinh' => $row['Id_hocsinh'],
                'Hovaten' => $row['Hovaten'],
                'Lop' => $row['Lop'],
                'TotalScore' => $row['total_score'] ? $row['total_score'] : 0 // Đảm bảo tổng điểm không null
            ];
        }
    }*/
	/*$students = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
           /* $students[] = [
                'Id_hocsinh' => $row['Id_hocsinh'],
                'Hovaten' => $row['Hovaten'],
                'Lop' => $row['Lop'],
                'TotalScore' => $row['total_score'] ? $row['total_score'] : 0 // Đảm bảo tổng điểm không null
            ];*/
		/*	$students[] = $row;
        }
    }

    // Trả về danh sách học sinh
    return $students;
}*/
public function getTotalScoreById($userId) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT SUM(Diem) AS total_score FROM diem_daiso WHERE Id_hocsinh = '" . $userId . "'";

    // Thực hiện truy vấn
    $result = $this->dbConnect->query($sql);

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Query failed: " . $this->dbConnect->error);
    }

    // Lấy tổng điểm từ kết quả truy vấn
    $totalScore = 0; // Mặc định tổng điểm là 0
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalScore = $row['total_score']; // Lấy tổng điểm
    }

    // Trả về tổng điểm
    return $totalScore;
}
/*public function getTotalScoreById1($userId) {
    // Kiểm tra điều kiện đầu vào
    if (is_null($userId)) {
        return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
    }

    // Truy vấn SQL để tính tổng điểm
    $query = "SELECT SUM(Diem) AS total_score FROM diem_daiso WHERE Id_hocsinh = :userId";

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($query);
    $stmt->bindValue(":userId", $userId); // Bind giá trị ID

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        // Lấy tổng điểm từ kết quả truy vấn
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalScore = $row['total_score'] ?? 0; // Lấy tổng điểm, mặc định là 0 nếu không có kết quả
        return $totalScore; // Trả về tổng điểm
    } else {
        //return ['msg' => "Lỗi truy vấn: " . implode(", ", $stmt->errorInfo()), 'msgType' => "error"];
    }
}*/
public function getTotalScoreById_Noidung($Id) {
    // Xây dựng câu lệnh SQL
    $sql = "SELECT SUM(Diem) AS total_score FROM diem_daiso WHERE Id_noidung = '" . $Id . "'";

    // Thực hiện truy vấn
    $result = $this->dbConnect->query($sql);

    // Kiểm tra lỗi truy vấn
    if (!$result) {
        die("Query failed: " . $this->dbConnect->error);
    }

    // Lấy tổng điểm từ kết quả truy vấn
    $totalScore = 0; // Mặc định tổng điểm là 0
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalScore = $row['total_score']; // Lấy tổng điểm
    }

    // Trả về tổng điểm
    return $totalScore;
}
/*public function getTotalScoreById1($userId) {
    // Kiểm tra điều kiện đầu vào
    if (is_null($userId)) {
        return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
    }

    // Truy vấn SQL để tính tổng điểm
    $query = "SELECT SUM(Diem) AS total_score FROM diem_daiso WHERE Id_hocsinh = :userId";

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($query);
    $stmt->bindValue(":userId", $userId); // Bind giá trị ID

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        // Lấy tổng điểm từ kết quả truy vấn
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalScore = $row['total_score'] ?? 0; // Lấy tổng điểm, mặc định là 0 nếu không có kết quả
        return $totalScore; // Trả về tổng điểm
    } else {
        //return ['msg' => "Lỗi truy vấn: " . implode(", ", $stmt->errorInfo()), 'msgType' => "error"];
    }
}*/

function insert_result($Id,$Id_hocsinh,$Id_noidung,$Ngay,$Id_khoa,$Number,$Kq_hs,$kq,$Ketqua,$Diem,$Name) {
		 if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }
	if (is_null($Id) || is_null($Id_hocsinh) || is_null($Id_noidung) || is_null($Number) || is_null($Id_khoa) || is_null($Kq_hs) || is_null($kq)|| is_null($Ketqua)|| is_null($Diem)) {
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
}
     // Gọi hàm checkId
    $Resultnumber = $this->Checknumber($Number);
    if ($Resultnumber['msgType'] === "warning") {
        /*$tableName = 'view'; // Tên bảng bạn muốn cập nhật
        // Gọi hàm updateCode
        $isUpdated = $this->updateCode($Id, $tableName, $ma);*/
        return ['msg' => "Sentence successed", 'msgType' => "success"];
    } else {
		$SumDiem1=$this->getTotalScoreById_Noidung($Id_noidung);
		if($SumDiem1 > 1000)
			$Diem=0;
    // SQL query to insert a new user
    $sql = "INSERT INTO diem_daiso (`Id_user`,`Id_hocsinh`,`Id_noidung`,`Ngay`,`Id_khoa`,`Number`,`Kq_hs`,`Kq`,`Ketqua`,`Diem`) VALUES (:Id_user,:Id_hocsinh,:Id_noidung,:Ngay,:Id_khoa,:Number,:Kq_hs,:Kq,:Ketqua,:Diem)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
		//$stmtLimit->bind_param("i", $userId);
        $stmt->bindValue(":Id_user", $Id);
        $stmt->bindValue(":Id_hocsinh", $Id_hocsinh);
        $stmt->bindValue(":Id_noidung", $Id_noidung);
        $stmt->bindValue(":Ngay", $Ngay);
        $stmt->bindValue(":Id_khoa", $Id_khoa);
        $stmt->bindValue(":Number", $Number);
        $stmt->bindValue(":Kq_hs", $Kq_hs);
		$stmt->bindValue(":Kq", $kq);
		$stmt->bindValue(":Ketqua", $Ketqua);
		$stmt->bindValue(":Diem", $Diem);//
        
		 // In ra câu lệnh SQL với các tham số đã được bind
        $debugSql = str_replace(
            [":Id_user", ":Id_hocsinh", ":Id_noidung", ":Ngay", ":Id_khoa", ":Number", ":Kq_hs",":Kq",":Ketqua",":Diem"],
            [$Id, $Id_hocsinh, $Id_noidung, $Ngay, $Id_khoa, $Number, $Kq_hs,$kq,$Ketqua,$Diem],
            $sql
        );

        // In ra câu lệnh SQL
        //echo "SQL: " . $debugSql; // Tạm thời in ra câu lệnh SQL

		
        // Execute the statement
       //$stmt->execute();
	    // Thực thi câu lệnh
    $success = $stmt->execute();
	$SumDiem1=0;
    // Kiểm tra kết quả
    //if ($success) {
        // Câu lệnh thực thi thành công
     //$message  ="Insert successfully";
	    //$SumDiem = 0;
        $message  ="Insert successfully";
        $rowCount = $stmt->rowCount();
		$debugSql = $debugSql;
		$SumDiem =$this->getTotalScoreById($Id);
		return [
            'rowCount' => $stmt->rowCount(),
			 'debugSql' => $debugSql, // Thêm câu lệnh SQL vào phản hồi
			 'message'  => $message,
			 'Name'  => $Name,
		      'SumDiem'  => $SumDiem,'Diem'  => $Diem
        ];
    //} else {
        // Câu lệnh không thực thi thành công
      /*  $message = "Insert failed";
        $rowCount = 0;
		$debugSql = $debugSql;
		return [
            'rowCount' =>  $rowCount,
			 'debugSql' => $debugSql, // Thêm câu lệnh SQL vào phản hồi
			 'message'  =>$message
        ];*/
    //}
		
        // Lấy ID của bản ghi vừa chèn
        //$lastId = $this->dbConnect->lastInsertId();
        
        // Trả về số lượng hàng bị ảnh hưởng và ID của bản ghi
       /*return [
            'rowCount' => $stmt->rowCount(),
             'lastInsertId' => $this->dbConnect1->lastInsertId(),
			 'debugSql' => $debugSql, // Thêm câu lệnh SQL vào phản hồi
			 'message'  =>$message
        ];*/
   } catch (Exception $e) {
		 /*$message  ="Insert successfully";
         $rowCount =$stmt->rowCount();
		$debugSql = $debugSql;*/
        // Nếu có lỗi, trả về 0 cho rowCount và null cho lastInsertId
       return [
            'rowCount' => 0,
            'lastInsertId' => null,
            'error' => $e->getMessage(), // Thêm thông tin lỗi nếu cần
			 'debugSql' => $debugSql,// Thêm câu lệnh SQL vào phản hồi lỗi
			 'message'  =>$message
        ];
    }
	
	}
	//echo json_encode(['message' => $message,'rowCount' => $rowCount,'debugSql' => $debugSql]);
	//echo json_encode(['message' => $message]);
    //echo json_encode(['success' => $success,'Number your Web'=>$currentCount,'LimitWebsite' =>$limitWebsite, 'message' => $message]);
    //return;
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
public function getCode($Id, $userTable, $includeSelf = false) {
    // Khởi tạo câu lệnh SQL
    /*$sql = "SELECT * FROM " . $userTable;

    // Nếu không bao gồm bản ghi của người dùng hiện tại, thêm điều kiện
    if (!$includeSelf) {
        $sql .= " WHERE Id = :Id";
    }

    // Chuẩn bị câu lệnh
    $stmt = $this->dbConnect1->prepare($sql);
    
    // Nếu có điều kiện, liên kết giá trị
    if (!$includeSelf) {
        //$stmt->bind_param("i", $Id); // Giả định Id là kiểu integer
		$stmt->bindValue(":Id", $Id);
    }

    // Thực thi câu lệnh
    $stmt->execute();
    $result = $stmt->get_result();
   //Thiết lập kiểu dữ liệu trả về
//$stmt->setFetchMode(PDO::FETCH_OBJ);

//Gán giá trị và thực thi
//$stmt->execute(array('name' => 'a'));
*/
$sql = "SELECT * FROM " . $userTable." WHERE Id = :Id";
$stmt = $this->dbConnect1->prepare($sql);
$stmt->bindValue(":Id", $Id);
//Thiết lập kiểu dữ liệu trả về
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
//$resultSet = $stmt->fetchAll();
/*Trong trường hợp chưa setFetchMode() bạn có thể sử dụng
$resultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);*/
/*foreach ($resultSet as $row) {
echo $row['name'] . '\n';
echo $row['email'] . '\n';
echo $row['age'] . '\n';
}*/
// Giả định bạn đã có kết quả truy vấn trong biến $stmt
$resultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Khởi tạo mảng kết quả
$links = [];

// Lấy dữ liệu từ kết quả truy vấn
if (!empty($resultSet)) {
    foreach ($resultSet as $row) {
        $links[] = $row; // Thêm từng hàng vào mảng
    }
}

    // Khởi tạo mảng kết quả
/*    $links = [];

    // Lấy dữ liệu từ kết quả truy vấn
    if ($result->num_rows > 0) {
        while ($row = $resultSet->fetch_assoc()) {
            $links[] = $row; // Thêm từng hàng vào mảng
        }
    }*/

    // Trả về mảng kết quả, có thể rỗng nếu không có dữ liệu
    return $links;
}
public function getCode1($Id, $userTable) {
    $sql = "SELECT * FROM " . $userTable . " WHERE Id = '" . $Id . "'";
    $result = $this->dbConnect->query($sql);
    $debugSql = str_replace(  [":Id"],
        [$Id], $sql);
   // Kiểm tra xem $links có dữ liệu không
if (!empty($result)) {
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
public function Checknumber($number) {
    if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }

    // Xây dựng câu lệnh SQL
    $sql = "SELECT COUNT(*) AS count FROM diem_daiso WHERE Number = :Number";

    try {
        // Chuẩn bị câu lệnh
        $stmt = $this->dbConnect1->prepare($sql);
        $stmt->bindValue(":Number", $number);
        
        // Thực thi câu lệnh
        $stmt->execute();
        
        // Lấy kết quả
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra số lượng ID tồn tại
        return $result['count'] > 0
            ? ['msg' => "Number already exists", 'msgType' => "warning"]
            : ['msg' => "Number is available", 'msgType' => "success"];
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

	function insertUser($id_form,$first_name, $last_name, $phone_mobile, $email,$password,$authtoken,$emailfriend,$message,$id_hocsinh) {
		 if ($this->dbConnect1 === null) {
        return ['msg' => "Database connection is not established.", 'msgType' => "error"];
    }//$id_hocsinh
	if (is_null($id_form)|| is_null($id_hocsinh) || is_null($first_name) || is_null($last_name) || is_null($phone_mobile) || is_null($email) || is_null($password)|| is_null($emailfriend)|| is_null($message)) {
    return ['msg' => "Một hoặc nhiều biến đầu vào không hợp lệ.", 'msgType' => "error"];
}
    $authtoken = $this->getAuthtoken($email);
		$Limit_website=2;	
    // SQL query to insert a new user
    $sql = "INSERT INTO `user` (`Id_form`,`Id_hocsinh`,`first_name`,`last_name`, `phone_mobile`, `email`,`authtoken`,`password`,`emailfriend`,`message`,`Limit_website`) VALUES (:id_form,:Id_hocsinh,:first_name, :last_name, :phone_mobile, :email,:authtoken,:password,:emailfriend,:message,:Limit_website)"; 
    try {
        $stmt = $this->dbConnect1->prepare($sql);
		//$stmtLimit->bind_param("i", $userId);
        $stmt->bindValue(":id_form", $id_form);
		$stmt->bindValue(":Id_hocsinh", $id_hocsinh);
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
            [":id_form",":Id_hocsinh", ":first_name", ":last_name", ":phone_mobile", ":email", ":authtoken", ":password",":emailfriend",":message",":Limit_website"],
            [$id_form,$id_hocsinh,$first_name, $last_name, $phone_mobile, $email, $authtoken, $password,$emailfriend,$message,$Limit_website],
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
public function getNoidungByHocSinh($id_hocsinh)
{
    $sql = "
    SELECT n.*
    FROM tbl_noidung n
    INNER JOIN tbl_hocsinh_noidung h
    ON n.id = h.id_noidung
    WHERE h.id_hocsinh = :id_hocsinh
    AND h.active = 1
    ORDER BY n.id
    ";

    $stmt = $this->dbConnect1->prepare($sql);
    $stmt->bindValue(':id_hocsinh',$id_hocsinh);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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