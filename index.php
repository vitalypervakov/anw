<?
/*
      $file = fopen("test.cfg", "r");
      $data = fread($file, filesize("test.cfg"));
      fclose($file);
      $data = str_replace("\n", " ", $data);
      $data = str_replace("\r", " ", $data);
      preg_match("/\<head\>(.*)\<\/head\>/", $data, $matches);
      echo $matches[0];*/

  //модули движка
  include "modules/connect.php";
  include "modules/user.php";
  include "modules/utils.php";
  include "modules/templates.php";
  include "modules/login.php";
  include "modules/logger.php";
  include "modules/date.php";

  //модули разделов
  include "modules/common.php";
  include "modules/main.php";
  include "modules/news.php";
  include "modules/publications.php";
  include "modules/about.php";
  include "modules/voting.php";
  include "modules/contacts.php";
  include "modules/sections.php";
  include "modules/licenses.php";
  include "modules/projects.php";
  include "modules/partners.php";
  include "modules/upload.php";
  include "modules/feedback.php";
  include "modules/map.php";
  include "modules/search.php";
  include "modules/vacancy.php";
  include "modules/gallery.php";
  
  //отключаем кэширование
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

  //подключаемся к базе данных
  connect_db();

  //узнаем пользователя
  $ack = "guest";
  if (isset($_COOKIE['anw_ack'])) $ack = $_COOKIE['anw_ack'];

  //создаем и инициализируем пользователя
  $user = new User();
  $user->loadUserByAck($ack);
  if ($user->login != "guest") $user->updateUserAck();
  $user->updateAcks();
  
  //узнаем действие которое необходимо произвести  
  $page = "main";
  if (isset($_POST["page"])) $page = $_POST["page"];
  if (isset($_GET["page"])) $page = $_GET["page"];

  //узнаем где все происходит  
  $zone = "all";
  if (isset($_POST["zone"])) $zone = $_POST["zone"];
  if (isset($_GET["zone"])) $zone = $_GET["zone"];
  if (strToLower($zone) == "building") {
    $zone = "building";
  } elseif (strToLower($zone) == "sysint") {
    $zone = "sysint";
  } elseif (strToLower($zone) == "distribution") {
    $zone = "distribution";
  } else {
    $zone = "all";
  }

  //вход, выход обрабатываем отдельно
  if (isset($_POST['login'])) {
    applyLogin();
    logEvent($user->login, "login");
  }

  if (isset($_GET['logout'])) {
    logEvent($user->login, "logout");
    applyLogout();
  }
  
  if (isset($_GET['variant'])) {
    makeVote();
  }
  
  $page = strToLower($page);
  if ($page == "main") {
    showMain();
  } elseif ($page == "auth") {
    echo getTemplate('templates/login.html');
  } elseif ($page == "news") {
    showNews();
  } elseif ($page == "publications") {
    showPublications();
  } elseif ($page == "about") {
    showAbout();
  } elseif ($page == "votingresults") {
    showVoting();
  } elseif (($page == "contacts") || ($page == "spbcontacts") || ($page == "mskcontacts")|| ($page == "yarcontacts")) {
    showContacts();
  } elseif ($page == "section") {
    showSection();
  } elseif ($page == "projects") {
    showProjects();
  } elseif ($page == "licenses") {
    showLicenses();
  } elseif ($page == "partners") {
    showPartners();
  } elseif ($page == "upload") {
    if ($user->status == "admin") showUpload();
  } elseif ($page == "questions") {
    showFeedback();  
  } elseif ($page == "map") {
    showMap();  
  } elseif ($page == "search") {
    showSearchResults();  
  } elseif ($page == "vacancy") {
    showVacancy();  
  } elseif ($page == "gallery") {
    showGallery();  
  }
  
?>