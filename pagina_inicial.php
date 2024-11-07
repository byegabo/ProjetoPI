<?php
session_start();
include('conexao.php');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Inicial</title>
    <script src="slide.js" defer></script>
    <link rel="stylesheet" href="pagina_inicial.css">
</head>
<body>
    <nav>
        <div class="logo">PixelPedia</div>
        <div class="search-bar">
            <input type="text" id="txtBusca" placeholder="Buscar...">
        </div>
        <div class="btn">
            <?php if(isset($_SESSION['id'])): ?>
                <button onclick="window.location.href='perfil.php'" class="perfil">
                    <img src="<?php echo $_SESSION['foto_perfil']; ?>" alt="Foto de Perfil" class="foto-perfil">
                </button>
                <button onclick="window.location.href='logout.php'" class="logout">Logout</button>
            <?php else: ?>
                <button onclick="window.location.href='logecad.php'" class="cadastro">Sing in/Sing up</button>
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

    <section class="slider">
        <div class="slider-content">
            <div class="slide-box">
                <img src="Valorant.jpg" alt="slide 1">
            </div>
            <div class="slide-box">
                <img src="CS.jpg" alt="slide 2">
            </div>
            <div class="slide-box">
                <img src="LOL.jpg" alt="slide 3">
            </div>
            <div class="slide-box">
                <img src="R6.jpg" alt="slide 4">
            </div>
        </div>

        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <button class="next" onclick="nextSlide()">&#10095;</button>
    </section>
    
    <div class="content">
        <div class="description">
            <h2>Bem-vindo ao PixelPedia</h2>
            <br>
            <p>O PixelPedia é o portal definitivo para jogadores que desejam se conectar com outros players e compartilhar suas conquistas no universo dos games. Personalize seu perfil para exibir seu nick, elo, redes sociais, e muito mais! Mostre ao mundo seu histórico de rankings e banimentos, garantindo transparência e seriedade na comunidade.</p>
            <br>
            <p>Na página inicial, visualize jogos, descubra perfis de outros jogadores e conheça seus estilos e rankings, com acesso completo aos perfis mediante login. Cada jogador possui uma breve descrição, informando o que busca, facilitando a interação e a criação de novas amizades. Com um clique, envie uma mensagem diretamente pelo PixelPedia e comece a jogar com quem tem os mesmos interesses.</p>
            <br>
            <p>Inspirado em plataformas como GameTree e Tracker.gg, o PixelPedia se diferencia pela praticidade e objetivo claro: conectar jogadores que buscam parceiros de jogo com rapidez e facilidade, sem burocracias. Seja bem-vindo ao PixelPedia, onde cada partida pode ser o início de uma nova conexão!</p>
        </div>
    </div>

    <div class="usuarios-online">
        <h3>Usuários Online</h3>
        <ul>
            <?php
            $query = "SELECT nick, status FROM usuarios";
            $result = mysqli_query($conn, $query);
            
            while($row = mysqli_fetch_assoc($result)): ?>
                <li>
                    <div class="status">
                        <?php if($row['status'] == 'online'): ?>
                            <span class="circle online"></span>
                        <?php elseif($row['status'] == 'ausente'): ?>
                            <span class="circle ausente"></span>
                        <?php else: ?>
                            <span class="circle offline"></span>
                        <?php endif; ?>
                    </div>
                    <span><?php echo $row['nick']; ?> - <?php echo ucfirst($row['status']); ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

</body>
</html>
