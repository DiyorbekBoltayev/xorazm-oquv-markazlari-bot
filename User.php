<?php
require_once 'connect.php';

class User
{
    private $chat_id, $firstname, $lastname, $username;

    public function __construct($chat_id, $firstname, $lastname, $username)
    {
        global $conn;
        $this->chat_id = $chat_id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->username = $username;
        $sql = "select * from users where chat_id='$chat_id'";
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows == 0) {
            $sql = "insert into users (chat_id,firstname,lastname,username,page) values ('$chat_id','$firstname','$lastname','$username','')";
            $result = mysqli_query($conn, $sql);
        }

    }

    function setLang($lang)
    {
        global $conn;
        $sql = "update users set language='$lang' where chat_id='$this->chat_id'";
        mysqli_query($conn, $sql);
    }

    function getLang()
    {
        global $conn;
        $sql = "select language from users where chat_id='$this->chat_id'";
        $result = mysqli_query($conn, $sql);
        $result = $result->fetch_assoc();
        return $result['language'];
    }

    function changeLang()
    {
        if ($this->getLang() == "uz") {
            $this->setLang('ru');
        } elseif ($this->getLang() == 'ru') {
            $this->setLang('uz');
        }
    }

    function setPage($page)
    {
        global $conn;
        $sql = "update users set page='$page' where chat_id='$this->chat_id'";
        mysqli_query($conn, $sql);
    }

    function getPage()
    {
        global $conn;
        $sql = "select page from users where chat_id='$this->chat_id'";
        $result = mysqli_query($conn, $sql);
        $result = $result->fetch_assoc();
        $s = $result['page'];
        if (is_null($s)) {
            $s = "";
        };
        return $s;
    }


    function getDistricts(): array
    {
        global $conn;
        $lang = $this->getLang();
        $sql = "select $lang from districts ";
        $result = mysqli_query($conn, $sql);
        $d = [];
        while ($row = $result->fetch_assoc()) {
            $d[] = $row[$lang];
        }
        return $d;
    }

    function setDist($text)
    {
        global $conn;
        $id = 0;
        $sql = "select id from districts where uz='$text' or ru='$text'";
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows != 0) {
            $result = $result->fetch_assoc();
            $id = (int)$result['id'];
        }

        $sql = "update users set district_id=$id where chat_id=$this->chat_id";
        mysqli_query($conn, $sql);

    }

    function getSubjects(): array
    {
        global $conn;
        $lang = $this->getLang();
        $sql = "select $lang from subjects";
        $result = mysqli_query($conn, $sql);
        $d = [];
        while ($row = $result->fetch_assoc()) {
            $d[] = $row[$lang];
        }
        return $d;
    }

    function setSubj($text)
    {
        global $conn;
        $id = 0;
        $sql = "select id from subjects where uz='$text' or ru='$text'";
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows != 0) {
            $result = $result->fetch_assoc();
            $id = (int)$result['id'];
        }

        $sql = "update users set subject_id=$id where chat_id=$this->chat_id";
        mysqli_query($conn, $sql);

    }

    function getTexts($keyword)
    {
        global $conn;
        $lang = $this->getLang();
        $sql = "select * from texts where keyword='{$keyword}' limit 1";
        $result = mysqli_query($conn, $sql);
        $result = $result->fetch_assoc();
        return $result[$lang];
    }

    function getTrainingCenters(): array
    {
        global $conn;
        $sql = "select * from users where chat_id=" . $this->chat_id . " limit 1";
        $result = mysqli_query($conn, $sql)->fetch_assoc();
        $district_id = $result['district_id'];
        $subject_id = $result['subject_id'];
        $sql = "select keyword from subjects where id=" . $subject_id . " limit 1";
        $result = mysqli_query($conn, $sql)->fetch_assoc();
        $keyword = $result['keyword'];

        $sql = "select * from centers";
        $result = mysqli_query($conn, $sql);
        $centers = [];
        while ($row = $result->fetch_assoc()) {
            $districts = explode(',', $row['district_id']);
            if (in_array($district_id, $districts)) {
                $subjects = explode(',', $row['subjects']);
                if (in_array($keyword, $subjects)) {
                    $centers[] = $row;
                }
            }

        }
        return $centers;

    }

    function getAllTrainingCenters(): array
    {
        global $conn;
        $sql = "select * from centers";
        $result = mysqli_query($conn, $sql);
        $centers = [];
        while ($row = $result->fetch_assoc()) {
            $centers[] = $row;

        }
        return $centers;

    }

    function getInfo($id): string
    {
        global $conn;
        $lang = $this->getLang();
        $sql = "select * from centers where id=" . $id . " limit 1";
        $result = mysqli_query($conn, $sql)->fetch_assoc();
        $info = $result[$lang];
        $districts=$result['district_id'];
        $subjects = $result['subjects'];
        $xabar = "📜 " . $this->getTexts('x_m_nomi') . "\n";
        $xabar .= "🏢 " . $result['name'] . "\n \n";
        $info = explode(';;', $info);
        $xabar .= "💭 " . $this->getTexts('x_q_izoh') . "\n";
        $xabar .= $info[0] . "\n \n";
        $xabar .= "📚 " . $this->getTexts('x_fanlar') . "\n";
        $subjects = explode(',', $subjects);
        foreach ($subjects as $subject) {
            $sql="select * from districts where keyword=$subject limit 1";
            $d=mysqli_query($conn,$sql)->fetch_assoc();
            $xabar .= "🔷 " . $d[$lang] . " \n";
        }
        $xabar .= "\n";
        $xabar .= "📍 " . $this->getTexts('x_manzil') . " \n";
        $xabar .= $info[1] . " \n \n";
        $xabar .= "🗺 " . $this->getTexts('x_filial') . "\n";
        $districts = explode(',',$districts);
        foreach ($districts as $district) {
            $sql="select * from districts where id='$district' limit 1";
            $d=mysqli_query($conn,$sql)->fetch_assoc();

            $xabar .= "🌆 " . $d[$lang] . " \n";
        }
        $xabar .= "\n";
        $xabar .= "☎️ " . $this->getTexts('x_telefon') . " \n";
        $xabar .= $info[2] . " \n \n";
        $xabar .= "👨‍💻 " . $this->getTexts('x_telegram') . "  \n";
        $xabar .= $info[3] . " \n \n \n ";
        $xabar .= "@XorazmOquvMarkazlariBot";
        return $xabar;


    }
}