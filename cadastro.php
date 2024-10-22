<?php
    include('conexaocad.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nick = $_POST['nick'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $confirmar_senha = $_POST['confirmar_senha'];
        $data_nascimento = $_POST['data_nascimento'];

        if ($senha !== $confirmar_senha) {
            $erro = "As senhas não coincidem!";
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "SELECT * FROM usuarios WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $erro = "E-mail já cadastrado!";
            } else {
                $sql = "INSERT INTO usuarios (nick, email, senha, data_nascimento) VALUES ('$nick', '$email', '$senha_hash', '$data_nascimento')";
                if ($conn->query($sql) === TRUE) {
                    header("Location: login.php"); 
                    exit();
                } else {
                    $erro = "Erro ao cadastrar o usuário: " . $conn->error;
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="tela_login">
        <h1>Cadastro</h1>
        <form method="POST">
            <input type="text" name="nick" placeholder="Seu Nick" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="password" name="confirmar_senha" placeholder="Confirmar Senha" required>
            <input type="date" name="data_nascimento" placeholder="Data de Nascimento" required>
            <button type="submit">Cadastrar</button>
            <?php if(isset($erro)): ?>
                <div class="erro"><?php echo $erro; ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
