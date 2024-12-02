<?php
include 'conexao.php';

$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = $_POST['nick']; 
    $data_nascimento = $_POST['data_nascimento'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($nova_senha !== $confirmar_senha) {
        echo "As senhas não coincidem!";
        exit;
    }

    $sql = "SELECT * FROM usuarios WHERE nick = ? AND data_nascimento = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $nick, $data_nascimento);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $senha_hashed = password_hash($nova_senha, PASSWORD_BCRYPT);

            $update_sql = "UPDATE usuarios SET senha = ? WHERE nick = ?";
            $update_stmt = $conn->prepare($update_sql);

            if ($update_stmt) {
                $update_stmt->bind_param("ss", $senha_hashed, $nick);
                $atualizado = $update_stmt->execute();

                if ($atualizado) {
                    $sucesso = true;
                } else {
                    echo "Erro ao redefinir a senha.";
                }

                $update_stmt->close();
            } else {
                echo "Erro ao preparar a consulta de atualização: " . $conn->error;
            }
        } else {
            echo "Nick ou data de nascimento incorretos.";
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta de verificação: " . $conn->error;
    }

    $conn->close();

}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
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


    .tela_redefinir {
        background-color: rgba(0, 0, 0, 0.8); 
        padding: 50px;
        border-radius: 15px;
        color: white;
        width: 300px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }


    .tela_redefinir h2 {
        text-align: center;
        margin-bottom: 20px;
    }


    .tela_redefinir input {
        padding: 15px;
        border: none;
        outline: none;
        font-size: 15px;
        width: 100%; 
        box-sizing: border-box; 
        margin-bottom: 15px;
        border-radius: 8px; 
    }


    .tela_redefinir button {
        background-color: purple;
        border: none;
        padding: 15px;
        width: 100%;
        border-radius: 10px;
        font-size: 16px;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }


    .tela_redefinir button:hover {
        background-color: blueviolet;
    }


    .tela_redefinir .erro {
        color: red;
        margin-top: 10px;
        font-size: 14px;
        text-align: center;
    }


    .tela_redefinir .sucesso {
        color: limegreen;
        margin-top: 10px;
        font-size: 14px;
        text-align: center;
    }
    </style>
    <script>
        function mostrarPopup() {
            alert("Senha redefinida com sucesso!");
        }

        <?php if ($sucesso): ?>
            mostrarPopup();
            setTimeout(function() {
                window.location.href = "logecad.php"; 
            }, 1000);
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="tela_redefinir">
        <h2>Redefinir Senha</h2>
        <form method="POST">
            <label for="nick">Nick do Usuário:</label>
            <input type="text" id="nick" name="nick" placeholder="Digite seu nick" required>

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" required>

            <label for="nova_senha">Nova Senha:</label>
            <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite a nova senha" required>

            <label for="confirmar_senha">Confirmar Nova Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a nova senha" required>

            <button type="submit">Redefinir Senha</button>

            <?php if (isset($erro)): ?>
                <div class="erro"><?php echo $erro; ?></div>
            <?php elseif ($sucesso): ?>
                <div class="sucesso">Senha redefinida com sucesso!</div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
