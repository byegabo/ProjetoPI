<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: logecad.php');
    exit();
}

require 'conexao.php';

$query = "SELECT id, nome FROM tags";
$result = mysqli_query($conn, $query);
$tags = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tags[] = $row;
}

$usuarios = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tags'])) {
    $selectedTags = $_POST['tags'];
    $tagIds = implode(',', array_map('intval', $selectedTags));

    $query = "
        SELECT DISTINCT u.id, u.nick, u.foto_perfil 
        FROM usuarios u
        INNER JOIN usuario_tags ut ON u.id = ut.usuario_id
        WHERE ut.tag_id IN ($tagIds)
    ";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $usuarios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PixelPedia - Valorant</title>
    <script src="slide.js" defer></script>
    <link rel="stylesheet" href="pagina_inicial.css">
    <style>
main {
    display: flex;
    flex-direction: column;
    align-items: center;
}
main h2 {
    font-family: 'Roboto', sans-serif;
    font-size: 2rem;
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}
form {
    width: 100%;
    max-width: 600px;
    text-align: center;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 30px;
}

.tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    margin: 20px 0;
}

.tags-container div {
    display: flex;
    align-items: center;
    gap: 5px;
}

.tags-container input[type="checkbox"] {
    appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid #9147ff;
    border-radius: 4px;
    background-color: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tags-container input[type="checkbox"]:checked {
    background-color: #9147ff;
    border-color: #772ce8;
    position: relative;
}

.tags-container input[type="checkbox"]:checked::before {
    content: '✓';
    color: white;
    font-size: 14px;
    position: absolute;
    top: 2px;
    left: 4px;
}

.tags-container label {
    font-family: 'Roboto', sans-serif;
    font-size: 1rem;
    color: #333;
    cursor: pointer;
}
form button[type="submit"] {
    display: block;
    margin: 20px auto;
    padding: 10px 20px;
    font-size: 1rem;
    font-family: 'Roboto', sans-serif;
    color: #fff;
    background-color: #9147ff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button[type="submit"]:hover {
    background-color: #772ce8;
}
.user-list-container {
    width: 100%;
    max-width: 600px;
    text-align: center;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-top: 30px;
}
.user-list {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}
.user-list li {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-align: center;
    background-color: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    width: 150px;
}
.user-list .user-photo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}
.user-list span {
    font-family: 'Roboto', sans-serif;
    font-size: 1rem;
    color: #333;
}
main p {
    font-family: 'Roboto', sans-serif;
    font-size: 1rem;
    text-align: center;
    color: #555;
}

    </style>
</head>
<body>
    <nav>
        <div class="logo">
        <a href="pagina_inicial.php">PixelPedia</a></div>
        <div class="search-bar">
            <input type="text" id="txtBusca" placeholder="Buscar...">
        </div>
        <div class="btn">
            <?php if(isset($_SESSION['id'])): ?>
                    <button class="perfil" onclick="toggleMenu()">
                        <img src="<?php echo $_SESSION['foto_perfil']; ?>" alt="Foto de Perfil" class="foto-perfil">
                    </button>
                    <div class="perfil-menu" id="perfilMenu">
                        <a style="text-decoration: none;" href="perfil.php">Meu Perfil</a>
                    </div>
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

    <main>
    <h2>Ache o seu Duo</h2>
    <form method="POST">
        <label for="tags">Selecione as tags:</label>
        <div class="tags-container">
            <?php foreach ($tags as $tag): ?>
                <div>
                    <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" id="tag-<?php echo $tag['id']; ?>">
                    <label for="tag-<?php echo $tag['id']; ?>"><?php echo htmlspecialchars($tag['nome']); ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit">Buscar</button>
    </form>

    <?php if (!empty($usuarios)): ?>
        <div class="user-list-container">
            <h3>Usuários encontrados:</h3>
            <ul class="user-list">
                <?php foreach ($usuarios as $usuario): ?>
                    <li>
                        <a href="perfil_usuario.php?id=<?php echo $usuario['id']; ?>" style="text-decoration: none; color: inherit;">
                            <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de <?php echo htmlspecialchars($usuario['nick']); ?>" class="user-photo">
                            <span><?php echo htmlspecialchars($usuario['nick']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>Nenhum usuário encontrado com as tags selecionadas.</p>
    <?php endif; ?>
</main>


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
</body>
</html>
