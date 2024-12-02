<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id'])) {
    header("Location: logecad.php");
    exit();
}

$userId = $_SESSION['id'];
$sql = "SELECT * FROM usuarios WHERE id = $userId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    die("Usuário não encontrado.");
}

$tagsSql = "SELECT t.nome FROM tags t 
            JOIN usuario_tags ut ON t.id = ut.tag_id
            WHERE ut.usuario_id = $userId";
$tagsResult = $conn->query($tagsSql);
$tags = [];
while ($tag = $tagsResult->fetch_assoc()) {
    $tags[] = $tag['nome'];
}
$tagsList = implode(", ", $tags); 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Jogador</title>
    <script src="slide.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            box-sizing: border-box;
        }
        .perfil-container {
            width: 71%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .perfil-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .perfil-header img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
        .perfil-header .editar-perfil-btn {
            background-color: #5c0180;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .perfil-header .editar-perfil-btn:hover {
            background-color: #b702ff;
        }
        .wallpaper {
            width: 100%;
            height: 300px;
            background-image: url('<?php echo $usuario['wallpaper']; ?>');
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 5px solid black;
        }
        .perfil-info {
            margin-top: 5px;
            transform: translate( 20%, -30%);
            display: flex;
            flex-direction:column;
            text-align: center;
            margin-top: -15px;
        }
        .perfil-info h2 {
            font-size: 24px;
            margin-bottom: 10px;
            transform: translate( -67%, 70%);
        }
        .fotoperf {
            display: inline-block;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid black; 
            width: 160px; 
            height: 160px; 
            box-sizing: border-box;
        }      
        .nickname {
            margin-top: -15px;
            transform: translate( -10%, 0%);
        }
        .bio {
            margin-top: -15px;
        }
        .discord {
            margin-top: -15px;
        }
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            transform: translate( -20%, 110%);
        }
        .tag {
            background-color: #5c0180;
            color: #fff;
            padding: 5px 10px;
            border-radius: 20px;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f3f3f3;
            color: #202020;
        }

        .logo {
            font-weight: 800;
            font-size: 25px;
            text-decoration: none;
            color: black;
        }

        .search-bar {
            flex-grow: 1;
            margin: 0 20px;
            display: flex;
            align-items: center;
        }

        .search-bar input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login, .cadastro {
            padding: 10px 20px;
            font-size: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s background;
        }

        .login {
            color: #202020;
            background-color: #f3f2f2;
        }

        .login:hover {
            background-color: #e2e2e2;
        }

        .cadastro {
            color: #fff;
            background-color: #5c0180;
        }

        .cadastro:hover {
            background-color: #b702ff;
        }

        .perfil {
            border: none;
            background: none;
            cursor: pointer;
            padding: 0;
        }

        .foto-perfil {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #5c0180;
        }

        .logout {
            margin-left: 10px;
            padding: 10px 20px;
            font-size: 15px;
            border: none;
            border-radius: 5px;
            color: #fff;
            background-color: #5c0180;
            cursor: pointer;
            transition: 0.3s background;
        }

        .logout:hover {
            background-color: #280038;
        }

        .links {
            width: 100%;
            background-color: #f3f3f3;
            padding: 10px 0;
            text-align: center;
        }

        .nav-itens {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: space-around; 
        }

        .nav-itens > li {
            flex: 1; 
            text-align: center; 
        }

        .nav-itens > li > a {
            display: block;
            text-decoration: none;
            color: #202020;
            font-weight: bold;
            font-size: 18px; 
            padding: 10px; 
            border: 2px solid #000000; 
            border-radius: 20px; 
            transition: color 0.3s, background-color 0.3s;
        }

        .nav-itens > li > a:hover {
            color: #fff;
            background-color: #ffffff38;
        }
        .bio {
            font-size: 8px;
            transform: translate( -10%, 90%);
            width: 200%;
            padding-left: 10px;
            text-align: left;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .discord {
            font-size: 8px;
            transform: translate( -10%, 90%);
            width: 200%;
            padding-left: 10px;
            text-align: left;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 5px;

        }
    </style>
</head>
<body>

<nav>
        <a href="pagina_inicial.php" class="logo">PixelPedia</a>
        <div class="search-bar">
            <input type="text" id="txtBusca" placeholder="Buscar...">
        </div>
        <div class="btn">
            <?php if(isset($_SESSION['id'])): ?>
                    <button class="perfil" onclick="toggleMenu()">
                        <img src="<?php echo $_SESSION['foto_perfil']; ?>" alt="Foto de Perfil" class="foto-perfil">
                    </button>
                <button onclick="window.location.href='logout.php'" class="logout">Logout</button>
            <?php else: ?>
                <button onclick="window.location.href='logecad.php'" class="cadastro">Sign in/Sign up</button>
            <?php endif; ?>
        </div>
    </nav>
    
    <div class="links">
        <ul class="nav-itens">
            <li><a href="vava.php">Valorant</a></li>
            <li><a href="r6.php">Rainbow Six</a></li>
            <li><a href="lol.php">League of Legends</a></li>
            <li><a href="cs.php">Counter-Strike</a></li>
        </ul>
    </div>
    <script>
        function toggleMenu() {
            const menu = document.getElementById('perfilMenu');
            const container = menu.parentElement;

            container.classList.toggle('active');
        }

        document.addEventListener('click', function (event) {
            const menu = document.getElementById('perfilMenu');
            const container = menu.parentElement;

            if (!container.contains(event.target)) {
                container.classList.remove('active');
            }
        });
    </script>

<div class="perfil-container">
    <div class="wallpaper"></div>

    <div class="perfil-header">
        <div class="perfil-info">
            <div class="fotoperf">
            <img src="<?php echo $usuario['foto_perfil']; ?>" alt="Foto de Perfil">
            </div>
            <div class="nickname">
            <h1><?php echo $usuario['nick']; ?></h1></div>
            <div class="bio">
            <h1>Sobre mim: <?php echo $usuario['bio']; ?></h1></div>
            <div class="discord">
            <h1>Discord: <?php echo $usuario['discord']; ?></h1></div>
        </div>

        <?php if (isset($_SESSION['id'])): ?>
            <a href="editperf.php" class="editar-perfil-btn">Editar Perfil</a>
        <?php endif; ?>
    </div>

    <div class="perfil-info">
        <h2>Tags</h2>
        <div class="tags">
            <?php
            if (!empty($tagsList)) {
                $tagsArray = explode(", ", $tagsList);
                foreach ($tagsArray as $tag) {
                    echo "<span class='tag'>$tag</span>";
                }
            } else {
                echo "<span class='tag'>Nenhuma tag selecionada</span>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>
