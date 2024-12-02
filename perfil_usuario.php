<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: logecad.php');
    exit();
}

require 'conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM usuarios WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $usuario = mysqli_fetch_assoc($result);
    } else {
        echo "Usuário não encontrado.";
        exit();
    }
} else {
    echo "ID do usuário não fornecido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nick']); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
            background-image: linear-gradient(45deg, white, purple);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .perfil-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 15px;
            color: white;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .perfil-container h1 {
            margin-bottom: 20px;
            font-size: 1.8rem;
            text-transform: uppercase;
        }
        .perfil-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        .foto-perfil {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid purple;
        }
        .perfil-detalhes {
            text-align: left;
            width: 100%;
        }
        .perfil-detalhes p {
            font-size: 1rem;
            margin: 10px 0;
        }
        .perfil-detalhes p strong {
            color: #9147ff;
        }
    </style>
</head>
<body>
    <div class="perfil-container">
        <h1>Perfil de <?php echo htmlspecialchars($usuario['nick']); ?></h1>
        <div class="perfil-info">
            <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de <?php echo htmlspecialchars($usuario['nick']); ?>" class="foto-perfil">
            <div class="perfil-detalhes">
                <p><strong>Nick:</strong> <?php echo htmlspecialchars($usuario['nick']); ?></p>
                <p><strong>Bio:</strong> <?php echo htmlspecialchars($usuario['bio']); ?></p>
                <p><strong>Discord:</strong> <?php echo htmlspecialchars($usuario['discord']); ?></p>

                <p><a style="text-decoration:none; color:blueviolet" href="pagina_inicial.php">Voltar a página principal</a></p>
            </div>
        </div>
    </div>
</body>
</html>